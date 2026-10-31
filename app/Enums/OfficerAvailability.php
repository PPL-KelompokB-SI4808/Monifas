<?php

namespace App\Enums;

enum OfficerAvailability: string
{
    case Available = 'available';
    case Busy = 'busy';
    case Unavailable = 'unavailable';
}
