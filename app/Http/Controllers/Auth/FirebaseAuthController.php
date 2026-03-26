<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class FirebaseAuthController extends Controller
{
    public function login(Request $request)
    {
        $idToken = $request->input('id_token');

        try {
            $payload = $this->verifyIdToken($idToken);

            if (!$payload) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired token',
                ], 401);
            }

            $email = $payload['email'];
            $name = $payload['name'] ?? explode('@', $email)[0];

            $user = User::where('email', $email)->first();

            if (!$user) {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make(Str::random(24)),
                    'email_verified_at' => now(),
                ]);
            }

            Auth::login($user);

            return response()->json([
                'success' => true,
                'redirect' => route('dashboard'),
            ]);

        } catch (\Exception $e) {
            Log::error('Firebase Login Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Exception detail: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
            ], 500);
        }
    }

    public function syncPassword(Request $request)
    {
        $idToken = $request->input('id_token');
        $newPassword = $request->input('password');

        try {
            $payload = $this->verifyIdToken($idToken);

            if (!$payload) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired token',
                ], 401);
            }

            $email = $payload['email'];
            $user = User::where('email', $email)->first();

            if ($user) {
                $user->update([
                    'password' => Hash::make($newPassword),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Password synced successfully',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'User not found in local records',
            ], 404);

        } catch (\Exception $e) {
            Log::error('Firebase Password Sync Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error syncing password',
            ], 500);
        }
    }

    private function verifyIdToken($token)
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) return null;

        list($headb64, $bodyb64, $sigb64) = $parts;
        $header = json_decode(base64_decode(strTr($headb64, '-_', '+/')), true);
        $payload = json_decode(base64_decode(strTr($bodyb64, '-_', '+/')), true);

        if (!$header || !$payload) return null;

        // Verify claims
        $projectId = config('services.firebase.project_id');
        if (empty($projectId)) return null;
        if ($payload['iss'] !== "https://securetoken.google.com/{$projectId}") return null;
        if ($payload['aud'] !== $projectId) return null;
        if ($payload['exp'] < time()) return null;

        // Verify signature
        $kid = $header['kid'];
        $publicKeys = Http::get('https://www.googleapis.com/robot/v1/metadata/x509/securetoken@system.gserviceaccount.com')->json();

        if (!isset($publicKeys[$kid])) return null;

        $publicKey = $publicKeys[$kid];
        $data = "{$headb64}.{$bodyb64}";
        $signature = base64_decode(strTr($sigb64, '-_', '+/'));

        $verified = openssl_verify($data, $signature, $publicKey, OPENSSL_ALGO_SHA256);

        return $verified === 1 ? $payload : null;
    }
}
