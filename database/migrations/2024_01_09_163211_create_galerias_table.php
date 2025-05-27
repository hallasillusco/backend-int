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
        Schema::create('galerias', function (Blueprint $table) {
            $table->id();
            // $table->text('detalle')->nullable();
            $table->text('img_url')->nullable();
            $table->boolean('portada');
            $table->boolean('habilitado');

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
        Schema::dropIfExists('galerias');
    }
};
