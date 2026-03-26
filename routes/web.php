<?php

use App\Http\Controllers\Auth\FirebaseAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\AutoReplyController;
use App\Http\Controllers\ApiKeyController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SingleSendController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\BillingController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/firebase/login', [FirebaseAuthController::class, 'login'])->name('auth.firebase.login');
Route::post('/auth/firebase/sync-password', [FirebaseAuthController::class, 'syncPassword'])->name('auth.firebase.sync-password');

// Public routes
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('/contact', [App\Http\Controllers\HomeController::class, 'submitContact'])->name('contact.submit');

Route::get('/docs', function () {
    return view('docs.index');
})->name('docs.index');

// Public Abuse Report
Route::get('/report-abuse', [App\Http\Controllers\AbuseReportController::class, 'create'])->name('abuse.create');
Route::post('/report-abuse', [App\Http\Controllers\AbuseReportController::class, 'store'])->name('abuse.store');
Route::get('/report-abuse/thanks', [App\Http\Controllers\AbuseReportController::class, 'thanks'])->name('abuse.thanks');

// Policies
Route::get('/privacy-policy', function () { return view('pages.privacy-policy'); })->name('privacy-policy');
Route::get('/terms-of-service', function () { return view('pages.terms-of-service'); })->name('terms-of-service');
Route::get('/anti-spam-policy', function () { return view('pages.anti-spam-policy'); })->name('anti-spam-policy');

// Authenticated routes
Route::middleware(['auth', 'user.only'])->group(function () {
    // Dashboard - main entry point
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [DashboardController::class, 'analytics'])->name('analytics');

    // Devices
    Route::get('/devices', [DeviceController::class, 'index'])->name('devices.index');
    Route::post('/devices', [DeviceController::class, 'store'])->name('devices.store');
    Route::delete('/devices/{device}', [DeviceController::class, 'destroy'])->name('devices.destroy');
    Route::post('/devices/{device}/scan', [DeviceController::class, 'scan'])->name('devices.scan');
    Route::post('/devices/{device}/pairing-code', [DeviceController::class, 'pairingCode'])->name('devices.pairing-code');
    Route::post('/devices/{device}/logout', [DeviceController::class, 'logout'])->name('devices.logout');
    Route::get('/devices/{device}/status', [DeviceController::class, 'status'])->name('devices.status');

    // WhatsApp Service Health Proxy
    Route::get('/whatsapp/health', [DeviceController::class, 'health'])->name('whatsapp.health');

    // Contacts
    Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
    Route::post('/contacts', [ContactController::class, 'store'])->name('contacts.store');
    Route::put('/contacts/{contact}', [ContactController::class, 'update'])->name('contacts.update');
    Route::delete('/contacts/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy');
    Route::post('/contacts/{contact}/toggle-block', [ContactController::class, 'toggleBlock'])->name('contacts.toggle-block');

    // Messages
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{message}', [MessageController::class, 'show'])->name('messages.show');

    // Single Send (Manual Testing)
    Route::get('/single-send', [SingleSendController::class, 'index'])->name('single-send.index');
    Route::post('/single-send', [SingleSendController::class, 'send'])->name('single-send.send');

    // Broadcast / Campaigns
    Route::get('/broadcast', [CampaignController::class, 'index'])->name('broadcast.index');
    Route::post('/broadcast', [CampaignController::class, 'store'])->name('broadcast.store');
    Route::get('/broadcast/{campaign}', [CampaignController::class, 'show'])->name('broadcast.show');
    Route::get('/broadcast/{campaign}/progress', [CampaignController::class, 'progress'])->name('broadcast.progress');
    Route::post('/broadcast/{campaign}/start', [CampaignController::class, 'start'])->name('broadcast.start');
    Route::post('/broadcast/{campaign}/pause', [CampaignController::class, 'pause'])->name('broadcast.pause');
    Route::post('/broadcast/{campaign}/cancel', [CampaignController::class, 'cancel'])->name('broadcast.cancel');

    // Templates
    Route::get('/templates', [TemplateController::class, 'index'])->name('templates.index');
    Route::post('/templates', [TemplateController::class, 'store'])->name('templates.store');
    Route::put('/templates/{template}', [TemplateController::class, 'update'])->name('templates.update');
    Route::delete('/templates/{template}', [TemplateController::class, 'destroy'])->name('templates.destroy');

    // Auto Reply
    Route::get('/auto-reply', [AutoReplyController::class, 'index'])->name('auto-reply.index');
    Route::post('/auto-reply', [AutoReplyController::class, 'store'])->name('auto-reply.store');
    Route::put('/auto-reply/{autoReply}', [AutoReplyController::class, 'update'])->name('auto-reply.update');
    Route::delete('/auto-reply/{autoReply}', [AutoReplyController::class, 'destroy'])->name('auto-reply.destroy');
    Route::post('/auto-reply/{autoReply}/toggle', [AutoReplyController::class, 'toggle'])->name('auto-reply.toggle');

    // API Keys
    Route::get('/api-keys', [ApiKeyController::class, 'index'])->name('api-keys.index');
    Route::post('/api-keys', [ApiKeyController::class, 'store'])->name('api-keys.store');
    Route::post('/api-keys/{apiKey}/regenerate', [ApiKeyController::class, 'regenerate'])->name('api-keys.regenerate');
    Route::post('/api-keys/{apiKey}/revoke', [ApiKeyController::class, 'revoke'])->name('api-keys.revoke');
    Route::delete('/api-keys/{apiKey}', [ApiKeyController::class, 'destroy'])->name('api-keys.destroy');

    // My Apps (Applications)
    Route::resource('applications', ApplicationController::class);

    // Webhooks
    Route::get('/webhooks', [WebhookController::class, 'index'])->name('webhooks.index');
    Route::post('/webhooks', [WebhookController::class, 'store'])->name('webhooks.store');
    Route::put('/webhooks/{webhook}', [WebhookController::class, 'update'])->name('webhooks.update');
    Route::delete('/webhooks/{webhook}', [WebhookController::class, 'destroy'])->name('webhooks.destroy');
    Route::post('/webhooks/{webhook}/toggle', [WebhookController::class, 'toggle'])->name('webhooks.toggle');
    Route::post('/webhooks/{webhook}/test', [WebhookController::class, 'test'])->name('webhooks.test');
    Route::post('/webhooks/{webhook}/regenerate-secret', [WebhookController::class, 'regenerateSecret'])->name('webhooks.regenerate-secret');
    Route::get('/webhooks/{webhook}/logs', [WebhookController::class, 'logs'])->name('webhooks.logs');
    Route::get('/webhooks/{webhook}/secret', [WebhookController::class, 'showSecret'])->name('webhooks.show-secret');

    // API Logs
    Route::get('/api-logs', function () {
        return view('api-logs.index');
    })->name('api-logs.index');

    // Billing
    Route::prefix('billing')->name('billing.')->group(function () {
        Route::get('/', [BillingController::class, 'index'])->name('index');
        Route::get('/plans', [BillingController::class, 'plans'])->name('plans');
        Route::match(['get', 'post'], '/subscribe/{plan}', [BillingController::class, 'subscribe'])->name('subscribe');
        Route::get('/checkout/{invoice}', [BillingController::class, 'checkout'])->name('checkout');
        Route::post('/upload-proof/{invoice}', [BillingController::class, 'uploadPaymentProof'])->name('upload-proof');
        Route::get('/invoices', [BillingController::class, 'invoices'])->name('invoices');
        Route::get('/invoices/{invoice}/download', [BillingController::class, 'downloadInvoice'])->name('invoice.download');
        Route::post('/auto-renew', [BillingController::class, 'toggleAutoRenew'])->name('auto-renew');
        Route::post('/tax-info', [BillingController::class, 'updateTaxInfo'])->name('tax-info');
        Route::post('/cancel', [BillingController::class, 'cancel'])->name('cancel');
    });


    // Settings
    Route::get('/settings/profile', [SettingsController::class, 'profile'])->name('settings.profile');
    Route::put('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::post('/settings/avatar', [SettingsController::class, 'updateAvatar'])->name('settings.avatar.update');
    Route::get('/settings/security', [SettingsController::class, 'security'])->name('settings.security');
    Route::put('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password.update');
    Route::post('/settings/logout-others', [SettingsController::class, 'logoutOtherSessions'])->name('settings.logout-others');
    Route::delete('/settings/sessions/{session}', [SettingsController::class, 'revokeSession'])->name('settings.revoke-session');

    // Breeze Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

