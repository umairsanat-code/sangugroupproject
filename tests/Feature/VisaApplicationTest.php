<?php

namespace Tests\Feature;

use App\Mail\AdminBookingNotification;
use App\Mail\BookingConfirmation;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\PaymentSettings;
use App\Models\Visa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class VisaApplicationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();

        // Seed basic payment settings
        PaymentSettings::create([
            'id' => 1,
            'method' => 'revolut',
            'currency' => 'EUR',
            'account_holder' => 'SanguGroup S.L.',
            'iban' => 'ES9121000418450200051332',
            'bic_swift' => 'REVOBBMM',
        ]);
    }

    public function test_visa_application_validation_requires_essential_fields()
    {
        $response = $this->postJson('/api/visa/apply', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'full_name',
            'email',
            'phone',
            'nationality',
            'destination_country',
            'visa_type',
        ]);
    }

    public function test_visa_application_pay_later_saves_booking_and_generates_reference()
    {
        // Create sample visa in DB
        $visa = Visa::create([
            'country'    => 'Saudi Arabia',
            'type'       => 'Visit Visa',
            'price'      => 150.00,
            'slug'       => 'saudi-visit-visa',
        ]);

        $payload = [
            'full_name'           => 'Zainab Fatima',
            'email'               => 'zainab@example.com',
            'phone'               => '+34 672 06 81 48',
            'nationality'         => 'Spain',
            'destination_country' => 'Saudi Arabia',
            'visa_type'           => 'Visit Visa',
            'travel_date'         => '2026-12-15',
            'travellers'          => 2,
            'visa_id'             => $visa->id,
            'pay_action'          => 'pay_later',
        ];

        $response = $this->postJson('/api/visa/apply', $payload);

        $response->assertStatus(201);
        $response->assertJson([
            'success'    => true,
            'pay_action' => 'pay_later',
        ]);

        $data = $response->json();
        $this->assertStringStartsWith('SGV-', $data['booking_reference']);

        $this->assertDatabaseHas('bookings', [
            'booking_reference'   => $data['booking_reference'],
            'first_name'          => 'Zainab Fatima',
            'email'               => 'zainab@example.com',
            'destination_country' => 'Saudi Arabia',
            'visa_type'           => 'Visit Visa',
            'payment_method'      => 'pay_later',
            'payment_status'      => 'Pending',
            'total_amount'        => 300.00,
            'currency'            => 'EUR',
        ]);

        Mail::assertSent(BookingConfirmation::class);
        Mail::assertSent(AdminBookingNotification::class);
    }

    public function test_pay_now_creates_stripe_checkout_session_or_fallbacks_gracefully()
    {
        $payload = [
            'full_name'           => 'Carlos Martinez',
            'email'               => 'carlos@example.com',
            'phone'               => '+34 612 34 56 78',
            'nationality'         => 'Spain',
            'destination_country' => 'United Arab Emirates',
            'visa_type'           => 'Tourist Visa',
            'travellers'          => 1,
            'pay_action'          => 'pay_now',
        ];

        $response = $this->postJson('/api/visa/apply', $payload);

        $response->assertStatus(201);
        $data = $response->json();

        $this->assertTrue($data['success']);
        $this->assertNotEmpty($data['booking_reference']);

        // When Stripe keys are valid test keys, either checkout_url is generated or graceful fallback occurs
        if (isset($data['checkout_url'])) {
            $this->assertStringContainsString('stripe.com', $data['checkout_url']);
            $this->assertNotEmpty($data['session_id']);
        } else {
            $this->assertEquals('pay_later', $data['pay_action']);
            $this->assertNotEmpty($data['stripe_error']);
        }
    }

    public function test_create_checkout_session_for_existing_booking()
    {
        $booking = Booking::create([
            'booking_reference'   => 'SGV-TEST01',
            'booking_type'        => 'visa',
            'first_name'          => 'Ahmad Ali',
            'email'               => 'ahmad@example.com',
            'phone'               => '+34 600 000 000',
            'destination_country' => 'Turkey',
            'visa_type'           => 'e-Visa',
            'total_amount'        => 85.00,
            'currency'            => 'EUR',
            'payment_method'      => 'stripe',
            'payment_status'      => 'Pending',
            'booking_status'      => 'Pending Payment',
            'booking_date'        => now(),
        ]);

        $response = $this->postJson('/api/visa/checkout-session', [
            'booking_reference' => $booking->booking_reference,
        ]);

        // If network allows calling Stripe test mode
        if ($response->status() === 200) {
            $response->assertJsonStructure(['success', 'checkout_url', 'session_id']);
            $this->assertDatabaseHas('bookings', [
                'booking_reference' => 'SGV-TEST01',
            ]);
        } else {
            $this->assertContains($response->status(), [400, 500]);
        }
    }

    public function test_create_checkout_session_prevents_duplicate_payments_if_already_paid()
    {
        $booking = Booking::create([
            'booking_reference'   => 'SGV-PAID01',
            'booking_type'        => 'visa',
            'first_name'          => 'Omar Hassan',
            'email'               => 'omar@example.com',
            'total_amount'        => 120.00,
            'payment_status'      => 'Paid',
            'booking_status'      => 'Processing',
            'booking_date'        => now(),
        ]);

        $response = $this->postJson('/api/visa/checkout-session', [
            'booking_reference' => $booking->booking_reference,
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'success'      => false,
            'already_paid' => true,
        ]);
    }

    public function test_stripe_webhook_marks_booking_as_paid()
    {
        $booking = Booking::create([
            'booking_reference'   => 'SGV-WEBHOOK1',
            'booking_type'        => 'visa',
            'first_name'          => 'Fatima Noor',
            'email'               => 'fatima@example.com',
            'phone'               => '+34 672 06 81 48',
            'destination_country' => 'Saudi Arabia',
            'visa_type'           => 'Visit Visa',
            'total_amount'        => 200.00,
            'currency'            => 'EUR',
            'payment_status'      => 'Pending',
            'booking_status'      => 'Pending Payment',
            'booking_date'        => now(),
        ]);

        $webhookPayload = [
            'id'   => 'evt_test_123',
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id'             => 'cs_test_session_999',
                    'payment_intent' => 'pi_test_intent_999',
                    'payment_status' => 'paid',
                    'amount_total'   => 20000,
                    'metadata'       => [
                        'booking_reference' => 'SGV-WEBHOOK1',
                        'booking_type'      => 'visa',
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/api/stripe/webhook', $webhookPayload);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        $booking->refresh();
        $this->assertEquals('Paid', $booking->payment_status);
        $this->assertEquals('Processing', $booking->booking_status);
        $this->assertEquals('pi_test_intent_999', $booking->stripe_payment_intent_id);

        $this->assertDatabaseHas('payments', [
            'booking_id'            => $booking->id,
            'payment_method'        => 'stripe',
            'status'                => 'Completed',
            'amount'                => 200.00,
            'transaction_reference' => 'pi_test_intent_999',
        ]);
    }

    public function test_stripe_webhook_handles_failed_and_refunded_payments()
    {
        $booking = Booking::create([
            'booking_reference'        => 'SGV-FAIL01',
            'booking_type'             => 'visa',
            'first_name'               => 'Bilal Khan',
            'email'                    => 'bilal@example.com',
            'total_amount'             => 100.00,
            'stripe_payment_intent_id' => 'pi_test_fail_123',
            'payment_status'           => 'Pending',
            'booking_status'           => 'Pending Payment',
            'booking_date'             => now(),
        ]);

        // 1. Payment failed event
        $failedPayload = [
            'type' => 'payment_intent.payment_failed',
            'data' => [
                'object' => [
                    'id' => 'pi_test_fail_123',
                ],
            ],
        ];

        $response = $this->postJson('/api/stripe/webhook', $failedPayload);
        $response->assertStatus(200);

        $booking->refresh();
        $this->assertEquals('Failed', $booking->payment_status);

        // 2. Refunded event
        $refundPayload = [
            'type' => 'charge.refunded',
            'data' => [
                'object' => [
                    'payment_intent' => 'pi_test_fail_123',
                ],
            ],
        ];

        $response = $this->postJson('/api/stripe/webhook', $refundPayload);
        $response->assertStatus(200);

        $booking->refresh();
        $this->assertEquals('Refunded', $booking->payment_status);
        $this->assertEquals('Cancelled', $booking->booking_status);
    }

    public function test_admin_can_update_booking_status_and_payment_status()
    {
        $booking = Booking::create([
            'booking_reference'   => 'SGV-STATUS01',
            'booking_type'        => 'visa',
            'first_name'          => 'Sara Ali',
            'email'               => 'sara@example.com',
            'total_amount'        => 150.00,
            'payment_status'      => 'Pending',
            'booking_status'      => 'Pending Review',
            'booking_date'        => now(),
        ]);

        $response = $this->putJson('/api/bookings/' . $booking->booking_reference . '/status', [
            'booking_status' => 'Approved',
            'payment_status' => 'Paid',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $booking->refresh();
        $this->assertEquals('Approved', $booking->booking_status);
        $this->assertEquals('Paid', $booking->payment_status);
    }

    public function test_admin_can_export_bookings_csv()
    {
        Booking::create([
            'booking_reference'   => 'SGV-CSV01',
            'booking_type'        => 'visa',
            'first_name'          => 'Tariq Javed',
            'email'               => 'tariq@example.com',
            'phone'               => '+34 600 11 22 33',
            'nationality'         => 'Pakistan',
            'destination_country' => 'United Kingdom',
            'visa_type'           => 'Standard Visitor',
            'total_amount'        => 180.00,
            'currency'            => 'EUR',
            'payment_method'      => 'stripe',
            'payment_status'      => 'Paid',
            'booking_status'      => 'Processing',
            'booking_date'        => now(),
        ]);

        $response = $this->get('/api/bookings/export-csv?type=visa');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();
        $this->assertStringContainsString('SGV-CSV01', $content);
        $this->assertStringContainsString('Tariq Javed', $content);
        $this->assertStringContainsString('United Kingdom', $content);
        $this->assertStringContainsString('180.00', $content);
    }
}
