<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('tag')->nullable();
            $table->string('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->string('image_url')->nullable();
            $table->string('author')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
