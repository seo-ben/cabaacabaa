<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Country::create([
            'name' => 'Togo',
            'phone_prefix' => '+228',
            'flag_icon' => 'tg',
            'is_active' => true,
        ]);

        \App\Models\Country::create([
            'name' => 'Sénégal',
            'phone_prefix' => '+221',
            'flag_icon' => 'sn',
            'is_active' => true,
        ]);
    }
}
