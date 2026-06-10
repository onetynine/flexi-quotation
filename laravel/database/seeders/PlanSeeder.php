<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            ['name' => 'LAPTOP - CI5-6 / 8GB RAM / 120GB SSD',    'deposit_per_unit' => 500,  'daily_rate' => 60,  'weekly_rate' => 180, 'monthly_rate' => 360],
            ['name' => 'LAPTOP - CI5-6 / 16GB RAM / 256GB SSD',   'deposit_per_unit' => 1000, 'daily_rate' => 70,  'weekly_rate' => 210, 'monthly_rate' => 420],
            ['name' => 'LAPTOP - CI5-8 / 8GB RAM / 120GB SSD',    'deposit_per_unit' => 800,  'daily_rate' => 80,  'weekly_rate' => 240, 'monthly_rate' => 480],
            ['name' => 'LAPTOP - CI5-8 / 16GB RAM / 256GB SSD',   'deposit_per_unit' => 1600, 'daily_rate' => 90,  'weekly_rate' => 270, 'monthly_rate' => 540],
            ['name' => 'LAPTOP - CI5-10/11 / 8GB RAM / 120GB SSD','deposit_per_unit' => 1200, 'daily_rate' => 100, 'weekly_rate' => 300, 'monthly_rate' => 600],
            ['name' => 'LAPTOP - CI5-10/11 / 16GB RAM / 256GB SSD','deposit_per_unit'=> 2400, 'daily_rate' => 110, 'weekly_rate' => 330, 'monthly_rate' => 660],
            ['name' => 'LAPTOP - CI7-6 / 8GB RAM / 128GB SSD',    'deposit_per_unit' => 1200, 'daily_rate' => 100, 'weekly_rate' => 300, 'monthly_rate' => 600],
            ['name' => 'LAPTOP - CI7-8 / 16GB RAM / 256GB SSD',   'deposit_per_unit' => 2400, 'daily_rate' => 120, 'weekly_rate' => 360, 'monthly_rate' => 720],
            ['name' => 'IPAD - 128GB',                             'deposit_per_unit' => 800,  'daily_rate' => 80,  'weekly_rate' => 240, 'monthly_rate' => 480],
            ['name' => 'Custom',                                   'deposit_per_unit' => 0,    'daily_rate' => 0,   'weekly_rate' => 0,   'monthly_rate' => 0, 'is_custom' => true],
        ];

        foreach ($plans as $plan) {
            \App\Models\Plan::create(array_merge(['specs' => null, 'is_custom' => false, 'active' => true], $plan));
        }
    }
}
