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

    'new_pending_animal_change' => [
        'subject_create' => 'New animal suggested: :animal',
        'subject_update' => 'Change suggested for :animal',
        'intro_create' => ':user suggested adding a new animal, :animal, to the shelter records.',
        'intro_update' => ':user suggested a change to :animal\'s record.',
        'button' => 'Review the suggestion',
    ],
];
