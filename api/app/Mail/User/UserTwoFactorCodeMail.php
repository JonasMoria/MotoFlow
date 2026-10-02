<?php

namespace App\Mail\User;

use App\Models\User\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserTwoFactorCodeMail extends Mailable implements ShouldQueue {
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly int $code,
    ) {
    }

    public function envelope(): Envelope {
        $appName = config('app.name');

        return new Envelope(
            subject: "Seu código de autenticação - {$appName}",
        );
    }

    public function content(): Content {
        return new Content(
            view: 'emails.user-two-factor-code',
            with: [
                'user' => $this->user,
                'code' => $this->code,
            ],
        );
    }

    public function attachments(): array {
        return [];
    }
}
