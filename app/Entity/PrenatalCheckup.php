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

#[Entity, Table('prenatal_checkups')]
#[HasLifecycleCallbacks]
class PrenatalCheckup 
{
    
    #[Id, Column(options: ['unsigned' => true]), GeneratedValue]
    private int $id;

    #[Column( name: 'family_member')]
    private string $familyMember;

    #[Column(name: 'last_menstrual_date')]
    private \DateTime $lastMenstrualDate;

    #[Column( name: 'confine_date_estimated')]
    private \DateTime $confineDateEstimated;

    #[Column( name: 'fetal_heart_tones', options: ['default' => 'N/A'])]
    private string $fetalHeartTones;

    #[Column(nullable: true)]
    private string $gravida;

    #[Column(nullable: true)]
    private string $para;

    #[Column(options: ['default' => 'N/A'])]
    private string $labaratory;

    #[Column(options: ['default' => 'N/A'])]
    private string $urinalysis;

    #[Column(name: 'blood_count', options: ['default' => 'N/A'])]
    private string $bloodCount;

    #[Column(options: ['default' => 'N/A'])]
    private string $fecalysis;

    #[Column(options: ['default' => false])]
    private bool $isPrescribed;

    #[Column(name: 'created_at')]
    private \DateTime $createdAt;

    #[Column(name: 'updated_at')]
      private \DateTime $updatedAt;

    #[Column(name: 'checkup_date')]
      private \DateTime $checkupDate;

    #[Column(options: ['default' => FALSE])]
    private bool $requested;

    #[ManyToOne(inversedBy: 'prenatalCheckups')]
    private Hospital $hospital;

    #[ManyToOne(inversedBy: 'prenatalCheckups')]
    private Patient $patient;

    #[ManyToOne(inversedBy: 'prenatalCheckups')]
    private Doctor $doctor;

    #[OneToOne(mappedBy: 'prenatalCheckup', targetEntity: Prescription::class, cascade: ['remove'])]
    private Prescription $prescription;

    #[OneToOne(mappedBy: 'prenatalCheckup', targetEntity: RequestCheckup::class, cascade: ['remove'])]
    private RequestCheckup $requestCheckup;


    public function __construct()
{
    $this->labaratory = 'N/A';
    $this->urinalysis = 'N/A';
    $this->bloodCount = 'N/A';
    $this->fecalysis = 'N/A';
    $this->fetalHeartTones = 'N/A';
    $this->isPrescribed = false;
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
    public function getId()
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
    public function setHospital(Hospital $hospital): PrenatalCheckup
    {
        $hospital->addPrenatalCheckup($this);
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
    public function setPatient(Patient $patient): PrenatalCheckup
    {
        $patient->addPrenatalCheckup($this);
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
    public function setDoctor(Doctor $doctor): PrenatalCheckup
    {
        $doctor->addPrenatalCheckup($this);
        $this->doctor = $doctor;

        return $this;
    }


    /**
     * Get the value of familyMember
     */ 
    public function getFamilyMember(): string
    {
        return $this->familyMember;
    }

    /**
     * Set the value of familyMember
     *
     * @return  self
     */ 
    public function setFamilyMember(string $familyMember): PrenatalCheckup
    {
        $this->familyMember = $familyMember;

        return $this;
    }

    /**
     * Get the value of lastMenstrualDate
     */ 
    public function getLastMenstrualDate(): \DateTime 
    {
        return $this->lastMenstrualDate;
    }

    /**
     * Set the value of lastMenstrualDate
     *
     * @return  self
     */ 
    public function setLastMenstrualDate( \DateTime $lastMenstrualDate)
    {
        $this->lastMenstrualDate = $lastMenstrualDate;

        return $this;
    }

    /**
     * Get the value of confineDateEstimated
     */ 
    public function getConfineDateEstimated(): \DateTime
    {
        return $this->confineDateEstimated;
    }

    /**
     * Set the value of confineDateEstimated
     *
     * @return  self
     */ 
    public function setConfineDateEstimated(\DateTime $confineDateEstimated): PrenatalCheckup
    {
        $this->confineDateEstimated = $confineDateEstimated;

        return $this;
    }

    /**
     * Get the value of fetalHeartTones
     */ 
    public function getFetalHeartTones(): string
    {
        return $this->fetalHeartTones;
    }

    /**
     * Set the value of fetalHeartTones
     *
     * @return  self
     */ 
    public function setFetalHeartTones(string $fetalHeartTones): PrenatalCheckup
    {
        $this->fetalHeartTones = $fetalHeartTones;

        return $this;
    }

    /**
     * Get the value of gravida
     */ 
    public function getGravida(): ?string
    {
        return $this->gravida;
    }

    /**
     * Set the value of gravida
     *
     * @return  self
     */ 
    public function setGravida( ?string $gravida): PrenatalCheckup
    {
        $this->gravida = $gravida;

        return $this;
    }

    /**
     * Get the value of para
     */ 
    public function getPara(): ?string
    {
        return $this->para;
    }

    /**
     * Set the value of para
     *
     * @return  self
     */ 
    public function setPara(? string $para): PrenatalCheckup
    {
        $this->para = $para;

        return $this;
    }

    /**
     * Get the value of labaratory
     */ 
    public function getLabaratory(): string
    {
        return $this->labaratory;
    }

    /**
     * Set the value of labaratory
     *
     * @return  self
     */ 
    public function setLabaratory(string $labaratory): PrenatalCheckup
    {
        $this->labaratory = $labaratory;

        return $this;
    }

    /**
     * Get the value of urinalysis
     */ 
    public function getUrinalysis(): string
    {
        return $this->urinalysis;
    }

    /**
     * Set the value of urinalysis
     *
     * @return  self
     */ 
    public function setUrinalysis(string $urinalysis): PrenatalCheckup
    {
        $this->urinalysis = $urinalysis;

        return $this;
    }

    /**
     * Get the value of bloodCount
     */ 
    public function getBloodCount(): string
    {
        return $this->bloodCount;
    }

    /**
     * Set the value of bloodCount
     *
     * @return  self
     */ 
    public function setBloodCount(string $bloodCount): PrenatalCheckup
    {
        $this->bloodCount = $bloodCount;

        return $this;
    }

    /**
     * Get the value of fecalysis
     */ 
    public function getFecalysis(): string
    {
        return $this->fecalysis;
    }

    /**
     * Set the value of fecalysis
     *
     * @return  self
     */ 
    public function setFecalysis(string $fecalysis): PrenatalCheckup
    {
        $this->fecalysis = $fecalysis;

        return $this;
    }

    /**
     * Get the value of isPrescribed
     */ 
    public function getIsPrescribed(): bool
    {
        return $this->isPrescribed;
    }

    /**
     * Set the value of isPrescribed
     *
     * @return  self
     */ 
    public function setIsPrescribed(bool $isPrescribed): PrenatalCheckup
    {
        $this->isPrescribed = $isPrescribed;

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
    public function setCreatedAt( \DateTime $createdAt): PrenatalCheckup
    {
        $this->createdAt = $createdAt;

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
      public function setUpdatedAt(\DateTime $updatedAt): PrenatalCheckup
      {
            $this->updatedAt = $updatedAt;

            return $this;
      }

      /**
       * Get the value of checkupDate
       */ 
      public function getCheckupDate(): \DateTime
      {
            return $this->checkupDate;
      }

      /**
       * Set the value of checkupDate
       *
       * @return  self
       */ 
      public function setCheckupDate(\DateTime $checkupDate): PrenatalCheckup
      {
            $this->checkupDate = $checkupDate;

            return $this;
      }

    /**
     * Get the value of prescription
     */ 
    public function getPrescription(): Prescription
    {
        return $this->prescription;
    }

    /**
     * Set the value of prescription
     *
     * @return  self
     */ 
    public function setPrescription(Prescription $prescription): PrenatalCheckup
    {
        $this->prescription = $prescription;

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
    public function setRequested(bool $requested): PrenatalCheckup
    {
        $this->requested = $requested;

        return $this;
    }

    /**
     * Get the value of requestCheckup
     */ 
    public function getRequestCheckup(): RequestCheckup
    {
        return $this->requestCheckup;
    }

    /**
     * Set the value of requestCheckup
     *
     * @return  self
     */ 
    public function setRequestCheckup(RequestCheckup $requestCheckup): PrenatalCheckup
    {
        $this->requestCheckup = $requestCheckup;

        return $this;
    }
} 