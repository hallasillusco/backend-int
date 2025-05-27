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
        Schema::create('detalle_ingresos', function (Blueprint $table) {
            $table->id();
            $table->integer('cantidad')->unsigned();
            $table->double('precio_compra',10,2)->nullable();
            
            $table->bigInteger('ingreso_id')->unsigned();
            $table->foreign('ingreso_id')->references('id')
            ->on('ingresos')->onDelete('cascade');
            $table->bigInteger('lote_id')->unsigned();
            $table->foreign('lote_id')->references('id')
            ->on('lotes')->onDelete('cascade');
            $table->bigInteger('producto_id')->unsigned();
            $table->foreign('producto_id')->references('id')
            ->on('productos')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_ingresos');
    }
};
