<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    // Gallery listing with optional filtering
    public function index(Request $request)
    {
        $query = Portfolio::published()->latest();

        // Filter by category (bass/guitar)
        if ($request->has('category') && in_array($request->category, ['bass', 'guitar'])) {
            $query->where('category', $request->category);
        }

        $portfolios = $query->paginate(12);

        return view('portfolio', compact('portfolios'));
    }

    // Detail page for a single instrument
    public function show(Portfolio $portfolio)
    {
        // Only show published items
        if ($portfolio->status !== 'published') {
            abort(404);
        }

        // Get related instruments (same category, exclude current)
        $related = Portfolio::published()
            ->where('category', $portfolio->category)
            ->where('id', '!=', $portfolio->id)
            ->take(3)
            ->get();

        return view('portfolio_detail', compact('portfolio', 'related'));
    }
}