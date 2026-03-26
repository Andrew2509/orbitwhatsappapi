<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DeviceController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\PromoCodeController;
use App\Http\Controllers\Admin\BlacklistController;
use App\Http\Controllers\Admin\AbuseReportController;
use App\Http\Controllers\Admin\SystemController;
use App\Http\Controllers\Admin\SupportController;
use App\Http\Controllers\Admin\SettingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Routes for Admin Panel. All routes are prefixed with /admin and
| protected by auth and admin middleware.
|
*/

Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // User Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::post('/{user}/impersonate', [UserController::class, 'impersonate'])->name('impersonate');
        Route::post('/{user}/suspend', [UserController::class, 'suspend'])->name('suspend');
        Route::post('/{user}/unsuspend', [UserController::class, 'unsuspend'])->name('unsuspend');
        Route::put('/{user}/limits', [UserController::class, 'updateLimits'])->name('update-limits');
    });

    // Device Management
    Route::prefix('devices')->name('devices.')->group(function () {
        Route::get('/', [DeviceController::class, 'index'])->name('index');
        Route::get('/usage', [DeviceController::class, 'usageDashboard'])->name('usage');
        Route::post('/{device}/force-logout', [DeviceController::class, 'forceLogout'])->name('force-logout');
        Route::post('/{device}/reset-limit', [DeviceController::class, 'resetLimit'])->name('reset-limit');
        Route::post('/{device}/start-warmup', [DeviceController::class, 'startWarmup'])->name('start-warmup');
        Route::post('/{device}/skip-warmup', [DeviceController::class, 'skipWarmup'])->name('skip-warmup');
        Route::get('/logs', [DeviceController::class, 'logs'])->name('logs');
    });

    // Plan Management
    Route::resource('plans', PlanController::class)->except(['show']);

    // Transactions
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', [TransactionController::class, 'index'])->name('index');
        Route::get('/pending', [TransactionController::class, 'pending'])->name('pending');
        Route::post('/{invoice}/approve', [TransactionController::class, 'approve'])->name('approve');
        Route::post('/{invoice}/reject', [TransactionController::class, 'reject'])->name('reject');
        Route::delete('/{invoice}', [TransactionController::class, 'destroy'])->name('destroy');
        Route::get('/reports', [TransactionController::class, 'reports'])->name('reports');
    });

    // Promo Codes
    Route::resource('promo-codes', PromoCodeController::class);
    Route::patch('promo-codes/{promo_code}/toggle', [PromoCodeController::class, 'toggle'])->name('promo-codes.toggle');
    Route::get('promo-codes-generate', [PromoCodeController::class, 'generateCode'])->name('promo-codes.generate-code');

    // Word Blacklist (Content Filtering)
    Route::resource('blacklist', BlacklistController::class)->except(['show']);
    Route::patch('blacklist/{blacklist}/toggle', [BlacklistController::class, 'toggle'])->name('blacklist.toggle');
    Route::post('blacklist/bulk-import', [BlacklistController::class, 'bulkImport'])->name('blacklist.bulk-import');

    // Abuse Reports
    Route::prefix('abuse-reports')->name('abuse-reports.')->group(function () {
        Route::get('/', [AbuseReportController::class, 'index'])->name('index');
        Route::get('/{abuse_report}', [AbuseReportController::class, 'show'])->name('show');
        Route::patch('/{abuse_report}/investigate', [AbuseReportController::class, 'investigate'])->name('investigate');
        Route::patch('/{abuse_report}/resolve', [AbuseReportController::class, 'resolve'])->name('resolve');
        Route::delete('/{abuse_report}', [AbuseReportController::class, 'destroy'])->name('destroy');
    });

    // System
    Route::prefix('system')->name('system.')->group(function () {
        Route::get('/queue', [SystemController::class, 'queue'])->name('queue');
        Route::get('/webhooks', [SystemController::class, 'webhooks'])->name('webhooks');
        Route::get('/logs', [SystemController::class, 'logs'])->name('logs');
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    });

    // Support
    Route::prefix('support')->name('support.')->group(function () {
        Route::get('/tickets', [SupportController::class, 'tickets'])->name('tickets');
        Route::get('/tickets/{ticket}', [SupportController::class, 'showTicket'])->name('tickets.show');
        Route::post('/tickets/{ticket}/reply', [SupportController::class, 'replyTicket'])->name('tickets.reply');
        Route::get('/announcements', [SupportController::class, 'announcements'])->name('announcements');
        Route::post('/announcements', [SupportController::class, 'createAnnouncement'])->name('announcements.store');
    });
});

