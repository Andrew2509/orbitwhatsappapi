@extends('layouts.app')

@section('title', 'My Apps')
@section('page-title', 'My Apps')

@section('content')
<div class="space-y-6 animate-fade-in" x-data="applicationManager">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <p class="text-[var(--text-secondary)]">Manage your API integrations and credentials</p>
        <a href="{{ route('applications.create') }}" class="btn btn-primary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Create New App
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl flex items-center gap-3">
        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="text-emerald-500">{{ session('success') }}</span>
    </div>
    @endif

    <!-- Apps Grid -->
    @if($applications->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($applications as $app)
        <div class="card hover:border-[var(--border-color-hover)] transition-all group">
            <!-- Header -->
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-lg font-bold">
                        {{ strtoupper(substr($app->name, 0, 2)) }}
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg">{{ $app->name }}</h3>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="w-2 h-2 rounded-full {{ $app->is_active ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                            <span class="text-xs text-[var(--text-muted)]">
                                {{ $app->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-2 gap-4 py-4 border-t border-b border-[var(--border-color)]">
                <div>
                    <p class="text-2xl font-bold">{{ number_format($app->messages_count) }}</p>
                    <p class="text-xs text-[var(--text-muted)]">Messages Sent</p>
                </div>
                <div>
                    <p class="text-2xl font-bold">{{ $app->devices_count }}</p>
                    <p class="text-xs text-[var(--text-muted)]">Devices</p>
                </div>
            </div>

            <!-- App Key -->
            <div class="mt-4 p-3 bg-[var(--bg-secondary)] rounded-lg">
                <p class="text-xs text-[var(--text-muted)] mb-1">App Key</p>
                <code class="text-sm font-mono text-[var(--text-secondary)]">{{ $app->masked_app_key }}</code>
            </div>

            <!-- API Key Info -->
            @if($app->apiKey)
            <div class="mt-2 flex items-center justify-between text-xs">
                <span class="text-[var(--text-muted)]">API Key: {{ $app->apiKey->name }}</span>
                <span class="badge badge-{{ $app->apiKey->is_active ? 'success' : 'danger' }} text-xs">
                    {{ $app->apiKey->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            @endif

            <!-- Last Used -->
            <p class="text-xs text-[var(--text-muted)] mt-3">
                Last used: {{ $app->last_used_at ? $app->last_used_at->diffForHumans() : 'Never' }}
            </p>

            <!-- Actions -->
            <div class="flex items-center gap-2 mt-4 pt-4 border-t border-[var(--border-color)]">
                <!-- REST API Button -->
                <button @click="openApiModal({
                    name: '{{ $app->name }}',
                    appKey: '{{ $app->app_key }}',
                    apiKey: '{{ $app->apiKey?->key ?? '' }}',
                    apiKeyName: '{{ $app->apiKey?->name ?? 'Not set' }}'
                })"
                class="btn btn-primary flex-1 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                    </svg>
                    REST API
                </button>

                <!-- View Details -->
                <a href="{{ route('applications.show', $app) }}" class="btn btn-secondary text-sm px-3" title="View Details">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </a>

                <!-- Delete -->
                <form action="{{ route('applications.destroy', $app) }}" method="POST"
                      onsubmit="return confirm('Delete this application?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger text-sm px-3" title="Delete">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <!-- Empty State -->
    <div class="card text-center py-16">
        <div class="w-20 h-20 mx-auto rounded-full bg-gradient-to-br from-indigo-500/20 to-purple-600/20 flex items-center justify-center mb-6">
            <svg class="w-10 h-10 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
            </svg>
        </div>
        <h3 class="text-xl font-semibold mb-2">No applications yet</h3>
        <p class="text-[var(--text-secondary)] mb-6 max-w-md mx-auto">
            Create your first application to get API credentials and start integrating with your systems.
        </p>
        <a href="{{ route('applications.create') }}" class="btn btn-primary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Create Your First App
        </a>
    </div>
    @endif

    <!-- REST API Modal -->
    <div x-show="showApiModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         @keydown.escape.window="showApiModal = false">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showApiModal = false"></div>

        <!-- Modal Content -->
        <div class="relative bg-[var(--bg-primary)] rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden border border-[var(--border-color)]"
             @click.stop>
            <template x-if="currentApp">
                <div>
                    <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-[var(--border-color)]">
                <div>
                    <h2 class="text-xl font-bold" x-text="'REST API - ' + (currentApp ? currentApp.name : '')"></h2>
                    <p class="text-sm text-[var(--text-muted)]">Create New Message</p>
                </div>
                <button @click="showApiModal = false" class="p-2 hover:bg-[var(--bg-secondary)] rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <div class="p-6 overflow-y-auto max-h-[calc(90vh-180px)]">
                <!-- Endpoint -->
                <div class="mb-6">
                    <label class="text-sm font-medium text-[var(--text-secondary)] mb-2 block">Endpoint URL</label>
                    <div class="flex items-center gap-2">
                        <code class="flex-1 p-3 bg-[var(--bg-secondary)] rounded-lg font-mono text-sm">
                            POST {{ url('/api/v1/messages/send') }}
                        </code>
                        <button @click="copyText('{{ url('/api/v1/messages/send') }}')"
                                class="p-3 bg-[var(--bg-secondary)] hover:bg-[var(--bg-tertiary)] rounded-lg transition-colors" title="Copy">
                            <svg class="w-5 h-5 text-[var(--text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Credentials -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="p-4 bg-[var(--bg-secondary)] rounded-lg">
                        <label class="text-xs text-[var(--text-muted)] mb-1 block">App Key (appkey)</label>
                        <div class="flex items-center justify-between">
                            <code class="font-mono text-sm" x-text="currentApp ? currentApp.appKey : ''"></code>
                            <button @click="copyText(currentApp ? currentApp.appKey : '')" class="p-1 hover:bg-[var(--bg-primary)] rounded transition-colors" title="Copy">
                                <svg class="w-4 h-4 text-[var(--text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="p-4 bg-[var(--bg-secondary)] rounded-lg">
                        <label class="text-xs text-[var(--text-muted)] mb-1 block">API Key (authkey)</label>
                        <div class="flex items-center justify-between">
                            <code class="font-mono text-sm" x-text="(currentApp && currentApp.apiKey) ? currentApp.apiKey : 'Not configured'"></code>
                            <button x-show="currentApp && currentApp.apiKey" @click="copyText(currentApp ? currentApp.apiKey : '')" class="p-1 hover:bg-[var(--bg-primary)] rounded transition-colors" title="Copy">
                                <svg class="w-4 h-4 text-[var(--text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Code Tabs -->
                <div class="mb-4">
                    <div class="flex gap-2 border-b border-[var(--border-color)]">
                        <button @click="activeTab = 'curl'"
                                :class="activeTab === 'curl' ? 'text-indigo-500 border-b-2 border-indigo-500' : 'text-[var(--text-muted)]'"
                                class="px-4 py-2 text-sm font-medium transition-colors">cURL</button>
                        <button @click="activeTab = 'php'"
                                :class="activeTab === 'php' ? 'text-indigo-500 border-b-2 border-indigo-500' : 'text-[var(--text-muted)]'"
                                class="px-4 py-2 text-sm font-medium transition-colors">PHP</button>
                        <button @click="activeTab = 'nodejs'"
                                :class="activeTab === 'nodejs' ? 'text-indigo-500 border-b-2 border-indigo-500' : 'text-[var(--text-muted)]'"
                                class="px-4 py-2 text-sm font-medium transition-colors">Node.js</button>
                        <button @click="activeTab = 'python'"
                                :class="activeTab === 'python' ? 'text-indigo-500 border-b-2 border-indigo-500' : 'text-[var(--text-muted)]'"
                                class="px-4 py-2 text-sm font-medium transition-colors">Python</button>
                    </div>
                </div>

                <!-- cURL Tab -->
                <div x-show="activeTab === 'curl'" class="space-y-4">
                    <div class="relative">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium">Text Message Only</span>
                            <button @click="copyText(document.getElementById('curl-text').innerText)" class="text-xs text-indigo-500 hover:text-indigo-400">Copy</button>
                        </div>
                        <pre id="curl-text" class="p-4 bg-[var(--bg-secondary)] rounded-lg overflow-x-auto text-sm font-mono"><code>curl --location --request POST '{{ url('/api/v1/messages/send') }}' \
  --form 'appkey="<span x-text="currentApp ? currentApp.appKey : ''"></span>"' \
  --form 'authkey="<span x-text="currentApp ? currentApp.apiKey : ''"></span>"' \
  --form 'to="RECEIVER_NUMBER"' \
  --form 'message="Example message"'</code></pre>
                    </div>

                    <div class="relative">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium">Text Message with File</span>
                        </div>
                        <pre class="p-4 bg-[var(--bg-secondary)] rounded-lg overflow-x-auto text-sm font-mono"><code>curl --location --request POST '{{ url('/api/v1/messages/send') }}' \
  --form 'appkey="<span x-text="currentApp ? currentApp.appKey : ''"></span>"' \
  --form 'authkey="<span x-text="currentApp ? currentApp.apiKey : ''"></span>"' \
  --form 'to="RECEIVER_NUMBER"' \
  --form 'message="Example message"' \
  --form 'file="https://example.com/sample.pdf"'</code></pre>
                    </div>

                    <div class="relative">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium">Template Only</span>
                        </div>
                        <pre class="p-4 bg-[var(--bg-secondary)] rounded-lg overflow-x-auto text-sm font-mono"><code>curl --location --request POST '{{ url('/api/v1/messages/send') }}' \
  --form 'appkey="<span x-text="currentApp ? currentApp.appKey : ''"></span>"' \
  --form 'authkey="<span x-text="currentApp ? currentApp.apiKey : ''"></span>"' \
  --form 'to="RECEIVER_NUMBER"' \
  --form 'template_id="TEMPLATE_ID"' \
  --form 'variables[ {variableKey1} ]="jhons"' \
  --form 'variables[ {variableKey2} ]="replaceable value"'</code></pre>
                    </div>
                </div>

                <!-- PHP Tab -->
                <div x-show="activeTab === 'php'" class="space-y-4">
                    <div class="relative">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium">Text Message Only</span>
                        </div>
                        <pre class="p-4 bg-[var(--bg-secondary)] rounded-lg overflow-x-auto text-sm font-mono"><code>&lt;?php
$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => '{{ url('/api/v1/messages/send') }}',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => array(
        'appkey' => '<span x-text="currentApp ? currentApp.appKey : ''"></span>',
        'authkey' => '<span x-text="currentApp ? currentApp.apiKey : ''"></span>',
        'to' => 'RECEIVER_NUMBER',
        'message' => 'Example message',
        'sandbox' => 'false'
    ),
));

$response = curl_exec($curl);
curl_close($curl);
echo $response;</code></pre>
                    </div>
                </div>

                <!-- Node.js Tab -->
                <div x-show="activeTab === 'nodejs'" class="space-y-4">
                    <div class="relative">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium">Node.js - Request</span>
                        </div>
                        <pre class="p-4 bg-[var(--bg-secondary)] rounded-lg overflow-x-auto text-sm font-mono"><code>var request = require('request');
var options = {
  'method': 'POST',
  'url': '{{ url('/api/v1/messages/send') }}',
  'headers': {
  },
  formData: {
    'appkey': '<span x-text="currentApp ? currentApp.appKey : ''"></span>',
    'authkey': '<span x-text="currentApp ? currentApp.apiKey : ''"></span>',
    'to': 'RECEIVER_NUMBER',
    'message': 'Example message'
  }
};
request(options, function (error, response) {
  if (error) throw new Error(error);
  console.log(response.body);
});</code></pre>
                    </div>
                </div>

                <!-- Python Tab -->
                <div x-show="activeTab === 'python'" class="space-y-4">
                    <div class="relative">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium">Using requests</span>
                        </div>
                        <pre class="p-4 bg-[var(--bg-secondary)] rounded-lg overflow-x-auto text-sm font-mono"><code>import requests

url = "{{ url('/api/v1/messages/send') }}"

payload = {
    'appkey': '<span x-text="currentApp ? currentApp.appKey : ''"></span>',
    'authkey': '<span x-text="currentApp ? currentApp.apiKey : ''"></span>',
    'to': 'RECEIVER_NUMBER',
    'message': 'Example message'
}
files=[

]
headers = {}

response = requests.request("POST", url, headers=headers, data=payload, files=files)

print(response.text)</code></pre>
                    </div>
                </div>

                <!-- Success Response -->
                <div class="mt-6">
                    <h4 class="text-sm font-medium mb-2">Successful Json Callback</h4>
                    <pre class="p-4 bg-[var(--bg-secondary)] rounded-lg overflow-x-auto text-sm font-mono"><code>{
    "message_status": "Success",
    "data": [
        {
            "from": "SENDER_NUMBER",
            "to": "RECEIVER_NUMBER",
            "status_code": 200
        }
    ]
}</code></pre>
                </div>

                <!-- Parameters Table -->
                <div class="mt-6">
                    <h4 class="text-sm font-medium mb-3">Parameters</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-[var(--border-color)]">
                                    <th class="text-left py-2 px-3 text-[var(--text-muted)] font-medium">S/N</th>
                                    <th class="text-left py-2 px-3 text-[var(--text-muted)] font-medium">VALUE</th>
                                    <th class="text-left py-2 px-3 text-[var(--text-muted)] font-medium">TYPE</th>
                                    <th class="text-left py-2 px-3 text-[var(--text-muted)] font-medium">REQUIRED</th>
                                    <th class="text-left py-2 px-3 text-[var(--text-muted)] font-medium">DESCRIPTION</th>
                                </tr>
                            </thead>
                            <tbody class="text-[var(--text-secondary)]">
                                <tr class="border-b border-[var(--border-color)]">
                                    <td class="py-2 px-3">1.</td>
                                    <td class="py-2 px-3 font-mono">appkey</td>
                                    <td class="py-2 px-3">string</td>
                                    <td class="py-2 px-3 text-indigo-500">Yes</td>
                                    <td class="py-2 px-3">Used to authorize a transaction for the app</td>
                                </tr>
                                <tr class="border-b border-[var(--border-color)]">
                                    <td class="py-2 px-3">2.</td>
                                    <td class="py-2 px-3 font-mono">authkey</td>
                                    <td class="py-2 px-3">string</td>
                                    <td class="py-2 px-3 text-indigo-500">Yes</td>
                                    <td class="py-2 px-3">Used to authorize a transaction for the is valid user</td>
                                </tr>
                                <tr class="border-b border-[var(--border-color)]">
                                    <td class="py-2 px-3">3.</td>
                                    <td class="py-2 px-3 font-mono">to</td>
                                    <td class="py-2 px-3">number</td>
                                    <td class="py-2 px-3 text-indigo-500">Yes</td>
                                    <td class="py-2 px-3">Who will receive the message the Whatsapp number should be full number with country code</td>
                                </tr>
                                <tr class="border-b border-[var(--border-color)]">
                                    <td class="py-2 px-3">4.</td>
                                    <td class="py-2 px-3 font-mono">template_id</td>
                                    <td class="py-2 px-3">string</td>
                                    <td class="py-2 px-3">No</td>
                                    <td class="py-2 px-3">Used to authorize a transaction for the template</td>
                                </tr>
                                <tr class="border-b border-[var(--border-color)]">
                                    <td class="py-2 px-3">5.</td>
                                    <td class="py-2 px-3 font-mono">message</td>
                                    <td class="py-2 px-3">string</td>
                                    <td class="py-2 px-3">No</td>
                                    <td class="py-2 px-3">The transactional message max.1000 words</td>
                                </tr>
                                <tr class="border-b border-[var(--border-color)]">
                                    <td class="py-2 px-3">6.</td>
                                    <td class="py-2 px-3 font-mono">file</td>
                                    <td class="py-2 px-3">string</td>
                                    <td class="py-2 px-3">No</td>
                                    <td class="py-2 px-3">file extension type should be in jpg,jpeg,png,webp,pdf,docx,xlsx,csv,txt</td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-3">7.</td>
                                    <td class="py-2 px-3 font-mono">variables</td>
                                    <td class="py-2 px-3">Array</td>
                                    <td class="py-2 px-3">No</td>
                                    <td class="py-2 px-3">the first value you list replaces the {1} variable in the template message and the second value you list replaces the {2} variable in the template message and the third value you list replaces the {3} variable and so on.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="p-4 border-t border-[var(--border-color)] bg-[var(--bg-secondary)]">
                <div class="flex items-center justify-between">
                    <a href="{{ route('docs.index') }}" class="text-sm text-indigo-500 hover:text-indigo-400">
                        View Full Documentation →
                    </a>
                    <button @click="showApiModal = false" class="btn btn-secondary">Close</button>
                </div>
                </div>
            </template>
        </div>
    </div>
</div>
@endsection
