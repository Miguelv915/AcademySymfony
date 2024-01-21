<?php

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Pidia\Apps\Demo\Repository\ConfigMenuRepository;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\Uid\Ulid;

#[Entity(repositoryClass: ConfigMenuRepository::class)]
#[ORM\Table(name: 'core_config_menu')]
class ConfigMenu
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer', options: ['unsigned' => true])]
    private ?int $id = null;

    #[Column(type: 'string', length: 50)]
    private ?string $name;

    #[Column(type: 'string', length: 255)]
    private ?string $route;

    #[Column(type: 'boolean')]
    private bool $isActive = true;

    #[Column(type: UlidType::NAME)]
    private ?Ulid $uuid;

    public function __construct()
    {
        $this->uuid = new Ulid();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getRoute(): ?string
    {
        return $this->route;
    }

    public function setRoute(string $route): self
    {
        $this->route = $route;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    public function changeActive(): void
    {
        $this->isActive = !$this->isActive();
    }

    public function uuid(): ?Ulid
    {
        return $this->uuid;
    }

    public function __toString(): string
    {
        return $this->getName();
    }
}
