<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    // Display the contact / order form
    public function create()
    {
        return view('contact');
    }

    // Store a new contact inquiry
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|min:3|max:255',
            'email'           => 'required|email|max:255',
            'phone'           => 'nullable|string|max:50',
            'country'         => 'nullable|string|max:100',
            'instrument'      => 'nullable|string|max:255',
            'instrument_type' => 'nullable|in:bass,guitar,other',
            'budget_range'    => 'nullable|string|max:100',
            'message'         => 'required|string|min:10|max:5000',
            'inquiry_type'    => 'nullable|in:general,order,technical',
        ]);

        // Default inquiry type to 'general' if not provided
        $validated['inquiry_type'] = $validated['inquiry_type'] ?? 'general';
        $validated['status'] = 'new';

        Contact::create($validated);

        return back()->with('success', 'Thank you! Your message has been sent. We will respond within 24-48 hours.');
    }
}