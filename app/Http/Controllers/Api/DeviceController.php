<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index(Request $request)
    {
        $devices = Device::where('user_id', $request->user()->id)
            ->select(['id', 'name', 'phone_number', 'status', 'messages_sent', 'messages_received', 'last_connected_at'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $devices,
        ]);
    }

    public function show(Request $request, $id)
    {
        $device = Device::where('user_id', $request->user()->id)
            ->select(['id', 'name', 'phone_number', 'status', 'messages_sent', 'messages_received', 'last_connected_at'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $device,
        ]);
    }
}
