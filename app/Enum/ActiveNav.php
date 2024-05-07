<?php

declare(strict_types = 1);

namespace App\Enum;

enum ActiveNav
{
    case DASHBOARD;
    case PROFILE;
    case REGISTERED;
    case REGISTER;
    case REQUEST;
    case ARCHIVED;
    case NOTIFICATION;
    case SETTING;
    case ADMIN;
    case ASSISTANT;
}
