<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('revisiones_apa', function (Blueprint $table) {
            $table->json('resultado_citas')->nullable()->after('resultado_estructura');
            $table->json('resultado_referencias')->nullable()->after('resultado_citas');
        });
    }

    public function down(): void
    {
        Schema::table('revisiones_apa', function (Blueprint $table) {
            $table->dropColumn(['resultado_citas', 'resultado_referencias']);
        });
    }
};
