<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\PreUpdate;
use App\Contracts\HospitalInterface;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\Common\Collections\ArrayCollection;

#[Entity, Table('hospitals')]
#[HasLifecycleCallbacks]
class Hospital implements HospitalInterface
{
    #[Id, Column(options: ['unsigned' => true]), GeneratedValue]
    private int $id;

    #[Column]
    private string $name;

    #[Column]
    private string $password;

    #[Column]
    private string $email;

    #[Column(name: 'contact_number')]
    private string $contactNumber;

    #[Column]
    private string $filename;

    #[Column(name: 'hospital_filename')]
    private string $hospitalFilename;

    #[Column]
    private string $address;

    #[Column(name: 'storage_filename')]
    private string $storageFilename;

    #[Column(name: 'hospital_storage_filename')]
    private string $hospitalStorageFilename;
    
    #[Column(options: ['default' => '0'])]
    private string $status;

    #[Column(name: 'is_archived', options: ['default' => false])]
    private bool $isArchived;

    #[Column(name: 'created_at')]
    private \DateTime $createdAt;

    #[Column(name: 'approved_at', nullable: true)]
    private ?\DateTime  $approvedAt;

    #[Column(name: 'updated_at')]
    private \DateTime $updatedAt;   
    
    #[Column(name: 'profile_picture', options: ['default' => 'default-picture.jpeg'])]
    private string $profilePicture;

    #[OneToMany(mappedBy: 'hospital', targetEntity: Doctor::class, cascade: ['remove'])]
    private Collection $doctors;

    #[OneToMany(mappedBy: 'hospital', targetEntity: Patient::class, cascade: ['remove'])]
    private Collection $patients;

    #[OneToMany(mappedBy: 'hospital', targetEntity: AdmissionForm::class, cascade: ['remove'])]
    private Collection $admissionForms;

    #[OneToMany(mappedBy: 'hospital', targetEntity: PrenatalCheckup::class, cascade: ['remove'])]
    private Collection $prenatalCheckups;

    #[OneToMany(mappedBy: 'hospital', targetEntity: MedicalCertificate::class, cascade: ['remove'])]
    private Collection $medicalCertificates;

    #[OneToMany(mappedBy: 'hospital', targetEntity: AdmissionFormOld::class, cascade: ['remove'])]
    private Collection $admissionFormOlds;

    #[OneToMany(mappedBy: 'hospital', targetEntity: Referral::class, cascade: ['remove'])]
    private Collection $referrals;
    
    #[OneToMany(mappedBy: 'hospital', targetEntity: HospitalLoginCode::class, cascade: ['remove'])]
    private Collection $hospitalLoginCodes;

    public function __construct()
    {
        $this->doctors = new ArrayCollection();
        $this->patients = new ArrayCollection();
        $this->admissionForms = new ArrayCollection();
        $this->admissionFormOlds = new ArrayCollection();
        $this->prenatalCheckups = new ArrayCollection();
        $this->medicalCertificates = new ArrayCollection();
        $this->referrals = new ArrayCollection();
        $this->profilePicture = 'default-picture.jpeg';
        $this->status = '0';
        $this->isArchived = false;
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


    public function getName(): string
    {
        return $this->name;
    }


    public function setName(string $name): Hospital
    {
        $this->name = $name;

        return $this;
    }


    public function getPassword(): string
    {
        return $this->password;
    }


    public function setPassword(string $password): Hospital
    {
        $this->password = $password;

        return $this;
    }


    public function getEmail(): string
    {
        return $this->email;
    }


    public function setEmail(string $email): Hospital
    {
        $this->email = $email;

        return $this;
    }


    public function getContactNumber(): string
    {
        return $this->contactNumber;
    }


    public function setContactNumber(string $contactNumber): Hospital
    {
        $this->contactNumber = $contactNumber;

        return $this;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }


    public function setFilename(string $filename): Hospital
    {
        $this->filename = $filename;

        return $this;
    }


    public function getStorageFilename(): string
    {
        return $this->storageFilename;
    }


    public function setStorageFilename(string $storageFilename): Hospital
    {
        $this->storageFilename = $storageFilename;

        return $this;
    }


    public function getDoctors(): ArrayCollection|Collection
    {
        return $this->doctors;
    }


    public function addDoctor(Doctor $doctor): Hospital
    {
        $this->doctors->add($doctor);

        return $this;
    }

 
    public function getPatients(): ArrayCollection|Collection
    {
        return $this->patients;
    }

    public function addPatient(Patient $patient): Hospital
    {
        $this->patients->add($patient);

        return $this;
    }
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): Hospital
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): Hospital
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }


    public function setAddress(string $address): Hospital
    {
        $this->address = $address;

        return $this;
    }

    public function getApprovedAt(): ?\DateTime
    {
        return $this->approvedAt;
    }


    public function setApprovedAt(\DateTime $approvedAt): Hospital
    {
        $this->approvedAt = $approvedAt;

        return $this;
    }

    public function getHospitalFilename(): string
    {
        return $this->hospitalFilename;
    }


    public function setHospitalFilename(string $hospitalFilename): Hospital
    {
        $this->hospitalFilename = $hospitalFilename;

        return $this;
    }

    public function getHospitalStorageFilename(): string
    {
        return $this->hospitalStorageFilename;
    }


    public function setHospitalStorageFilename(string $hospitalStorageFilename): Hospital
    {
        $this->hospitalStorageFilename = $hospitalStorageFilename;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }


    public function setStatus(string $status): Hospital
    {
        $this->status = $status;

        return $this;
    }

    public function getIsArchived(): bool
    {
        return $this->isArchived;
    }

 
    public function setIsArchived(bool $isArchived): Hospital
    {
        $this->isArchived = $isArchived;

        return $this;
    }

    public function getProfilePicture(): string
    {
        return $this->profilePicture;
    }


    public function setProfilePicture(string $profilePicture): Hospital
    {
        $this->profilePicture = $profilePicture;

        return $this;
    }

    public function getAdmissionForms(): ArrayCollection|Collection
    {
        return $this->admissionForms;
    }


    public function addAdmissionForm(AdmissionForm $admissionForm): Hospital
    {
        $this->admissionForms->add($admissionForm);

        return $this;
    }

    /**
     * Get the value of admissionForm
     */ 
    public function getPrenatalCheckups(): ArrayCollection|Collection
    {
        return $this->prenatalCheckups;
    }


    public function addPrenatalCheckup(PrenatalCheckup $prenatalCheckup): Hospital
    {
        $this->prenatalCheckups->add($prenatalCheckup);

        return $this;
    }

    /**
     * Get the value of medicalcertificate
     */ 
    public function getMedicalCertificates(): ArrayCollection|Collection
    {
        return $this->medicalCertificates;
    }


    public function addMedicalCertificate(MedicalCertificate $medicalCertificate): Hospital
    {
        $this->medicalCertificates->add($medicalCertificate);

        return $this;
    }


    public function getAdmissionFormOlds(): ArrayCollection|Collection
    {
        return $this->admissionFormOlds;
    }


    public function addAdmissionFormOld(AdmissionFormOld $admissionFormOld): Hospital
    {
        $this->admissionFormOlds->add($admissionFormOld);

        return $this;
    }

    public function getRefferals(): ArrayCollection|Collection
    {
        return $this->refferals;
    }

    public function addReferral(Referral $referral): Hospital
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