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
        Schema::create('campaign_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
            $table->foreignId('device_id')->constrained()->onDelete('cascade');
            $table->integer('messages_sent')->default(0); // Track per-device usage in this campaign
            $table->integer('priority')->default(0); // Rotation order priority
            $table->boolean('is_active')->default(true); // Can be disabled if device fails
            $table->timestamps();
            
            $table->unique(['campaign_id', 'device_id']);
            $table->index(['campaign_id', 'is_active']);
        });

        // Add rotation_strategy to campaigns table
        Schema::table('campaigns', function (Blueprint $table) {
            $table->string('rotation_strategy')->default('round_robin')->after('batch_delay');
            // round_robin = rotate after each message
            // limit_based = rotate when device hits limit
            // priority = use priority order, rotate on limit
            $table->integer('current_device_index')->default(0)->after('rotation_strategy');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_devices');
        
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn(['rotation_strategy', 'current_device_index']);
        });
    }
};
