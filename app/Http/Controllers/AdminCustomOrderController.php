<?php

namespace App\Http\Controllers;

use App\Models\CustomOrder;
use Illuminate\Http\Request;

class AdminCustomOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = CustomOrder::latest();

        // Filter by step
        if ($request->has('step') && $request->step !== 'all') {
            $query->byStep($request->step);
        }

        $orders = $query->paginate(15);
        return view('admin.custom_order_index', compact('orders'));
    }

    public function show(CustomOrder $customOrder)
    {
        return view('admin.custom_order_show', compact('customOrder'));
    }

    public function edit(CustomOrder $customOrder)
    {
        return view('admin.custom_order_form', compact('customOrder'));
    }

    public function update(Request $request, CustomOrder $customOrder)
    {
        $validated = $request->validate([
            'current_step' => 'required|in:consultation,design,build,quality_check,shipping,completed',
            'admin_notes'  => 'nullable|string|max:5000',
            'budget'       => 'nullable|string|max:100',
            'timeline'     => 'nullable|string|max:100',
        ]);

        // Set completed_at if step is 'completed'
        if ($validated['current_step'] === 'completed' && $customOrder->current_step !== 'completed') {
            $validated['completed_at'] = now();
        }

        $customOrder->update($validated);

        return redirect()->route('admin.custom-orders.index')
            ->with('success', 'Custom order updated successfully.');
    }

    public function destroy(CustomOrder $customOrder)
    {
        $customOrder->delete();
        return back()->with('success', 'Custom order deleted successfully.');
    }
}
