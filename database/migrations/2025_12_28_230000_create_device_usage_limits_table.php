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
        Schema::create('device_usage_limits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->integer('messages_sent')->default(0);
            $table->integer('messages_limit')->default(200); // Default daily limit
            $table->integer('warning_threshold')->default(80); // Percentage to show warning
            $table->boolean('is_blocked')->default(false);
            $table->timestamp('cooldown_until')->nullable();
            $table->timestamps();
            
            $table->unique(['device_id', 'date']);
            $table->index(['date', 'is_blocked']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_usage_limits');
    }
};
