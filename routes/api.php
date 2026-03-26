<?php

use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\WhatsAppWebhookController;
use App\Http\Controllers\Api\ChatbotController;
use App\Http\Controllers\Api\MidtransWebhookController;
use Illuminate\Support\Facades\Route;

// Internal webhook from WhatsApp service (no auth required)
Route::post('/webhook/whatsapp', [WhatsAppWebhookController::class, 'handle']);
Route::post('/webhook/incoming-message', [ChatbotController::class, 'handleIncoming']);
Route::post('/webhook/midtrans', [MidtransWebhookController::class, 'handle']);


// API v1 routes with API key authentication and rate limiting
Route::prefix('v1')->middleware(['api.auth', 'api.rate'])->group(function () {
    // Messages - requires messages.send or messages.read scope
    Route::post('/messages/send', [MessageController::class, 'send'])
        ->middleware('api.scope:messages.send');
    Route::get('/messages', [MessageController::class, 'index'])
        ->middleware('api.scope:messages.read');
    Route::get('/messages/{messageId}/status', [MessageController::class, 'status'])
        ->middleware('api.scope:messages.read');

    // Devices - requires devices.read scope
    Route::get('/devices', [DeviceController::class, 'index'])
        ->middleware('api.scope:devices.read');
    Route::get('/devices/{id}', [DeviceController::class, 'show'])
        ->middleware('api.scope:devices.read');
});

// JWT Authentication Routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [\App\Http\Controllers\Api\ApiAuthController::class, 'register']);
    Route::post('/login', [\App\Http\Controllers\Api\ApiAuthController::class, 'login']);
    
    Route::middleware('auth:api')->group(function () {
        Route::get('/me', [\App\Http\Controllers\Api\ApiAuthController::class, 'me']);
        Route::post('/logout', [\App\Http\Controllers\Api\ApiAuthController::class, 'logout']);
        Route::post('/refresh', [\App\Http\Controllers\Api\ApiAuthController::class, 'refresh']);
    });
});


