<?php
namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class AdminContactController extends Controller
{
    public function index()
    {
        $messages = Contact::latest()->paginate(15);
        return view('admin.contact_index', compact('messages'));
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();
        return back()->with('success', 'Message deleted.');
    }
}