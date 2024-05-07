<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use App\Contracts\AdminInterface;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\PreUpdate;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\Common\Collections\ArrayCollection;

#[Entity, Table('admins')]
#[HasLifecycleCallbacks]
class Admin implements AdminInterface
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
    private string $address;

    #[Column]
    private string $email;

    #[Column(name: 'contact_number')]
    private string $contactNumber;

    #[Column]
    private string $filename;

    #[Column(name: 'storage_filename')]
    private string $storageFilename;

    #[Column(name: 'is_head_admin', options: ['default' => false])]
    private bool $isHeadAdmin;

    #[Column(name: 'profile_picture', options: ['default' => 'default-picture.jpg'])]
    private string $profilePicture;

    #[Column(name: 'created_at', nullable: true)]
    private \DateTime $createdAt;

    #[Column(name: 'updated_at', nullable: true)]
    private \DateTime $updatedAt; 

    public function __construct() {
         $this->isHeadAdmin = false;
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


    public function getId(): int
    {
        return $this->id;
    }


    public function getName(): string
    {
        return $this->name;
    }


    public function setName($name): Admin
    {
        $this->name = $name;

        return $this;
    }


    public function getPassword(): string
    {
        return $this->password;
    }



    public function setPassword($password): Admin
    {
        $this->password = $password;

        return $this;
    }


    public function getBirthdate(): \DateTime
    {
        return $this->birthdate;
    }


    public function setBirthdate($birthdate): Admin
    {
        $this->birthdate = $birthdate;

        return $this;
    }

 
    public function getGender(): String
    {
        return $this->gender;
    }


    public function setGender($gender): Admin
    {
        $this->gender = $gender;

        return $this;
    }

 
    public function getAddress(): string
    {
        return $this->address;
    }


    public function setAddress($address): Admin
    {
        $this->address = $address;

        return $this;
    }


    public function getEmail(): string 
    {
        return $this->email;
    }


    public function setEmail($email): Admin
    {
        $this->email = $email;

        return $this;
    }

    public function getContactNumber(): string
    {
        return $this->contactNumber;
    }


    public function setContactNumber($contactNumber): Admin
    {
        $this->contactNumber = $contactNumber;

        return $this;
    }


    public function getFilename(): string
    {
        return $this->filename;
    }

  
    public function setFilename($filename): Admin
    {
        $this->filename = $filename;

        return $this;
    }

  
    public function getStorageFilename(): string
    {
        return $this->storageFilename;
    }

 
    public function setStorageFilename($storageFilename): Admin
    {
        $this->storageFilename = $storageFilename;

        return $this;
    }

    /**
     * Get the value of isHeadAdmin
     */ 
    public function getIsHeadAdmin(): bool
    {
        return $this->isHeadAdmin;
    }

    /**
     * Set the value of isHeadAdmin
     *
     * @return  self
     */ 
    public function setIsHeadAdmin(bool $isHeadAdmin): Admin
    {
        $this->isHeadAdmin = $isHeadAdmin;

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
    public function setUpdatedAt(\DateTime $updatedAt): Admin
    {
        $this->updatedAt = $updatedAt;

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
    public function setCreatedAt(\DateTime $createdAt): Admin
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the value of profilePicture
     */ 
    public function getProfilePicture(): string
    {
        return $this->profilePicture;
    }

    /**
     * Set the value of profilePicture
     *
     * @return  self
     */ 
    public function setProfilePicture(string $profilePicture): Admin
    {
        $this->profilePicture = $profilePicture;

        return $this;
    }
    public function hasTwoFactorAuthEnabled(): bool
    {
        // TODO:

        return true;
    }
}