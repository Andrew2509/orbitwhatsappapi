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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->longText('avatar')->nullable()->after('email');
            } else {
                $table->longText('avatar')->nullable()->change();
            }
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->longText('payment_proof')->nullable()->change();
        });

        Schema::table('site_settings', function (Blueprint $table) {
            $table->longText('value')->nullable()->change();
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->longText('avatar')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->change();
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->string('payment_proof')->nullable()->change();
        });

        Schema::table('site_settings', function (Blueprint $table) {
            $table->text('value')->nullable()->change();
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->string('avatar')->nullable()->change();
        });
    }
};
