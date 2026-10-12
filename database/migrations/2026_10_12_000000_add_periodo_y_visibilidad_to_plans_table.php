<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            // Cuántos días da el plan al aprobar el pago (30 = mensual, 180 = semestral, 365 = anual)
            $table->unsignedSmallInteger('duration_days')->default(30)->after('price');
            // Orden en que se muestran los planes (menor primero)
            $table->unsignedSmallInteger('orden')->default(0)->after('max_revisiones');
            // Muestra la etiqueta "Recomendado" (solo uno a la vez)
            $table->boolean('destacado')->default(false)->after('orden');
            // Si está apagado, el plan no aparece en la web ni se puede comprar
            $table->boolean('activo')->default(true)->after('destacado');
        });

        // Los planes que ya existen quedan ordenados como antes (por precio)
        // y el más barato sigue siendo el "Recomendado", igual que hasta ahora.
        $planes = DB::table('plans')->orderBy('price')->get();
        foreach ($planes as $i => $plan) {
            DB::table('plans')->where('id', $plan->id)->update([
                'orden'     => ($i + 1) * 10,
                'destacado' => $i === 0,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['duration_days', 'orden', 'destacado', 'activo']);
        });
    }
};
