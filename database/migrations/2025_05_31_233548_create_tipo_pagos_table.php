<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tipo_pagos')) {
            Schema::create('tipo_pagos', function (Blueprint $table) {
                $table->id();
                $table->string('nombre', 190);
                $table->boolean('habilitado');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
            return;
        }

        Schema::table('tipo_pagos', function (Blueprint $table) {
            if (!Schema::hasColumn('tipo_pagos', 'nombre')) {
                $table->string('nombre', 190)->after('id');
            }
            if (!Schema::hasColumn('tipo_pagos', 'habilitado')) {
                $table->boolean('habilitado')->default(1)->after('nombre');
            }
            if (!Schema::hasColumn('tipo_pagos', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('tipo_pagos', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipo_pagos');
    }
};