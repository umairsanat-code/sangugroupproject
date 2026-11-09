<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_links', function (Blueprint $table) {
            $table->id();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('youtube')->nullable();
            $table->string('tiktok')->nullable();
            $table->string('telegram')->nullable();
            $table->string('twitter')->nullable();
            $table->timestamps();
        });

        \DB::table('social_links')->insert([
            'facebook' => 'https://www.facebook.com/profile.php?id=61593463514392',
            'instagram' => null,
            'whatsapp' => 'https://wa.me/923085093631',
            'youtube' => null,
            'tiktok' => null,
            'telegram' => null,
            'twitter' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('social_links');
    }
};