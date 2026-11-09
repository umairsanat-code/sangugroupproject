<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_settings', function (Blueprint $table) {
            $table->id();
            $table->string('method')->default('revolut');
            $table->string('account_holder')->nullable();
            $table->string('account_number')->nullable();
            $table->string('sort_code')->nullable();
            $table->string('iban')->nullable();
            $table->string('bic_swift')->nullable();
            $table->string('currency')->default('EUR');
            $table->string('payment_link')->nullable();
            $table->text('instructions')->nullable();
            $table->timestamps();
        });

        \DB::table('payment_settings')->insert([
            'method' => 'revolut',
            'account_holder' => 'SanguGroup Travel',
            'account_number' => null,
            'sort_code' => null,
            'iban' => null,
            'bic_swift' => null,
            'currency' => 'EUR',
            'payment_link' => null,
            'instructions' => 'Please transfer the exact amount using the booking reference so we can match your payment instantly.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_settings');
    }
};