<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewBookingMail extends Mailable
{
    use Queueable, SerializesModels;

    public $bookingData;

    /**
     * Create a new message instance.
     */
    public function __construct($bookingData)
    {
        $this->bookingData = $bookingData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New ' . ucfirst($this->bookingData['type'] ?? 'Service') . ' Booking Received!',
        );
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $amount = isset($this->bookingData['total_amount']) ? 'PKR ' . number_format($this->bookingData['total_amount'], 2) : 'N/A';
        
        return $this->html("
            <h2>New Booking Received</h2>
            <p><strong>Service Type:</strong> " . ucfirst($this->bookingData['type'] ?? 'Unknown') . "</p>
            <p><strong>Customer Name:</strong> {$this->bookingData['traveller_name']}</p>
            <p><strong>Passport Number:</strong> {$this->bookingData['passport_number']}</p>
            <p><strong>Booking Amount:</strong> {$amount}</p>
            <p><strong>Contact Info:</strong> {$this->bookingData['contact_info']}</p>
            <br>
            <p>Please log in to the admin panel to view full booking and uploaded document details.</p>
        ");
    }
}
