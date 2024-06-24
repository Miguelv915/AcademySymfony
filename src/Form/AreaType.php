<?php

namespace Pidia\Apps\Demo\Form;

use Pidia\Apps\Demo\Entity\Area;
use Pidia\Apps\Demo\Entity\Config;
use Pidia\Apps\Demo\Entity\Usuario;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AreaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre'); 
            /// datos que se mostraran, columanas de la BD tabla 
            // ->add('uuid')
            // ->add('createdAt', null, [
            //     'widget' => 'single_text'
            // ])
            // ->add('updatedAt', null, [
            //     'widget' => 'single_text'
            // ])
            // ->add('isActive')
            // ->add('owner', EntityType::class, [
            //     'class' => Usuario::class,
            //     'choice_label' => 'id',
            // ])
            // ->add('config', EntityType::class, [
            //     'class' => Config::class,
            //     'choice_label' => 'id',
            // ]);
            // 
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Area::class,
        ]);
    }
}
