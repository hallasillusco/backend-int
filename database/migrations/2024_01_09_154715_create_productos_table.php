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
        Schema::dropIfExists('productos');
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->nullable();
            $table->string('nombre')->nullable();
            $table->string('slug')->nullable();
            $table->text('img_url')->nullable();
            $table->integer('stock')->nullable();
            $table->text('detalle')->nullable();
            $table->text('descripcion')->nullable();
            $table->double('precio_desc',10,2)->nullable();
            $table->double('precio_unit',10,2)->nullable();
            $table->double('descuento',10,2)->nullable();
            $table->boolean('habilitado');
            $table->boolean('destacado');
            
            $table->bigInteger('tipo_id')->unsigned();
            $table->foreign('tipo_id')->references('id')
            ->on('tipo_categorias')->onDelete('cascade');
            $table->bigInteger('categoria_id')->unsigned();
            $table->foreign('categoria_id')->references('id')
            ->on('categorias')->onDelete('cascade');
            $table->bigInteger('unidad_id')->unsigned();
            $table->foreign('unidad_id')->references('id')
            ->on('unidads')->onDelete('cascade');
            $table->bigInteger('marca_id')->unsigned();
            $table->foreign('marca_id')->references('id')
            ->on('marcas')->onDelete('cascade');
            $table->bigInteger('sub_categoria_id')->unsigned();
            $table->foreign('sub_categoria_id')->references('id')
            ->on('sub_categorias')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
