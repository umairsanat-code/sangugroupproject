<?php

namespace App\Http\Controllers;

use App\Mail\AdminBookingNotification;
use App\Mail\BookingConfirmation;
use App\Mail\NewBookingMail;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\PaymentSettings;
use App\Models\Visa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Stripe;
use Stripe\StripeClient;
use Stripe\Webhook as StripeWebhook;

class VisaPaymentController extends Controller
{
    /**
     * Get configured Stripe client.
     */
    protected function getStripeClient(): ?StripeClient
    {
        $secret = config('services.stripe.secret');
        if (!$secret) {
            return null;
        }
        return new StripeClient($secret);
    }

    /**
     * Handle multi-step visa application submission (Step 1 + Step 2).
     */
    public function submitApplication(Request $request)
    {
        $validated = $request->validate([
            'full_name'           => 'required|string|max:255',
            'email'               => 'required|email|max:255',
            'phone'               => 'required|string|max:50',
            'nationality'         => 'required|string|max:100',
            'destination_country' => 'required|string|max:100',
            'visa_type'           => 'required|string|max:100',
            'travel_date'         => 'nullable|date',
            'travellers'          => 'nullable|integer|min:1|max:20',
            'visa_id'             => 'nullable|integer',
            'visa_slug'           => 'nullable|string',
            'pay_action'          => 'nullable|in:pay_now,pay_later',
            'notes'               => 'nullable|string|max:1000',
            'applicant_photo'     => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'passport_copy'       => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        // Store uploaded documents (visa-documents/ in public disk)
        $applicantPhotoPath = null;
        $passportCopyPath   = null;

        if ($request->hasFile('applicant_photo') && $request->file('applicant_photo')->isValid()) {
            $applicantPhotoPath = $request->file('applicant_photo')->store('visa-documents', 'public');
        }
        if ($request->hasFile('passport_copy') && $request->file('passport_copy')->isValid()) {
            $passportCopyPath = $request->file('passport_copy')->store('visa-documents', 'public');
        }

        $travellers = (int) ($validated['travellers'] ?? 1);
        $payAction = $validated['pay_action'] ?? 'pay_later';

        // Resolve visa unit fee
        $unitPrice = 0.0;
        $visa = null;

        if (!empty($validated['visa_id'])) {
            $visa = Visa::find($validated['visa_id']);
        } elseif (!empty($validated['visa_slug'])) {
            $visa = Visa::where('slug', $validated['visa_slug'])->first();
        }

        if (!$visa) {
            $visa = Visa::where('country', $validated['destination_country'])
                ->where('type', $validated['visa_type'])
                ->first();
        }

        if ($visa) {
            $unitPrice = (float) ($visa->discounted_price ?? $visa->price ?? 0);
        } else {
            // Default baseline service fee if dynamic custom visa
            $unitPrice = 120.00;
        }

        $totalAmount = round($unitPrice * $travellers, 2);

        // Generate unique reference SGV-XXXXXX
        do {
            $reference = 'SGV-' . strtoupper(Str::random(6));
        } while (Booking::where('booking_reference', $reference)->exists());

        $booking = new Booking();
        $booking->booking_reference = $reference;
        $booking->booking_type = 'visa';
        $booking->first_name = $validated['full_name'];
        $booking->email = $validated['email'];
        $booking->phone = $validated['phone'];
        $booking->nationality = $validated['nationality'];
        $booking->destination_country = $validated['destination_country'];
        $booking->visa_type = $validated['visa_type'];
        $booking->travel_date = $validated['travel_date'] ?? null;
        $booking->item_id = $visa ? $visa->id : null;
        $booking->item_name = $validated['destination_country'] . ' ' . $validated['visa_type'] . ' Visa';
        $booking->base_price = $unitPrice;
        $booking->total_amount = $totalAmount;
        $booking->currency = 'EUR';
        $booking->booking_date = now();
        $booking->additional_data = [
            'travellers'       => $travellers,
            'unit_price'       => $unitPrice,
            'notes'            => $validated['notes'] ?? null,
            'source'           => '2-step visa application',
            'applicant_photo'  => $applicantPhotoPath,
            'passport_copy'    => $passportCopyPath,
        ];

        if ($payAction === 'pay_now') {
            $booking->payment_method = 'stripe';
            $booking->payment_status = 'Pending';
            $booking->booking_status = 'Pending Payment';
        } else {
            $booking->payment_method = 'pay_later';
            $booking->payment_status = 'Pending';
            $booking->booking_status = 'Pending Payment';
        }

        $booking->save();

        // Send initial email notifications (non-blocking)
        $this->sendBookingEmails($booking);

        // If paying now with Stripe, create Stripe Checkout session
        if ($payAction === 'pay_now') {
            try {
                $sessionData = $this->generateStripeSession($booking);
                return response()->json([
                    'success'           => true,
                    'booking_reference' => $booking->booking_reference,
                    'booking'           => $booking,
                    'checkout_url'      => $sessionData['url'],
                    'session_id'        => $sessionData['id'],
                    'pay_action'        => 'pay_now',
                ], 201);
            } catch (\Exception $e) {
                Log::error('Stripe Checkout Session Creation Error: ' . $e->getMessage(), [
                    'booking_reference' => $booking->booking_reference,
                ]);

                // Fallback gracefully so the application is saved even if Stripe API fails
                return response()->json([
                    'success'           => true,
                    'booking_reference' => $booking->booking_reference,
                    'booking'           => $booking,
                    'pay_action'        => 'pay_later',
                    'stripe_error'      => 'Payment gateway currently unavailable. Your application is saved with reference ' . $booking->booking_reference . '. Our team will contact you.',
                ], 201);
            }
        }

        // Pay later response with bank / Revolut details
        $paymentSettings = PaymentSettings::first();

        return response()->json([
            'success'           => true,
            'booking_reference' => $booking->booking_reference,
            'booking'           => $booking,
            'pay_action'        => 'pay_later',
            'payment_settings'  => $paymentSettings,
        ], 201);
    }

    /**
     * Create a Stripe Checkout Session for a booking reference.
     */
    public function createCheckoutSession(Request $request)
    {
        $validated = $request->validate([
            'booking_reference' => 'required|string',
        ]);

        $booking = Booking::where('booking_reference', $validated['booking_reference'])->firstOrFail();

        // Prevent duplicate payment if already paid
        if (strtolower($booking->payment_status) === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'This booking has already been paid for.',
                'already_paid' => true,
            ], 400);
        }

        try {
            $sessionData = $this->generateStripeSession($booking);

            return response()->json([
                'success'      => true,
                'checkout_url' => $sessionData['url'],
                'session_id'   => $sessionData['id'],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create Stripe session: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Unable to initiate Stripe payment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate Stripe Checkout session via StripeClient.
     */
    protected function generateStripeSession(Booking $booking): array
    {
        $stripeSecret = config('services.stripe.secret');
        if (!$stripeSecret) {
            throw new \RuntimeException('Stripe secret key is not configured.');
        }

        $stripe = new StripeClient($stripeSecret);

        $currency = strtolower($booking->currency ?: 'eur');
        $unitAmount = (int) round(((float) $booking->total_amount) * 100);

        // Ensure minimum 50 cents for Stripe
        if ($unitAmount < 50) {
            $unitAmount = 50;
        }

        $productName = $booking->item_name ?: ($booking->destination_country . ' ' . $booking->visa_type . ' Visa');
        $description = 'Visa Application ' . $booking->booking_reference . ' for ' . $booking->first_name;

        $session = $stripe->checkout->sessions->create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency'     => $currency,
                    'product_data' => [
                        'name'        => $productName,
                        'description' => $description,
                    ],
                    'unit_amount'  => $unitAmount,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'customer_email' => $booking->email,
            'metadata' => [
                'booking_reference' => $booking->booking_reference,
                'booking_id'        => (string) $booking->id,
                'booking_type'      => 'visa',
                'customer_name'     => $booking->first_name,
            ],
            'success_url' => url('/visa-payment-success?session_id={CHECKOUT_SESSION_ID}&ref=' . $booking->booking_reference),
            'cancel_url'  => url('/visa/' . Str::slug($booking->destination_country . '-' . $booking->visa_type) . '/apply?cancelled=1&ref=' . $booking->booking_reference),
        ]);

        $booking->stripe_session_id = $session->id;
        $booking->save();

        return [
            'id'  => $session->id,
            'url' => $session->url,
        ];
    }

    /**
     * Server-side verification of Stripe payment session.
     */
    public function verifyPayment(Request $request)
    {
        $sessionId = $request->input('session_id');
        $ref = $request->input('ref');

        if (!$sessionId && !$ref) {
            return response()->json(['success' => false, 'message' => 'Missing session ID or reference'], 400);
        }

        $booking = null;
        if ($ref) {
            $booking = Booking::where('booking_reference', $ref)->first();
        }
        if (!$booking && $sessionId) {
            $booking = Booking::where('stripe_session_id', $sessionId)->first();
        }

        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Booking not found'], 404);
        }

        // If already marked as paid
        if (strtolower($booking->payment_status) === 'paid') {
            return response()->json([
                'success' => true,
                'status'  => 'paid',
                'booking' => $booking,
            ]);
        }

        // Verify with Stripe API
        $stripeSecret = config('services.stripe.secret');
        if ($sessionId && $stripeSecret) {
            try {
                $stripe = new StripeClient($stripeSecret);
                $session = $stripe->checkout->sessions->retrieve($sessionId);

                if ($session && $session->payment_status === 'paid') {
                    $this->markBookingAsPaid($booking, $session->payment_intent, (float) ($session->amount_total / 100), 'stripe');

                    return response()->json([
                        'success' => true,
                        'status'  => 'paid',
                        'booking' => $booking->fresh(),
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Stripe Verification Error: ' . $e->getMessage(), ['session_id' => $sessionId]);
            }
        }

        return response()->json([
            'success' => true,
            'status'  => $booking->payment_status,
            'booking' => $booking,
        ]);
    }

    /**
     * Stripe Webhook Handler.
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('stripe-signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        $event = null;

        if ($webhookSecret && $sigHeader) {
            try {
                $event = StripeWebhook::constructEvent($payload, $sigHeader, $webhookSecret);
            } catch (\UnexpectedValueException $e) {
                Log::error('Invalid Stripe webhook payload: ' . $e->getMessage());
                return response()->json(['error' => 'Invalid payload'], 400);
            } catch (\Stripe\Exception\SignatureVerificationException $e) {
                Log::error('Invalid Stripe webhook signature: ' . $e->getMessage());
                return response()->json(['error' => 'Invalid signature'], 400);
            }
        } else {
            // When webhook secret is not set, decode payload directly
            $data = json_decode($payload, true);
            if (!$data || !isset($data['type'])) {
                return response()->json(['error' => 'Invalid JSON payload'], 400);
            }
            $event = json_decode($payload);
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                $reference = $session->metadata->booking_reference ?? null;
                $booking = null;

                if ($reference) {
                    $booking = Booking::where('booking_reference', $reference)->first();
                }
                if (!$booking && isset($session->id)) {
                    $booking = Booking::where('stripe_session_id', $session->id)->first();
                }

                if ($booking && strtolower($booking->payment_status) !== 'paid') {
                    $amount = isset($session->amount_total) ? ((float) $session->amount_total) / 100 : (float) $booking->total_amount;
                    $paymentIntent = $session->payment_intent ?? null;
                    $this->markBookingAsPaid($booking, $paymentIntent, $amount, 'stripe');
                    Log::info("Booking {$booking->booking_reference} marked as paid via Stripe webhook.");
                }
                break;

            case 'payment_intent.payment_failed':
                $intent = $event->data->object;
                $booking = Booking::where('stripe_payment_intent_id', $intent->id)->first();
                if ($booking) {
                    $booking->payment_status = 'Failed';
                    $booking->save();
                    Log::warning("Payment failed for booking {$booking->booking_reference}");
                }
                break;

            case 'charge.refunded':
                $charge = $event->data->object;
                $booking = Booking::where('stripe_payment_intent_id', $charge->payment_intent)->first();
                if ($booking) {
                    $booking->payment_status = 'Refunded';
                    $booking->booking_status = 'Cancelled';
                    $booking->save();
                    Log::info("Booking {$booking->booking_reference} marked as refunded.");
                }
                break;

            default:
                Log::info('Unhandled Stripe webhook event: ' . $event->type);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Mark booking as paid, create Payment record and send confirmation emails.
     */
    protected function markBookingAsPaid(Booking $booking, ?string $paymentIntentId, float $amount, string $method = 'stripe'): void
    {
        $booking->payment_status = 'Paid';
        $booking->booking_status = 'Processing';
        $booking->payment_method = $method;
        if ($paymentIntentId) {
            $booking->stripe_payment_intent_id = $paymentIntentId;
        }
        $booking->save();

        // Record payment in payments table if not already recorded
        $exists = Payment::where('booking_id', $booking->id)
            ->where(function ($q) use ($paymentIntentId) {
                if ($paymentIntentId) {
                    $q->where('transaction_reference', $paymentIntentId);
                }
            })
            ->exists();

        if (!$exists) {
            Payment::create([
                'booking_id'            => $booking->id,
                'type'                  => 'Customer Payment',
                'amount'                => $amount,
                'payment_method'        => $method,
                'status'                => 'Completed',
                'payment_date'          => now(),
                'transaction_reference' => $paymentIntentId,
            ]);
        }

        // Send confirmed emails
        $this->sendBookingEmails($booking, true);
    }

    /**
     * Send customer confirmation & admin notification emails.
     */
    protected function sendBookingEmails(Booking $booking, bool $isPaid = false): void
    {
        try {
            if (!empty($booking->email)) {
                Mail::to($booking->email)->send(new BookingConfirmation($booking));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send customer confirmation email: ' . $e->getMessage());
        }

        try {
            $adminEmail = env('MAIL_TO_BOOKINGS', 'info@sangupk.com');
            $ccList = array_filter([
                env('MAIL_CC_BOOKINGS', 'sanat.saad@gmail.com'),
                env('MAIL_CC2_BOOKINGS', 'sangutravel@gmail.com'),
            ]);

            $mail = Mail::to($adminEmail);
            if (!empty($ccList)) {
                $mail->cc($ccList);
            }
            $mail->send(new AdminBookingNotification($booking));
        } catch (\Exception $e) {
            Log::error('Failed to send admin notification email: ' . $e->getMessage());
        }
    }

    /**
     * Export visa applications as CSV for admin.
     */
    public function exportCsv(Request $request)
    {
        $query = Booking::orderBy('created_at', 'desc');

        if ($request->has('type') && $request->type !== '') {
            $query->where('booking_type', $request->type);
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('booking_status', $request->status);
        }

        if ($request->has('payment_status') && $request->payment_status !== '') {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->has('search') && $request->search !== '') {
            $s = '%' . $request->search . '%';
            $query->where(function ($q) use ($s) {
                $q->where('booking_reference', 'like', $s)
                  ->orWhere('first_name', 'like', $s)
                  ->orWhere('email', 'like', $s)
                  ->orWhere('phone', 'like', $s)
                  ->orWhere('destination_country', 'like', $s)
                  ->orWhere('visa_type', 'like', $s);
            });
        }

        $bookings = $query->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="visa_applications_' . date('Y-m-d_His') . '.csv"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($bookings) {
            $file = fopen('php://output', 'w');
            // Add UTF-8 BOM for Excel
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'Reference',
                'Application Date',
                'Applicant Name',
                'Email',
                'Phone',
                'Nationality',
                'Destination Country',
                'Visa Type',
                'Travel Date',
                'Amount (EUR)',
                'Payment Method',
                'Payment Status',
                'Application Status',
                'Stripe Transaction ID',
            ]);

            foreach ($bookings as $b) {
                fputcsv($file, [
                    $b->booking_reference,
                    $b->created_at ? $b->created_at->format('Y-m-d H:i') : '',
                    $b->first_name,
                    $b->email,
                    $b->phone,
                    $b->nationality,
                    $b->destination_country,
                    $b->visa_type,
                    $b->travel_date ? $b->travel_date->format('Y-m-d') : '',
                    number_format((float) $b->total_amount, 2),
                    $b->payment_method,
                    $b->payment_status,
                    $b->booking_status,
                    $b->stripe_payment_intent_id ?: $b->stripe_session_id ?: '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
