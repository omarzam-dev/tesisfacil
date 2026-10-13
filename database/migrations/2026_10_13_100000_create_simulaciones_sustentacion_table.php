<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Algunos llamados a la IA se registran por su costo, pero no descuentan del límite mensual
        Schema::table('usos_ia', function (Blueprint $table) {
            $table->boolean('consume_uso')->default(true)->after('costo_usd');
        });

        Schema::create('simulaciones_sustentacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('titulo');
            $table->string('carrera')->nullable();
            $table->string('tono', 20);
            $table->text('planteamiento');
            $table->text('metodologia');
            $table->text('resultados');
            $table->text('conclusiones')->nullable();
            $table->json('preguntas');
            $table->json('respuestas')->nullable();
            $table->string('estado', 20)->default('en_curso');
            $table->decimal('nota_final', 4, 1)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('simulaciones_sustentacion');
        Schema::table('usos_ia', function (Blueprint $table) {
            $table->dropColumn('consume_uso');
        });
    }
};
