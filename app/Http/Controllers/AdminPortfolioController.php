<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use Illuminate\Http\Request;

class AdminPortfolioController extends Controller
{
    // Tampil list portfolio
    public function index()
    {
        $portfolios = Portfolio::latest()->paginate(10);
        return view('admin.portfolio_index', compact('portfolios'));
    }

    // Tampil form tambah
    public function create()
    {
        return view('admin.portfolio_form');
    }

    // Simpan data baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'category' => 'required|string',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Proses Upload Gambar
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            // Pindahkan ke folder public/uploads
            $file->move(public_path('uploads'), $filename);
            $validated['image'] = 'uploads/' . $filename;
        }

        Portfolio::create($validated);

        return redirect()->route('admin.portfolio.index')->with('success', 'Portfolio berhasil ditambahkan.');
    }

    // Tampil form edit
    public function edit(Portfolio $portfolio)
    {
        return view('admin.portfolio_form', compact('portfolio'));
    }

    // Update data
    public function update(Request $request, Portfolio $portfolio)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'category' => 'required|string',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Jika ada upload gambar baru
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($portfolio->image && file_exists(public_path($portfolio->image))) {
                unlink(public_path($portfolio->image));
            }
            
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $filename);
            $validated['image'] = 'uploads/' . $filename;
        }

        $portfolio->update($validated);

        return redirect()->route('admin.portfolio.index')->with('success', 'Portfolio berhasil diperbarui.');
    }

    // Hapus data
    public function destroy(Portfolio $portfolio)
    {
        if ($portfolio->image && file_exists(public_path($portfolio->image))) {
            unlink(public_path($portfolio->image));
        }
        
        $portfolio->delete();
        return back()->with('success', 'Portfolio berhasil dihapus.');
    }
}