<?php

declare(strict_types = 1);

namespace App\Enum;

enum NavHospital
{
    case DASHBOARD;
    case PROFILE;
    case DOCTOR_REGISTER;
    case REGISTER;
    case REQUEST;
    case ARCHIVED_PATIENT;
    case ARCHIVED_DOCTOR;
    case NOTIFICATION;
    case SETTING;
    case ADMIN;
    case ASSISTANT;
    case PATIENT_REGISTER;
    case DOCTOR;
    case PENDING;
}
