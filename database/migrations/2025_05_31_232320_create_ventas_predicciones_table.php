<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('ventas_predicciones', function (Blueprint $table) {
        $table->id();
        $table->string('mes');
        $table->decimal('ventas_reales', 10, 2);
        $table->decimal('ventas_previstas', 10, 2);
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas_predicciones');
    }
};
