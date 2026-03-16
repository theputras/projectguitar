<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqApiController extends Controller
{
    /**
     * GET /api/faq
     * List all active FAQs (public).
     */
    public function index()
    {
        $faqs = Faq::active()->sorted()->get();

        return response()->json([
            'success' => true,
            'data'    => $faqs,
        ]);
    }

    /**
     * GET /api/admin/faq
     * List all FAQs (admin).
     */
    public function adminIndex()
    {
        $faqs = Faq::sorted()->paginate(20);

        return response()->json([
            'success' => true,
            'data'    => $faqs,
        ]);
    }

    /**
     * POST /api/admin/faq
     * Create a new FAQ (admin).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'question'   => 'required|string|max:500',
            'answer'     => 'required|string|max:5000',
            'sort_order' => 'nullable|integer|min:0',
            'is_active'  => 'nullable|boolean',
        ]);

        $validated['is_active'] = $validated['is_active'] ?? true;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $faq = Faq::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'FAQ created successfully.',
            'data'    => $faq,
        ], 201);
    }

    /**
     * GET /api/admin/faq/{id}
     * Show a single FAQ (admin).
     */
    public function show($id)
    {
        $faq = Faq::findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $faq,
        ]);
    }

    /**
     * PUT /api/admin/faq/{id}
     * Update a FAQ (admin).
     */
    public function update(Request $request, $id)
    {
        $faq = Faq::findOrFail($id);

        $validated = $request->validate([
            'question'   => 'sometimes|required|string|max:500',
            'answer'     => 'sometimes|required|string|max:5000',
            'sort_order' => 'nullable|integer|min:0',
            'is_active'  => 'nullable|boolean',
        ]);

        $faq->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'FAQ updated successfully.',
            'data'    => $faq->fresh(),
        ]);
    }

    /**
     * DELETE /api/admin/faq/{id}
     * Delete a FAQ (admin).
     */
    public function destroy($id)
    {
        $faq = Faq::findOrFail($id);
        $faq->delete();

        return response()->json([
            'success' => true,
            'message' => 'FAQ deleted successfully.',
        ]);
    }
}
