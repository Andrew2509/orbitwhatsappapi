<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->string('app_key', 64)->unique()->after('api_key_id');
        });

        // Generate app_key for existing records
        $applications = \Illuminate\Support\Facades\DB::table('applications')->get();
        foreach ($applications as $app) {
            \Illuminate\Support\Facades\DB::table('applications')
                ->where('id', $app->id)
                ->update(['app_key' => 'app_' . Str::random(24)]);
        }
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn('app_key');
        });
    }
};
