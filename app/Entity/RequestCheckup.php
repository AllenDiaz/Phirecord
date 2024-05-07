<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Patient;
use App\Entity\Hospital;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\PreUpdate;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;

#[Entity, Table('request_checkups')]
#[HasLifecycleCallbacks]
class RequestCheckup 
{
    
    #[Id, Column(options: ['unsigned' => true]), GeneratedValue]
    private int $id;
    
    #[Column(name: 'checkup_code')]
    private string $checkupCode;

    #[Column(name: 'created_at')]
    private \DateTime $createdAt;

    #[OneToOne(inversedBy: 'requestCheckup')]
    private PrenatalCheckup $prenatalCheckup;
    

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
     * Get the value of admissionCode
     */ 
    public function getCheckupCode(): string
    {
        return $this->checkupCode;
    }

    /**
     * Set the value of admissionCode
     *
     * @return  self
     */ 
    public function setCheckupCode(string $checkupCode): RequestCheckup
    {
        $this->checkupCode = $checkupCode;

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
    public function setCreatedAt(\DateTime $createdAt): RequestCheckup
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the value of admissionForm
     */ 
    public function getPrenatalCheckup(): PrenatalCheckup
    {
        return $this->prenatalCheckup;
    }

    /**
     * Set the value of admissionForm
     *
     * @return  self
     */ 
    public function setPrenatalCheckup(PrenatalCheckup $prenatalCheckup): RequestCheckup
    {
        $this->prenatalCheckup = $prenatalCheckup;

        return $this;
    }
} 