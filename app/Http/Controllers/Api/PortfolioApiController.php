<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use App\Services\ImageService;
use Illuminate\Http\Request;

class PortfolioApiController extends Controller
{
    /**
     * GET /api/products
     * List all published instruments. Filterable by ?category=bass|guitar
     */
    public function index(Request $request)
    {
        $query = Portfolio::published()->latest();

        if ($request->has('category') && in_array($request->category, ['bass', 'guitar'])) {
            $query->where('category', $request->category);
        }

        $portfolios = $query->paginate($request->input('limit', 12));

        return response()->json([
            'success' => true,
            'data'    => $portfolios,
        ]);
    }

    /**
     * GET /api/products/{id}
     * Show a single instrument with full details.
     */
    public function show($id)
    {
        $portfolio = Portfolio::findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $portfolio,
        ]);
    }

    /**
     * POST /api/admin/products
     * Create a new instrument (admin only).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'category'     => 'required|string|in:bass,guitar',
            'description'  => 'nullable|string',
            'wood_type'    => 'nullable|string|max:255',
            'pickup'       => 'nullable|string|max:255',
            'scale_length' => 'nullable|string|max:100',
            'finish'       => 'nullable|string|max:255',
            'strings'      => 'nullable|integer|min:4|max:12',
            'price_range'  => 'nullable|string|max:100',
            'is_featured'  => 'nullable|boolean',
            'status'       => 'nullable|in:draft,published',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'gallery.*'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        // Upload & compress main image
        if ($request->hasFile('image')) {
            $result = ImageService::upload($request->file('image'), 'portfolio', null, null, null, true);
            $validated['image'] = $result['path'];
        }

        // Upload & compress gallery images
        $validated['gallery'] = [];
        if ($request->hasFile('gallery')) {
            $validated['gallery'] = ImageService::uploadGallery($request->file('gallery'), 'portfolio');
        }

        // Build specifications
        $validated['specifications'] = array_filter([
            'wood_type'    => $validated['wood_type'] ?? null,
            'pickup'       => $validated['pickup'] ?? null,
            'scale_length' => $validated['scale_length'] ?? null,
            'finish'       => $validated['finish'] ?? null,
            'strings'      => $validated['strings'] ?? null,
        ]);

        $validated['is_featured'] = $validated['is_featured'] ?? false;
        $validated['status'] = $validated['status'] ?? 'draft';

        $portfolio = Portfolio::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully. Images auto-compressed to WebP.',
            'data'    => $portfolio,
        ], 201);
    }

    /**
     * PUT /api/admin/products/{id}
     * Update an instrument (admin only).
     */
    public function update(Request $request, $id)
    {
        $portfolio = Portfolio::findOrFail($id);

        $validated = $request->validate([
            'title'        => 'sometimes|required|string|max:255',
            'category'     => 'sometimes|required|string|in:bass,guitar',
            'description'  => 'nullable|string',
            'wood_type'    => 'nullable|string|max:255',
            'pickup'       => 'nullable|string|max:255',
            'scale_length' => 'nullable|string|max:100',
            'finish'       => 'nullable|string|max:255',
            'strings'      => 'nullable|integer|min:4|max:12',
            'price_range'  => 'nullable|string|max:100',
            'is_featured'  => 'nullable|boolean',
            'status'       => 'nullable|in:draft,published',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        // Upload & compress new image
        if ($request->hasFile('image')) {
            ImageService::delete($portfolio->image);
            $result = ImageService::upload($request->file('image'), 'portfolio', null, null, null, true);
            $validated['image'] = $result['path'];
        }

        // Rebuild specifications if any spec field is present
        $specFields = ['wood_type', 'pickup', 'scale_length', 'finish', 'strings'];
        if (collect($specFields)->contains(fn ($f) => $request->has($f))) {
            $existing = $portfolio->specifications ?? [];
            $validated['specifications'] = array_filter(array_merge($existing, [
                'wood_type'    => $validated['wood_type'] ?? ($existing['wood_type'] ?? null),
                'pickup'       => $validated['pickup'] ?? ($existing['pickup'] ?? null),
                'scale_length' => $validated['scale_length'] ?? ($existing['scale_length'] ?? null),
                'finish'       => $validated['finish'] ?? ($existing['finish'] ?? null),
                'strings'      => $validated['strings'] ?? ($existing['strings'] ?? null),
            ]));
        }

        $portfolio->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully.',
            'data'    => $portfolio->fresh(),
        ]);
    }

    /**
     * DELETE /api/admin/products/{id}
     * Delete an instrument (admin only).
     */
    public function destroy($id)
    {
        $portfolio = Portfolio::findOrFail($id);

        ImageService::delete($portfolio->image);
        ImageService::deleteMany($portfolio->gallery);

        $portfolio->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully.',
        ], 200);
    }
}
