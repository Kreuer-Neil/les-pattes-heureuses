<?php

namespace App\Enums\Animals;

enum MovementType: string
{
    case Recovery = 'recovery';
    case AdoptedDeparture = 'adopted_departure';
    case DeceasedDeparture = 'deceased_departure';
}
