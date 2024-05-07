<?php

declare(strict_types = 1);

namespace App\Entity;

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

#[Entity, Table('prescriptions')]
#[HasLifecycleCallbacks]
class Prescription
{
    #[Id, Column(options: ['unsigned' => true]), GeneratedValue]
    private int $id;

    #[Column(name: 'storage_filename')]
    private string $storageFilename;

    #[Column(name: 'created_at')]
    private \DateTime $createdAt;

    #[OneToOne(inversedBy: 'prescription')]
    private PrenatalCheckup $prenatalCheckup;

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

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): Prescription
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getStorageFilename(): string
    {
        return $this->storageFilename;
    }

    public function setStorageFilename(string $storageFilename): Prescription
    {
        $this->storageFilename = $storageFilename;

        return $this;
    }

    /**
     * Get the value of prenatalCheckup
     */ 
    public function getPrenatalCheckup(): PrenatalCheckup
    {
        return $this->prenatalCheckup;
    }

    /**
     * Set the value of prenatalCheckup
     *
     * @return  self
     */ 
    public function setPrenatalCheckup(PrenatalCheckup $prenatalCheckup): Prescription
    {
        $this->prenatalCheckup = $prenatalCheckup;

        return $this;
    }
}
