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
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;

#[Entity, Table('admission_form_olds')]
#[HasLifecycleCallbacks]
class AdmissionFormOld
{
    
    #[Id, Column(options: ['unsigned' => true]), GeneratedValue]
    private int $id;

    #[Column( name: 'family_member')]
    private string $familyMember;

    #[Column]
    private string $patientName;

    #[Column]
    private string $doctorName;

    #[Column]
    private string $symptoms;

    #[Column(name: 'date_created')]
    private \DateTime $dateCreated;

    #[Column( name: 'blood_pressure')]
    private string $bloodPressure;

    #[Column]
    private string $temperature;

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

    #[ManyToOne(inversedBy: 'admission_form_olds')]
    private Hospital $hospital;


    /**
     * Get the value of id
     */ 
    public function getId(): int
    {
        return $this->id;
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
    public function setFamilyMember(string $familyMember): AdmissionFormOld
    {
        $this->familyMember = $familyMember;

        return $this;
    }

    /**
     * Get the value of patientName
     */ 
    public function getPatientName(): string 
    {
        return $this->patientName;
    }

    /**
     * Set the value of patientName
     *
     * @return  self
     */ 
    public function setPatientName(string $patientName): AdmissionFormOld
    {
        $this->patientName = $patientName;

        return $this;
    }

    /**
     * Get the value of doctorName
     */ 
    public function getDoctorName(): string
    {
        return $this->doctorName;
    }

    /**
     * Set the value of doctorName
     *
     * @return  self
     */ 
    public function setDoctorName(string $doctorName): AdmissionFormOld
    {
        $this->doctorName = $doctorName;

        return $this;
    }

    /**
     * Get the value of symptoms
     */ 
    public function getSymptoms(): string
    {
        return $this->symptoms;
    }

    /**
     * Set the value of symptoms
     *
     * @return  self
     */ 
    public function setSymptoms(string $symptoms): AdmissionFormOld
    {
        $this->symptoms = $symptoms;

        return $this;
    }

    /**
     * Get the value of bloodPressure
     */ 
    public function getBloodPressure(): string
    {
        return $this->bloodPressure;
    }

    /**
     * Set the value of bloodPressure
     *
     * @return  self
     */ 
    public function setBloodPressure(string $bloodPressure): AdmissionFormOld
    {
        $this->bloodPressure = $bloodPressure;

        return $this;
    }

    /**
     * Get the value of temperature
     */ 
    public function getTemperature(): string 
    {
        return $this->temperature;
    }

    /**
     * Set the value of temperature
     *
     * @return  self
     */ 
    public function setTemperature(string $temperature): AdmissionFormOld
    {
        $this->temperature = $temperature;

        return $this;
    }

    /**
     * Get the value of weight
     */ 
    public function getWeight(): string
    {
        return $this->weight;
    }

    /**
     * Set the value of weight
     *
     * @return  self
     */ 
    public function setWeight(string $weight): AdmissionFormOld
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get the value of respiratoryRate
     */ 
    public function getRespiratoryRate(): string
    {
        return $this->respiratoryRate;
    }

    /**
     * Set the value of respiratoryRate
     *
     * @return  self
     */ 
    public function setRespiratoryRate($respiratoryRate): AdmissionFormOld
    {
        $this->respiratoryRate = $respiratoryRate;

        return $this;
    }

    /**
     * Get the value of pulseRate
     */ 
    public function getPulseRate(): string
    {
        return $this->pulseRate;
    }

    /**
     * Set the value of pulseRate
     *
     * @return  self
     */ 
    public function setPulseRate(string $pulseRate): AdmissionFormOld
    {
        $this->pulseRate = $pulseRate;

        return $this;
    }

    /**
     * Get the value of oxygenSaturation
     */ 
    public function getOxygenSaturation(): string
    {
        return $this->oxygenSaturation;
    }

    /**
     * Set the value of oxygenSaturation
     *
     * @return  self
     */ 
    public function setOxygenSaturation(string $oxygenSaturation)
    {
        $this->oxygenSaturation = $oxygenSaturation;

        return $this;
    }

    /**
     * Get the value of diagnosis
     */ 
    public function getDiagnosis(): string
    {
        return $this->diagnosis;
    }

    /**
     * Set the value of diagnosis
     *
     * @return  self
     */ 
    public function setDiagnosis(string $diagnosis): AdmissionFormOld
    {
        $this->diagnosis = $diagnosis;

        return $this;
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
    public function setHospital(Hospital $hospital): AdmissionFormOld
    {
        $hospital->addAdmissionFormOld($this);

        $this->hospital = $hospital;

        return $this;
    }

    /**
     * Get the value of dateCreated
     */ 
    public function getDateCreated(): \DateTime
    {
        return $this->dateCreated;
    }

    /**
     * Set the value of dateCreated
     *
     * @return  self
     */ 
    public function setDateCreated(\DateTime $dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }
} 