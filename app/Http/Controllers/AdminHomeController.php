<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\ImageService;
use Illuminate\Http\Request;

class AdminHomeController extends Controller
{
    public function edit()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('admin.home_edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'about_image'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'release_image_1' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'release_image_2' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'release_image_3' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        // 1. Handle Text Inputs
        foreach ($request->except('_token', '_method') as $key => $value) {
            if ($request->hasFile($key)) {
                continue;
            }
            
            if (!in_array($key, ['about_image', 'release_image_1', 'release_image_2', 'release_image_3'])) {
                Setting::updateOrCreate(['key' => $key], ['value' => $value]);
            }
        }

        // 2. Handle File Uploads (About Image)
        if ($request->hasFile('about_image')) {
            $oldImage = Setting::where('key', 'about_image')->first();
            if ($oldImage) {
                ImageService::delete($oldImage->value);
            }

            // High quality for home banner
            $result = ImageService::upload($request->file('about_image'), 'settings', 1920, 1080, 85);
            Setting::updateOrCreate(['key' => 'about_image'], ['value' => $result['path']]);
        }

        // 3. Handle File Uploads (Release Ars Images - 3 Slot)
        for ($i = 1; $i <= 3; $i++) {
            $fileKey = 'release_image_' . $i;
            
            if ($request->hasFile($fileKey)) {
                $oldImage = Setting::where('key', $fileKey)->first();
                if ($oldImage) {
                    ImageService::delete($oldImage->value);
                }

                // Square-ish aspect ratio for release images, good quality
                $result = ImageService::upload($request->file($fileKey), 'settings', 800, 800, 80);
                Setting::updateOrCreate(['key' => $fileKey], ['value' => $result['path']]);
            }
        }

        return back()->with('success', 'Home settings updated successfully.');
    }
}