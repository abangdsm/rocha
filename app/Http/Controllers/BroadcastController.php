<?php

namespace App\Http\Controllers;

use App\Models\Broadcast;
use App\Models\Device;
use App\Models\Contact;
use App\Models\Group;
use App\Models\Tag;
use App\Jobs\SendBroadcastJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BroadcastController extends Controller
{
    public function index()
    {
        $broadcasts = Auth::user()->broadcasts()->with('device')->latest()->paginate(20);
        return view('broadcasts.index', compact('broadcasts'));
    }

    public function create()
    {
        $devices = Auth::user()->devices()->where('status', 'connected')->get();
        $groups = Auth::user()->groups;
        $tags = Auth::user()->tags;
        $contacts = Auth::user()->contacts;
        
        return view('broadcasts.create', compact('devices', 'groups', 'tags', 'contacts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'device_id' => 'required|exists:devices,id',
            'message' => 'required|string',
            'send_type' => 'required|in:all,groups,tags,selected',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        // Get contacts based on send type
        $contacts = collect();
        
        if ($request->send_type == 'all') {
            $contacts = Auth::user()->contacts;
        } elseif ($request->send_type == 'groups' && $request->groups) {
            $contacts = Contact::whereHas('groups', function($q) use ($request) {
                $q->whereIn('groups.id', $request->groups);
            })->where('user_id', Auth::id())->get();
        } elseif ($request->send_type == 'tags' && $request->tags) {
            $contacts = Contact::whereHas('tags', function($q) use ($request) {
                $q->whereIn('tags.id', $request->tags);
            })->where('user_id', Auth::id())->get();
        } elseif ($request->send_type == 'selected' && $request->contacts) {
            $contacts = Contact::whereIn('id', $request->contacts)
                              ->where('user_id', Auth::id())
                              ->get();
        }

        if ($contacts->isEmpty()) {
            return back()->with('error', 'No contacts selected for broadcast.');
        }

        // Create broadcast
        $broadcast = Broadcast::create([
            'user_id' => Auth::id(),
            'device_id' => $request->device_id,
            'name' => $request->name,
            'message' => $request->message,
            'type' => 'text',
            'status' => $request->scheduled_at ? 'scheduled' : 'pending',
            'total_contacts' => $contacts->count(),
            'scheduled_at' => $request->scheduled_at,
        ]);

        // Attach contacts to broadcast
        foreach ($contacts as $contact) {
            $broadcast->broadcastContacts()->create([
                'contact_id' => $contact->id,
                'status' => 'pending',
            ]);
        }

        // If not scheduled, start sending immediately
        if (!$request->scheduled_at) {
            $broadcast->update(['status' => 'processing', 'started_at' => now()]);
            
            // Dispatch jobs for each contact
            foreach ($broadcast->broadcastContacts as $broadcastContact) {
                SendBroadcastJob::dispatch($broadcast, $broadcastContact);
            }
        }

        return redirect()->route('broadcasts.index')->with('success', 'Broadcast created successfully.');
    }

    public function show(Broadcast $broadcast)
    {
        if ($broadcast->user_id !== Auth::id()) {
            abort(403);
        }
        
        $broadcast->load('broadcastContacts.contact', 'device');
        
        return view('broadcasts.show', compact('broadcast'));
    }

    public function edit(Broadcast $broadcast)
    {
        if ($broadcast->user_id !== Auth::id()) {
            abort(403);
        }
        
        if ($broadcast->status != 'pending') {
            return redirect()->route('broadcasts.index')->with('error', 'Cannot edit broadcast that is already processing.');
        }
        
        $devices = Auth::user()->devices()->where('status', 'connected')->get();
        
        return view('broadcasts.edit', compact('broadcast', 'devices'));
    }

    public function update(Request $request, Broadcast $broadcast)
    {
        if ($broadcast->user_id !== Auth::id()) {
            abort(403);
        }
        
        if ($broadcast->status != 'pending') {
            return back()->with('error', 'Cannot edit broadcast that is already processing.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'device_id' => 'required|exists:devices,id',
            'message' => 'required|string',
            'scheduled_at' => 'nullable|date|after:now',
        ]);
        
        $broadcast->update([
            'name' => $request->name,
            'device_id' => $request->device_id,
            'message' => $request->message,
            'scheduled_at' => $request->scheduled_at,
        ]);
        
        return redirect()->route('broadcasts.index')->with('success', 'Broadcast updated successfully.');
    }

    public function destroy(Broadcast $broadcast)
    {
        if ($broadcast->user_id !== Auth::id()) {
            abort(403);
        }
        
        $broadcast->delete();
        
        return redirect()->route('broadcasts.index')->with('success', 'Broadcast deleted successfully.');
    }

    public function cancel(Broadcast $broadcast)
    {
        if ($broadcast->user_id !== Auth::id()) {
            abort(403);
        }
        
        if ($broadcast->status == 'completed') {
            return back()->with('error', 'Cannot cancel completed broadcast.');
        }
        
        $broadcast->update(['status' => 'cancelled']);
        
        return redirect()->route('broadcasts.index')->with('success', 'Broadcast cancelled.');
    }

    public function retry(Broadcast $broadcast)
    {
        if ($broadcast->user_id !== Auth::id()) {
            abort(403);
        }
        
        $failedContacts = $broadcast->broadcastContacts()
                                    ->where('status', 'failed')
                                    ->get();
        
        if ($failedContacts->isEmpty()) {
            return back()->with('info', 'No failed contacts to retry.');
        }
        
        $broadcast->update([
            'status' => 'processing',
            'started_at' => now(),
        ]);
        
        foreach ($failedContacts as $contact) {
            $contact->update(['status' => 'pending']);
            SendBroadcastJob::dispatch($broadcast, $contact);
        }
        
        return redirect()->route('broadcasts.show', $broadcast)->with('success', 'Retrying failed contacts.');
    }
}