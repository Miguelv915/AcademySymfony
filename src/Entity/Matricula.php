<?php

namespace Pidia\Apps\Demo\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Pidia\Apps\Demo\Repository\MatriculaRepository;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Pidia\Apps\Demo\Entity\Traits\EntityTrait;

#[ORM\Entity(repositoryClass: MatriculaRepository::class)]
#[HasLifecycleCallbacks]
class Matricula
{
    use EntityTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $descuento = null;

    #[ORM\Column]
    private ?bool $material = null;

    #[ORM\ManyToOne(inversedBy: 'matricula')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Alumno $alumno = null;

    #[ORM\ManyToOne(inversedBy: 'matricula')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ciclo $ciclo = null;

    /**
     * @var Collection<int, Pago>
     */
    #[ORM\OneToMany(targetEntity: Pago::class, mappedBy: 'matricula')]
    private Collection $pagos;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $deuda = null;

    public function __construct()
    {
        $this->pagos = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getDeuda();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescuento(): ?string
    {
        return $this->descuento;
    }

    public function setDescuento(string $descuento): static
    {
        $this->descuento = $descuento;

        return $this;
    }

    public function isMaterial(): ?bool
    {
        return $this->material;
    }

    public function setMaterial(bool $material): static
    {
        $this->material = $material;

        return $this;
    }

    public function getAlumno(): ?Alumno
    {
        return $this->alumno;
    }

    public function setAlumno(?Alumno $alumno): static
    {
        $this->alumno = $alumno;

        return $this;
    }

    public function getCiclo(): ?Ciclo
    {
        return $this->ciclo;
    }

    public function setCiclo(?Ciclo $ciclo): static
    {
        $this->ciclo = $ciclo;

        return $this;
    }

    /**
     * @return Collection<int, Pago>
     */
    public function getPagos(): Collection
    {
        return $this->pagos;
    }

    public function addPago(Pago $pago): static
    {
        if (!$this->pagos->contains($pago)) {
            $this->pagos->add($pago);
            $pago->setMatricula($this);
        }

        return $this;
    }

    public function removePago(Pago $pago): static
    {
        if ($this->pagos->removeElement($pago)) {
            // set the owning side to null (unless already changed)
            if ($pago->getMatricula() === $this) {
                $pago->setMatricula(null);
            }
        }

        return $this;
    }

    public function getDeuda(): ?string
    {
        return $this->deuda;
    }

    public function setDeuda(string $deuda): static
    {
        $this->deuda = $deuda;

        return $this;
    }
}
