@extends('layouts.app')

@section('title', 'API Logs')
@section('page-title', 'API Logs / Debugging')

@section('content')
<div class="space-y-6 animate-fade-in" x-data="{ showDetailModal: false, selectedLog: null }">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="relative flex-1 lg:w-80">
                <input type="text" placeholder="Search by endpoint or request ID..." class="form-input pl-10">
                <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-[var(--text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>
        <div class="flex gap-3">
            <select class="form-input w-auto">
                <option>All Status</option>
                <option>200 OK</option>
                <option>400 Bad Request</option>
                <option>401 Unauthorized</option>
                <option>500 Server Error</option>
            </select>
            <button class="btn btn-secondary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Refresh
            </button>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="card p-0 overflow-hidden">
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>Method</th>
                        <th>Endpoint</th>
                        <th>Status</th>
                        <th>Duration</th>
                        <th>Request ID</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $logs = [
                        ['time' => '22:15:32', 'method' => 'POST', 'endpoint' => '/v1/messages/send', 'status' => 200, 'duration' => '124ms', 'id' => 'req_a1b2c3d4'],
                        ['time' => '22:15:28', 'method' => 'GET', 'endpoint' => '/v1/devices', 'status' => 200, 'duration' => '45ms', 'id' => 'req_e5f6g7h8'],
                        ['time' => '22:15:15', 'method' => 'POST', 'endpoint' => '/v1/messages/send', 'status' => 400, 'duration' => '23ms', 'id' => 'req_i9j0k1l2'],
                        ['time' => '22:14:58', 'method' => 'POST', 'endpoint' => '/v1/messages/send-image', 'status' => 200, 'duration' => '892ms', 'id' => 'req_m3n4o5p6'],
                        ['time' => '22:14:42', 'method' => 'GET', 'endpoint' => '/v1/messages', 'status' => 200, 'duration' => '156ms', 'id' => 'req_q7r8s9t0'],
                        ['time' => '22:14:30', 'method' => 'POST', 'endpoint' => '/v1/webhooks', 'status' => 401, 'duration' => '12ms', 'id' => 'req_u1v2w3x4'],
                        ['time' => '22:14:15', 'method' => 'DELETE', 'endpoint' => '/v1/devices/5', 'status' => 500, 'duration' => '234ms', 'id' => 'req_y5z6a7b8'],
                    ];
                    @endphp
                    @foreach($logs as $log)
                    <tr class="cursor-pointer hover:bg-[var(--bg-secondary)]"
                        @click="selectedLog = {{ json_encode($log) }}; showDetailModal = true">
                        <td class="text-sm font-mono text-[var(--text-secondary)]">{{ $log['time'] }}</td>
                        <td>
                            <span class="text-xs font-mono px-1.5 py-0.5 rounded {{ $log['method'] === 'GET' ? 'bg-blue-500/20 text-blue-500' : ($log['method'] === 'POST' ? 'bg-emerald-500/20 text-emerald-500' : ($log['method'] === 'DELETE' ? 'bg-red-500/20 text-red-500' : 'bg-amber-500/20 text-amber-500')) }}">
                                {{ $log['method'] }}
                            </span>
                        </td>
                        <td class="font-mono text-sm">{{ $log['endpoint'] }}</td>
                        <td>
                            <span class="badge badge-{{ $log['status'] === 200 ? 'success' : ($log['status'] === 400 ? 'warning' : ($log['status'] === 401 ? 'warning' : 'danger')) }}">
                                {{ $log['status'] }}
                            </span>
                        </td>
                        <td class="text-sm text-[var(--text-secondary)]">{{ $log['duration'] }}</td>
                        <td class="font-mono text-sm text-[var(--text-muted)]">{{ $log['id'] }}</td>
                        <td>
                            <button class="p-1.5 hover:bg-[var(--bg-tertiary)] rounded-lg" title="View Details">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Log Detail Modal -->
    <div x-show="showDetailModal" x-transition class="modal-overlay active" @click.self="showDetailModal = false">
        <div class="modal-content max-w-2xl" @click.stop>
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold">Request Details</h3>
                <button @click="showDetailModal = false" class="p-2 hover:bg-[var(--bg-secondary)] rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <template x-if="selectedLog">
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Request ID</label>
                            <code class="text-sm font-mono" x-text="selectedLog.id"></code>
                        </div>
                        <div>
                            <label class="form-label">Duration</label>
                            <span x-text="selectedLog.duration"></span>
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Request</label>
                        <pre class="p-4 bg-slate-900 text-slate-100 rounded-xl overflow-x-auto text-sm max-h-48"><code>{
  "phone": "6281234567890",
  "message": "Hello from Orbit API!",
  "device_id": 1
}</code></pre>
                    </div>

                    <div>
                        <label class="form-label">Response</label>
                        <pre class="p-4 bg-slate-900 text-slate-100 rounded-xl overflow-x-auto text-sm max-h-48"><code>{
  "success": true,
  "message_id": "MSG-12345-2024",
  "status": "sent",
  "timestamp": "2024-12-26T15:30:00Z"
}</code></pre>
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button @click="navigator.clipboard.writeText(JSON.stringify({request: {phone: '6281234567890'}, response: {success: true}}, null, 2))" class="btn btn-secondary flex-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                            </svg>
                            Copy as JSON
                        </button>
                        <button @click="showDetailModal = false" class="btn btn-primary flex-1">Close</button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
@endsection
