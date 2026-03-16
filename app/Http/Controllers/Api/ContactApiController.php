<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactApiController extends Controller
{
    /**
     * POST /api/contact
     * Submit a contact inquiry (public).
     */
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

        $validated['inquiry_type'] = $validated['inquiry_type'] ?? 'general';
        $validated['status'] = 'new';

        $contact = Contact::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Thank you! Your inquiry has been submitted. We will respond within 24-48 hours.',
            'data'    => ['id' => $contact->id],
        ], 201);
    }

    /**
     * GET /api/admin/contact-inquiries
     * List all contact inquiries (admin).
     */
    public function index(Request $request)
    {
        $query = Contact::latest();

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->has('type') && $request->type !== 'all') {
            $query->byType($request->type);
        }

        $contacts = $query->paginate($request->input('limit', 15));
        $unreadCount = Contact::unread()->count();

        return response()->json([
            'success'      => true,
            'unread_count' => $unreadCount,
            'data'         => $contacts,
        ]);
    }

    /**
     * GET /api/admin/contact-inquiries/{id}
     * Show a single inquiry and auto-mark as read (admin).
     */
    public function show($id)
    {
        $contact = Contact::findOrFail($id);

        if ($contact->status === 'new') {
            $contact->markAsRead();
        }

        return response()->json([
            'success' => true,
            'data'    => $contact,
        ]);
    }

    /**
     * PUT /api/admin/contact-inquiries/{id}
     * Update inquiry status & notes (admin).
     */
    public function update(Request $request, $id)
    {
        $contact = Contact::findOrFail($id);

        $validated = $request->validate([
            'status'      => 'required|in:new,read,responded',
            'admin_notes' => 'nullable|string|max:5000',
        ]);

        $contact->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Contact inquiry updated successfully.',
            'data'    => $contact->fresh(),
        ]);
    }

    /**
     * DELETE /api/admin/contact-inquiries/{id}
     * Delete a contact inquiry (admin).
     */
    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contact inquiry deleted successfully.',
        ]);
    }
}
