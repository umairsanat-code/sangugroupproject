<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')
                ->nullable()
                ->constrained('chat_conversations')
                ->nullOnDelete();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('service_interest')->nullable();
            $table->text('message')->nullable();
            $table->string('status', 20)->default('new');
            $table->boolean('consent')->default(false);
            $table->string('source', 30)->default('chatbot');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_leads');
    }
};
