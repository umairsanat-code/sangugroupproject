<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add discount fields to umrah_packages
        Schema::table('umrah_packages', function (Blueprint $table) {
            $table->decimal('discount_amount', 10, 2)->nullable()->after('price');
            $table->decimal('discount_percent', 5, 2)->nullable()->after('discount_amount');
            $table->string('discount_label')->nullable()->after('discount_percent');
        });

        // Add discount fields to visas
        Schema::table('visas', function (Blueprint $table) {
            $table->decimal('discount_amount', 10, 2)->nullable()->after('price');
            $table->decimal('discount_percent', 5, 2)->nullable()->after('discount_amount');
            $table->string('discount_label')->nullable()->after('discount_percent');
        });

        // Add discount fields to umrah_groups
        Schema::table('umrah_groups', function (Blueprint $table) {
            $table->decimal('discount_amount', 10, 2)->nullable()->after('price');
            $table->decimal('discount_percent', 5, 2)->nullable()->after('discount_amount');
            $table->string('discount_label')->nullable()->after('discount_percent');
        });

        // Add discount fields to hotels
        Schema::table('hotels', function (Blueprint $table) {
            $table->decimal('discount_amount', 10, 2)->nullable()->after('price');
            $table->decimal('discount_percent', 5, 2)->nullable()->after('discount_amount');
            $table->string('discount_label')->nullable()->after('discount_percent');
        });

        // Add discount fields to bookings
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('discount_amount', 10, 2)->nullable()->after('surcharge');
            $table->decimal('discount_percent', 5, 2)->nullable()->after('discount_amount');
        });
    }

    public function down(): void
    {
        Schema::table('umrah_packages', function (Blueprint $table) {
            $table->dropColumn(['discount_amount', 'discount_percent', 'discount_label']);
        });

        Schema::table('visas', function (Blueprint $table) {
            $table->dropColumn(['discount_amount', 'discount_percent', 'discount_label']);
        });

        Schema::table('umrah_groups', function (Blueprint $table) {
            $table->dropColumn(['discount_amount', 'discount_percent', 'discount_label']);
        });

        Schema::table('hotels', function (Blueprint $table) {
            $table->dropColumn(['discount_amount', 'discount_percent', 'discount_label']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['discount_amount', 'discount_percent']);
        });
    }
};
