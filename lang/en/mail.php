<?php

return [
    'volunteer_account' => [
        'subject_new' => 'Your Les Pattes Heureuses volunteer account',
        'subject_reissued' => 'Your Les Pattes Heureuses account credentials were updated',
        'greeting' => 'Hello :name,',
        'intro_new' => 'An administrator has created a volunteer account for you on Les Pattes Heureuses.',
        'intro_reissued' => 'Your email address was updated, so a new temporary password has been generated for your account.',
        'credentials_intro' => 'You can log in with the following temporary credentials:',
        'email_label' => 'Email',
        'password_label' => 'Temporary password',
        'must_change_password' => 'For security reasons, you will be asked to choose a new password the first time you log in.',
        'login_button' => 'Log in',
        'footer' => 'If you were not expecting this email, please contact an administrator.',
    ],

    'new_adoption_request' => [
        'subject' => 'New adoption request for :animal',
        'intro' => 'A new adoption request was submitted for :animal.',
        'from_label' => 'From',
        'message_label' => 'Message',
        'button' => 'Review the request',
    ],

    'adoption_request_confirmation' => [
        'subject' => 'Your adoption request for :animal was received',
        'greeting' => 'Hello :name,',
        'intro' => 'We have received your adoption request for :animal. Someone from the shelter will get back to you soon.',
        'button' => 'View :animal\'s profile',
    ],

    'new_contact_message' => [
        'subject' => 'New message received',
        'intro' => 'A new message was submitted through the contact form.',
        'from_label' => 'From',
        'type_label' => 'Type',
        'message_label' => 'Message',
        'button' => 'Open the dashboard',
        'types' => [
            'contact' => 'Contact',
            'volunteer_request' => 'Volunteer request',
            'report' => 'Report',
        ],
    ],

    'reply_to_adoption_request' => [
        'subject' => 'Reply to your adoption request for :animal',
        'greeting' => 'Hello :name,',
        'original_message_intro' => 'For reference, here is your original message about :animal:',
    ],

    'reply_to_contact_message' => [
        'subject' => 'Reply to your message',
        'greeting' => 'Hello :name,',
        'original_message_intro' => 'For reference, here is your original message:',
    ],

    'signature' => [
        // Hardcoded rather than derived from any gender field — only Élise is admin for now.
        'admin_title' => 'Administrator',
        'volunteer_title' => 'Volunteer',
        'template' => ":name,\n:role of Les Pattes Heureuses",
    ],
];
