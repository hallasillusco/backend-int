<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
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
            'tipo_pagos',
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
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
            ConfigurationSeeder::class,
            // PruebaSeeder::class,

        ]);
    }

    public function truncateTables(array $tables)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
