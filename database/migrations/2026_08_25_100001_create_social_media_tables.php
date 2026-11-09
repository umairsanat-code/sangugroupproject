<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('page_id')->unique();
            $table->string('page_name');
            $table->text('access_token');
            $table->text('user_token')->nullable();
            $table->dateTime('user_token_expires_at')->nullable();
            $table->string('instagram_account_id')->nullable();
            $table->string('instagram_username')->nullable();
            $table->string('instagram_name')->nullable();
            $table->enum('status', ['discovered', 'connected', 'disconnected'])->default('discovered');
            $table->boolean('needs_reconnect')->default(false);
            $table->timestamp('connected_at')->nullable();
            $table->timestamps();
        });

        Schema::create('social_posts', function (Blueprint $table) {
            $table->id();
            $table->text('caption')->nullable();
            $table->string('link_url', 1000)->nullable();
            $table->json('media')->nullable();
            $table->json('targets')->nullable();

            $table->enum('facebook_status', ['pending', 'published', 'failed', 'skipped'])->default('pending');
            $table->string('facebook_post_id')->nullable();
            $table->string('facebook_url', 1000)->nullable();
            $table->text('facebook_error')->nullable();

            $table->enum('instagram_status', ['pending', 'published', 'failed', 'skipped'])->default('pending');
            $table->string('instagram_post_id')->nullable();
            $table->string('instagram_url', 1000)->nullable();
            $table->text('instagram_error')->nullable();

            $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('published_at')->nullable();
            $table->timestamps();

            $table->index(['scheduled_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_posts');
        Schema::dropIfExists('social_accounts');
    }
};
