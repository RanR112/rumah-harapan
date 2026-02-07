<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * ResetPasswordOtpMail sends a one-time password (OTP) for password reset.
 * The email contains the 6-digit code and instructions for the user.
 */
class ResetPasswordOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The OTP code.
     *
     * @var string
     */
    public string $otp;

    /**
     * Create a new message instance.
     *
     * @param string $otp
     */
    public function __construct(string $otp)
    {
        $this->otp = $otp;
    }

    /**
     * Get the message envelope.
     *
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kode Reset Password Anda',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return Content
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reset-password-otp',
            with: ['otp' => $this->otp],
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
