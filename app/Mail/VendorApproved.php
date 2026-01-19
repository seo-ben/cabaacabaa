<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VendorApproved extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $vendeur;

    /**
     * Create a new message instance.
     */
    public function __construct($vendeur)
    {
        $this->vendeur = $vendeur;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: \App\Models\AppSetting::get('email_vendor_approved_subject', 'Votre compte vendeur est approuvé !'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.vendors.approved',
            with: [
                'customMessage' => \App\Models\AppSetting::get('email_vendor_approved_body', 'Nous sommes heureux de vous annoncer que votre demande de compte vendeur a été acceptée.'),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
