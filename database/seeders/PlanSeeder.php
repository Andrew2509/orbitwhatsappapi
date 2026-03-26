<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free / Trial',
                'slug' => 'free',
                'description' => 'Untuk mencoba fitur dasar platform WhatsApp API',
                'price' => 0,
                'billing_period' => 'monthly',
                'max_devices' => 1,
                'max_messages_per_day' => 100,
                'max_contacts' => 100,
                'features' => [
                    'basic_messaging' => true,
                    'broadcast' => false,
                    'auto_reply' => false,
                    'webhook' => false,
                    'api_access' => true,
                    'priority_support' => false,
                ],
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 1,
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'description' => 'Untuk UMKM & penggunaan bisnis menengah',
                'price' => 150000,
                'billing_period' => 'monthly',
                'max_devices' => 5,
                'max_messages_per_day' => 10000,
                'max_contacts' => 5000,
                'features' => [
                    'basic_messaging' => true,
                    'broadcast' => true,
                    'auto_reply' => true,
                    'webhook' => true,
                    'api_access' => true,
                    'priority_support' => true,
                ],
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'Untuk kebutuhan bisnis skala besar dengan volume tinggi',
                'price' => 500000,
                'billing_period' => 'monthly',
                'max_devices' => -1, // Unlimited
                'max_messages_per_day' => -1, // Unlimited
                'max_contacts' => -1, // Unlimited
                'features' => [
                    'basic_messaging' => true,
                    'broadcast' => true,
                    'auto_reply' => true,
                    'webhook' => true,
                    'dedicated_ip' => true,
                    'api_access' => true,
                    'priority_support' => true,
                    'account_manager' => true,
                ],
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $planData) {
            Plan::updateOrCreate(
                ['slug' => $planData['slug']],
                $planData
            );
        }
    }
}
