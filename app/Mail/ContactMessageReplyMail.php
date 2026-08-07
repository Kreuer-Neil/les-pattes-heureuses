<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMessageReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ContactMessage $contactMessage,
        public string $message,
        public string $signature,
    ) {
        $this->subject(
            __('mail.reply_to_contact_message.subject')
        )->markdown('emails.reply-to-contact-message', [
            'contactMessage' => $this->contactMessage,
            'message' => $this->message,
            'signature' => $this->signature,
        ]);
    }
}
