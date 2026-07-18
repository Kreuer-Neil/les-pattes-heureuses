<?php

namespace App\Enums\Animals;

enum Status: string
{
    case Unknown = 'unknown';
    case Available = 'available';
    case Pending = 'pending';
    case Adopted = 'adopted';
    case Healing = 'healing';
    case Deceased = 'deceased';
}
