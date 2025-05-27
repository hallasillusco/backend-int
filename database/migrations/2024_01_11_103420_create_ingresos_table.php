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
        Schema::create('ingresos', function (Blueprint $table) {
            $table->id();
            $table->integer('nro')->nullable();
            $table->string('sigla')->nullable();
            // $table->string('codigo')->nullable();
            // $table->string('descripcion')->nullable();
            $table->date('fecha_ingreso')->nullable();
            $table->datetime('fecha_registro')->nullable();
            // $table->string('estado')->nullable();
            // $table->string('observacion')->nullable();
            $table->double('total',10,2)->nullable();
            $table->boolean('visible');
            
            $table->bigInteger('proveedor_id')->unsigned();
            $table->foreign('proveedor_id')->references('id')
            ->on('proveedors')->onDelete('cascade');
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')
            ->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingresos');
    }
};
