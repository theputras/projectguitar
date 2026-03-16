<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    public function index()
    {
        // Ambil semua data portfolio
        $projects = Portfolio::latest()->get();
        
        // Kirim ke view portfolio.blade.php
        return view('portfolio', compact('projects'));
    }
}