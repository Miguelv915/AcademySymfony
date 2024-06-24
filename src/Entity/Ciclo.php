<?php

namespace Pidia\Apps\Demo\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Pidia\Apps\Demo\Repository\CicloRepository;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Pidia\Apps\Demo\Entity\Traits\EntityTrait;

#[ORM\Entity(repositoryClass: CicloRepository::class)]
#[HasLifecycleCallbacks]
class Ciclo
{
    use EntityTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $fechainicio = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $precio = null;

    #[ORM\ManyToOne(inversedBy: 'ciclos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Area $categoria = null;

    /**
     * @var Collection<int, Matricula>
     */
    #[ORM\OneToMany(targetEntity: Matricula::class, mappedBy: 'ciclo')]
    private Collection $matricula;

    public function __construct()
    {
        $this->matricula = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFechainicio(): ?\DateTimeInterface
    {
        return $this->fechainicio;
    }

    public function setFechainicio(\DateTimeInterface $fechainicio): static
    {
        $this->fechainicio = $fechainicio;

        return $this;
    }

    public function getPrecio(): ?string
    {
        return $this->precio;
    }

    public function setPrecio(string $precio): static
    {
        $this->precio = $precio;

        return $this;
    }

    public function getCategoria(): ?Area
    {
        return $this->categoria;
    }

    public function setCategoria(?Area $categoria): static
    {
        $this->categoria = $categoria;

        return $this;
    }

    /**
     * @return Collection<int, Matricula>
     */
    public function getCicloAlumno(): Collection
    {
        return $this->matricula;
    }

    public function addCicloAlumno(Matricula $matricula): static
    {
        if (!$this->matricula->contains($matricula)) {
            $this->matricula->add($matricula);
            $matricula->setCiclo($this);
        }

        return $this;
    }

    public function removeCicloAlumno(Matricula $matricula): static
    {
        if ($this->matricula->removeElement($matricula)) {
            // set the owning side to null (unless already changed)
            if ($matricula->getCiclo() === $this) {
                $matricula->setCiclo(null);
            }
        }

        return $this;
    }
}
