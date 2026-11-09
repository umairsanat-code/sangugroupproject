<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InquiryAutoReplyMail extends Mailable
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
            subject: 'Thank You - We Received Your Inquiry | SanguGroup',
        );
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->html("
            <h2>Thank You, {$this->inquiryData['name']}</h2>
            <p>We have received your inquiry and our team will get back to you shortly.</p>
            <p><strong>Subject:</strong> {$this->inquiryData['subject']}</p>
            <p><strong>Message:</strong></p>
            <p>{$this->inquiryData['message']}</p>
            <br>
            <p>If your inquiry is urgent, feel free to reach us on WhatsApp at +923085092631 or by phone at 0034672068148.</p>
            <p>Warm regards,<br>SanguGroup Travel Team</p>
        ");
    }
}