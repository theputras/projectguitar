<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Portfolio;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function __invoke()
    {
        $settings = Setting::all()->pluck('value', 'key');
        $featuredInstruments = Portfolio::published()->featured()->latest()->take(6)->get();

        return view('landing', compact('settings', 'featuredInstruments'));
    }
}