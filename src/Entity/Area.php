<?php

namespace Pidia\Apps\Demo\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Pidia\Apps\Demo\Entity\Traits\EntityTrait;
use Pidia\Apps\Demo\Repository\AreaRepository;
use Pidia\Apps\Demo\Repository\ParametroRepository;

// #[Entity(repositoryClass: ParametroRepository::class)]
#[ORM\Entity(repositoryClass: AreaRepository::class)]
#[HasLifecycleCallbacks]
class Area
{
    use EntityTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nombre = null;

    /**
     * @var Collection<int, Ciclo>
     */
    #[ORM\OneToMany(targetEntity: Ciclo::class, mappedBy: 'categoria')]
    private Collection $ciclos;

    public function __construct()
    {
        $this->ciclos = new ArrayCollection();
    }

    /**
     * el metodo ToString ayuda accader a las relaciones
     */

    public function __toString()
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

    /**
     * @return Collection<int, Ciclo>
     */
    public function getCiclos(): Collection
    {
        return $this->ciclos;
    }

    public function addCiclo(Ciclo $ciclo): static
    {
        if (!$this->ciclos->contains($ciclo)) {
            $this->ciclos->add($ciclo);
            $ciclo->setCategoria($this);
        }

        return $this;
    }

    public function removeCiclo(Ciclo $ciclo): static
    {
        if ($this->ciclos->removeElement($ciclo)) {
            // set the owning side to null (unless already changed)
            if ($ciclo->getCategoria() === $this) {
                $ciclo->setCategoria(null);
            }
        }

        return $this;
    }
}
