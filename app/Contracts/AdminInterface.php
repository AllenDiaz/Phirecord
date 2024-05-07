<?php

declare(strict_types = 1);

namespace App\Contracts;

interface AdminInterface
{
    public function getId(): int;
    public function getPassword(): string;
    public function hasTwoFactorAuthEnabled(): bool;
}