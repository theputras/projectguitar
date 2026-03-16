<?php

namespace App\Http\Controllers;

use App\Models\Tonewood;
use App\Services\ImageService;
use Illuminate\Http\Request;

class AdminTonewoodController extends Controller
{
    public function index()
    {
        $tonewoods = Tonewood::sorted()->paginate(20);
        return view('admin.tonewood_index', compact('tonewoods'));
    }

    public function create()
    {
        return view('admin.tonewood_form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'type'        => 'required|in:body,neck,fretboard',
            'origin'      => 'nullable|string|max:255',
            'description' => 'nullable|string|max:5000',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'sort_order'  => 'nullable|integer|min:0',
            'char_tone'        => 'nullable|string|max:255',
            'char_density'     => 'nullable|string|max:255',
            'char_workability' => 'nullable|string|max:255',
            'char_stability'   => 'nullable|string|max:255',
            'char_color'       => 'nullable|string|max:255',
        ]);

        // Upload & compress image
        if ($request->hasFile('image')) {
            $result = ImageService::upload($request->file('image'), 'tonewoods', 1200, 800);
            $validated['image'] = $result['path'];
        }

        // Build characteristics JSON
        $validated['characteristics'] = array_filter([
            'tone'        => $validated['char_tone'] ?? null,
            'density'     => $validated['char_density'] ?? null,
            'workability' => $validated['char_workability'] ?? null,
            'stability'   => $validated['char_stability'] ?? null,
            'color'       => $validated['char_color'] ?? null,
        ]);

        unset($validated['char_tone'], $validated['char_density'], $validated['char_workability'], $validated['char_stability'], $validated['char_color']);

        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        Tonewood::create($validated);

        return redirect()->route('admin.tonewoods.index')->with('success', 'Tonewood material added successfully.');
    }

    public function edit(Tonewood $tonewood)
    {
        return view('admin.tonewood_form', compact('tonewood'));
    }

    public function update(Request $request, Tonewood $tonewood)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'type'        => 'required|in:body,neck,fretboard',
            'origin'      => 'nullable|string|max:255',
            'description' => 'nullable|string|max:5000',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'sort_order'  => 'nullable|integer|min:0',
            'char_tone'        => 'nullable|string|max:255',
            'char_density'     => 'nullable|string|max:255',
            'char_workability' => 'nullable|string|max:255',
            'char_stability'   => 'nullable|string|max:255',
            'char_color'       => 'nullable|string|max:255',
        ]);

        // Upload & compress new image if provided
        if ($request->hasFile('image')) {
            ImageService::delete($tonewood->image);
            $result = ImageService::upload($request->file('image'), 'tonewoods', 1200, 800);
            $validated['image'] = $result['path'];
        }

        // Build characteristics JSON
        $validated['characteristics'] = array_filter([
            'tone'        => $validated['char_tone'] ?? null,
            'density'     => $validated['char_density'] ?? null,
            'workability' => $validated['char_workability'] ?? null,
            'stability'   => $validated['char_stability'] ?? null,
            'color'       => $validated['char_color'] ?? null,
        ]);

        unset($validated['char_tone'], $validated['char_density'], $validated['char_workability'], $validated['char_stability'], $validated['char_color']);

        $tonewood->update($validated);

        return redirect()->route('admin.tonewoods.index')->with('success', 'Tonewood material updated successfully.');
    }

    public function destroy(Tonewood $tonewood)
    {
        ImageService::delete($tonewood->image);
        $tonewood->delete();
        return back()->with('success', 'Tonewood material deleted successfully.');
    }
}
