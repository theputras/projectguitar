<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    // Method untuk menampilkan form
    public function create()
    {
        return view('contact');
    }

    // Method untuk mengirim data form (store)
    public function store(Request $request)
    {
        // Validasi sederhana
        $validated = $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|email',
            'instrument' => 'nullable',
            'message' => 'required|min:10'
        ]);

        // Di sini Anda bisa menambahkan logic pengiriman email atau simpan ke database
        // Contoh: Mail::to('admin@site.com')->send(new ContactMail($validated));

        // Kembali ke halaman contact dengan pesan sukses
        return back()->with('success', 'Terima kasih! Pesan Anda telah terkirim.');
    }
}