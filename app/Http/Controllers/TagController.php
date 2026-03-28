<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TagController extends Controller
{
    public function index()
    {
        $tags = Auth::user()->tags()->latest()->paginate(20);
        return view('tags.index', compact('tags'));
    }

    public function create()
    {
        return view('tags.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:7',
        ]);

        Tag::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'color' => $request->color ?? '#3B82F6',
        ]);

        return redirect()->route('tags.index')->with('success', 'Tag created successfully.');
    }

    public function show(Tag $tag)
    {
        if ($tag->user_id !== Auth::id()) {
            abort(403);
        }
        return view('tags.show', compact('tag'));
    }

    public function edit(Tag $tag)
    {
        if ($tag->user_id !== Auth::id()) {
            abort(403);
        }
        return view('tags.edit', compact('tag'));
    }

    public function update(Request $request, Tag $tag)
    {
        if ($tag->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:7',
        ]);

        $tag->update([
            'name' => $request->name,
            'color' => $request->color ?? '#3B82F6',
        ]);

        return redirect()->route('tags.index')->with('success', 'Tag updated successfully.');
    }

    public function destroy(Tag $tag)
    {
        if ($tag->user_id !== Auth::id()) {
            abort(403);
        }

        $tag->delete();
        return redirect()->route('tags.index')->with('success', 'Tag deleted successfully.');
    }
}