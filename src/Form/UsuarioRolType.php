<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Form;

use CarlosChininin\App\Infrastructure\Form\CollectionFormType;
use CarlosChininin\App\Infrastructure\Security\Form\MenuPermissionType;
use Doctrine\ORM\EntityRepository;
use Pidia\Apps\Demo\Entity\Config;
use Pidia\Apps\Demo\Entity\UsuarioRol;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UsuarioRolType extends AbstractType
{
    public function __construct(
        private readonly Security $security
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('permissions', CollectionFormType::class, [
                'required' => false,
                'label' => 'Permisos',
                'entry_type' => MenuPermissionType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ]);

        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            $builder
                ->add('rol', TextType::class, [
                    'required' => false,
                ])
                ->add('config', EntityType::class, [
                    'class' => Config::class,
                    'required' => true,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('config')
                            ->where('config.isActive = TRUE')
                            ->orderBy('config.alias', 'ASC');
                    },
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UsuarioRol::class,
        ]);
    }
}
