<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use App\Contracts\PatientInterface;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\PreUpdate;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\Common\Collections\ArrayCollection;

#[Entity, Table('patients')]
#[HasLifecycleCallbacks]
class Patient implements PatientInterface
{
    #[Id, Column(options: ['unsigned' => true]), GeneratedValue]
    private int $id;
    
    #[Column]
    private string $name;

    #[Column]
    private string $password;

    #[Column]
    private \DateTime $birthdate;

    #[Column]
    private string $gender;

    #[Column]
    private string $email;

    #[Column]
    private string $contact;

    #[Column]
    private string $address;

    #[Column(name: 'guardian_name', nullable: true)]
    private string $guardianName;

    #[Column(name: 'contact_guard', nullable: true)]
    private string $contactGuard;

    #[Column(name: 'philhealth_no', nullable: true)]
    private string $philhealthNo;

    #[Column(name: 'id_filename')]
    private string $idFilename;

    #[Column(name: 'id_storage_filename')]
    private string $idStorageFilename;

    #[Column(name: 'created_at')]
    private \DateTime $createdAt;

    #[Column(name: 'updated_at')]
    private \DateTime $updatedAt;

    #[Column(name: 'profile_picture', options: ['default' => 'default-picture.jpg'])]
    private string $profilePicture;
    
    #[Column(name: 'approved_at', nullable: true)]
    private \DateTime $approvedAt;

    #[Column(name: 'is_archived', options: ['default' => true])]
    private bool $isArchived;

    #[Column(options: ['default' => '0'])]
    private string $status;

    #[ManyToOne(inversedBy: 'patients')]
    private Hospital $hospital;

    #[OneToMany(mappedBy: 'patient', targetEntity: AdmissionForm::class, cascade: ['remove'])]
    private Collection $admissionForms;

    #[OneToMany(mappedBy: 'patient', targetEntity: MedicalCertificate::class, cascade: ['remove'])]
    private Collection $medicalCertificates;

    #[OneToMany(mappedBy: 'patient', targetEntity: PrenatalCheckup::class, cascade: ['remove'])]
    private Collection $prenatalCheckups;

    #[OneToMany(mappedBy: 'patient', targetEntity: Referral::class, cascade: ['remove'])]
    private Collection $referrals;

    #[OneToMany(mappedBy: 'patient', targetEntity: PatientLoginCode::class, cascade: ['remove'])]
    private Collection $patientLoginCodes;

    public function __construct()
    {
        $this->admissionForms = new ArrayCollection();
        $this->prenatalCheckups = new ArrayCollection();
        $this->medicalCertificates = new ArrayCollection();
        $this->referrals = new ArrayCollection();
        $this->status = '0';
        $this->isArchived = false;
        $this->profilePicture = 'default-picture.jpg';
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
     * Get the value of name
     */ 
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Patient
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of birthdate
     */ 
    public function getBirthdate(): \DateTime
    {
        return $this->birthdate;
    }


    public function setBirthdate($birthdate): Patient
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    /**
     * Get the value of gender
     */ 
    public function getGender(): string
    {
        return $this->gender;
    }

    public function setGender($gender): Patient
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get the value of hospital
     */ 
    public function getHospital(): Hospital
    {
        return $this->hospital;
    }

    public function setHospital(Hospital $hospital): Patient
    {
        $hospital->addPatient($this);
        $this->hospital = $hospital;

        return $this;
    }

    /**
     * Get the value of contact
     */ 
    public function getContact(): string
    {
        return $this->contact;
    }

 
    public function setContact($contact): Patient
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * Get the value of address
     */ 
    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress($address): Patient
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get the value of guardianName
     */ 
    public function getGuardianName(): ?string
    {
        return $this->guardianName;
    }


    public function setGuardianName(? string $guardianName): Patient
    {
        $this->guardianName = $guardianName;

        return $this;
    }

    /**
     * Get the value of philhealthNo
     */ 
    public function getPhilhealthNo(): ?string
    {
        return $this->philhealthNo;
    }


    public function setPhilhealthNo(? string $philhealthNo): Patient
    {
        $this->philhealthNo = $philhealthNo;

        return $this;
    }

    /**
     * Get the value of filename
     */ 
    public function getIdFilename(): string
    {
        return $this->idFilename;
    }


    public function setIdFilename(string $idfilename): Patient
    {
        $this->idFilename = $idfilename;

        return $this;
    }


    public function getIdStorageFilename(): string
    {
        return $this->idStorageFilename;
    }


    public function setIdStorageFilename(string $idStorageFilename): Patient
    {
        $this->idStorageFilename = $idStorageFilename;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }


    public function setPassword(string $password): Patient
    {
        $this->password = $password;

        return $this;
    }


    public function getApprovedAt(): ? \DateTime
    {
        return $this->approvedAt;
    }

    public function setApprovedAt($approvedAt): Patient
    {
        $this->approvedAt = $approvedAt;

        return $this;
    }


    public function getIsArchived(): bool
    {
        return $this->isArchived;
    }


    public function setIsArchived($isArchived): Patient
    {
        $this->isArchived = $isArchived;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus($status): Patient
    {
        $this->status = $status;

        return $this;
    }


    public function getProfilePicture(): string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(string $profilePicture): Patient
    {
        $this->profilePicture = $profilePicture;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): Patient
    {
        $this->email = $email;

        return $this;
    }


    public function getContactGuard(): ?string
    {
        return $this->contactGuard;
    }


    public function setContactGuard( ?string $contactGuard)
    {
        $this->contactGuard = $contactGuard;

        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }


    public function setCreatedAt(\DateTime $createdAt): Patient
    {
        $this->createdAt = $createdAt;

        return $this;
    }


    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }


    public function setUpdatedAt($updatedAt): Patient
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }


    public function getAdmissionForms(): ArrayCollection|Collection
    {
        return $this->admissionForms;
    }


    public function addAdmissionForm(AdmissionForm $admissionForm): Patient
    {
        $this->admissionForms->add($admissionForm);

        return $this;
    }

    public function getPrenatalCheckups(): ArrayCollection|Collection
    {
        return $this->prenatalCheckups;
    }


    public function addPrenatalCheckup(PrenatalCheckup $prenatalCheckup): Patient
    {
        $this->prenatalCheckups->add($prenatalCheckup);

        return $this;
    }

    public function getMedicalCertificates(): ArrayCollection|Collection
    {
        return $this->medicalCertificates;
    }

    public function addMedicalCertificate(MedicalCertificate $medicalCertificate): Patient
    {
        $this->medicalCertificates->add($medicalCertificate);

        return $this;
    }

    /**
     * Get the value of refferals
     */ 
    public function getReferrals(): ArrayCollection|Collection
    {
        return $this->refferals;
    }

    public function addReferral(Referral $referral): Patient
    {
        $this->referrals->add($referral);

        return $this;
    }
    public function hasTwoFactorAuthEnabled(): bool
    {
        // TODO:

        return true;
    }
}
