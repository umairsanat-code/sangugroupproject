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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->json('permissions')->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->constrained('roles')->nullOnDelete();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('passport_number')->nullable();
            $table->date('passport_expiry')->nullable();
            $table->timestamps();
        });

        Schema::create('travel_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->string('destination');
            $table->date('travel_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['Airline', 'Hotel', 'Transport']);
            $table->text('contact_info')->nullable();
            $table->timestamps();
        });

        Schema::create('airlines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('logo_path')->nullable();
            $table->timestamps();
        });

        Schema::create('airports', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('city');
            $table->string('country');
            $table->timestamps();
        });

        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('airline_id')->constrained('airlines')->cascadeOnDelete();
            $table->string('flight_number');
            $table->string('departure_city')->nullable(); // For simple queries
            $table->string('arrival_city')->nullable(); // For simple queries
            $table->foreignId('departure_airport_id')->nullable()->constrained('airports');
            $table->foreignId('arrival_airport_id')->nullable()->constrained('airports');
            $table->dateTime('departure_time');
            $table->dateTime('arrival_time');
            $table->timestamps();
        });

        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flight_id')->constrained('flights')->cascadeOnDelete();
            $table->string('pnr')->nullable();
            $table->enum('status', ['Available', 'Booked', 'Cancelled', 'Refunded'])->default('Available');
            $table->decimal('price', 10, 2);
            $table->string('ticket_class')->default('Economy');
            $table->timestamps();
        });

        Schema::create('umrah_packages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('duration_days');
            $table->decimal('price', 10, 2);
            $table->text('hotel_details')->nullable();
            $table->text('transport_details')->nullable();
            $table->boolean('visa_included')->default(false);
            $table->string('image_path')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->nullOnDelete();
            $table->string('name');
            $table->string('city');
            $table->string('location')->nullable();
            $table->integer('stars')->default(3);
            $table->timestamps();
        });

        Schema::create('hotel_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->string('room_type');
            $table->decimal('price_per_night', 10, 2);
            $table->timestamps();
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->enum('status', ['Pending', 'Confirmed', 'Cancelled'])->default('Pending');
            $table->dateTime('booking_date');
            $table->timestamps();
        });

        Schema::create('booking_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->enum('item_type', ['Ticket', 'Umrah', 'Room']);
            $table->unsignedBigInteger('item_id');
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->enum('type', ['Customer Payment', 'Vendor Payout']);
            $table->decimal('amount', 10, 2);
            $table->string('payment_method')->nullable();
            $table->string('status')->default('Completed');
            $table->dateTime('payment_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('booking_items');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('hotel_rooms');
        Schema::dropIfExists('hotels');
        Schema::dropIfExists('umrah_packages');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('flights');
        Schema::dropIfExists('airports');
        Schema::dropIfExists('airlines');
        Schema::dropIfExists('vendors');
        Schema::dropIfExists('travel_histories');
        Schema::dropIfExists('customers');
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });
        Schema::dropIfExists('roles');
    }
};
