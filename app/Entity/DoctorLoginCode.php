<?php

declare(strict_types = 1);

namespace App\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table('doctor_login_codes')]
class DoctorLoginCode
{
    #[Id, Column(options: ['unsigned' => true]), GeneratedValue]
    private int $id;

    #[Column(length: 6)]
    private string $code;

    #[Column(name: 'is_active', options: ['default' => true])]
    private bool $isActive;

    #[Column]
    private \DateTime $expiration;

    #[ManyToOne]
    private Doctor $doctor;

    public function __construct()
    {
        $this->isActive = true;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): DoctorLoginCode
    {
        $this->code = $code;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): DoctorLoginCode
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getExpiration(): \DateTime
    {
        return $this->expiration;
    }

    public function setExpiration(\DateTime $expiration): DoctorLoginCode
    {
        $this->expiration = $expiration;

        return $this;
    }

    public function getDoctor(): Doctor
    {
        return $this->doctor;
    }

    public function setDoctor(Doctor $doctor): DoctorLoginCode
    {
        $this->doctor = $doctor;

        return $this;
    }
    public function hasTwoFactorAuthEnabled(): bool
    {
        // TODO:

        return true;
    }
}
