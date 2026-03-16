<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use App\Services\ImageService;
use Illuminate\Http\Request;

class AdminPortfolioController extends Controller
{
    public function index()
    {
        $portfolios = Portfolio::latest()->paginate(10);
        return view('admin.portfolio_index', compact('portfolios'));
    }

    public function create()
    {
        return view('admin.portfolio_form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'category'     => 'required|string|in:bass,guitar',
            'description'  => 'nullable|string',
            'image'        => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
            'wood_type'    => 'nullable|string|max:255',
            'pickup'       => 'nullable|string|max:255',
            'scale_length' => 'nullable|string|max:100',
            'finish'       => 'nullable|string|max:255',
            'strings'      => 'nullable|integer|min:4|max:12',
            'price_range'  => 'nullable|string|max:100',
            'is_featured'  => 'nullable|boolean',
            'status'       => 'required|in:draft,published',
            'gallery.*'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        // Upload & compress main image (auto-resize + WebP conversion)
        if ($request->hasFile('image')) {
            $result = ImageService::upload($request->file('image'), 'portfolio', null, null, null, true);
            $validated['image'] = $result['path'];
        }

        // Upload & compress gallery images
        $validated['gallery'] = [];
        if ($request->hasFile('gallery')) {
            $validated['gallery'] = ImageService::uploadGallery($request->file('gallery'), 'portfolio');
        }

        // Build specifications JSON
        $validated['specifications'] = array_filter([
            'wood_type'    => $validated['wood_type'] ?? null,
            'pickup'       => $validated['pickup'] ?? null,
            'scale_length' => $validated['scale_length'] ?? null,
            'finish'       => $validated['finish'] ?? null,
            'strings'      => $validated['strings'] ?? null,
        ]);

        $validated['is_featured'] = $request->has('is_featured');

        Portfolio::create($validated);

        return redirect()->route('admin.portfolio.index')->with('success', 'Portfolio instrument added successfully.');
    }

    public function edit(Portfolio $portfolio)
    {
        return view('admin.portfolio_form', compact('portfolio'));
    }

    public function update(Request $request, Portfolio $portfolio)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'category'     => 'required|string|in:bass,guitar',
            'description'  => 'nullable|string',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'wood_type'    => 'nullable|string|max:255',
            'pickup'       => 'nullable|string|max:255',
            'scale_length' => 'nullable|string|max:100',
            'finish'       => 'nullable|string|max:255',
            'strings'      => 'nullable|integer|min:4|max:12',
            'price_range'  => 'nullable|string|max:100',
            'is_featured'  => 'nullable|boolean',
            'status'       => 'required|in:draft,published',
            'gallery.*'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        // Upload new main image if provided
        if ($request->hasFile('image')) {
            ImageService::delete($portfolio->image);
            $result = ImageService::upload($request->file('image'), 'portfolio', null, null, null, true);
            $validated['image'] = $result['path'];
        }

        // Handle gallery: keep existing + add new compressed images
        $galleryPaths = $portfolio->gallery ?? [];
        if ($request->hasFile('gallery')) {
            $newPaths = ImageService::uploadGallery($request->file('gallery'), 'portfolio');
            $galleryPaths = array_merge($galleryPaths, $newPaths);
        }

        // Handle removing specific gallery images
        if ($request->has('remove_gallery')) {
            foreach ($request->input('remove_gallery') as $removePath) {
                ImageService::delete($removePath);
                $galleryPaths = array_values(array_diff($galleryPaths, [$removePath]));
            }
        }
        $validated['gallery'] = $galleryPaths;

        // Build specifications JSON
        $validated['specifications'] = array_filter([
            'wood_type'    => $validated['wood_type'] ?? null,
            'pickup'       => $validated['pickup'] ?? null,
            'scale_length' => $validated['scale_length'] ?? null,
            'finish'       => $validated['finish'] ?? null,
            'strings'      => $validated['strings'] ?? null,
        ]);

        $validated['is_featured'] = $request->has('is_featured');

        $portfolio->update($validated);

        return redirect()->route('admin.portfolio.index')->with('success', 'Portfolio instrument updated successfully.');
    }

    public function destroy(Portfolio $portfolio)
    {
        ImageService::delete($portfolio->image);
        ImageService::deleteMany($portfolio->gallery);

        $portfolio->delete();
        return back()->with('success', 'Portfolio instrument deleted successfully.');
    }
}