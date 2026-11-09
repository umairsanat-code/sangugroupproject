<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewInquiryMail extends Mailable
{
    use Queueable, SerializesModels;

    public $inquiryData;

    /**
     * Create a new message instance.
     */
    public function __construct($inquiryData)
    {
        $this->inquiryData = $inquiryData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Website Inquiry: ' . ($this->inquiryData['subject'] ?? 'Contact Form'),
        );
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->html("
            <h2>New Inquiry Received</h2>
            <p><strong>Name:</strong> {$this->inquiryData['name']}</p>
            <p><strong>Phone:</strong> {$this->inquiryData['phone']}</p>
            <p><strong>Email:</strong> {$this->inquiryData['email']}</p>
            <p><strong>Subject:</strong> {$this->inquiryData['subject']}</p>
            <p><strong>Message:</strong></p>
            <p>{$this->inquiryData['message']}</p>
        ");
    }
}
