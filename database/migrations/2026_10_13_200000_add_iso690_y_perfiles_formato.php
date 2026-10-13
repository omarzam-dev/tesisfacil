<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Formato de página de cada universidad (ISO 690 no lo define)
        // Las comprobaciones permiten volver a correr la migración si se cortó a la mitad
        if (! Schema::hasTable('perfiles_formato')) {
        Schema::create('perfiles_formato', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 120);
            $table->decimal('margen_superior', 4, 2)->default(2.5);
            $table->decimal('margen_inferior', 4, 2)->default(2.5);
            $table->decimal('margen_izquierdo', 4, 2)->default(3.0);
            $table->decimal('margen_derecho', 4, 2)->default(2.5);
            $table->decimal('interlineado', 3, 2)->default(1.5);
            // Fuentes válidas separadas por coma: "Arial, Times New Roman"
            $table->string('fuentes', 255)->default('Arial, Times New Roman');
            $table->decimal('tamano_fuente', 4, 1)->default(12);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
        }

        // La norma deja de ser solo APA6/APA7: ahora también ISO690A / ISO690N
        Schema::table('revisiones_apa', function (Blueprint $table) {
            $table->string('edicion_apa', 20)->change();
        });

        if (! Schema::hasColumn('revisiones_apa', 'perfil_formato_id')) {
        Schema::table('revisiones_apa', function (Blueprint $table) {
            $table->foreignId('perfil_formato_id')->nullable()->after('edicion_apa')
                ->constrained('perfiles_formato')->nullOnDelete();
            // Nombre del perfil al momento de revisar (por si luego se renombra)
            $table->string('perfil_nombre', 120)->nullable()->after('perfil_formato_id');
        });
        }
    }

    public function down(): void
    {
        Schema::table('revisiones_apa', function (Blueprint $table) {
            $table->dropConstrainedForeignId('perfil_formato_id');
            $table->dropColumn('perfil_nombre');
        });

        Schema::dropIfExists('perfiles_formato');
    }
};
