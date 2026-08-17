<?php

namespace App\Enums;

enum PendingChanges:string
{
    case Store = 'store';
    case Update = 'update';
    case Delete = 'delete';
}
