<?php
// app/Mail/BookingConfirmation.php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $itemDetails;

    /**
     * Create a new message instance.
     */
    public function __construct($booking, $itemDetails = [])
    {
        $this->booking = $booking;
        $this->itemDetails = $itemDetails;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Booking Confirmation - ' . ($this->booking->booking_reference ?? 'Travel Agency'),
            tags: ['booking', 'confirmation'],
            metadata: [
                'booking_id' => $this->booking->id ?? null,
                'booking_reference' => $this->booking->booking_reference ?? null,
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-confirmation',
            with: [
                'booking' => $this->booking,
                'itemDetails' => $this->itemDetails,
                'totalAmount' => number_format($this->booking->total_amount ?? 0, 2),
                'bookingDate' => $this->booking->created_at ? $this->booking->created_at->format('F j, Y g:i A') : now()->format('F j, Y g:i A'),
            ]
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Build the message (for Laravel 7.x and below).
     */
    public function build()
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
            ->subject('Booking Confirmation - ' . $this->booking->booking_reference)
            ->view('emails.booking-confirmation')
            ->with([
                'booking' => $this->booking,
                'itemDetails' => $this->itemDetails,
                'totalAmount' => number_format($this->booking->total_amount, 2),
                'bookingDate' => $this->booking->created_at->format('F j, Y g:i A'),
            ]);
    }
}
