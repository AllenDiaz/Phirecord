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

#[Entity, Table('request_medicals')]
#[HasLifecycleCallbacks]
class RequestMedical 
{
    
    #[Id, Column(options: ['unsigned' => true]), GeneratedValue]
    private int $id;
    
    #[Column(name: 'medical_code')]
    private string $medicalCode;

    #[Column(name: 'created_at')]
    private \DateTime $createdAt;

    #[OneToOne(inversedBy: 'requestMedical')]
    private MedicalCertificate $medicalCertificate;
    

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
    public function getMedicalCode(): string
    {
        return $this->medicalCode;
    }

    /**
     * Set the value of admissionCode
     *
     * @return  self
     */ 
    public function setMedicalCode(string $medicalCode): RequestMedical
    {
        $this->medicalCode = $medicalCode;

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
    public function setCreatedAt(\DateTime $createdAt): RequestMedical
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the value of admissionForm
     */ 
    public function getMedicalCertificate(): MedicalCertificate
    {
        return $this->medicalCertificate;
    }

    /**
     * Set the value of admissionForm
     *
     * @return  self
     */ 
    public function setMedicalCertificate(MedicalCertificate $medicalCertificate): RequestMedical
    {
        $this->medicalCertificate = $medicalCertificate;

        return $this;
    }
} 