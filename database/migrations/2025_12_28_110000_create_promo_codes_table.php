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
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            
            // Discount Type
            $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('discount_value', 10, 2); // 20 for 20%, or 50000 for Rp 50.000
            
            // Usage Restrictions
            $table->decimal('min_purchase', 15, 2)->default(0); // Minimum purchase amount
            $table->decimal('max_discount', 15, 2)->nullable(); // Max discount for percentage type
            $table->integer('usage_limit')->nullable(); // Total times code can be used (null = unlimited)
            $table->integer('usage_limit_per_user')->default(1); // Times per user
            $table->integer('times_used')->default(0); // Counter
            
            // Validity
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            
            // Applicable Plans (JSON array of plan IDs, null = all plans)
            $table->json('applicable_plans')->nullable();
            
            $table->timestamps();
        });

        // Pivot table to track which users used which promo codes
        Schema::create('promo_code_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promo_code_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('invoice_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('discount_applied', 15, 2); // Actual discount given
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promo_code_usages');
        Schema::dropIfExists('promo_codes');
    }
};
