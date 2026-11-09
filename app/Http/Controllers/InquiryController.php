<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\NewInquiryMail;
use App\Mail\InquiryAutoReplyMail;

class InquiryController extends Controller
{
    public function store(Request $request)
    {
        // Validate Request (Supports both old and new form formats)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:50',
            'subject' => 'nullable|string|max:255',
            'message' => 'nullable|string'
        ]);

        // Best-effort notification email to the office team (never breaks the request)
        try {
            Mail::to(env('MAIL_TO_BOOKINGS', 'info@sangupk.com'))
                ->cc([
                    env('MAIL_CC_BOOKINGS', 'sanat.saad@gmail.com'),
                    env('MAIL_CC2_BOOKINGS', 'sangutravel@gmail.com'),
                ])
                ->send(new NewInquiryMail($validated));
        } catch (\Exception $e) {
            Log::error('Inquiry Email Exception: ' . $e->getMessage());
        }

        // Best-effort auto-reply to the customer (only if they left an email)
        if (!empty($validated['email'])) {
            try {
                Mail::to($validated['email'])
                    ->send(new InquiryAutoReplyMail($validated));
            } catch (\Exception $e) {
                Log::error('Inquiry Auto-Reply Exception: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Your inquiry has been sent successfully!'
        ]);
    }
}
