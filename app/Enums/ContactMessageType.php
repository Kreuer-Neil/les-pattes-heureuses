<?php

namespace App\Enums;

enum ContactMessageType: string
{
    case Contact = 'contact';
    case VolunteerRequest = 'volunteer_request';
    case Report = 'report';
}
