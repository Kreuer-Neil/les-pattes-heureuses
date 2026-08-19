<?php

namespace App\Mail;

use App\Models\AdoptionRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdoptionRequestConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public AdoptionRequest $adoptionRequest,
    ) {
        $this->subject(
            __('mail.adoption_request_confirmation.subject', ['animal' => $this->adoptionRequest->animal->name])
        )->markdown('emails.adoption-request-confirmation', [
            'adoptionRequest' => $this->adoptionRequest,
        ]);
    }
}