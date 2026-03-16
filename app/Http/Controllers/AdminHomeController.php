<?php

namespace App\Http\Controllers;

use App\Models\Setting;
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
        // 1. Handle Text Inputs
        foreach ($request->except('_token', '_method') as $key => $value) {
            // Jika input adalah file, skip dulu (ditangani di bawah)
            if ($request->hasFile($key)) {
                continue;
            }
            
            // Jika key bukan key gambar (karena gambar akan dihandle terpisah)
            // Kita simpan text biasa
            if (!in_array($key, ['about_image', 'release_image_1', 'release_image_2', 'release_image_3'])) {
                Setting::updateOrCreate(['key' => $key], ['value' => $value]);
            }
        }

        // 2. Handle File Uploads (About Image)
        if ($request->hasFile('about_image')) {
            $file = $request->file('about_image');
            $filename = 'about_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $filename);
            
            // Hapus gambar lama jika ada (opsional)
            $oldImage = Setting::where('key', 'about_image')->first();
            if ($oldImage && file_exists(public_path($oldImage->value))) {
                unlink(public_path($oldImage->value));
            }

            Setting::updateOrCreate(['key' => 'about_image'], ['value' => 'uploads/' . $filename]);
        }

        // 3. Handle File Uploads (Release Ars Images - 3 Slot)
        for ($i = 1; $i <= 3; $i++) {
            $fileKey = 'release_image_' . $i;
            
            if ($request->hasFile($fileKey)) {
                $file = $request->file($fileKey);
                $filename = 'release_' . $i . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads'), $filename);

                // Hapus gambar lama
                $oldImage = Setting::where('key', $fileKey)->first();
                if ($oldImage && file_exists(public_path($oldImage->value))) {
                    unlink(public_path($oldImage->value));
                }

                Setting::updateOrCreate(['key' => $fileKey], ['value' => 'uploads/' . $filename]);
            }
        }

        return back()->with('success', 'Home settings updated successfully.');
    }
}