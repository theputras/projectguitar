<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function __invoke()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('landing', compact('settings'));
    }
}