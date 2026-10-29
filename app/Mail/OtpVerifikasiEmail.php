<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpVerifikasiEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $otp;
    public string $namaUser;
    public string $tujuan; // 'verifikasi_email' | 'reset_password'

    public function __construct(string $otp, string $namaUser, string $tujuan = 'verifikasi_email')
    {
        $this->otp      = $otp;
        $this->namaUser = $namaUser;
        $this->tujuan   = $tujuan;
    }

    public function envelope(): Envelope
    {
        $subject = match ($this->tujuan) {
            'reset_password'   => '[TanamKu] Kode Reset Password Anda',
            default            => '[TanamKu] Kode Verifikasi Email Anda',
        };

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.otp');
    }

    public function attachments(): array
    {
        return [];
    }
}

