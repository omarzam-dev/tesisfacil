<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('label');       // ej: "Yape - Omar Zamudio"
            $table->string('type');        // ej: "Yape", "BCP", "Interbank"
            $table->string('account_info'); // ej: "981 763 601" o "000-000000000-0-00"
            $table->boolean('active')->default(true);
            $table->unsignedInteger('orden')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('payment_methods');
    }
};
