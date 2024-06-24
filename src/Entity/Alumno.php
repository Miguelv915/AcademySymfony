<?php

namespace Pidia\Apps\Demo\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Pidia\Apps\Demo\Entity\Traits\EntityTrait;

use Pidia\Apps\Demo\Repository\AlumnoRepository;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;

#[ORM\Entity(repositoryClass: AlumnoRepository::class)]
#[HasLifecycleCallbacks]
class Alumno
{
    use EntityTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    private ?string $apellido = null;

    #[ORM\Column(length: 255)]
    private ?string $direccion = null;

    #[ORM\Column(length: 9)]
    private ?string $telefono = null;

    #[ORM\Column(length: 255)]
    private ?string $nombreApoderado = null;

    #[ORM\Column(length: 255)]
    private ?string $apellidoApoderado = null;

    #[ORM\Column(length: 9)]
    private ?string $telefonoApoderado = null;

    /**
     * @var Collection<int, Matricula>
     */
    #[ORM\OneToMany(targetEntity: Matricula::class, mappedBy: 'alumno', orphanRemoval: true)]
    private Collection $matricula;

    // permite llamar al modelo por su nombre 
    public function __construct()
    {
        $this->matricula = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->nombre;
    }

    public function getId(): ?int
    {
        return $this->id; 
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getApellido(): ?string
    {
        return $this->apellido;
    }

    public function getNombreApellido(): ?string
    {
        
        return $this->apellido. " ". $this->nombre;
    }
    
    public function setApellido(string $apellido): static
    {
        $this->apellido = $apellido;

        return $this;
    }

    public function getDireccion(): ?string
    {
        return $this->direccion;
    }

    public function setDireccion(string $direccion): static
    {
        $this->direccion = $direccion;

        return $this;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(string $telefono): static
    {
        $this->telefono = $telefono;

        return $this;
    }

    public function getNombreApoderado(): ?string
    {
        return $this->nombreApoderado;
    }

    public function setNombreApoderado(string $nombreApoderado): static
    {
        $this->nombreApoderado = $nombreApoderado;

        return $this;
    }

    public function getApellidoApoderado(): ?string
    {
        return $this->apellidoApoderado;
    }

    public function setApellidoApoderado(string $apellidoApoderado): static
    {
        $this->apellidoApoderado = $apellidoApoderado;

        return $this;
    }

    public function getTelefonoApoderado(): ?string
    {
        return $this->telefonoApoderado;
    }

    public function setTelefonoApoderado(string $telefonoApoderado): static
    {
        $this->telefonoApoderado = $telefonoApoderado;

        return $this;
    }

    /**
     * @return Collection<int, Matricula>
     */
    public function getMatricula(): Collection
    {
        return $this->matricula;
    }

    public function addMatricula(Matricula $matricula): static
    {
        if (!$this->matricula->contains($matricula)) {
            $this->matricula->add($matricula);
            $matricula->setAlumno($this);
        }

        return $this;
    }

    public function removeMatricula(Matricula $matricula): static
    {
        if ($this->matricula->removeElement($matricula)) {
            // set the owning side to null (unless already changed)
            if ($matricula->getAlumno() === $this) {
                $matricula->setAlumno(null);
            }
        }

        return $this;
    }
}
