<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        Plan::updateOrCreate(
            ['name' => 'Tesista'],
            ['price' => 29, 'max_tesis' => 3, 'max_revisiones' => 30]
        );

        Plan::updateOrCreate(
            ['name' => 'Investigador'],
            ['price' => 49, 'max_tesis' => 10, 'max_revisiones' => 100]
        );
    }
}
