<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignRecipient;
use App\Models\Device;
use App\Models\Template;
use App\Models\Contact;
use App\Jobs\ProcessCampaignJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::where('user_id', Auth::id())
            ->with(['device', 'template'])
            ->latest()
            ->get();

        $devices = Device::where('user_id', Auth::id())->where('status', 'connected')->get();
        
        // Get both user and system templates
        $templates = Template::where(function($q) {
            $q->where('user_id', Auth::id())
              ->orWhere('is_system', true);
        })->where('is_active', true)->get();
        
        $contacts = Contact::where('user_id', Auth::id())->get();
        $labels = $contacts->pluck('labels')->flatten()->unique()->filter()->values();

        $stats = [
            'completed' => Campaign::where('user_id', Auth::id())->where('status', 'completed')->count(),
            'scheduled' => Campaign::where('user_id', Auth::id())->where('status', 'scheduled')->count(),
            'running' => Campaign::where('user_id', Auth::id())->where('status', 'running')->count(),
        ];

        return view('broadcast.index', compact('campaigns', 'devices', 'templates', 'labels', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'device_id' => 'required|exists:devices,id',
            'message_type' => 'required|in:text,template',
            'template_id' => 'required_if:message_type,template|nullable|exists:templates,id',
            'custom_message' => 'required_if:message_type,text|nullable|string',
            'recipients_type' => 'required|in:all,label,manual,csv',
            'recipients_manual' => 'required_if:recipients_type,manual|nullable|string',
            'recipients_label' => 'required_if:recipients_type,label|nullable|string',
            'recipients_csv' => 'required_if:recipients_type,csv|nullable|file|mimes:csv,txt|max:5120',
            'delay_min' => 'nullable|integer|min:5|max:120',
            'delay_max' => 'nullable|integer|min:5|max:300',
            'batch_size' => 'nullable|integer|min:10|max:200',
            'batch_delay' => 'nullable|integer|min:60|max:1800',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        // Collect recipients based on type
        $recipientsData = [];
        
        if ($request->recipients_type === 'all') {
            $contacts = Contact::where('user_id', Auth::id())->get();
            foreach ($contacts as $contact) {
                $recipientsData[] = [
                    'phone' => $contact->phone_number,
                    'name' => $contact->name,
                    'variables' => [],
                ];
            }
        } elseif ($request->recipients_type === 'label') {
            $contacts = Contact::where('user_id', Auth::id())
                ->whereJsonContains('labels', $request->recipients_label)
                ->get();
            foreach ($contacts as $contact) {
                $recipientsData[] = [
                    'phone' => $contact->phone_number,
                    'name' => $contact->name,
                    'variables' => [],
                ];
            }
        } elseif ($request->recipients_type === 'csv' && $request->hasFile('recipients_csv')) {
            $file = $request->file('recipients_csv');
            $content = file_get_contents($file->getRealPath());
            $lines = array_filter(explode("\n", $content));
            
            $header = null;
            foreach ($lines as $index => $line) {
                $row = str_getcsv(trim($line));
                if ($index === 0 && (strtolower($row[0]) === 'phone' || strtolower($row[0]) === 'nama')) {
                    // This is header row
                    $header = array_map('strtolower', $row);
                    continue;
                }
                
                if ($header) {
                    $data = array_combine($header, array_pad($row, count($header), ''));
                    $recipientsData[] = [
                        'phone' => $data['phone'] ?? $row[0],
                        'name' => $data['nama'] ?? $data['name'] ?? '',
                        'variables' => array_diff_key($data, array_flip(['phone', 'nama', 'name'])),
                    ];
                } else {
                    $recipientsData[] = [
                        'phone' => $row[0],
                        'name' => $row[1] ?? '',
                        'variables' => [],
                    ];
                }
            }
        } else {
            // Manual input
            $phones = array_map('trim', preg_split('/[,\n\r]+/', $request->recipients_manual ?? ''));
            foreach (array_filter($phones) as $phone) {
                $recipientsData[] = [
                    'phone' => $phone,
                    'name' => '',
                    'variables' => [],
                ];
            }
        }

        if (empty($recipientsData)) {
            return back()->with('error', 'Tidak ada penerima yang ditemukan.')->withInput();
        }

        // Create campaign
        $campaign = Campaign::create([
            'user_id' => Auth::id(),
            'device_id' => $request->device_id,
            'template_id' => $request->message_type === 'template' ? $request->template_id : null,
            'name' => $request->name,
            'message_type' => $request->message_type,
            'custom_message' => $request->message_type === 'text' ? $request->custom_message : null,
            'total_recipients' => count($recipientsData),
            'delay_min' => $request->delay_min ?? 10,
            'delay_max' => $request->delay_max ?? 30,
            'batch_size' => $request->batch_size ?? 50,
            'batch_delay' => $request->batch_delay ?? 300,
            'scheduled_at' => $request->scheduled_at,
            'status' => $request->scheduled_at ? 'scheduled' : 'draft',
        ]);

        // Create campaign recipients
        foreach ($recipientsData as $data) {
            CampaignRecipient::create([
                'campaign_id' => $campaign->id,
                'phone' => $data['phone'],
                'name' => $data['name'],
                'variables' => $data['variables'],
                'status' => 'pending',
            ]);
        }

        return redirect()->route('broadcast.index')
            ->with('success', "Campaign '{$campaign->name}' berhasil dibuat dengan {$campaign->total_recipients} penerima.");
    }

    public function show(Campaign $campaign)
    {
        if ($campaign->user_id !== Auth::id()) {
            abort(403);
        }
        
        $recipients = $campaign->campaignRecipients()->orderBy('id')->get();
        return view('broadcast.show', compact('campaign', 'recipients'));
    }

    public function start(Campaign $campaign)
    {
        if ($campaign->user_id !== Auth::id()) {
            abort(403);
        }

        if (!in_array($campaign->status, ['draft', 'paused', 'scheduled'])) {
            return back()->with('error', 'Campaign tidak bisa dimulai.');
        }

        $campaign->update([
            'status' => 'running',
            'started_at' => $campaign->started_at ?? now(),
        ]);

        // Dispatch the job
        ProcessCampaignJob::dispatch($campaign);

        return redirect()->route('broadcast.index')
            ->with('success', "Campaign '{$campaign->name}' telah dimulai!");
    }

    public function pause(Campaign $campaign)
    {
        if ($campaign->user_id !== Auth::id()) {
            abort(403);
        }

        $campaign->update(['status' => 'paused']);

        return redirect()->route('broadcast.index')
            ->with('success', 'Campaign di-pause.');
    }

    public function cancel(Campaign $campaign)
    {
        if ($campaign->user_id !== Auth::id()) {
            abort(403);
        }

        $campaign->update(['status' => 'cancelled']);

        return redirect()->route('broadcast.index')
            ->with('success', 'Campaign dibatalkan.');
    }

    /**
     * API endpoint for real-time progress updates.
     */
    public function progress(Campaign $campaign)
    {
        if ($campaign->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'id' => $campaign->id,
            'status' => $campaign->status,
            'total' => $campaign->total_recipients,
            'sent' => $campaign->sent_count,
            'failed' => $campaign->failed_count,
            'progress' => $campaign->progress_percent,
            'current_batch' => $campaign->current_batch,
        ]);
    }
}
