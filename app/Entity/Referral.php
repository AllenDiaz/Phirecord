<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Patient;
use App\Entity\Hospital;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\PreUpdate;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;

#[Entity, Table('referral')]
#[HasLifecycleCallbacks]
class Referral 
{
    
    #[Id, Column(options: ['unsigned' => true]), GeneratedValue]
    private int $id;
    
    #[Column(name: 'referral_code')]
    private string $referralCode;

    #[Column(name: 'created_at')]
    private \DateTime $createdAt;

    #[Column(options: ['default' => false])]
    private bool $isAccepted;

    #[Column(name: 'to_hospital')]
    private int $toHospital;

    #[ManyToOne(inversedBy: 'referrals')]
    private Hospital $hospital;

    #[ManyToOne(inversedBy: 'referrals')]
    private Patient $patient;

    public function __construct()
    {
        $this->isAccepted = false; 
    }
    

    #[PrePersist, PreUpdate]
    public function updateTimestamps(LifecycleEventArgs $args): void
    {
        if (! isset($this->createdAt)) {
            $this->createdAt = new \DateTime();
        }
    }

        /**
     * Get the value of id
     */ 
    public function getId(): int
    {
        return $this->id;
    }


    /**
     * Get the value of hospital
     */ 
    public function getHospital(): Hospital 
    {
        return $this->hospital;
    }

    /**
     * Set the value of hospital
     *
     * @return  self
     */ 
    public function setHospital(Hospital $hospital): Referral
    {
        $hospital->addReferral($this);
        $this->hospital = $hospital;

        return $this;
    }

    /**
     * Get the value of patient
     */ 
    public function getPatient(): Patient
    {
        return $this->patient;
    }

    /**
     * Set the value of patient
     *
     * @return  self
     */ 
    public function setPatient(Patient $patient): Referral
    {
        $patient->addReferral($this);
        $this->patient = $patient;

        return $this;
    }


    /**
     * Get the value of refferalCode
     */ 
    public function getReferralCode(): string
    {
        return $this->referralCode;
    }

    /**
     * Set the value of refferalCode
     *
     * @return  self
     */ 
    public function setReferralCode(string $referralCode): Referral
    {
        $this->referralCode = $referralCode;

        return $this;
    }

    /**
     * Get the value of createdAt
     */ 
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     *
     * @return  self
     */ 
    public function setCreatedAt( \DateTime $createdAt): Referral
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the value of toHospital
     */ 
    public function getToHospital(): int
    {
        return $this->toHospital;
    }

    /**
     * Set the value of toHospital
     *
     * @return  self
     */ 
    public function setToHospital(int $toHospital): Referral
    {
        $this->toHospital = $toHospital;

        return $this;
    }

    /**
     * Get the value of isAccepted
     */ 
    public function getIsAccepted(): bool
    {
        return $this->isAccepted;
    }

    /**
     * Set the value of isAccepted
     *
     * @return  self
     */ 
    public function setIsAccepted(bool $isAccepted): Referral
    {
        $this->isAccepted = $isAccepted;

        return $this;
    }
} 