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
        Schema::create('umrah_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('departure_date')->nullable();
            $table->date('return_date')->nullable();
            $table->string('image_url')->nullable();
            $table->integer('seats')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('hotel')->nullable();
            $table->string('transport')->nullable();
            $table->text('group_details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('umrah_groups');
    }
};
