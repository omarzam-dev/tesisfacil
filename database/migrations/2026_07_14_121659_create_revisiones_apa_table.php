<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('revisiones_apa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nombre_archivo');
            $table->string('ruta_archivo');
            $table->enum('edicion_apa', ['APA6', 'APA7']);
            $table->enum('estado', ['procesando', 'completado', 'error'])->default('procesando');
            $table->json('resultado_estructura')->nullable();
            $table->text('error_mensaje')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revisiones_apa');
    }
};
