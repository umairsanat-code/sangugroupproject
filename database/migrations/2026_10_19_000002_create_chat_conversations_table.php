<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 64)->unique();
            $table->string('session_id', 100)->nullable();
            $table->string('visitor_ip', 64)->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->string('page_url', 512)->nullable();
            $table->string('status', 20)->default('open');
            $table->unsignedBigInteger('lead_id')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('last_activity_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_conversations');
    }
};
