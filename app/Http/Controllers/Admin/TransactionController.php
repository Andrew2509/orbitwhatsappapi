<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['user:id,name,email', 'subscription.plan:id,name']);

        // Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('invoice_number', 'like', "%{$request->search}%")
                    ->orWhereHas('user', function ($u) use ($request) {
                        $u->where('name', 'like', "%{$request->search}%")
                            ->orWhere('email', 'like', "%{$request->search}%");
                    });
            });
        }

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $invoices = $query->latest()->paginate(20);

        // Statistics
        $stats = [
            'total' => Invoice::sum('total'),
            'paid' => Invoice::paid()->sum('total'),
            'pending' => Invoice::pending()->sum('total'),
            'failed' => Invoice::where('status', 'failed')->sum('total'),
            'pending_count' => Invoice::pending()->count(),
        ];

        return view('admin.transactions.index', compact('invoices', 'stats'));
    }

    public function pending()
    {
        $invoices = Invoice::with(['user:id,name,email', 'subscription.plan:id,name'])
            ->pending()
            ->latest()
            ->paginate(20);

        return view('admin.transactions.pending', compact('invoices'));
    }

    public function approve(Request $request, Invoice $invoice)
    {
        if (!$invoice->isPending()) {
            return back()->with('error', 'Invoice ini sudah tidak dalam status pending.');
        }

        DB::transaction(function () use ($invoice) {
            // Mark invoice as paid
            $invoice->markAsPaid(Auth::id());

            // Activate subscription
            if ($invoice->subscription) {
                $invoice->subscription->activate();
            }
        });

        return back()->with('success', "Invoice {$invoice->invoice_number} berhasil di-approve.");
    }

    public function reject(Request $request, Invoice $invoice)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        if (!$invoice->isPending()) {
            return back()->with('error', 'Invoice ini sudah tidak dalam status pending.');
        }

        $invoice->reject($request->reason);

        return back()->with('success', "Invoice {$invoice->invoice_number} ditolak.");
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return back()->with('success', "Invoice {$invoice->invoice_number} berhasil dihapus.");
    }

    public function reports(Request $request)
    {
        $year = $request->year ?? now()->year;

        // Monthly revenue data
        $monthlyRevenue = Invoice::paid()
            ->whereYear('paid_at', $year)
            ->select(
                DB::raw('MONTH(paid_at) as month'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Top plans
        $topPlans = Invoice::paid()
            ->whereYear('paid_at', $year)
            ->join('subscriptions', 'invoices.subscription_id', '=', 'subscriptions.id')
            ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
            ->select('plans.name', DB::raw('SUM(invoices.total) as revenue'), DB::raw('COUNT(*) as count'))
            ->groupBy('plans.id', 'plans.name')
            ->orderByDesc('revenue')
            ->take(5)
            ->get();

        return view('admin.transactions.reports', compact('monthlyRevenue', 'topPlans', 'year'));
    }
}
