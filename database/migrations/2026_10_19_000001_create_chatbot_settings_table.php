<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('enabled')->default(true);
            // Stored encrypted at rest (see ChatbotSetting::$casts). Never serialized to the frontend.
            $table->text('api_key')->nullable();
            $table->string('model')->default('gpt-4o-mini');
            $table->text('system_prompt')->nullable();
            $table->text('welcome_message')->nullable();
            $table->json('suggested_questions')->nullable();
            $table->json('services')->nullable();
            $table->json('faqs')->nullable();
            $table->decimal('temperature', 3, 2)->default(0.40);
            $table->integer('max_tokens')->default(500);
            $table->boolean('lead_capture_enabled')->default(true);
            $table->timestamps();
        });

        DB::table('chatbot_settings')->insert([
            'enabled' => true,
            'api_key' => null,
            'model' => 'gpt-4o-mini',
            'system_prompt' => 'You are the friendly AI travel assistant for SanguGroup (also known as Sangu Travel & Tours), a trusted travel agency. '
                . 'You help visitors with flight tickets, visa services, Umrah packages, hotel bookings and travel eSIM plans. '
                . 'Be warm, concise and helpful. Answer in the same language the visitor uses. '
                . 'Never invent prices, policies or availability — if you are unsure, invite the visitor to share their details so a human agent can follow up. '
                . 'Only discuss topics related to SanguGroup and travel. If asked about unrelated topics, politely redirect to travel services.',
            'welcome_message' => 'Assalamu Alaikum! Welcome to SanguGroup 👋 I am your AI travel assistant. How can I help you today — flights, visas, Umrah packages or hotels?',
            'suggested_questions' => json_encode([
                'What visa services do you offer?',
                'Tell me about your Umrah packages',
                'How do I book cheap flight tickets?',
                'Do you book hotels near the Haramain?',
            ]),
            'services' => json_encode([
                ['name' => 'Flight Tickets', 'description' => 'Domestic and international air tickets with competitive fares.'],
                ['name' => 'Visa Services', 'description' => 'Tourist, visit and business visa processing with full document guidance.'],
                ['name' => 'Umrah Packages', 'description' => 'Economy to luxury Umrah packages including hotels near the Haramain.'],
                ['name' => 'Hotel Bookings', 'description' => 'Worldwide hotel reservations, including hotels close to the Haram.'],
                ['name' => 'Travel eSIM', 'description' => 'Affordable data plans so you stay connected while travelling abroad.'],
                ['name' => 'Umrah Groups', 'description' => 'Group Umrah departures with guided support throughout the journey.'],
            ]),
            'faqs' => json_encode([
                ['question' => 'Do you help with visa applications?', 'answer' => 'Yes. We assist with tourist, visit and business visas, and guide you through the required documents. Visa approval is decided by the embassy.'],
                ['question' => 'Are your Umrah packages customisable?', 'answer' => 'Yes, packages can be tailored to your budget, travel dates and hotel preferences.'],
                ['question' => 'How can I contact a human agent?', 'answer' => 'You can share your name and phone number here and our team will contact you, or email us at sangutravel@gmail.com.'],
                ['question' => 'What are your opening hours?', 'answer' => 'Our team is available Monday to Saturday, 9am to 9pm PKT.'],
            ]),
            'temperature' => 0.40,
            'max_tokens' => 500,
            'lead_capture_enabled' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_settings');
    }
};
