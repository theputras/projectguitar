<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function index()
    {
        $contents = Content::getBySection('shipping');
        return view('shipping', compact('contents'));
    }
}
