<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Helpers\ImageHelper;
use Illuminate\Validation\Rules\Password;
use Jenssegers\Agent\Agent;

class SettingsController extends Controller
{
    public function profile()
    {
        return view('settings.profile', ['user' => Auth::user()]);
    }

    public function security(Request $request)
    {
        $sessions = DB::table('sessions')
            ->where('user_id', Auth::id())
            ->orderBy('last_activity', 'desc')
            ->get()
            ->map(function ($session) use ($request) {
                $agent = new Agent();
                $agent->setUserAgent($session->user_agent);
                
                return (object) [
                    'id' => $session->id,
                    'ip_address' => $session->ip_address,
                    'is_current' => $session->id === $request->session()->getId(),
                    'last_active' => \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                    'device' => $agent->device() ?: 'Unknown',
                    'platform' => $agent->platform() ?: 'Unknown',
                    'browser' => $agent->browser() ?: 'Unknown',
                    'is_desktop' => $agent->isDesktop(),
                    'is_mobile' => $agent->isMobile(),
                ];
            });

        return view('settings.security', [
            'user' => Auth::user(),
            'sessions' => $sessions,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
        ], [
            'name.required' => 'Nama harus diisi.',
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->update($request->only(['name', 'email']));

        return redirect()->route('settings.profile')
            ->with('success', 'Profile berhasil diperbarui!');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
        ], [
            'avatar.required' => 'Pilih foto untuk diupload.',
            'avatar.image' => 'File harus berupa gambar.',
            'avatar.mimes' => 'Format gambar harus JPG, PNG, atau GIF.',
            'avatar.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Convert to Base64 (Store directly in DB)
        $base64 = ImageHelper::fileToBase64($request->file('avatar'));
        $user->update(['avatar' => $base64]);

        return redirect()->route('settings.profile')
            ->with('success', 'Foto profile berhasil diperbarui!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.required' => 'Password saat ini harus diisi.',
            'current_password.current_password' => 'Password saat ini salah.',
            'password.required' => 'Password baru harus diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('settings.profile')
            ->with('success', 'Password berhasil diperbarui!');
    }

    public function logoutOtherSessions(Request $request)
    {
        // Delete all other sessions from database
        DB::table('sessions')
            ->where('user_id', Auth::id())
            ->where('id', '!=', $request->session()->getId())
            ->delete();

        return redirect()->route('settings.security')
            ->with('success', 'Semua session lain berhasil di-logout!');
    }

    public function revokeSession(Request $request, string $sessionId)
    {
        // Delete specific session
        $deleted = DB::table('sessions')
            ->where('id', $sessionId)
            ->where('user_id', Auth::id())
            ->where('id', '!=', $request->session()->getId()) // Can't revoke current session
            ->delete();

        if ($deleted) {
            return redirect()->route('settings.security')
                ->with('success', 'Session berhasil di-revoke!');
        }

        return redirect()->route('settings.security')
            ->with('error', 'Tidak dapat me-revoke session ini.');
    }
}
