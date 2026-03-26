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
        Schema::table('templates', function (Blueprint $table) {
            $table->string('category')->change();
            $table->boolean('is_system')->default(false)->after('id');
            $table->index('category');
            $table->index('is_system');
            
            // Allow user_id to be nullable for system templates
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->string('category')->change(); // Unfortunately we can't easily go back to enum without defining the values
            $table->dropIndex(['category']);
            $table->dropIndex(['is_system']);
            $table->dropColumn('is_system');
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
    }
};
