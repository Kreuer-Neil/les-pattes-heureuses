<?php

namespace App\Mail;

use App\Models\AdoptionRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdoptionRequestReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public AdoptionRequest $adoptionRequest,
        public string $message,
        public string $signature,
    ) {
        $this->subject(
            __('mail.reply_to_adoption_request.subject', ['animal' => $this->adoptionRequest->animal->name])
        )->markdown('emails.reply-to-adoption-request', [
            'adoptionRequest' => $this->adoptionRequest,
            'message' => $this->message,
            'signature' => $this->signature,
        ]);
    }
}
