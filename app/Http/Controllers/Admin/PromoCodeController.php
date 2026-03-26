<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\PromoCode;
use Illuminate\Http\Request;

class PromoCodeController extends Controller
{
    public function index()
    {
        $promoCodes = PromoCode::withCount('usages')
            ->latest()
            ->paginate(20);

        // Statistics
        $stats = [
            'total' => PromoCode::count(),
            'active' => PromoCode::active()->count(),
            'expired' => PromoCode::where('expires_at', '<', now())->count(),
            'total_usage' => PromoCode::sum('times_used'),
        ];

        return view('admin.promo-codes.index', compact('promoCodes', 'stats'));
    }

    public function create()
    {
        $plans = Plan::where('is_active', true)->get();
        return view('admin.promo-codes.create', compact('plans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:promo_codes,code',
            'description' => 'nullable|string|max:255',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'required|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'applicable_plans' => 'nullable|array',
            'applicable_plans.*' => 'exists:plans,id',
        ]);

        // Convert empty values
        $validated['min_purchase'] = $validated['min_purchase'] ?? 0;
        $validated['applicable_plans'] = $request->applicable_plans ?: null;
        $validated['is_active'] = $request->has('is_active');

        PromoCode::create($validated);

        return redirect()->route('admin.promo-codes.index')
            ->with('success', 'Kode promo berhasil dibuat.');
    }

    public function show(PromoCode $promoCode)
    {
        $promoCode->load(['usages.user', 'usages.invoice']);
        
        // Calculate statistics
        $stats = [
            'times_used' => $promoCode->times_used,
            'remaining' => $promoCode->getRemainingUses(),
            'total_discount_given' => $promoCode->usages->sum('discount_applied'),
            'total_revenue' => $promoCode->getTotalRevenue(),
        ];

        return view('admin.promo-codes.show', compact('promoCode', 'stats'));
    }

    public function edit(PromoCode $promoCode)
    {
        $plans = Plan::where('is_active', true)->get();
        return view('admin.promo-codes.edit', compact('promoCode', 'plans'));
    }

    public function update(Request $request, PromoCode $promoCode)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:promo_codes,code,' . $promoCode->id,
            'description' => 'nullable|string|max:255',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'required|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'applicable_plans' => 'nullable|array',
            'applicable_plans.*' => 'exists:plans,id',
        ]);

        $validated['min_purchase'] = $validated['min_purchase'] ?? 0;
        $validated['applicable_plans'] = $request->applicable_plans ?: null;
        $validated['is_active'] = $request->has('is_active');

        $promoCode->update($validated);

        return redirect()->route('admin.promo-codes.index')
            ->with('success', 'Kode promo berhasil diupdate.');
    }

    public function destroy(PromoCode $promoCode)
    {
        if ($promoCode->times_used > 0) {
            return back()->with('error', 'Tidak bisa menghapus kode promo yang sudah digunakan.');
        }

        $promoCode->delete();
        return redirect()->route('admin.promo-codes.index')
            ->with('success', 'Kode promo berhasil dihapus.');
    }

    public function toggle(PromoCode $promoCode)
    {
        $promoCode->update(['is_active' => !$promoCode->is_active]);
        
        $status = $promoCode->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Kode promo berhasil {$status}.");
    }

    public function generateCode()
    {
        return response()->json([
            'code' => PromoCode::generateCode()
        ]);
    }
}
