<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VolunteerPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $temporaryPassword,
        public bool $isNewAccount = true,
    ) {
        $this->subject(
            $this->isNewAccount
                ? __('mail.volunteer_account.subject_new')
                : __('mail.volunteer_account.subject_reissued')
        )->markdown('emails.volunteer-account-created', [
            'user' => $this->user,
            'temporaryPassword' => $this->temporaryPassword,
            'isNewAccount' => $this->isNewAccount,
        ]);
    }
}
