<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $query = Contact::where('user_id', Auth::id())
            ->withCount('messages');

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('phone_number', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        if ($request->label) {
            $query->whereJsonContains('labels', $request->label);
        }

        $contacts = $query->latest()->paginate(20);

        return view('contacts.index', compact('contacts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string|max:20',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'labels' => 'nullable|array',
            'notes' => 'nullable|string|max:1000',
        ]);

        Contact::create([
            'user_id' => Auth::id(),
            'phone_number' => $request->phone_number,
            'name' => $request->name,
            'email' => $request->email,
            'labels' => $request->labels ?? [],
            'notes' => $request->notes,
        ]);

        return redirect()->route('contacts.index')
            ->with('success', 'Contact added successfully.');
    }

    public function update(Request $request, Contact $contact)
    {
        // Check ownership
        if ($contact->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'phone_number' => 'required|string|max:20',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'labels' => 'nullable|array',
            'notes' => 'nullable|string|max:1000',
        ]);

        $contact->update([
            'phone_number' => $request->phone_number,
            'name' => $request->name,
            'email' => $request->email,
            'labels' => $request->labels ?? [],
            'notes' => $request->notes,
        ]);

        return redirect()->route('contacts.index')
            ->with('success', 'Contact updated successfully.');
    }

    public function destroy(Contact $contact)
    {
        // Check ownership
        if ($contact->user_id !== Auth::id()) {
            abort(403);
        }
        
        $contact->delete();

        return redirect()->route('contacts.index')
            ->with('success', 'Contact deleted successfully.');
    }

    public function toggleBlock(Contact $contact)
    {
        // Check ownership
        if ($contact->user_id !== Auth::id()) {
            abort(403);
        }
        
        $contact->update(['is_blocked' => !$contact->is_blocked]);

        return redirect()->route('contacts.index')
            ->with('success', $contact->is_blocked ? 'Contact blocked.' : 'Contact unblocked.');
    }
}
