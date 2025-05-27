<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('razon_social')->nullable();
            $table->string('nombre_completo')->nullable();
            $table->string('nit')->nullable();
            $table->string('celular')->nullable();
            $table->string('direccion')->nullable();
            $table->string('email')->nullable();
            $table->boolean('habilitado');

            $table->bigInteger('tipo_cliente_id')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
