<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

/**
 * Planes iniciales para una instalación NUEVA.
 * En un sistema que ya tiene planes, NO corras este seeder:
 * edita los planes desde /admin/plans para no crear duplicados.
 */
class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $planes = [
            ['name' => 'Estudiante',           'price' => 30,  'duration_days' => 30,  'max_tesis' => 1,  'max_revisiones' => 30,  'orden' => 10, 'destacado' => true],
            ['name' => 'Asesor',               'price' => 90,  'duration_days' => 30,  'max_tesis' => 10, 'max_revisiones' => 150, 'orden' => 20, 'destacado' => false],
            ['name' => 'Estudiante semestral', 'price' => 150, 'duration_days' => 180, 'max_tesis' => 1,  'max_revisiones' => 30,  'orden' => 30, 'destacado' => false],
            ['name' => 'Asesor semestral',     'price' => 450, 'duration_days' => 180, 'max_tesis' => 10, 'max_revisiones' => 150, 'orden' => 40, 'destacado' => false],
        ];

        foreach ($planes as $plan) {
            Plan::updateOrCreate(
                ['name' => $plan['name']],
                $plan + ['activo' => true]
            );
        }
    }
}
