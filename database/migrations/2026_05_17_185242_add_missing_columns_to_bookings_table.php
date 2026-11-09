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
        Schema::table('bookings', function (Blueprint $table) {
            // First drop the foreign key so we can modify the column
            $table->dropForeign(['customer_id']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->string('booking_reference')->nullable()->unique()->after('id');
            $table->unsignedBigInteger('customer_id')->nullable()->change();
            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
            
            $table->string('booking_type')->nullable();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('item_name')->nullable();
            $table->decimal('base_price', 10, 2)->default(0);
            $table->decimal('surcharge', 10, 2)->default(0);
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->default('pending');
            $table->string('booking_status')->default('pending');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('passport_number')->nullable();
            $table->date('passport_expiry')->nullable();
            $table->date('dob')->nullable();
            $table->string('nationality')->nullable();
            $table->json('additional_data')->nullable();
            $table->dateTime('booking_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
        });
        
        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->nullable(false)->change();
            $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
            
            $table->dropColumn([
                'booking_reference', 'booking_type', 'item_id', 'item_name', 
                'base_price', 'surcharge', 'payment_method', 'payment_status', 
                'booking_status', 'first_name', 'last_name', 'email', 'phone', 
                'passport_number', 'passport_expiry', 'dob', 'nationality', 'additional_data'
            ]);
        });
    }
};
