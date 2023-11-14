<?php

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Entity;

use CarlosChininin\App\Domain\Model\AuthRole\AuthRole;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\Table;
use Pidia\Apps\Demo\Entity\Traits\EntityTrait;
use Pidia\Apps\Demo\Repository\UsuarioRolRepository;

#[Entity(repositoryClass: UsuarioRolRepository::class)]
#[Table(name: 'core_usuario_rol')]
#[HasLifecycleCallbacks]
class UsuarioRol extends AuthRole
{
    use EntityTrait;

    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer', options: ['unsigned' => true])]
    private ?int $id = null;

    #[Column(type: 'string', length: 50)]
    private ?string $name;

    #[Column(type: 'string', length: 30)]
    private ?string $rol;

    #[ManyToMany(targetEntity: Usuario::class, mappedBy: 'usuarioRoles')]
    private Collection $users;

    #[Column(type: 'menu_permission_json', nullable: true)]
    private ?array $permissions = [];

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = trim($name);
    }

    public function getRol(): ?string
    {
        return $this->rol;
    }

    public function setRol(?string $rol): self
    {
        $this->rol = mb_strtoupper(trim($rol));

        return $this;
    }

    /**
     * @return Collection|Usuario[]
     */
    public function getUsers(): Collection|array
    {
        return $this->users;
    }

    public function addUser(Usuario $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }

        return $this;
    }

    public function removeUser(Usuario $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
        }

        return $this;
    }

    public function getPermissions(): ?array
    {
        return $this->permissions;
    }

    public function setPermissions(?array $permissions): self
    {
        $this->permissions = $permissions;

        return $this;
    }

    public function permissions(): array
    {
        return $this->getPermissions();
    }

    public function role(): string
    {
        return $this->getRol();
    }
}
