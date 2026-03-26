<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('api_keys', function (Blueprint $table) {
            // IP Whitelisting - JSON array of allowed IP addresses
            $table->json('allowed_ips')->nullable()->after('is_active');
            
            // API Scopes/Permissions - JSON array of allowed actions
            $table->json('scopes')->nullable()->after('allowed_ips');
            
            // Optional: Expiration date for API key
            $table->timestamp('expires_at')->nullable()->after('scopes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('api_keys', function (Blueprint $table) {
            $table->dropColumn(['allowed_ips', 'scopes', 'expires_at']);
        });
    }
};
