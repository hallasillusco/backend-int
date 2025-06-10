<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VentasPrediccionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('ventas_predicciones')->insert([
            ['mes' => '2025-01', 'ventas_reales' => 5000, 'ventas_previstas' => 5200],
            ['mes' => '2025-02', 'ventas_reales' => 5400, 'ventas_previstas' => 5600],
            ['mes' => '2025-03', 'ventas_reales' => 5800, 'ventas_previstas' => 6000],
            ['mes' => '2025-04', 'ventas_reales' => 6200, 'ventas_previstas' => 6400],
        ]);
    }
}

