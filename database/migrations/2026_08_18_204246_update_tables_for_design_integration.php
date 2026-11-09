<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('umrah_packages', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable();
            $table->string('nights')->nullable();
            $table->string('city')->nullable();
            $table->string('hotel')->nullable();
            $table->float('rating')->nullable();
            $table->boolean('group')->default(false);
            $table->json('includes')->nullable();
            $table->text('desc')->nullable();
        });

        Schema::table('hotels', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable();
            $table->string('distance')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->float('rating')->nullable();
            $table->text('desc')->nullable();
            $table->json('amenities')->nullable();
        });

        Schema::create('visas', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('country');
            $table->string('type');
            $table->string('flag')->nullable();
            $table->string('duration')->nullable();
            $table->string('entry')->nullable();
            $table->string('validity')->nullable();
            $table->string('processing')->nullable();
            $table->decimal('price', 10, 2);
            $table->text('desc')->nullable();
            $table->json('docs')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visas');
        
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropColumn(['slug', 'distance', 'price', 'rating', 'desc', 'amenities']);
        });

        Schema::table('umrah_packages', function (Blueprint $table) {
            $table->dropColumn(['slug', 'nights', 'city', 'hotel', 'rating', 'group', 'includes', 'desc']);
        });
    }
};
