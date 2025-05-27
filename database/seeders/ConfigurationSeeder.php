<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Role::create([
            'name' => 'administrador',
            'display_name' => 'administrador',
        ]);
        \App\Models\Role::create([
            'name' => 'almacen',
            'display_name' => 'almacen',
        ]);
        \App\Models\Role::create([
            'name' => 'vendedor',
            'display_name' => 'vendedor',
        ]);
        \App\Models\TipoBlog::create([
            'nombre' => 'BLOG',
        ]);
        \App\Models\TipoBlog::create([
            'nombre' => 'EVENTO',
        ]);
        \App\Models\Config::create([
            'razon_social' => 'SUCURSAL PRINCIPAL',
            'direccion' => 'Av.',
            'telefono' => '471615',
            'pago_bancario' => true,
            'pago_qr' => true,
        ]);

        $faker = Faker::create();
        $nombre = $faker->name();
        $apellido = $faker->lastname();
        // $nombre = 'Mauricio';
        // $apellido = 'Roldan Herbas';
        \App\Models\User::factory()->create([
            'nombres' => $nombre,
            'apellidos' => $apellido,
            'nombre_completo' => $nombre . ' ' . $apellido,
            'username' => 'admin',
            'email' => 'prueba@example.com',
            'rol_id' => 1,
        ]);

        \App\Models\Sucursal::create([
            'nombre' => 'SUCURSAL PRINCIPAL',
            'direccion' => 'Av.',
            'telefono' => '471615',
            'habilitado' => true,
        ]);
        \App\Models\TipoPago::create([
            'nombre' => 'PAGO QR',
            'habilitado' => true,
        ]);
        \App\Models\TipoPago::create([
            'nombre' => 'TRANSFERENCIA BANCARIA',
            'habilitado' => true,
        ]);
    }
}
