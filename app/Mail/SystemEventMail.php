<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SystemEventMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $headline,
        public string $body,
        public ?string $actionUrl = null,
        public ?string $actionLabel = null,
        public ?string $recipientName = null,
        public ?string $footerNote = null,
        public ?string $mailSubject = null,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->mailSubject ?: 'Notifikasi Sistem'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.system-event'
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
