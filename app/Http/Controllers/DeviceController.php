<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeviceController extends Controller
{
    public function __construct(
        protected WhatsAppService $whatsApp
    ) {}

    public function index()
    {
        $devices = Device::where('user_id', Auth::id())
            ->latest()
            ->get();

        // Check WhatsApp service health
        $serviceOnline = $this->whatsApp->healthCheck();

        return view('devices.index', compact('devices', 'serviceOnline'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $device = Device::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'status' => 'pending',
        ]);

        // Initialize WhatsApp session
        $this->whatsApp->initSession($device->id, Auth::id(), $request->name);

        return redirect()->route('devices.index')
            ->with('success', 'Device created. Scan the QR code to connect.');
    }

    public function destroy(Device $device)
    {
        // Verify ownership
        if ($device->user_id !== Auth::id()) {
            abort(403);
        }
        
        // Disconnect WhatsApp session first
        $this->whatsApp->disconnect($device->id);
        
        $device->delete();

        return redirect()->route('devices.index')
            ->with('success', 'Device deleted successfully.');
    }

    public function scan(Device $device)
    {
        // Verify ownership
        if ($device->user_id !== Auth::id()) {
            abort(403);
        }

        // If not already initializing, start session
        $status = $this->whatsApp->getStatus($device->id);
        
        if ($status['status'] === 'not_initialized') {
            $this->whatsApp->initSession($device->id, Auth::id(), $device->name);
            // Wait a moment for QR to generate
            sleep(2);
        }

        // Get QR code
        $qrCode = $this->whatsApp->getQR($device->id);

        return response()->json([
            'success' => true,
            'qr_code' => $qrCode,
            'device_id' => $device->id,
            'status' => $status['status'],
        ]);
    }

    public function logout(Device $device)
    {
        // Verify ownership
        if ($device->user_id !== Auth::id()) {
            abort(403);
        }

        $this->whatsApp->disconnect($device->id);
        
        $device->update([
            'status' => 'disconnected',
            'session_data' => null,
            'qr_code' => null,
        ]);

        return redirect()->route('devices.index')
            ->with('success', 'Device logged out successfully.');
    }

    public function pairingCode(Device $device, Request $request)
    {
        // Verify ownership
        if ($device->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'phone' => 'required|string',
        ]);

        $result = $this->whatsApp->requestPairingCode($device->id, $request->phone);

        return response()->json($result);
    }

    public function status(Device $device)
    {
        // Verify ownership
        if ($device->user_id !== Auth::id()) {
            abort(403);
        }

        $status = $this->whatsApp->getStatus($device->id);
        
        return response()->json([
            'success' => true,
            'device_id' => $device->id,
            'status' => $status['status'],
            'phone' => $status['phone'] ?? null,
        ]);
    }
}
