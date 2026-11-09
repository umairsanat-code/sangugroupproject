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
            if (!Schema::hasColumn('bookings', 'destination_country')) {
                $table->string('destination_country')->nullable()->after('nationality');
            }
            if (!Schema::hasColumn('bookings', 'visa_type')) {
                $table->string('visa_type')->nullable()->after('destination_country');
            }
            if (!Schema::hasColumn('bookings', 'travel_date')) {
                $table->date('travel_date')->nullable()->after('visa_type');
            }
            if (!Schema::hasColumn('bookings', 'stripe_session_id')) {
                $table->string('stripe_session_id')->nullable()->index()->after('payment_method');
            }
            if (!Schema::hasColumn('bookings', 'stripe_payment_intent_id')) {
                $table->string('stripe_payment_intent_id')->nullable()->index()->after('stripe_session_id');
            }
            if (!Schema::hasColumn('bookings', 'currency')) {
                $table->string('currency', 10)->default('EUR')->after('total_amount');
            }
        });

        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                if (!Schema::hasColumn('payments', 'transaction_reference')) {
                    $table->string('transaction_reference')->nullable()->after('payment_method');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $columnsToDrop = [];
            foreach (['destination_country', 'visa_type', 'travel_date', 'stripe_session_id', 'stripe_payment_intent_id', 'currency'] as $col) {
                if (Schema::hasColumn('bookings', $col)) {
                    $columnsToDrop[] = $col;
                }
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });

        if (Schema::hasTable('payments') && Schema::hasColumn('payments', 'transaction_reference')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropColumn('transaction_reference');
            });
        }
    }
};
