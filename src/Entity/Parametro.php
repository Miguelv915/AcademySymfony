<?php

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Pidia\Apps\Demo\Entity\Traits\EntityTrait;
use Pidia\Apps\Demo\Repository\ParametroRepository;
use Symfony\Component\Validator\Constraints\Length;

#[Entity(repositoryClass: ParametroRepository::class)]
#[Table(name: 'core_parametro')]
#[HasLifecycleCallbacks]
class Parametro
{
    use EntityTrait;

    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer', options: ['unsigned' => true])]
    private ?int $id = null;

    #[Column(type: 'string', length: 100)]
    private ?string $name;

    #[Column(type: 'string', length: 10, nullable: true)]
    #[Length(max: 10, maxMessage: 'Debe tener un máximo de 10 carácteres')]
    private ?string $alias;

    #[Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?string $value;

    #[ManyToOne(targetEntity: self::class)]
    private ?Parametro $parent;

    public function __toString(): string
    {
        return $this->getName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(string $alias): self
    {
        $this->alias = $alias; // Generator::withoutWhiteSpaces($alias);

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public static function namesToArray(Collection $parametros): array
    {
        $names = [];

        /** @var Parametro $parametro */
        foreach ($parametros as $parametro) {
            $names[] = $parametro->getName();
        }

        return $names;
    }
}
