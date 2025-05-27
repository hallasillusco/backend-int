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
        Schema::dropIfExists('producto_sucursals');
        Schema::create('producto_sucursals', function (Blueprint $table) {
            $table->id();
            $table->integer('cantidad')->unsigned();
            $table->datetime('fecha_registro')->nullable();
            $table->boolean('disponible');
            
            $table->bigInteger('producto_id')->unsigned();
            $table->foreign('producto_id')->references('id')
            ->on('productos')->onDelete('cascade');
            $table->bigInteger('sucursal_id')->unsigned();
            $table->foreign('sucursal_id')->references('id')
            ->on('sucursals')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producto_sucursals');
    }
};
