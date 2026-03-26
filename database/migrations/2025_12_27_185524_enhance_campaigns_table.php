<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            // Message configuration
            $table->enum('message_type', ['text', 'template'])->default('template')->after('name');
            $table->text('custom_message')->nullable()->after('message_type');
            $table->string('media_path')->nullable()->after('custom_message');
            
            // Safety settings (Anti-ban)
            $table->unsignedInteger('delay_min')->default(10)->after('scheduled_at'); // seconds
            $table->unsignedInteger('delay_max')->default(30)->after('delay_min'); // seconds
            $table->unsignedInteger('batch_size')->default(50)->after('delay_max'); // messages per batch
            $table->unsignedInteger('batch_delay')->default(300)->after('batch_size'); // seconds between batches
            
            // Progress tracking
            $table->unsignedInteger('current_batch')->default(0)->after('batch_delay');
        });
    }

    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn([
                'message_type',
                'custom_message', 
                'media_path',
                'delay_min',
                'delay_max',
                'batch_size',
                'batch_delay',
                'current_batch'
            ]);
        });
    }
};
