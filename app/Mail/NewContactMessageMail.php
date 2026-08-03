<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewContactMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ContactMessage $contactMessage,
    ) {
        $this->subject(
            __('mail.new_contact_message.subject')
        )->markdown('emails.new-contact-message', [
            'contactMessage' => $this->contactMessage,
        ]);
    }
}
