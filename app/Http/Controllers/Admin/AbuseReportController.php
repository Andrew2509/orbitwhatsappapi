<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbuseReport;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbuseReportController extends Controller
{
    public function index(Request $request)
    {
        $query = AbuseReport::with('resolvedBy');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by reason
        if ($request->filled('reason')) {
            $query->where('reason', $request->reason);
        }

        // Search by phone
        if ($request->filled('search')) {
            $query->where('reported_phone', 'like', '%' . $request->search . '%');
        }

        $reports = $query->latest()->paginate(20);

        // Statistics
        $stats = [
            'total' => AbuseReport::count(),
            'pending' => AbuseReport::where('status', 'pending')->count(),
            'investigating' => AbuseReport::where('status', 'investigating')->count(),
            'resolved' => AbuseReport::where('status', 'resolved')->count(),
        ];

        $reasons = AbuseReport::REASONS;
        $statuses = AbuseReport::STATUS_LABELS;

        return view('admin.abuse-reports.index', compact('reports', 'stats', 'reasons', 'statuses'));
    }

    public function show(AbuseReport $abuseReport)
    {
        // Check if this phone belongs to any user in our system
        $relatedDevice = Device::where('phone_number', $abuseReport->reported_phone)->first();
        
        // Get report history for this phone
        $reportHistory = AbuseReport::where('reported_phone', $abuseReport->reported_phone)
            ->where('id', '!=', $abuseReport->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.abuse-reports.show', compact('abuseReport', 'relatedDevice', 'reportHistory'));
    }

    public function investigate(AbuseReport $abuseReport)
    {
        $abuseReport->markAsInvestigating();
        
        return back()->with('success', 'Status diubah menjadi "Sedang Investigasi".');
    }

    public function resolve(Request $request, AbuseReport $abuseReport)
    {
        $request->validate([
            'resolution_notes' => 'required|string|max:1000',
            'action' => 'required|in:resolve,dismiss',
        ]);

        if ($request->action === 'resolve') {
            $abuseReport->resolve(Auth::id(), $request->resolution_notes);
            $message = 'Laporan berhasil diselesaikan.';
        } else {
            $abuseReport->dismiss(Auth::id(), $request->resolution_notes);
            $message = 'Laporan ditolak.';
        }

        return redirect()->route('admin.abuse-reports.index')
            ->with('success', $message);
    }

    public function destroy(AbuseReport $abuseReport)
    {
        $abuseReport->delete();

        return redirect()->route('admin.abuse-reports.index')
            ->with('success', 'Laporan berhasil dihapus.');
    }
}
