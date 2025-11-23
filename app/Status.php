<?php

namespace App;

enum Status: string
{
    case AVAILABLE = 'available';
    case PENDING = 'pending';
    case ADOPTED = 'adopted';
    case HEALING = 'healing';
}
