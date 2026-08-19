<?php

namespace App\Enums;

enum NotificationPreference: string
{
    case AdoptionRequests = 'notify_adoption_requests';
    case ContactMessages = 'notify_contact_messages';
}
