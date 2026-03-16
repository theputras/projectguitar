<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tonewood;
use App\Services\ImageService;
use Illuminate\Http\Request;

class TonewoodApiController extends Controller
{
    /**
     * GET /api/tonewood
     */
    public function index(Request $request)
    {
        $query = Tonewood::sorted();

        if ($request->has('type') && in_array($request->type, ['body', 'neck', 'fretboard'])) {
            $query->byType($request->type);
        }

        $tonewoods = $query->get();

        return response()->json([
            'success' => true,
            'data'    => $tonewoods,
        ]);
    }

    /**
     * GET /api/tonewood/{id}
     */
    public function show($id)
    {
        $tonewood = Tonewood::findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $tonewood,
        ]);
    }

    /**
     * POST /api/admin/tonewood
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'type'        => 'required|in:body,neck,fretboard',
            'origin'      => 'nullable|string|max:255',
            'description' => 'nullable|string|max:5000',
            'sort_order'  => 'nullable|integer|min:0',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
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

        $validated['characteristics'] = array_filter([
            'tone'        => $validated['char_tone'] ?? null,
            'density'     => $validated['char_density'] ?? null,
            'workability' => $validated['char_workability'] ?? null,
            'stability'   => $validated['char_stability'] ?? null,
            'color'       => $validated['char_color'] ?? null,
        ]);

        unset($validated['char_tone'], $validated['char_density'], $validated['char_workability'], $validated['char_stability'], $validated['char_color']);

        $tonewood = Tonewood::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tonewood created. Image auto-compressed to WebP.',
            'data'    => $tonewood,
        ], 201);
    }

    /**
     * PUT /api/admin/tonewood/{id}
     */
    public function update(Request $request, $id)
    {
        $tonewood = Tonewood::findOrFail($id);

        $validated = $request->validate([
            'name'        => 'sometimes|required|string|max:255',
            'type'        => 'sometimes|required|in:body,neck,fretboard',
            'origin'      => 'nullable|string|max:255',
            'description' => 'nullable|string|max:5000',
            'sort_order'  => 'nullable|integer|min:0',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'char_tone'        => 'nullable|string|max:255',
            'char_density'     => 'nullable|string|max:255',
            'char_workability' => 'nullable|string|max:255',
            'char_stability'   => 'nullable|string|max:255',
            'char_color'       => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('image')) {
            ImageService::delete($tonewood->image);
            $result = ImageService::upload($request->file('image'), 'tonewoods', 1200, 800);
            $validated['image'] = $result['path'];
        }

        $charFields = ['char_tone', 'char_density', 'char_workability', 'char_stability', 'char_color'];
        if (collect($charFields)->contains(fn ($f) => $request->has($f))) {
            $existing = $tonewood->characteristics ?? [];
            $validated['characteristics'] = array_filter(array_merge($existing, [
                'tone'        => $validated['char_tone'] ?? ($existing['tone'] ?? null),
                'density'     => $validated['char_density'] ?? ($existing['density'] ?? null),
                'workability' => $validated['char_workability'] ?? ($existing['workability'] ?? null),
                'stability'   => $validated['char_stability'] ?? ($existing['stability'] ?? null),
                'color'       => $validated['char_color'] ?? ($existing['color'] ?? null),
            ]));
        }

        unset($validated['char_tone'], $validated['char_density'], $validated['char_workability'], $validated['char_stability'], $validated['char_color']);

        $tonewood->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tonewood updated successfully.',
            'data'    => $tonewood->fresh(),
        ]);
    }

    /**
     * DELETE /api/admin/tonewood/{id}
     */
    public function destroy($id)
    {
        $tonewood = Tonewood::findOrFail($id);

        ImageService::delete($tonewood->image);
        $tonewood->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tonewood deleted successfully.',
        ]);
    }
}
