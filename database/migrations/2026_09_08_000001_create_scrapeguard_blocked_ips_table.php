<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('scrapeguard_blocked_ips', function (Blueprint $t) {
            $t->id();
            $t->string('ip', 45)->unique();
            $t->string('reason')->nullable();
            $t->boolean('active')->default(true)->index();
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('scrapeguard_blocked_ips'); }
};
