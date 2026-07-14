<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tesis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('titulo');
            $table->string('linea_investigacion')->nullable();
            $table->enum('estado', ['en_progreso', 'en_revision', 'aprobada'])->default('en_progreso');
            $table->timestamps();
        });

        Schema::create('capitulos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tesis_id')->constrained('tesis')->cascadeOnDelete();
            $table->string('nombre');
            $table->unsignedInteger('orden')->default(0);
            $table->enum('estado', ['pendiente', 'en_progreso', 'en_revision', 'aprobado'])->default('pendiente');
            $table->timestamps();
        });

        Schema::create('capitulo_versiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('capitulo_id')->constrained('capitulos')->cascadeOnDelete();
            $table->unsignedInteger('numero_version');
            $table->string('nombre_archivo');
            $table->string('ruta_archivo');
            $table->text('comentario')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capitulo_versiones');
        Schema::dropIfExists('capitulos');
        Schema::dropIfExists('tesis');
    }
};
