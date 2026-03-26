<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\Device;
use App\Models\DeviceUsageLimit;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Service to handle device rotation for campaigns.
 * 
 * Supports multiple rotation strategies:
 * - round_robin: Rotate through devices after each message
 * - limit_based: Use one device until it hits daily limit, then rotate
 * - priority: Use devices in priority order, rotate when limit reached
 */
class DeviceRotationService
{
    /**
     * Get the next available device for sending.
     */
    public function getNextDevice(Campaign $campaign): ?Device
    {
        $devices = $this->getActiveDevices($campaign);
        
        if ($devices->isEmpty()) {
            Log::warning("Campaign {$campaign->id}: No active devices available.");
            return null;
        }

        return match ($campaign->rotation_strategy) {
            'round_robin' => $this->roundRobinSelect($campaign, $devices),
            'priority' => $this->prioritySelect($campaign, $devices),
            default => $this->limitBasedSelect($campaign, $devices), // limit_based is default
        };
    }

    /**
     * Get all active and connected devices for a campaign.
     */
    protected function getActiveDevices(Campaign $campaign): Collection
    {
        return $campaign->campaignDevices()
            ->where('is_active', true)
            ->with(['device' => function ($query) {
                $query->where('status', 'connected');
            }])
            ->orderBy('priority')
            ->get()
            ->filter(fn($cd) => $cd->device !== null)
            ->map(fn($cd) => $cd->device);
    }

    /**
     * Round-robin selection: rotate after each message.
     */
    protected function roundRobinSelect(Campaign $campaign, Collection $devices): ?Device
    {
        $deviceList = $devices->values();
        $count = $deviceList->count();
        
        if ($count === 0) return null;

        // Get current index and advance it
        $index = $campaign->current_device_index % $count;
        $campaign->increment('current_device_index');

        $device = $deviceList[$index];
        
        // Check if device can still send
        if (!$this->canDeviceSend($device)) {
            // Try next device
            return $this->findAvailableDevice($devices);
        }

        return $device;
    }

    /**
     * Limit-based selection: use device until limit, then rotate.
     */
    protected function limitBasedSelect(Campaign $campaign, Collection $devices): ?Device
    {
        // Try current device first
        $currentDevice = $this->getCurrentDevice($campaign, $devices);
        
        if ($currentDevice && $this->canDeviceSend($currentDevice)) {
            return $currentDevice;
        }

        // Find next available device
        return $this->findAvailableDevice($devices);
    }

    /**
     * Priority-based selection: use highest priority device until limit.
     */
    protected function prioritySelect(Campaign $campaign, Collection $devices): ?Device
    {
        // Devices already sorted by priority from getActiveDevices
        foreach ($devices as $device) {
            if ($this->canDeviceSend($device)) {
                return $device;
            }
        }

        return null;
    }

    /**
     * Get the currently selected device.
     */
    protected function getCurrentDevice(Campaign $campaign, Collection $devices): ?Device
    {
        $deviceList = $devices->values();
        $count = $deviceList->count();
        
        if ($count === 0) return null;

        $index = $campaign->current_device_index % $count;
        return $deviceList[$index] ?? null;
    }

    /**
     * Find any available device that can still send.
     */
    protected function findAvailableDevice(Collection $devices): ?Device
    {
        foreach ($devices as $device) {
            if ($this->canDeviceSend($device)) {
                return $device;
            }
        }

        return null;
    }

    /**
     * Check if a device can send more messages today.
     */
    public function canDeviceSend(Device $device): bool
    {
        // Check device connection status
        if ($device->status !== 'connected') {
            return false;
        }

        // Check daily limit
        $today = now()->toDateString();
        $usage = DeviceUsageLimit::where('device_id', $device->id)
            ->where('date', $today)
            ->first();

        if (!$usage) {
            return true; // No usage record = hasn't sent anything today
        }

        return $usage->canSend();
    }

    /**
     * Record that a device sent a message for a campaign.
     */
    public function recordSend(Campaign $campaign, Device $device): void
    {
        // Update pivot table
        $campaign->campaignDevices()
            ->where('device_id', $device->id)
            ->increment('messages_sent');

        // Update device daily usage
        $usage = DeviceUsageLimit::getOrCreateToday($device->id);
        $usage->incrementSent();

        // Log for debugging
        Log::debug("Campaign {$campaign->id}: Message sent via Device {$device->id}. " .
            "Device daily usage: {$usage->messages_sent}/{$usage->daily_limit}");
    }

    /**
     * Mark a device as inactive for the campaign (e.g., after repeated failures).
     */
    public function deactivateDevice(Campaign $campaign, Device $device, string $reason = null): void
    {
        $campaign->campaignDevices()
            ->where('device_id', $device->id)
            ->update(['is_active' => false]);

        Log::warning("Campaign {$campaign->id}: Device {$device->id} deactivated. Reason: {$reason}");
    }

    /**
     * Check if all devices for a campaign have hit their limits.
     */
    public function allDevicesAtLimit(Campaign $campaign): bool
    {
        $devices = $this->getActiveDevices($campaign);
        
        if ($devices->isEmpty()) {
            return true;
        }

        foreach ($devices as $device) {
            if ($this->canDeviceSend($device)) {
                return false;
            }
        }

        return true;
    }
}
