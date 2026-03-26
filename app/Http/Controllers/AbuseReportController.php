<?php

namespace App\Http\Controllers;

use App\Models\AbuseReport;
use Illuminate\Http\Request;

class AbuseReportController extends Controller
{
    /**
     * Show the public abuse report form.
     */
    public function create()
    {
        $reasons = AbuseReport::REASONS;
        return view('abuse.report', compact('reasons'));
    }

    /**
     * Store a new abuse report from the public.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reporter_name' => 'required|string|max:100',
            'reporter_email' => 'required|email|max:100',
            'reporter_phone' => 'nullable|string|max:20',
            'reported_phone' => 'required|string|max:20',
            'reason' => 'required|string|in:' . implode(',', array_keys(AbuseReport::REASONS)),
            'description' => 'required|string|min:20|max:2000',
            'evidence' => 'nullable|string|max:2000',
        ]);

        $validated['ip_address'] = $request->ip();

        AbuseReport::create($validated);

        return redirect()->route('abuse.thanks');
    }

    /**
     * Show thank you page after submission.
     */
    public function thanks()
    {
        return view('abuse.thanks');
    }
}
