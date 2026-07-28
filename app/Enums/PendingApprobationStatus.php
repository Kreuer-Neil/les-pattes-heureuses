<?php

namespace App\Enums;

enum PendingApprobationStatus: string
{
    // Unattended = Specific to Adoptions
    case Unattended = 'unattended';
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
}
