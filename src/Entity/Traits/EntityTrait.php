<?php

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Entity\Traits;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use Pidia\Apps\Demo\Entity\Config;
use Pidia\Apps\Demo\Entity\Usuario;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

trait EntityTrait
{
    #[Column(name: 'created_at', type: 'datetime')]
    protected ?DateTimeInterface $createdAt = null;

    #[Column(name: 'updated_at', type: 'datetime')]
    protected ?DateTimeInterface $updatedAt = null;
    #[ManyToOne(targetEntity: Usuario::class)]
    #[JoinColumn(nullable: true)]
    private ?Usuario $owner = null;

    #[ManyToOne(targetEntity: Config::class)]
    #[JoinColumn(nullable: true)]
    private ?Config $config = null;

    #[Column(type: 'boolean')]
    #[Groups(groups: 'default')]
    private bool $isActive;

    #[Column(type: 'uuid', unique: true)]
    private ?Uuid $uuid = null;

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    public function changeActive(): bool
    {
        $state = $this->isActive;
        $this->isActive = !$state;

        return $state;
    }

    public function createdAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function updatedAt(): DateTimeInterface
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

    public function uuid(): ?Uuid
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
        $this->uuid = Uuid::v4();
        $this->isActive = true;
    }

    #[PrePersist, PreUpdate]
    public function updatedDatetime(): void
    {
        $this->updatedAt = new DateTime();
        if (null === $this->createdAt) {
            $this->createdAt = new DateTime();
        }
    }
}
