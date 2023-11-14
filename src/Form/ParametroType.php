<?php

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Form;

use Doctrine\ORM\EntityRepository;
use Pidia\Apps\Demo\Entity\Parametro;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParametroType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('parent', EntityType::class, [
                'class' => Parametro::class,
                'required' => false,
                'placeholder' => 'Seleccione ...',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('parametro')
                        ->leftJoin('parametro.parent', 'parent')
                        ->where('parametro.isActive = TRUE')
                        ->orderBy('parametro.id', 'DESC');
                },
            ])
            ->add('name')
            ->add('alias')
            ->add('value');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Parametro::class,
        ]);
    }
}
