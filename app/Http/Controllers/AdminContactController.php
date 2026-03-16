<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class AdminContactController extends Controller
{
    public function index(Request $request)
    {
        $query = Contact::latest();

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by inquiry type
        if ($request->has('type') && $request->type !== 'all') {
            $query->byType($request->type);
        }

        $contacts = $query->paginate(15);
        $unreadCount = Contact::unread()->count();

        return view('admin.contact_index', compact('contacts', 'unreadCount'));
    }

    public function show(Contact $contact)
    {
        // Auto-mark as read when viewed
        if ($contact->status === 'new') {
            $contact->markAsRead();
        }

        return view('admin.contact_show', compact('contact'));
    }

    public function update(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'status'      => 'required|in:new,read,responded',
            'admin_notes' => 'nullable|string|max:5000',
        ]);

        $contact->update($validated);

        return redirect()->route('admin.contacts.index')
            ->with('success', 'Contact inquiry updated successfully.');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();
        return back()->with('success', 'Contact inquiry deleted successfully.');
    }
}