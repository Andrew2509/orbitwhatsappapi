<?php

namespace App\Http\Controllers;

use App\Models\ApiKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiKeyController extends Controller
{
    public function index()
    {
        $apiKeys = ApiKey::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('api-keys.index', compact('apiKeys'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'environment' => 'required|in:live,test',
        ]);

        $key = ApiKey::generateKey($request->environment);

        ApiKey::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'key' => $key,
            'environment' => $request->environment,
            'is_active' => true,
        ]);

        return redirect()->route('api-keys.index')
            ->with('success', 'API Key created.')
            ->with('new_key_generated', $key);
    }

    public function regenerate(ApiKey $apiKey)
    {
        if ($apiKey->user_id !== Auth::id()) {
            abort(403);
        }

        $newKey = ApiKey::generateKey($apiKey->environment);
        $apiKey->update(['key' => $newKey, 'is_active' => true]);

        return redirect()->route('api-keys.index')
            ->with('success', 'API Key regenerated.')
            ->with('new_key_generated', $newKey);
    }

    public function revoke(ApiKey $apiKey)
    {
        if ($apiKey->user_id !== Auth::id()) {
            abort(403);
        }

        $apiKey->update(['is_active' => false]);

        return redirect()->route('api-keys.index')
            ->with('success', 'API Key revoked.');
    }

    public function destroy(ApiKey $apiKey)
    {
        if ($apiKey->user_id !== Auth::id()) {
            abort(403);
        }
        
        $apiKey->delete();

        return redirect()->route('api-keys.index')
            ->with('success', 'API Key deleted.');
    }
}
