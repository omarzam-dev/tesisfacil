<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('user');
            $table->timestamp('trial_ends_at')->nullable();
            $table->enum('subscription_status', ['trial','active','expired','pending'])->default('trial');
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role','trial_ends_at','subscription_status']);
        });
    }
};
