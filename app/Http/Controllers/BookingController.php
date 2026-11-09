<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\PaymentSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function index()
    {
        // For admin panel
        return response()->json(Booking::orderBy('created_at', 'desc')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|string',
            'title' => 'required|string',
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'travellers' => 'required|integer',
            'total' => 'required|string',
            'discount_amount' => 'nullable|numeric',
            'discount_percent' => 'nullable|numeric',
        ]);

        $additionalData = ['travellers' => $data['travellers']];

        // Store optional visa applicant fields so the admin can review the full form
        $applicantFields = [
            'dob', 'profession', 'country_of_birth', 'nationality', 'marital_status',
            'address_country', 'address_city', 'zip_code', 'full_address',
            'passport_type', 'passport_no', 'passport_issue_place',
            'passport_issue_date', 'passport_expiry', 'arrival_date', 'departure_date',
            'accepted_terms',
        ];
        foreach ($applicantFields as $field) {
            $value = $request->input($field);
            if ($value !== null && $value !== '') {
                $additionalData[$field] = $value;
            }
        }

        // Handle uploaded documents
        if ($request->hasFile('documents')) {
            $docPaths = [];
            foreach ($request->file('documents') as $key => $file) {
                $path = $file->store('bookings', 'public');
                $docPaths[$key] = '/storage/' . $path;
            }
            $additionalData['documents'] = $docPaths;
        }

        $booking = new Booking();
        $booking->booking_reference = 'SGT-' . strtoupper(Str::random(6));
        $booking->booking_type = $data['type'];
        $booking->item_name = $data['title'];
        $booking->first_name = $data['name'];
        $booking->email = $data['email'];
        $booking->phone = $data['phone'];
        $booking->total_amount = floatval(preg_replace('/[^\d.]/', '', $data['total']));
        $booking->discount_amount = isset($data['discount_amount']) ? floatval($data['discount_amount']) : null;
        $booking->discount_percent = isset($data['discount_percent']) ? floatval($data['discount_percent']) : null;
        $booking->additional_data = json_encode($additionalData);
        $booking->payment_method = 'revolut';
        $booking->booking_status = 'Pending Payment';
        $booking->booking_date = now();
        $booking->save();

        // Notify the office about the new booking (best-effort — never blocks the booking itself)
        try {
            $bookingData = [
                'type' => $booking->booking_type,
                'traveller_name' => $booking->first_name,
                'passport_number' => 'Check Admin Panel',
                'total_amount' => $booking->total_amount,
                'contact_info' => $booking->email . ' / ' . $booking->phone
            ];
            \Illuminate\Support\Facades\Mail::to(env('MAIL_TO_BOOKINGS', 'info@sangupk.com'))
                ->cc([
                    env('MAIL_CC_BOOKINGS', 'sanat.saad@gmail.com'),
                    env('MAIL_CC2_BOOKINGS', 'sangutravel@gmail.com'),
                ])
                ->send(new \App\Mail\NewBookingMail($bookingData));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send booking notification email: ' . $e->getMessage());
        }

        // No online card gateway — the client pays directly to the Revolut account shown on the confirmation screen
        $payment = PaymentSettings::firstOrCreate(
            ['id' => 1],
            ['method' => 'revolut', 'currency' => 'EUR']
        );

        return response()->json([
            'success' => true,
            'booking_reference' => $booking->booking_reference,
            'booking' => $booking,
            'payment' => $payment
        ], 201);
    }

    public function paymentSuccess(Request $request)
    {
        $reference = $request->input('reference');

        if (!$reference) {
            return response()->json(['error' => 'Missing parameters'], 400);
        }

        $booking = Booking::where('booking_reference', $reference)->first();
        if ($booking) {
            return response()->json(['success' => true, 'booking' => $booking]);
        }

        return response()->json(['error' => 'Booking not found'], 404);
    }

    public function show($id)
    {
        $booking = Booking::where('booking_reference', $id)
            ->orWhere('id', is_numeric($id) ? (int)$id : 0)
            ->firstOrFail();
        return response()->json($booking);
    }

    public function updateStatus(Request $request, $id)
    {
        $booking = Booking::where('booking_reference', $id)
            ->orWhere('id', is_numeric($id) ? (int)$id : 0)
            ->firstOrFail();
        
        $request->validate([
            'status'         => 'nullable|string',
            'booking_status' => 'nullable|string',
            'payment_status' => 'nullable|string',
        ]);

        if ($request->filled('booking_status')) {
            $booking->booking_status = $request->booking_status;
        } elseif ($request->filled('status')) {
            $booking->booking_status = $request->status;
        }

        if ($request->filled('payment_status')) {
            $booking->payment_status = $request->payment_status;
        }

        $booking->save();

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully',
            'booking' => $booking
        ]);
    }
}
