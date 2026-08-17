<?php

namespace App\Mail;

use App\Models\AdoptionRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewAdoptionRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public AdoptionRequest $adoptionRequest,
    ) {
        $this->subject(
            __('mail.new_adoption_request.subject', ['animal' => $this->adoptionRequest->animal->name])
        )->markdown('emails.new-adoption-request', [
            'adoptionRequest' => $this->adoptionRequest,
        ]);
    }
}
