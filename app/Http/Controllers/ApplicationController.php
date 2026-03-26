<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Device;
use App\Models\ApiKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    /**
     * Display a listing of applications
     */
    public function index()
    {
        $applications = Application::where('user_id', Auth::id())
            ->with('apiKey')
            ->withCount('devices')
            ->latest()
            ->get();

        return view('applications.index', compact('applications'));
    }

    /**
     * Show the form for creating a new application
     */
    public function create()
    {
        $devices = Device::where('user_id', Auth::id())->get();
        $apiKeys = ApiKey::where('user_id', Auth::id())->where('is_active', true)->get();
        
        return view('applications.create', compact('devices', 'apiKeys'));
    }

    /**
     * Store a newly created application
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'api_key_id' => 'required|exists:api_keys,id',
            'webhook_url' => 'nullable|url|max:500',
            'devices' => 'nullable|array',
            'devices.*' => 'exists:devices,id',
        ]);

        // Verify API key belongs to user
        $apiKey = ApiKey::where('id', $request->api_key_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $application = Application::create([
            'user_id' => Auth::id(),
            'api_key_id' => $apiKey->id,
            'name' => $request->name,
            'description' => $request->description,
            'webhook_url' => $request->webhook_url,
        ]);

        // Attach selected devices
        if ($request->devices) {
            $userDeviceIds = Device::where('user_id', Auth::id())
                ->whereIn('id', $request->devices)
                ->pluck('id');
            $application->devices()->attach($userDeviceIds);
        }

        return redirect()->route('applications.show', $application)
            ->with('success', 'Application created successfully!');
    }

    /**
     * Display the specified application
     */
    public function show(Application $application)
    {
        abort_if($application->user_id !== Auth::id(), 403);

        $application->load(['devices', 'apiKey']);
        
        return view('applications.show', compact('application'));
    }

    /**
     * Show the form for editing the specified application
     */
    public function edit(Application $application)
    {
        abort_if($application->user_id !== Auth::id(), 403);

        $devices = Device::where('user_id', Auth::id())->get();
        $apiKeys = ApiKey::where('user_id', Auth::id())->where('is_active', true)->get();
        $application->load(['devices', 'apiKey']);
        
        return view('applications.edit', compact('application', 'devices', 'apiKeys'));
    }

    /**
     * Update the specified application
     */
    public function update(Request $request, Application $application)
    {
        abort_if($application->user_id !== Auth::id(), 403);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'api_key_id' => 'required|exists:api_keys,id',
            'webhook_url' => 'nullable|url|max:500',
            'devices' => 'nullable|array',
            'devices.*' => 'exists:devices,id',
            'is_active' => 'boolean',
        ]);

        // Verify API key belongs to user
        $apiKey = ApiKey::where('id', $request->api_key_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $application->update([
            'api_key_id' => $apiKey->id,
            'name' => $request->name,
            'description' => $request->description,
            'webhook_url' => $request->webhook_url,
            'is_active' => $request->boolean('is_active', true),
        ]);

        // Sync devices
        if ($request->has('devices')) {
            $userDeviceIds = Device::where('user_id', Auth::id())
                ->whereIn('id', $request->devices)
                ->pluck('id');
            $application->devices()->sync($userDeviceIds);
        } else {
            $application->devices()->detach();
        }

        return redirect()->route('applications.show', $application)
            ->with('success', 'Application updated successfully.');
    }

    /**
     * Remove the specified application
     */
    public function destroy(Application $application)
    {
        abort_if($application->user_id !== Auth::id(), 403);

        $application->delete();

        return redirect()->route('applications.index')
            ->with('success', 'Application deleted successfully.');
    }
}
