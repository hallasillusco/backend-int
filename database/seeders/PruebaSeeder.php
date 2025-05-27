<?php

namespace Database\Seeders;

use Faker\Factory as Faker;
use Illuminate\Support\Str;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PruebaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $categorias = [
        //     'Material de escritorio',
        //     'Ornamental',
        // ];
        // foreach ($categorias as $value) {
        //     \App\Models\Categoria::create([
        //         'nombre' => $value,
        //         'habilitado' => true,
        //     ]);
        // }
        \App\Models\Unidad::create([
            'nombre' => 'Unidad',
            'sigla' => 'Unid',
            'habilitado' => true,
        ]);
        \App\Models\Unidad::create([
            'nombre' => 'Caja',
            'sigla' => 'CJA',
            'habilitado' => true,
        ]);
        $sucursales = [
            // 'SUC VINTO',
            // 'SUC QUILLACOLLO',
            // 'SUCURSAL 1',
            'SUCURSAL 2',
            // 'SUCURSAL 3',
        ];
        foreach ($sucursales as $value) {
            \App\Models\Sucursal::create([
                'nombre' => $value,
                'direccion' => 'Av.',
                'telefono' => '4'.rand(100000,999999),
                'habilitado' => true,
            ]);
        }

        $faker = Faker::create();
        $proveedores = [
            'DAS Audio',
            'Yamaha Audio Pro',
            'DB Technologies',
        ];
        for ($i=0; $i < count($proveedores); $i++) { 
            \App\Models\Proveedor::create([
                'razon_social' => $faker->company,
                'razon_social' => $faker->unique()->randomElement($proveedores),
                'nit' => rand(1,85) . '0000' . rand(100000,9900000000),
                'telefono' => '45' . rand(10000,99999),
                'contacto' => $faker->firstName .' '. $faker->lastName,
                'celular' => '7045' . rand(1000,9999),
                'habilitado' => true,
            ]);
        }
        
        // $imagen_url = 'images/imagen.jpg';
        // $producto = \App\Models\Producto::create([
        //     'codigo' => '001',
        //     'nombre' => 'Lapices Stabilo',
        //     'descripcion' => $faker->text,
        //     'img_url' => $imagen_url,
        //     'stock' => 0,
        //     'precio_unit' => 25,
        //     'habilitado' => true,
        //     'unidad_id' => 2,
        //     'categoria_id' => 1,
        // ]);
        // $producto = \App\Models\Producto::create([
        //     'codigo' => '002',
        //     'nombre' => 'Lapices Stabilo Color',
        //     'descripcion' => $faker->text,
        //     'img_url' => $imagen_url,
        //     'stock' => 0,
        //     'precio_unit' => 30,
        //     'habilitado' => true,
        //     'unidad_id' => 2,
        //     'categoria_id' => 1,
        // ]);
        // $producto = \App\Models\Producto::create([
        //     'codigo' => '003',
        //     'nombre' => 'Lapices Stabilo B2',
        //     'descripcion' => $faker->text,
        //     'img_url' => $imagen_url,
        //     'stock' => 0,
        //     'precio_unit' => 20,
        //     'habilitado' => true,
        //     'unidad_id' => 2,
        //     'categoria_id' => 1,
        // ]);
        
    }
}
