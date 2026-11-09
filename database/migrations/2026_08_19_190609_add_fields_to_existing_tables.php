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
        Schema::table('visas', function (Blueprint $table) {
            $table->string('title')->nullable();
            $table->string('image_url')->nullable();
            $table->json('requirements')->nullable();
            $table->json('documents_required')->nullable();
            $table->json('checkout_settings')->nullable();
            $table->text('important_info')->nullable();
        });

        Schema::table('umrah_packages', function (Blueprint $table) {
            $table->string('duration_type')->nullable(); // 14 Days, 21 Days, 28 Days, Custom
            $table->string('image_url')->nullable();
            $table->string('makkah_hotel')->nullable();
            $table->string('madinah_hotel')->nullable();
            $table->string('transport')->nullable();
        });

        Schema::table('hotels', function (Blueprint $table) {
            $table->string('image_url')->nullable();
            $table->json('gallery')->nullable();
            $table->json('room_types')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visas', function (Blueprint $table) {
            $table->dropColumn(['title', 'image_url', 'requirements', 'documents_required', 'checkout_settings', 'important_info']);
        });

        Schema::table('umrah_packages', function (Blueprint $table) {
            $table->dropColumn(['duration_type', 'image_url', 'makkah_hotel', 'madinah_hotel', 'transport']);
        });

        Schema::table('hotels', function (Blueprint $table) {
            $table->dropColumn(['image_url', 'gallery', 'room_types']);
        });
    }
};
