<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use App\Helpers\ImageHelper;

class SettingController extends Controller
{
    public function index()
    {
        try {
            $this->seedDefaults();
            $settings = SiteSetting::all()->groupBy('group');
        } catch (\Throwable $e) {
            $settings = collect();
        }

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except('_token');

        // Handle file uploads
        foreach ($request->files as $key => $file) {
            if ($file->isValid()) {
                $data[$key] = ImageHelper::fileToBase64($file);
            }
        }

        foreach ($data as $key => $value) {
            SiteSetting::where('key', $key)->update(['value' => $value]);
        }

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }

    private function seedDefaults()
    {
        $defaults = [
            ['key' => 'site_name', 'value' => 'Orbit WhatsApp API', 'type' => 'text', 'group' => 'general', 'description' => 'Nama Website'],
            ['key' => 'site_tagline', 'value' => 'Integrasi WhatsApp API Tercepat untuk Bisnis Anda', 'type' => 'text', 'group' => 'general', 'description' => 'Tagline Website'],
            ['key' => 'site_logo', 'value' => asset('Image/logo-wa-api-black.png'), 'type' => 'file', 'group' => 'branding', 'description' => 'Logo (Dark)'],
            ['key' => 'site_logo_white', 'value' => asset('Image/logo-wa-api-white.png'), 'type' => 'file', 'group' => 'branding', 'description' => 'Logo (Light)'],
            ['key' => 'site_favicon', 'value' => asset('favicon.ico'), 'type' => 'file', 'group' => 'branding', 'description' => 'Favicon'],
            ['key' => 'support_email', 'value' => 'sales@orbitwaapi.site', 'type' => 'text', 'group' => 'contact', 'description' => 'Email Support'],
            ['key' => 'support_whatsapp', 'value' => '+62 821-2222-3333', 'type' => 'text', 'group' => 'contact', 'description' => 'WhatsApp Support'],
            ['key' => 'meta_description', 'value' => 'Kirim notifikasi, OTP, dan blast pesan secara otomatis dengan infrastruktur enterprise-grade yang stabil dan aman.', 'type' => 'textarea', 'group' => 'seo', 'description' => 'Meta Description'],
            ['key' => 'footer_text', 'value' => '© 2026 Orbit WhatsApp API. All rights reserved.', 'type' => 'text', 'group' => 'general', 'description' => 'Teks Footer'],
        ];

        foreach ($defaults as $default) {
            SiteSetting::updateOrCreate(['key' => $default['key']], $default);
        }
    }
}
