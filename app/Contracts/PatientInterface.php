<?php

declare(strict_types = 1);

namespace App\Contracts;

interface PatientInterface
{
    public function getId(): int;
    public function getPassword(): string;
}