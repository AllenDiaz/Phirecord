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

#[Entity, Table('request_admissions')]
#[HasLifecycleCallbacks]
class RequestAdmission 
{
    
    #[Id, Column(options: ['unsigned' => true]), GeneratedValue]
    private int $id;
    
    #[Column(name: 'admission_code')]
    private string $admissionCode;

    #[Column(name: 'created_at')]
    private \DateTime $createdAt;

    #[OneToOne(inversedBy: 'requestAdmission')]
    private AdmissionForm $admissionForm;
    

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
    public function getAdmissionCode(): string
    {
        return $this->admissionCode;
    }

    /**
     * Set the value of admissionCode
     *
     * @return  self
     */ 
    public function setAdmissionCode(string $admissionCode): RequestAdmission
    {
        $this->admissionCode = $admissionCode;

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
    public function setCreatedAt(\DateTime $createdAt): RequestAdmission
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the value of admissionForm
     */ 
    public function getAdmissionForm(): AdmissionForm
    {
        return $this->admissionForm;
    }

    /**
     * Set the value of admissionForm
     *
     * @return  self
     */ 
    public function setAdmissionForm(AdmissionForm $admissionForm): RequestAdmission
    {
        $this->admissionForm = $admissionForm;

        return $this;
    }
} 