<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Content;
use Illuminate\Http\Request;

class ContentApiController extends Controller
{
    /**
     * GET /api/content?section=about
     * Get published content by section.
     */
    public function index(Request $request)
    {
        $query = Content::published();

        if ($request->has('section')) {
            $query->section($request->section);
        }

        $contents = $query->get();

        return response()->json([
            'success' => true,
            'data'    => $contents,
        ]);
    }

    /**
     * GET /api/admin/content
     * List all content (admin).
     */
    public function adminIndex(Request $request)
    {
        $query = Content::latest();

        if ($request->has('section') && $request->section !== 'all') {
            $query->section($request->section);
        }

        $contents = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data'    => $contents,
        ]);
    }

    /**
     * POST /api/admin/content
     * Create new content (admin).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'content'      => 'required|string',
            'section'      => 'required|in:about,tonewood,shipping,faq,custom_order,general',
            'is_published' => 'nullable|boolean',
        ]);

        $validated['is_published'] = $validated['is_published'] ?? true;

        $content = Content::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Content created successfully.',
            'data'    => $content,
        ], 201);
    }

    /**
     * GET /api/admin/content/{id}
     * Show single content (admin).
     */
    public function show($id)
    {
        $content = Content::findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $content,
        ]);
    }

    /**
     * PUT /api/admin/content/{id}
     * Update content (admin).
     */
    public function update(Request $request, $id)
    {
        $content = Content::findOrFail($id);

        $validated = $request->validate([
            'title'        => 'sometimes|required|string|max:255',
            'content'      => 'sometimes|required|string',
            'section'      => 'sometimes|required|in:about,tonewood,shipping,faq,custom_order,general',
            'is_published' => 'nullable|boolean',
        ]);

        $content->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Content updated successfully.',
            'data'    => $content->fresh(),
        ]);
    }

    /**
     * DELETE /api/admin/content/{id}
     * Delete content (admin).
     */
    public function destroy($id)
    {
        $content = Content::findOrFail($id);
        $content->delete();

        return response()->json([
            'success' => true,
            'message' => 'Content deleted successfully.',
        ]);
    }
}
