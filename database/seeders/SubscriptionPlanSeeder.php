<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\SubscriptionPlan::updateOrCreate(
            ['name' => 'Gratuit'],
            [
                'price' => 0,
                'product_limit' => 5,
                'staff_limit' => 1,
                'coupon_limit' => 0,
                'has_gallery' => false,
                'has_socials' => false,
                'is_boosted' => false,
                'can_recruit_drivers' => false,
            ]
        );

        \App\Models\SubscriptionPlan::updateOrCreate(
            ['name' => 'Standard'],
            [
                'price' => 15000,
                'product_limit' => 30,
                'staff_limit' => 2,
                'coupon_limit' => 3,
                'has_gallery' => true,
                'has_socials' => true,
                'is_boosted' => false,
                'can_recruit_drivers' => true,
            ]
        );

        \App\Models\SubscriptionPlan::updateOrCreate(
            ['name' => 'Premium'],
            [
                'price' => 45000,
                'product_limit' => 9999,
                'staff_limit' => 99,
                'coupon_limit' => 99,
                'has_gallery' => true,
                'has_socials' => true,
                'is_boosted' => true,
                'can_recruit_drivers' => true,
            ]
        );
    }
}
