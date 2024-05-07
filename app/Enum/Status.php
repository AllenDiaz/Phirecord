<?php

declare(strict_types = 1);

namespace App\Enum;

enum Status: string
{
    case Approved = '1';
    case Pending = '2';
    case Declined = '3';
}