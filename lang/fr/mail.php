<?php

return [
    'volunteer_account' => [
        'subject_new' => 'Votre compte bénévole Les Pattes Heureuses',
        'subject_reissued' => 'Vos identifiants Les Pattes Heureuses ont été mis à jour',
        'greeting' => 'Bonjour :name,',
        'intro_new' => 'Un administrateur a créé un compte bénévole pour vous sur Les Pattes Heureuses.',
        'intro_reissued' => 'Votre adresse email a été modifiée, un nouveau mot de passe temporaire a donc été généré pour votre compte.',
        'credentials_intro' => 'Vous pouvez vous connecter avec les identifiants temporaires suivants :',
        'email_label' => 'Email',
        'password_label' => 'Mot de passe temporaire',
        'must_change_password' => 'Pour des raisons de sécurité, il vous sera demandé de choisir un nouveau mot de passe lors de votre première connexion.',
        'login_button' => 'Se connecter',
        'footer' => 'Si vous ne vous attendiez pas à recevoir cet email, veuillez contacter un administrateur.',
    ],

    'new_adoption_request' => [
        'subject' => 'Nouvelle demande d\'adoption pour :animal',
        'intro' => 'Une nouvelle demande d\'adoption a été soumise pour :animal.',
        'from_label' => 'De la part de',
        'message_label' => 'Message',
        'button' => 'Consulter la demande',
    ],

    'new_contact_message' => [
        'subject' => 'Nouveau message reçu',
        'intro' => 'Un nouveau message a été soumis via le formulaire de contact.',
        'from_label' => 'De la part de',
        'type_label' => 'Type',
        'message_label' => 'Message',
        'button' => 'Ouvrir le tableau de bord',
        'types' => [
            'contact' => 'Contact',
            'volunteer_request' => 'Demande de bénévolat',
            'report' => 'Signalement',
        ],
    ],

    'reply_to_adoption_request' => [
        'subject' => 'Réponse à votre demande d\'adoption pour :animal',
        'greeting' => 'Bonjour :name,',
        'original_message_intro' => 'Pour rappel, voici votre message d\'origine concernant :animal :',
    ],

    'reply_to_contact_message' => [
        'subject' => 'Réponse à votre message',
        'greeting' => 'Bonjour :name,',
        'original_message_intro' => 'Pour rappel, voici votre message d\'origine :',
    ],

    'signature' => [
        // En dur plutôt que dérivé d'un champ de genre — seule Élise est admin pour l'instant.
        'admin_title' => 'Administratrice',
        'volunteer_title' => 'Bénévole',
        'template' => ":name,\n:role de Les Pattes Heureuses",
    ],
];
