<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::ordered()->withCount('subscriptions')->get();
        return view('admin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.plans.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'billing_period' => 'required|in:monthly,yearly',
            'max_devices' => 'required|integer|min:-1',
            'max_messages_per_day' => 'required|integer|min:-1',
            'max_contacts' => 'required|integer|min:-1',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['features'] = $this->parseFeatures($request);

        $plan = Plan::create($validated);

        AuditService::log('plan.created', $plan, null, "Admin created plan: {$plan->name}");

        return redirect()->route('admin.plans.index')
            ->with('success', 'Paket berhasil ditambahkan.');
    }

    public function edit(Plan $plan)
    {
        return view('admin.plans.edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'billing_period' => 'required|in:monthly,yearly',
            'max_devices' => 'required|integer|min:-1',
            'max_messages_per_day' => 'required|integer|min:-1',
            'max_contacts' => 'required|integer|min:-1',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $validated['features'] = $this->parseFeatures($request);

        $oldValues = $plan->getOriginal();
        $plan->update($validated);

        AuditService::log('plan.updated', $plan, $oldValues, "Admin updated plan: {$plan->name}");

        return redirect()->route('admin.plans.index')
            ->with('success', 'Paket berhasil diperbarui.');
    }

    public function destroy(Plan $plan)
    {
        // Check if plan has active subscriptions
        if ($plan->subscriptions()->active()->exists()) {
            return back()->with('error', 'Tidak bisa menghapus paket yang masih memiliki subscriber aktif.');
        }

        AuditService::log('plan.deleted', $plan, null, "Admin deleted plan: {$plan->name}");
        $plan->delete();

        return redirect()->route('admin.plans.index')
            ->with('success', 'Paket berhasil dihapus.');
    }

    private function parseFeatures(Request $request): array
    {
        return [
            'basic_messaging' => $request->boolean('feature_basic_messaging', true),
            'broadcast' => $request->boolean('feature_broadcast'),
            'auto_reply' => $request->boolean('feature_auto_reply'),
            'webhook' => $request->boolean('feature_webhook'),
            'api_access' => $request->boolean('feature_api_access', true),
            'priority_support' => $request->boolean('feature_priority_support'),
            'dedicated_ip' => $request->boolean('feature_dedicated_ip'),
            'account_manager' => $request->boolean('feature_account_manager'),
        ];
    }
}
