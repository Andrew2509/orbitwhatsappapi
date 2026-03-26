<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // Add foreign key to api_keys
            $table->foreignId('api_key_id')->nullable()->after('user_id')->constrained('api_keys')->nullOnDelete();
        });

        // SQLite workaround: drop index and columns carefully
        if (config('database.default') === 'sqlite') {
            Schema::table('applications', function (Blueprint $table) {
                // In SQLite, it's often safer to just not drop columns that have unique constraints
                // or have been indexed if it causes issues. However, let's try dropping the index.
                try {
                    $table->dropUnique('applications_api_key_unique');
                } catch (\Exception $e) {
                    // Ignore if index doesn't exist
                }
            });
        }

        Schema::table('applications', function (Blueprint $table) {
            if (Schema::hasColumn('applications', 'api_key')) {
                $table->dropColumn('api_key');
            }
        });

        Schema::table('applications', function (Blueprint $table) {
            if (Schema::hasColumn('applications', 'api_secret')) {
                $table->dropColumn('api_secret');
            }
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropForeign(['api_key_id']);
            $table->dropColumn('api_key_id');
            
            $table->string('api_key', 64)->unique()->after('user_id');
            $table->string('api_secret', 64)->after('api_key');
        });
    }
};
