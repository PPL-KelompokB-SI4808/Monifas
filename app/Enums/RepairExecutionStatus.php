<?php

namespace App\Enums;

enum RepairExecutionStatus: string
{
    case Draft = 'draft';
    case InProgress = 'in_progress';
    case Completed = 'completed';
}
