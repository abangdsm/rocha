<?php

namespace App\Http\Controllers;

use App\Exports\ContactsExport;
use App\Imports\ContactsImport;
use App\Models\Contact;
use App\Models\Group;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $query = Auth::user()->contacts();
        
        // Filter by group
        if ($request->group_id) {
            $query->whereHas('groups', function($q) use ($request) {
                $q->where('group_id', $request->group_id);
            });
        }
        
        // Filter by tag
        if ($request->tag_id) {
            $query->whereHas('tags', function($q) use ($request) {
                $q->where('tag_id', $request->tag_id);
            });
        }
        
        // Search
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }
        
        $contacts = $query->latest()->paginate(20);
        $groups = Auth::user()->groups;
        $tags = Auth::user()->tags;
        
        return view('contacts.index', compact('contacts', 'groups', 'tags'));
    }

    public function create()
    {
        $groups = Auth::user()->groups;
        $tags = Auth::user()->tags;
        return view('contacts.create', compact('groups', 'tags'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:whatsapp_contacts,phone',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'groups' => 'nullable|array',
            'tags' => 'nullable|array',
        ]);

        $contact = Contact::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'notes' => $request->notes,
        ]);

        if ($request->groups) {
            $contact->groups()->sync($request->groups);
        }

        if ($request->tags) {
            $contact->tags()->sync($request->tags);
        }

        return redirect()->route('contacts.index')->with('success', 'Contact created successfully.');
    }

    public function show(Contact $contact)
    {
        if ($contact->user_id !== Auth::id()) {
            abort(403);
        }
        return view('contacts.show', compact('contact'));
    }

    public function edit(Contact $contact)
    {
        if ($contact->user_id !== Auth::id()) {
            abort(403);
        }
        $groups = Auth::user()->groups;
        $tags = Auth::user()->tags;
        return view('contacts.edit', compact('contact', 'groups', 'tags'));
    }

    public function update(Request $request, Contact $contact)
    {
        if ($contact->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:whatsapp_contacts,phone,' . $contact->id,
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'groups' => 'nullable|array',
            'tags' => 'nullable|array',
        ]);

        $contact->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'notes' => $request->notes,
        ]);

        if ($request->groups) {
            $contact->groups()->sync($request->groups);
        } else {
            $contact->groups()->detach();
        }

        if ($request->tags) {
            $contact->tags()->sync($request->tags);
        } else {
            $contact->tags()->detach();
        }

        return redirect()->route('contacts.index')->with('success', 'Contact updated successfully.');
    }

    public function destroy(Contact $contact)
    {
        if ($contact->user_id !== Auth::id()) {
            abort(403);
        }
        
        $contact->delete();
        return redirect()->route('contacts.index')->with('success', 'Contact deleted successfully.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        Excel::import(new ContactsImport, $request->file('file'));
        
        return redirect()->route('contacts.index')->with('success', 'Contacts imported successfully.');
    }

    public function export()
    {
        return Excel::download(new ContactsExport, 'contacts.xlsx');
    }

    public function sendMessage(Request $request, Contact $contact)
    {
        // Akan diimplementasikan nanti
        return redirect()->back()->with('info', 'Send message feature coming soon.');
    }
}