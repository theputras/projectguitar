<?php

namespace App\Http\Controllers;

use App\Models\CustomOrder;
use App\Models\Content;
use Illuminate\Http\Request;

class CustomOrderController extends Controller
{
    // Display custom order process page
    public function index()
    {
        $contents = Content::getBySection('custom_order');
        return view('custom_order', compact('contents'));
    }

    // Display custom order form
    public function create()
    {
        return view('custom_order_form');
    }

    // Store a new custom order inquiry
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name'    => 'required|string|max:255',
            'customer_email'   => 'required|email|max:255',
            'customer_country' => 'required|string|max:255',
            'customer_phone'   => 'nullable|string|max:50',
            'order_type'       => 'required|in:custom_bass,custom_guitar,other',
            'budget'           => 'nullable|string|max:100',
            'timeline'         => 'nullable|string|max:100',
            'notes'            => 'nullable|string|max:5000',
            // Requirements fields (will be stored as JSON)
            'desired_wood'     => 'nullable|string|max:255',
            'string_count'     => 'nullable|integer|min:4|max:12',
            'scale_length'     => 'nullable|string|max:100',
            'pickup_preference' => 'nullable|string|max:255',
            'special_requests' => 'nullable|string|max:2000',
        ]);

        // Build requirements JSON from individual fields
        $requirements = array_filter([
            'desired_wood'      => $validated['desired_wood'] ?? null,
            'string_count'      => $validated['string_count'] ?? null,
            'scale_length'      => $validated['scale_length'] ?? null,
            'pickup_preference' => $validated['pickup_preference'] ?? null,
            'special_requests'  => $validated['special_requests'] ?? null,
        ]);

        CustomOrder::create([
            'customer_name'    => $validated['customer_name'],
            'customer_email'   => $validated['customer_email'],
            'customer_country' => $validated['customer_country'],
            'customer_phone'   => $validated['customer_phone'] ?? null,
            'order_type'       => $validated['order_type'],
            'budget'           => $validated['budget'] ?? null,
            'timeline'         => $validated['timeline'] ?? null,
            'notes'            => $validated['notes'] ?? null,
            'requirements'     => $requirements,
            'current_step'     => 'consultation',
        ]);

        return redirect()->route('custom-order.index')
            ->with('success', 'Your custom order inquiry has been submitted! We will contact you within 48 hours.');
    }

    // Track order status (customer-facing)
    public function track(Request $request)
    {
        $token = $request->query('token');

        if (!$token) {
            return view('custom_order_track', ['order' => null]);
        }

        $order = CustomOrder::where('tracking_token', $token)->first();

        return view('custom_order_track', compact('order'));
    }
}
