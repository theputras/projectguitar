<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Http\Request;

class AdminContentController extends Controller
{
    public function index(Request $request)
    {
        $query = Content::latest();

        // Filter by section
        if ($request->has('section') && $request->section !== 'all') {
            $query->section($request->section);
        }

        $contents = $query->paginate(20);
        return view('admin.content_index', compact('contents'));
    }

    public function create()
    {
        return view('admin.content_form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'content'      => 'required|string',
            'section'      => 'required|in:about,tonewood,shipping,faq,custom_order,general',
            'is_published' => 'nullable|boolean',
        ]);

        $validated['is_published'] = $request->has('is_published');

        Content::create($validated);

        return redirect()->route('admin.contents.index')->with('success', 'Content created successfully.');
    }

    public function edit(Content $content)
    {
        return view('admin.content_form', compact('content'));
    }

    public function update(Request $request, Content $content)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'content'      => 'required|string',
            'section'      => 'required|in:about,tonewood,shipping,faq,custom_order,general',
            'is_published' => 'nullable|boolean',
        ]);

        $validated['is_published'] = $request->has('is_published');

        $content->update($validated);

        return redirect()->route('admin.contents.index')->with('success', 'Content updated successfully.');
    }

    public function destroy(Content $content)
    {
        $content->delete();
        return back()->with('success', 'Content deleted successfully.');
    }
}
