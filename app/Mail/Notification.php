<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Parsedown;

class Notification extends Mailable
{
    use Queueable, SerializesModels;

    public $recipientName;
    public $bodyMessage;
    public $status;
    public $actionUrl;

    /**
     * Create a new message instance.
     */
    public function __construct($recipientName, $bodyMessage, $status, $actionUrl)
    {
        $this->recipientName = $recipientName;
        $this->bodyMessage = $bodyMessage;
        $this->status = $status;
        $this->actionUrl = $actionUrl;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Notification',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.notification',
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