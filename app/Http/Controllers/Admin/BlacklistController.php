<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlacklistedWord;
use Illuminate\Http\Request;

class BlacklistController extends Controller
{
    public function index(Request $request)
    {
        $query = BlacklistedWord::query();

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Search
        if ($request->filled('search')) {
            $query->where('word', 'like', '%' . $request->search . '%');
        }

        $words = $query->latest()->paginate(20);

        // Statistics
        $stats = [
            'total' => BlacklistedWord::count(),
            'active' => BlacklistedWord::where('is_active', true)->count(),
            'blocked' => BlacklistedWord::where('severity', 'block')->count(),
            'warning' => BlacklistedWord::where('severity', 'warning')->count(),
        ];

        $categories = BlacklistedWord::CATEGORIES;

        return view('admin.blacklist.index', compact('words', 'stats', 'categories'));
    }

    public function create()
    {
        $categories = BlacklistedWord::CATEGORIES;
        return view('admin.blacklist.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'word' => 'required|string|max:100|unique:blacklisted_words,word',
            'category' => 'required|string|in:' . implode(',', array_keys(BlacklistedWord::CATEGORIES)),
            'severity' => 'required|in:warning,block',
            'reason' => 'nullable|string|max:255',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['created_by'] = auth()->id();

        BlacklistedWord::create($validated);

        return redirect()->route('admin.blacklist.index')
            ->with('success', 'Kata berhasil ditambahkan ke blacklist.');
    }

    public function edit(BlacklistedWord $blacklist)
    {
        $categories = BlacklistedWord::CATEGORIES;
        return view('admin.blacklist.edit', compact('blacklist', 'categories'));
    }

    public function update(Request $request, BlacklistedWord $blacklist)
    {
        $validated = $request->validate([
            'word' => 'required|string|max:100|unique:blacklisted_words,word,' . $blacklist->id,
            'category' => 'required|string|in:' . implode(',', array_keys(BlacklistedWord::CATEGORIES)),
            'severity' => 'required|in:warning,block',
            'reason' => 'nullable|string|max:255',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $blacklist->update($validated);

        return redirect()->route('admin.blacklist.index')
            ->with('success', 'Kata berhasil diperbarui.');
    }

    public function destroy(BlacklistedWord $blacklist)
    {
        $blacklist->delete();

        return redirect()->route('admin.blacklist.index')
            ->with('success', 'Kata berhasil dihapus dari blacklist.');
    }

    public function toggle(BlacklistedWord $blacklist)
    {
        $blacklist->update(['is_active' => !$blacklist->is_active]);

        $status = $blacklist->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Kata berhasil {$status}.");
    }

    public function bulkImport(Request $request)
    {
        $request->validate([
            'words' => 'required|string',
            'category' => 'required|string|in:' . implode(',', array_keys(BlacklistedWord::CATEGORIES)),
            'severity' => 'required|in:warning,block',
        ]);

        $words = array_filter(array_map('trim', explode("\n", $request->words)));
        $imported = 0;

        foreach ($words as $word) {
            if (empty($word)) continue;

            BlacklistedWord::firstOrCreate(
                ['word' => strtolower($word)],
                [
                    'category' => $request->category,
                    'severity' => $request->severity,
                    'is_active' => true,
                    'created_by' => auth()->id(),
                ]
            );
            $imported++;
        }

        return back()->with('success', "{$imported} kata berhasil diimport.");
    }
}
