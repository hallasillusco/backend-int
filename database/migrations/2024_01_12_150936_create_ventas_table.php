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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->integer('nro')->nullable();
            $table->string('estado')->nullable();
            $table->string('sigla')->nullable();
            $table->string('tipo_pago')->nullable();
            $table->string('razon_social')->nullable();
            $table->string('nit')->nullable();
            $table->datetime('fecha_registro')->nullable();
            $table->boolean('factura');
            $table->double('total',10,2)->nullable();
            $table->boolean('cancelado');
            
            $table->bigInteger('sucursal_id')->nullable();
            // $table->bigInteger('tipo_pago_id')->unsigned();
            // $table->foreign('tipo_pago_id')->references('id')
            // ->on('tipo_pagos')->onDelete('cascade');
            $table->bigInteger('cliente_id')->unsigned();
            $table->foreign('cliente_id')->references('id')
            ->on('clientes')->onDelete('cascade');
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')
            ->on('users')->onDelete('cascade');
            $table->bigInteger('proforma_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
