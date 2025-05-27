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
        // Almacen
        Schema::create('lotes', function (Blueprint $table) {
            $table->id();
            $table->string('lote')->nullable();
            $table->integer('cantidad')->nullable();
            $table->integer('cantidad_actual')->nullable();
            $table->double('precio_compra')->nullable();
            // $table->string('ubicacion')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->datetime('fecha_registro')->nullable();
            $table->boolean('disponible');
            
            $table->bigInteger('producto_id')->unsigned();
            $table->foreign('producto_id')->references('id')
            ->on('productos')->onDelete('cascade');
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
        Schema::dropIfExists('lotes');
    }
};
