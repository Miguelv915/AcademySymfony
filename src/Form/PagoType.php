<?php

namespace Pidia\Apps\Demo\Form;

use Doctrine\ORM\EntityRepository;
use Pidia\Apps\Demo\Entity\Config;
use Pidia\Apps\Demo\Entity\Matricula;
use Pidia\Apps\Demo\Entity\Pago;
use Pidia\Apps\Demo\Entity\Usuario;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PagoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fecha', null, [
                'widget' => 'single_text'
            ])
            ->add('monto')
            // ->add('usuario')
            // ->add('matricula', EntityType::class, [
            //     'class' => Matricula::class,
            //     'choice_label' => 'id',
            // ])
            // ->add('uuid')
            // ->add('createdAt', null, [
            //     'widget' => 'single_text'
            // ])
            // ->add('updatedAt', null, [
            //     'widget' => 'single_text'
            // ])
            // ->add('isActive')

            ->add('matricula', EntityType::class, [
                'class' => Matricula::class,
                'choice_label' => function ($matricula) {
                    return $matricula->getAlumno()->getNombreApellido();
                },
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
            'data_class' => Pago::class,
        ]);
    }
}


// $builder
//             ->add('fecha', null, [
//                 'widget' => 'single_text'
//             ])
//             ->add('monto')
//             ->add('isActive')
//             ->add('matricula', EntityType::class, [
//                 'class' => Matricula::class,
//                 'choice_label' => function ($matricula) {
//                     return $matricula->getAlumno()->getNombre();
//                 },
//             ]);

// ->add('matricula', EntityType::class, [
//     'class' => Matricula::class,
//     'choice_label' => 'alumno',
// ])