<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use Pidia\Apps\Demo\Entity\Config;
use Pidia\Apps\Demo\Entity\Usuario;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Ulid;

trait EntityTrait
{
    #[Column(type: UlidType::NAME, unique: true)]
    protected ?Ulid $uuid;

    #[ManyToOne(targetEntity: Usuario::class)]
    #[JoinColumn(nullable: true)]
    protected ?Usuario $owner = null;

    #[ManyToOne(targetEntity: Config::class)]
    #[JoinColumn(nullable: true)]
    protected ?Config $config = null;

    #[Column(name: 'created_at', type: Types::DATETIME_MUTABLE)]
    protected ?\DateTimeInterface $createdAt = null;

    #[Column(name: 'updated_at', type: Types::DATETIME_MUTABLE)]
    protected ?\DateTimeInterface $updatedAt = null;

    #[Column(type: 'boolean')]
    protected bool $isActive = true;

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function changeActive(): bool
    {
        $state = $this->isActive;
        $this->isActive = !$state;

        return $state;
    }

    public function createdAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function updatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function owner(): ?Usuario
    {
        return $this->owner;
    }

    public function setOwner(Usuario|UserInterface|null $owner): self
    {
        if (null !== $owner) {
            $this->owner = $owner;
            $this->setConfig($owner->config());
        }

        return $this;
    }

    public function uuid(): ?Ulid
    {
        return $this->uuid;
    }

    public function config(): ?Config
    {
        return $this->config;
    }

    public function setConfig(?Config $config): self
    {
        $this->config = $config;

        return $this;
    }

    #[PrePersist]
    public function init(): void
    {
        $this->uuid = new Ulid();
    }

    #[PrePersist, PreUpdate]
    public function updatedDatetime(): void
    {
        $this->updatedAt = new \DateTime();
        if (null === $this->createdAt) {
            $this->createdAt = new \DateTime();
        }
    }
}
