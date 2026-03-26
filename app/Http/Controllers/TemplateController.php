<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TemplateController extends Controller
{
    public function index(Request $request)
    {
        $query = Template::query()
            ->where(function ($q) {
                $q->where('user_id', Auth::id())
                  ->orWhere('is_system', true);
            });

        // Search
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Category Filter
        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        $templates = $query->latest()->get();

        // Get unique categories for filter sidebar
        $categories = Template::where(function ($q) {
                $q->where('user_id', Auth::id())
                  ->orWhere('is_system', true);
            })
            ->select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->orderBy('category')
            ->get();

        return view('templates.index', compact('templates', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:text,media,button,list',
            'content' => 'required|string',
        ]);

        Template::create([
            'user_id' => Auth::id(),
            'name' => $request->input('name'),
            'category' => $request->input('category'),
            'content' => $request->input('content'),
            'variables' => $request->input('variables', []),
            'buttons' => $request->input('buttons', []),
        ]);

        return redirect()->route('templates.index')
            ->with('success', 'Template created successfully.');
    }

    public function update(Request $request, Template $template)
    {
        if ($template->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:text,media,button,list',
            'content' => 'required|string',
        ]);

        $template->update($request->only(['name', 'category', 'content']));

        return redirect()->route('templates.index')
            ->with('success', 'Template updated successfully.');
    }

    public function destroy(Template $template)
    {
        if ($template->user_id !== Auth::id()) {
            abort(403);
        }
        
        $template->delete();

        return redirect()->route('templates.index')
            ->with('success', 'Template deleted successfully.');
    }
}
