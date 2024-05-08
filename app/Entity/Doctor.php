<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Hospital;
use Doctrine\ORM\Mapping\Id;
use App\Entity\AdmissionForm;
use App\Entity\DoctorLoginCode;
use App\Entity\PrenatalCheckup;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use App\Contracts\DoctorInterface;
use App\Entity\MedicalCertificate;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\PreUpdate;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\Common\Collections\ArrayCollection;

#[Entity, Table('doctors')]
#[HasLifecycleCallbacks]
class Doctor implements DoctorInterface
{
    #[Id, Column(options: ['unsigned' => true]), GeneratedValue]
    private int $id;

    #[Column]
    private string $name;

    #[Column]
    private \DateTime $birthdate;

    #[Column]
    private string $gender;

    #[Column]
    private string $password;

    #[Column]
    private string $email;

    #[Column]
    private string $contact;

    #[Column]
    private string $address;
    
    #[Column(name: 'profile_picture', options: ['default' => 'default-picture.jpg'])]
    private string $profilePicture;

    #[Column(options: ['default' => '0'])]
    private string $status;

    #[Column(name: 'is_archived', options: ['default' => false])]
    private bool $isArchived;

    #[Column(name: 'emp_filename')]
    private string $empFilename;

    #[Column(name: 'id_filename')]
    private string $idFilename;

    #[Column(name: 'storage_emp_filename')]
    private string $storageEmpFilename;

    #[Column(name: 'storage_id_filename')]
    private string $storageIdFilename;

    #[Column(name: 'created_at')]
    private \DateTime $createdAt;

    #[Column(name: 'approved_at', nullable: true)]
    private ?\DateTime  $approvedAt;

    #[Column(name: 'updated_at')]
    private \DateTime $updatedAt;
    
    #[ManyToOne(inversedBy: 'doctors')]
    private Hospital $hospital;

    #[OneToMany(mappedBy: 'doctor', targetEntity: AdmissionForm::class, cascade: ['remove'])]
    private Collection $admissionForms;

    #[OneToMany(mappedBy: 'doctor', targetEntity: MedicalCertificate::class, cascade: ['remove'])]
    private Collection $medicalCertificates;

    #[OneToMany(mappedBy: 'doctor', targetEntity: PrenatalCheckup::class, cascade: ['remove'])]
    private Collection $prenatalCheckups;

    #[OneToMany(mappedBy: 'doctor', targetEntity: DoctorLoginCode::class, cascade: ['remove'])]
    private Collection $doctorLoginCodes;

    public function __construct()
    {
    $this->admissionForms = new ArrayCollection();
    $this->prenatalCheckups = new ArrayCollection();
    $this->medicalCertificates = new ArrayCollection();
    $this->profilePicture = 'default-picture.jpg';
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


    public function setName(string $name): Doctor
    {
        $this->name = $name;

        return $this;
    }


    public function getBirthdate(): \DateTime
    {
        return $this->birthdate;
    }


    public function setBirthdate(\DateTime $birthdate): Doctor
    {
        $this->birthdate = $birthdate;

        return $this;
    }


    public function getGender(): string
    {
        return $this->gender;
    }


    public function setGender(string $gender): Doctor
    {
        $this->gender = $gender;

        return $this;
    }


    public function getHospital(): Hospital
    {
        return $this->hospital;
    }


    public function setHospital(Hospital $hospital): Doctor
    {
        $hospital->addDoctor($this);

        $this->hospital = $hospital;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): Doctor
    {
        $this->email = $email;

        return $this;
    }

    public function getContact(): string
    {
        return $this->contact;
    }


    public function setContact(string $contact): Doctor
    {
        $this->contact = $contact;

        return $this;
    }   

    public function getAddress(): string
    {
        return $this->address;
    }


    public function setAddress(string $address): Doctor
    {
        $this->address = $address;

        return $this;
    }

    public function getEmpFilename(): string
    {
        return $this->empFilename;
    }

    public function setEmpFilename(string $empFilename): Doctor
    {
        $this->empFilename = $empFilename;

        return $this;
    }


    public function getidFilename(): string
    {
        return $this->idFilename;
    }


    public function setIdFilename(string $idFilename): Doctor
    {
        $this->idFilename = $idFilename;

        return $this;
    }

    public function getStorageEmpFilename(): string
    {
        return $this->storageEmpFilename;
    }


    public function setStorageEmpFilename(string $storageEmpFilename): Doctor
    {
        $this->storageEmpFilename = $storageEmpFilename;

        return $this;
    }

    public function getStorageIdFilename(): string
    {
        return $this->storageIdFilename;
    }


    public function setStorageIdFilename(string $storageIdFilename): Doctor
    {
        $this->storageIdFilename = $storageIdFilename;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }


    public function setPassword(string $password): Doctor
    {
        $this->password = $password;

        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }


    public function setCreatedAt(\DateTime $createdAt): Doctor
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getApprovedAt(): ?\DateTime
    {
        return $this->approvedAt;
    }

 
    public function setApprovedAt(\DateTime $approvedAt): Doctor
    {
        $this->approvedAt = $approvedAt;

        return $this;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): Doctor
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }


    public function getStatus(): string
    {
        return $this->status;
    }


    public function setStatus(string $status): Doctor
    {
        $this->status = $status;

        return $this;
    }

    public function getIsArchived(): bool
    {
        return $this->isArchived;
    }


    public function setIsArchived(bool $isArchived): Doctor
    {
        $this->isArchived = $isArchived;

        return $this;
    }

    public function getProfilePicture(): string
    {
        return $this->profilePicture;
    }


    public function setProfilePicture(string $profilePicture): Doctor
    {
        $this->profilePicture = $profilePicture;

        return $this;
    }

    public function getAdmissionForms(): ArrayCollection|Collection
    {
        return $this->admissionForms;
    }

    public function addAdmissionForm(AdmissionForm $admissionForm): Doctor
    {
        $this->admissionForms->add($admissionForm);

        return $this;
    }

    public function getPrenatalCheckups(): ArrayCollection|Collection
    {
        return $this->prenatalCheckups;
    }


    public function addPrenatalCheckup(PrenatalCheckup $prenatalCheckup): Doctor
    {
        $this->prenatalCheckups->add($prenatalCheckup);

        return $this;
    }


    public function getMedicalCertificates(): ArrayCollection|Collection
    {
        return $this->medicalCertificates;
    }


    public function addMedicalCertificate(MedicalCertificate $medicalCertificate): Doctor
    {
        $this->medicalCertificates->add($medicalCertificate);

        return $this;
    }
    public function hasTwoFactorAuthEnabled(): bool
    {
        // TODO:

        return true;
    }
}