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

#[Entity, Table('admission_forms')]
#[HasLifecycleCallbacks]
class AdmissionForm 
{
    
    #[Id, Column(options: ['unsigned' => true]), GeneratedValue]
    private int $id;

    #[Column( name: 'family_member')]
    private string $familyMember;

    #[Column]
    private string $symptoms;

    #[Column( name: 'blood_pressure')]
    private string $bloodPressure;

    #[Column]
    private string $temperature;

    #[Column(options: ['default' => FALSE])]
    private bool $prescription;

    #[Column(options: ['default' => FALSE])]
    private bool $requested;

    #[Column]
    private string $weight;

    #[Column( name: 'respiratory_rate')]
    private string $respiratoryRate;

    #[Column( name: 'pulse_rate')]
    private string $pulseRate;

    #[Column( name: 'oxygen_saturation')]
    private string $oxygenSaturation;

    #[Column]
    private string $diagnosis;

    #[Column(name: 'created_at')]
    private \DateTime $createdAt;

    #[Column(name: 'updated_at')]
      private \DateTime $updatedAt;

    #[Column(name: 'admission_date')]
      private \DateTime $admissionDate;

    #[ManyToOne(inversedBy: 'admissionForms')]
    private Hospital $hospital;

    #[ManyToOne(inversedBy: 'admissionForms')]
    private Patient $patient;

    #[ManyToOne(inversedBy: 'admissionForms')]
    private Doctor $doctor;

    #[OneToOne(mappedBy: 'admissionForm', targetEntity: RequestAdmission::class, cascade: ['remove'])]
    private RequestAdmission $requestAdmission;

    public function __construct() {
        $this->prescription = FALSE;
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

    public function getId(): int
    {
        return $this->id;
    }


    public function getFamilyMember(): string
    {
        return $this->familyMember;
    }

    public function setFamilyMember(string $familyMember): AdmissionForm
    {
        $this->familyMember = $familyMember;

        return $this;
    }

    public function getSymptoms(): string
    {
        return $this->symptoms;
    }

                           
    public function setSymptoms(string $symptoms): AdmissionForm
    {
        $this->symptoms = $symptoms;

        return $this;
    }

    public function getBloodPressure(): string
    {
        return $this->bloodPressure;
    }


    public function setBloodPressure(string $bloodPressure): AdmissionForm
    {
        $this->bloodPressure = $bloodPressure;

        return $this;
    }


    public function getTemperature(): string
    {
        return $this->temperature;
    }


    public function setTemperature(string $temperature): AdmissionForm
    {
        $this->temperature = $temperature;

        return $this;
    }

 
    public function getWeight(): string
    {
        return $this->weight;
    }


    public function setWeight(string $weight): AdmissionForm
    {
        $this->weight = $weight;

        return $this;
    }


    public function getRespiratoryRate(): string
    {
        return $this->respiratoryRate;
    }


    public function setRespiratoryRate(string $respiratoryRate): AdmissionForm
    {
        $this->respiratoryRate = $respiratoryRate;

        return $this;
    }


    public function getPulseRate(): string
    {
        return $this->pulseRate;
    }

    public function setPulseRate(string $pulseRate): AdmissionForm
    {
        $this->pulseRate = $pulseRate;

        return $this;
    }

    public function getOxygenSaturation(): string
    {
        return $this->oxygenSaturation;
    }


    public function setOxygenSaturation(string $oxygenSaturation): AdmissionForm
    {
        $this->oxygenSaturation = $oxygenSaturation;

        return $this;
    }


    public function getDiagnosis(): string
    {
        return $this->diagnosis;
    }


    public function setDiagnosis(string $diagnosis): AdmissionForm
    {
        $this->diagnosis = $diagnosis;

        return $this;
    }


    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): AdmissionForm
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getHospital(): Hospital 
    {
        return $this->hospital;
    }

    public function setHospital(Hospital $hospital): AdmissionForm
    {
        $hospital->addAdmissionForm($this);

        $this->hospital = $hospital;

        return $this;
    }


    public function getPatient(): Patient
    {
        return $this->patient;
    }


    public function setPatient(Patient $patient): AdmissionForm
    {
        $patient->addAdmissionForm($this);
        $this->patient = $patient;

        return $this;
    }


    public function getDoctor(): Doctor
    {
        return $this->doctor;
    }

    public function setDoctor(Doctor $doctor): AdmissionForm
    {
        $doctor->addAdmissionForm($this);
        $this->doctor = $doctor;

        return $this;
    }


      public function getUpdatedAt(): \DateTime
      {
            return $this->updatedAt;
      }


      public function setUpdatedAt(\DateTime $updatedAt): AdmissionForm
      {
            $this->updatedAt = $updatedAt;

            return $this;
      }


      public function getAdmissionDate(): \DateTime
      {
            return $this->admissionDate;
      }


      public function setAdmissionDate(\DateTime $admissionDate): AdmissionForm
      {
            $this->admissionDate = $admissionDate;

            return $this;
      }

    /**
     * Get the value of prescription
     */ 
    public function getPrescription(): bool
    {
        return $this->prescription;
    }


    public function setPrescription(bool $prescription): AdmissionForm
    {
        $this->prescription = $prescription;

        return $this;
    }

    /**
     * Get the value of requestAdmission
     */ 
    public function getRequestAdmission(): RequestAdmission
    {
        return $this->requestAdmission;
    }

    /**
     * Set the value of requestAdmission
     *
     * @return  self
     */ 
    public function setRequestAdmission(RequestAdmission $requestAdmission): AdmissionForm
    {
        $this->requestAdmission = $requestAdmission;

        return $this;
    }


    public function getRequested(): bool
    {
        return $this->requested;
    }


    public function setRequested(bool $requested): AdmissionForm
    {
        $this->requested = $requested;

        return $this;
    }
} 