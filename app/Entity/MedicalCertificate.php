<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Doctor;
use App\Entity\Patient;
use App\Entity\Hospital;
use Doctrine\ORM\Mapping\Id;
use App\Entity\RequestMedical;
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

#[Entity, Table('medical_certificates')]
#[HasLifecycleCallbacks]
class MedicalCertificate 
{
    
    #[Id, Column(options: ['unsigned' => true]), GeneratedValue]
    private int $id;

    #[Column(name: 'cerificate_date')]
    private \DateTime $certificateDate;
    
    #[Column]
    private string $impression;

    #[Column]
    private string $purpose;

    #[Column(name: 'created_at')]
    private \DateTime $createdAt;

    #[Column(name: 'updated_at')]
    private \DateTime $updatedAt;

    #[Column(options: ['default' => FALSE])]
    private bool $requested;

    #[ManyToOne(inversedBy: 'medicalCertificates')]
    private Hospital $hospital;

    #[ManyToOne(inversedBy: 'medicalCertificates')]
    private Patient $patient;

    #[ManyToOne(inversedBy: 'medicalCertificates')]
    private Doctor $doctor;

    #[OneToOne(mappedBy: 'medicalCertificate', targetEntity: RequestMedical::class, cascade: ['remove'])]
    private RequestMedical $requestMedical;

    public function __construct() {
        $this->requested = FALSE;
    }

    #[PrePersist, PreUpdate]
    public function updateTimestamps(LifecycleEventArgs $args): void
    {
        if (! isset($this->createdAt)) {
            $this->createdAt = new \DateTime();
        }

        $this->updatedAt = new \DateTime();
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
    public function setHospital(Hospital $hospital): MedicalCertificate
    {
        $hospital->addMedicalCertificate($this);
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
    public function setPatient(Patient $patient): MedicalCertificate
    {
        $patient->addMedicalCertificate($this);
        $this->patient = $patient;

        return $this;
    }

    /**
     * Get the value of doctor
     */ 
    public function getDoctor(): Doctor
    {
        return $this->doctor;
    }

    /**
     * Set the value of doctor
     *
     * @return  self
     */ 
    public function setDoctor(Doctor $doctor): MedicalCertificate
    {
        $doctor->addMedicalCertificate($this);
        $this->doctor = $doctor;

        return $this;
    }

      /**
       * Get the value of updatedAt
       */ 
      public function getUpdatedAt(): \DateTime
      {
            return $this->updatedAt;
      }

      /**
       * Set the value of updatedAt
       *
       * @return  self
       */ 
      public function setUpdatedAt(\DateTime $updatedAt): MedicalCertificate
      {
            $this->updatedAt = $updatedAt;

            return $this;
      }


    /**
     * Get the value of certificateDate
     */ 
    public function getCertificateDate(): \DateTime 
    {
        return $this->certificateDate;
    }

    /**
     * Set the value of certificateDate
     *
     * @return  self
     */ 
    public function setCertificateDate(\DateTime $certificateDate): MedicalCertificate
    {
        $this->certificateDate = $certificateDate;

        return $this;
    }

    /**
     * Get the value of impression
     */ 
    public function getImpression(): string
    {
        return $this->impression;
    }

    /**
     * Set the value of impression
     *
     * @return  self
     */ 
    public function setImpression(string $impression): MedicalCertificate
    {
        $this->impression = $impression;

        return $this;
    }

    /**
     * Get the value of purpose
     */ 
    public function getPurpose(): string 
    {
        return $this->purpose;
    }

    /**
     * Set the value of purpose
     *
     * @return  self
     */ 
    public function setPurpose(string $purpose): MedicalCertificate
    {
        $this->purpose = $purpose;

        return $this;
    }

        /**
     * Get the value of requested
     */ 
    public function getRequested(): bool
    {
        return $this->requested;
    }

    /**
     * Set the value of requested
     *
     * @return  self
     */ 
    public function setRequested(bool $requested): MedicalCertificate
    {
        $this->requested = $requested;

        return $this;
    }
    
        /**
     * Get the value of requestCheckup
     */ 
    public function getRequestMedical(): RequestMedical
    {
        return $this->requestMedical;
    }

    /**
     * Set the value of requestCheckup
     *
     * @return  self
     */ 
    public function setRequestMedical(RequestMedical $requestMedical): MedicalCertificate
    {
        $this->requestMedical = $requestMedical;

        return $this;
    }
} 