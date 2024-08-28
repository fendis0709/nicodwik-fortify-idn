<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class TwoFactorAuthenticationQRCode extends Mailable
{
    use Queueable, SerializesModels;

    protected User $user;

    protected bool $isResend;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, bool $isResend = false)
    {
        $this->user = $user;
        $this->isResend = $isResend;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '['.($this->isResend ? 'RESEND-' : '').'FORTUNE] INVITATION TO SCAN QR CODE',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $format = 'png';
        $targetFolder = sprintf(
            '/qr-code/%s-%s.%s',
            Str::slug($this->user?->email),
            decrypt($this->user?->two_factor_secret),
            $format
        );

        $this->user->generateQrCodeAndUpload($targetFolder);

        return new Content(
            view: 'email.twofactor-qrcode',
            with: [
                'targetFolder' => $targetFolder,
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
