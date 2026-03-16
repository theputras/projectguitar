<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function index()
    {
        $contents = Content::getBySection('about');
        return view('about', compact('contents'));
    }
}
