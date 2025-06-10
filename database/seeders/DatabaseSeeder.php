<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Primero vaciamos las tablas
        $this->truncateTables([
            // 'role_user',
            'users',
            'roles',
            'password_reset_tokens',
            'failed_jobs',
            'personal_access_tokens',
            
            'unidads',
            'categorias',
            'productos',
            'proveedors',
            'sucursals',
            'galerias',
            'lotes',
            'ingresos',
            'detalle_ingresos',
            'producto_sucursals',
            // 'tipo_pagos',
            'clientes',
            'proformas',
            'detalle_proformas',
            'ventas',
            'detalle_ventas',
            'banners',
            'tipo_categorias',
            'colors',
            'ayudas',
            'tipo_blogs',
            'blogs',
            'videos',
            'configs',
        ]);

        // Luego insertamos los datos
        $this->call([
            VentasPrediccionesSeeder::class,
            ConfigurationSeeder::class,
            // PruebaSeeder::class,
        ]);
    }

   public function truncateTables(array $tables)
{
    DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

    foreach ($tables as $table) {
        if (Schema::hasTable($table)) {
            DB::table($table)->truncate();
        }
    }

    DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
}

}
