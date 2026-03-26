<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webhook_id')->constrained()->onDelete('cascade');
            $table->string('event')->index();
            $table->json('payload');
            $table->integer('response_code')->nullable();
            $table->text('response_body')->nullable();
            $table->integer('attempt')->default(1);
            $table->integer('duration_ms')->nullable();
            $table->enum('status', ['success', 'failed', 'pending'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        // Add retry_count column to webhooks table
        Schema::table('webhooks', function (Blueprint $table) {
            $table->integer('max_retries')->default(3)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_logs');
        
        Schema::table('webhooks', function (Blueprint $table) {
            $table->dropColumn('max_retries');
        });
    }
};
