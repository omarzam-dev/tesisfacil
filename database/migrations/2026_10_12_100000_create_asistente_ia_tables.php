<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Usos de IA incluidos por mes en cada plan
        Schema::table('plans', function (Blueprint $table) {
            $table->unsignedInteger('max_ia')->default(20)->after('max_revisiones');
        });

        // Registro de cada uso de IA: sirve para el límite mensual y para ver el gasto
        Schema::create('usos_ia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('tipo', 30);
            $table->string('modelo', 60);
            $table->unsignedInteger('tokens_entrada')->default(0);
            $table->unsignedInteger('tokens_salida')->default(0);
            $table->decimal('costo_usd', 10, 6)->default(0);
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
        });

        // Matrices revisadas por cada tesista
        Schema::create('matrices_consistencia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('titulo')->nullable();
            $table->string('enfoque', 20);
            $table->text('problema_general');
            $table->text('problemas_especificos');
            $table->text('objetivo_general');
            $table->text('objetivos_especificos');
            $table->text('hipotesis_general')->nullable();
            $table->text('hipotesis_especificas')->nullable();
            $table->text('variables')->nullable();
            $table->unsignedTinyInteger('puntaje')->nullable();
            $table->string('veredicto', 30)->nullable();
            $table->json('resultado')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matrices_consistencia');
        Schema::dropIfExists('usos_ia');
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('max_ia');
        });
    }
};
