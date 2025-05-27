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
        Schema::dropIfExists('detalle_proformas');
        Schema::create('detalle_proformas', function (Blueprint $table) {
            $table->id();
            $table->integer('cantidad')->unsigned();
            $table->double('precio',10,2)->nullable();
            $table->double('descuento',10,2)->nullable();
            
            $table->bigInteger('lote_id')->unsigned();
            $table->foreign('lote_id')->references('id')
            ->on('lotes')->onDelete('cascade');
            $table->bigInteger('producto_id')->unsigned();
            $table->foreign('producto_id')->references('id')
            ->on('productos')->onDelete('cascade');
            $table->bigInteger('proforma_id')->unsigned();
            $table->foreign('proforma_id')->references('id')
            ->on('proformas')->onDelete('cascade');
            $table->bigInteger('color_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_proformas');
    }
};
