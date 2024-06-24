<?php

namespace Pidia\Apps\Demo\Form;

use Pidia\Apps\Demo\Entity\Alumno;
use Pidia\Apps\Demo\Entity\Ciclo;
use Pidia\Apps\Demo\Entity\Config;
use Pidia\Apps\Demo\Entity\Matricula;
use Pidia\Apps\Demo\Entity\Usuario;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MatriculaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('descuento')
            ->add('material')
            ->add('deuda')
            // ->add('uuid')
            // ->add('createdAt', null, [
            //     'widget' => 'single_text'
            // ])
            // ->add('updatedAt', null, [
            //     'widget' => 'single_text'
            // ])
            // ->add('isActive')
            ->add('alumno', EntityType::class, [
                'class' => Alumno::class,
                'choice_label' => 'nombre',
            ])
            ->add('ciclo', EntityType::class, [
                'class' => Ciclo::class,
                'choice_label' => 'categoria',
            ])
            // ->add('owner', EntityType::class, [
            //     'class' => Usuario::class,
            //     'choice_label' => 'id',
            // ])
            // ->add('config', EntityType::class, [
            //     'class' => Config::class,
            //     'choice_label' => 'id',
            // ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Matricula::class,
        ]);
    }
}
