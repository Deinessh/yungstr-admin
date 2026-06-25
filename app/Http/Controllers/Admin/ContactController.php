<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $submissions = ContactSubmission::latest()->paginate(20);

        return view('admin.contacts.index', compact('submissions'));
    }

    public function show(ContactSubmission $contact)
    {
        if (! $contact->is_read) {
            $contact->update(['is_read' => true]);
        }

        return view('admin.contacts.show', ['submission' => $contact]);
    }

    public function destroy(ContactSubmission $contact)
    {
        $contact->delete();

        return back()->with('success', 'Message deleted.');
    }
}
