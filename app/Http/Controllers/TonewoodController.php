<?php

namespace App\Http\Controllers;

use App\Models\Tonewood;
use Illuminate\Http\Request;

class TonewoodController extends Controller
{
    public function index()
    {
        // Group tonewoods by type: body, neck, fretboard
        $bodyWoods = Tonewood::byType('body')->sorted()->get();
        $neckWoods = Tonewood::byType('neck')->sorted()->get();
        $fretboardWoods = Tonewood::byType('fretboard')->sorted()->get();

        return view('tonewoods', compact('bodyWoods', 'neckWoods', 'fretboardWoods'));
    }
}
