<?php

namespace App\Enums;

enum ContactMessageStatus: string
{
    case Answered = 'answered';
    case Ignored = 'ignored';
}
