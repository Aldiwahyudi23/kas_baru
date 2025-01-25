<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class ReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $filePath;
    public $fileName;

    public $recipientName;
    public $bodyMessage;
    public $status;
    public $actionUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(string $recipientName, $bodyMessage, $status, $actionUrl, $filePath, $fileName)
    {

        $this->recipientName = $recipientName;
        $this->bodyMessage = $bodyMessage;
        $this->status = $status;
        $this->actionUrl = $actionUrl;

        $this->filePath = $filePath;
        $this->fileName = $fileName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Laporan Bulanan',
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
        return [
            Attachment::fromPath($this->filePath)
                ->as($this->fileName)
                ->withMime('application/pdf'),
        ];
    }
}