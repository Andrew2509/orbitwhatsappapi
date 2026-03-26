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
        Schema::table('auto_replies', function (Blueprint $table) {
            // Add reply_type enum (text or template)
            $table->enum('reply_type', ['text', 'template'])->default('text')->after('match_type');
            
            // Add template_id foreign key
            $table->foreignId('template_id')->nullable()->after('reply_type')->constrained('templates')->onDelete('set null');
            
            // Rename reply_message to reply_value for consistency
            $table->renameColumn('reply_message', 'reply_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auto_replies', function (Blueprint $table) {
            $table->renameColumn('reply_value', 'reply_message');
            $table->dropForeign(['template_id']);
            $table->dropColumn('template_id');
            $table->dropColumn('reply_type');
        });
    }
};
