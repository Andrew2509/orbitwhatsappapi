<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UsageLog;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['activeSubscription.plan', 'devices'])
            ->withCount(['devices', 'invoices']);

        // Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        // Filter by role
        if ($request->role) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->status === 'suspended') {
            $query->where('is_suspended', true);
        } elseif ($request->status === 'active') {
            $query->where('is_suspended', false);
        }

        // Filter by plan
        if ($request->plan) {
            $query->whereHas('activeSubscription', function ($q) use ($request) {
                $q->where('plan_id', $request->plan);
            });
        }

        $users = $query->latest()->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load([
            'activeSubscription.plan',
            'subscriptions.plan',
            'devices',
            'invoices' => fn($q) => $q->latest()->take(10),
        ]);

        // Get usage statistics
        $usageThisMonth = UsageLog::where('user_id', $user->id)
            ->whereMonth('date', now()->month)
            ->selectRaw('SUM(messages_sent) as messages, SUM(api_calls) as api_calls')
            ->first();

        return view('admin.users.show', compact('user', 'usageThisMonth'));
    }

    public function impersonate(User $user)
    {
        // Store admin ID in session to return back
        session(['impersonating_from' => Auth::id()]);

        // Login as the user
        Auth::login($user);

        AuditService::logAction('user.impersonated', $user, null, "Admin " . Auth::id() . " impersonated user: {$user->email}");

        return redirect()->route('dashboard')
            ->with('info', "Anda sekarang login sebagai {$user->name}. Klik 'Stop Impersonating' untuk kembali.");
    }

    public function suspend(Request $request, User $user)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        // Cannot suspend super_admin
        if ($user->role === 'super_admin') {
            return back()->with('error', 'Tidak bisa suspend Super Admin.');
        }

        $oldValues = $user->getOriginal();
        $user->update([
            'is_suspended' => true,
            'suspended_at' => now(),
            'suspension_reason' => $request->reason,
        ]);

        AuditService::log('user.suspended', $user, $oldValues, "Admin suspended user: {$user->email}. Reason: {$request->reason}");

        return back()->with('success', "User {$user->name} berhasil di-suspend.");
    }

    public function unsuspend(User $user)
    {
        $oldValues = $user->getOriginal();
        $user->update([
            'is_suspended' => false,
            'suspended_at' => null,
            'suspension_reason' => null,
        ]);

        AuditService::log('user.unsuspended', $user, $oldValues, "Admin unsuspended user: {$user->email}");

        return back()->with('success', "User {$user->name} berhasil di-unsuspend.");
    }

    public function updateLimits(Request $request, User $user)
    {
        // This would update custom limits for a user
        // For now, redirect to subscription management
        return back()->with('info', 'Untuk mengubah limit, silakan kelola subscription user.');
    }
}
