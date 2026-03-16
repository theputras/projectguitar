<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomOrder;
use Illuminate\Http\Request;

class CustomOrderApiController extends Controller
{
    /**
     * GET /api/admin/custom-orders
     * List all custom orders. Filterable by ?step=consultation|design|build|...
     */
    public function index(Request $request)
    {
        $query = CustomOrder::latest();

        if ($request->has('step') && $request->step !== 'all') {
            $query->byStep($request->step);
        }

        $orders = $query->paginate($request->input('limit', 15));

        return response()->json([
            'success' => true,
            'data'    => $orders,
        ]);
    }

    /**
     * GET /api/admin/custom-orders/{id}
     * Show a single custom order.
     */
    public function show($id)
    {
        $order = CustomOrder::findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => array_merge($order->toArray(), [
                'steps_progress' => collect(CustomOrder::STEPS)->map(function ($step, $key) use ($order) {
                    return [
                        'step'      => $key,
                        'label'     => $step['label'],
                        'completed' => $order->isStepCompleted($key),
                        'current'   => $order->current_step === $key,
                    ];
                })->values(),
            ]),
        ]);
    }

    /**
     * POST /api/custom-orders
     * Create a new custom order inquiry (public endpoint).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name'     => 'required|string|max:255',
            'customer_email'    => 'required|email|max:255',
            'customer_country'  => 'required|string|max:255',
            'customer_phone'    => 'nullable|string|max:50',
            'order_type'        => 'required|in:custom_bass,custom_guitar,other',
            'budget'            => 'nullable|string|max:100',
            'timeline'          => 'nullable|string|max:100',
            'notes'             => 'nullable|string|max:5000',
            'desired_wood'      => 'nullable|string|max:255',
            'string_count'      => 'nullable|integer|min:4|max:12',
            'scale_length'      => 'nullable|string|max:100',
            'pickup_preference' => 'nullable|string|max:255',
            'special_requests'  => 'nullable|string|max:2000',
        ]);

        $requirements = array_filter([
            'desired_wood'      => $validated['desired_wood'] ?? null,
            'string_count'      => $validated['string_count'] ?? null,
            'scale_length'      => $validated['scale_length'] ?? null,
            'pickup_preference' => $validated['pickup_preference'] ?? null,
            'special_requests'  => $validated['special_requests'] ?? null,
        ]);

        $order = CustomOrder::create([
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

        return response()->json([
            'success' => true,
            'message' => 'Custom order submitted successfully. We will contact you within 48 hours.',
            'data'    => [
                'id'             => $order->id,
                'tracking_token' => $order->tracking_token,
            ],
        ], 201);
    }

    /**
     * PUT /api/admin/custom-orders/{id}
     * Update order status/step (admin only).
     */
    public function update(Request $request, $id)
    {
        $order = CustomOrder::findOrFail($id);

        $validated = $request->validate([
            'current_step' => 'sometimes|required|in:consultation,design,build,quality_check,shipping,completed',
            'admin_notes'  => 'nullable|string|max:5000',
            'budget'       => 'nullable|string|max:100',
            'timeline'     => 'nullable|string|max:100',
        ]);

        if (isset($validated['current_step']) && $validated['current_step'] === 'completed' && $order->current_step !== 'completed') {
            $validated['completed_at'] = now();
        }

        $order->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Custom order updated successfully.',
            'data'    => $order->fresh(),
        ]);
    }

    /**
     * DELETE /api/admin/custom-orders/{id}
     * Delete a custom order (admin only).
     */
    public function destroy($id)
    {
        $order = CustomOrder::findOrFail($id);
        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Custom order deleted successfully.',
        ]);
    }

    /**
     * GET /api/custom-orders/track?token=xxx
     * Customer-facing order tracking (public endpoint).
     */
    public function track(Request $request)
    {
        $request->validate(['token' => 'required|string']);

        $order = CustomOrder::where('tracking_token', $request->token)->first();

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found. Please check your tracking token.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'id'            => $order->id,
                'order_type'    => $order->order_type,
                'current_step'  => $order->current_step,
                'created_at'    => $order->created_at,
                'completed_at'  => $order->completed_at,
                'steps'         => collect(CustomOrder::STEPS)->map(function ($step, $key) use ($order) {
                    return [
                        'step'      => $key,
                        'label'     => $step['label'],
                        'completed' => $order->isStepCompleted($key),
                        'current'   => $order->current_step === $key,
                    ];
                })->values(),
            ],
        ]);
    }
}
