<?php

namespace Pidia\Apps\Demo\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Pidia\Apps\Demo\Repository\PagoRepository;
use Pidia\Apps\Demo\Entity\Traits\EntityTrait;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;  
 
#[ORM\Entity(repositoryClass: PagoRepository::class)]
#[HasLifecycleCallbacks]
class Pago
{
    use EntityTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $monto = null;

    #[ORM\ManyToOne(inversedBy: 'pagos')]
    private ?Matricula $matricula = null;

    #[ORM\ManyToOne(cascade: ['persist', 'remove'])]
    private ?Usuario $usuario = null;
    public function __construct()
    {
        $this->fecha = new \DateTime();
    }

  

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): static
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getMonto(): ?string
    {
        return $this->monto;
    }

    public function setMonto(string $monto): static
    {
        $this->monto = $monto;

        return $this;
    }

    public function getMatricula(): ?Matricula
    {
        return $this->matricula;
    }

    public function setMatricula(?Matricula $matricula): static
    {
        $this->matricula = $matricula;

        return $this;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): static
    {
        $this->usuario = $usuario;

        return $this;
    }
}
