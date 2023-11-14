<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Form;

use CarlosChininin\App\Infrastructure\Form\Type\AttachedFileFormType;
use CarlosChininin\App\Infrastructure\Security\Security;
use Doctrine\ORM\EntityRepository;
use Pidia\Apps\Demo\Entity\Config;
use Pidia\Apps\Demo\Entity\Usuario;
use Pidia\Apps\Demo\Entity\UsuarioRol;
use Pidia\Apps\Demo\Form\Type\AttachFileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UsuarioType extends AbstractType
{
    public function __construct(
        private readonly Security $security
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullName', TextType::class)
            ->add('username', TextType::class)
            ->add('email', EmailType::class)
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Las contraseñas no coinciden',
                'required' => false,
                'first_options' => ['label' => 'Contraseña'],
                'second_options' => ['label' => 'Repetir contraseña'],
            ])
            ->add('usuarioRoles', EntityType::class, [
                'class' => UsuarioRol::class,
                'multiple' => true,
                'query_builder' => function (EntityRepository $er) {
                    $queryBuilder = $er->createQueryBuilder('r')
                        ->where('r.isActive = TRUE')
                        ->orderBy('r.name', 'ASC');
                    if (!$this->security->isSuperAdmin()) {
                        $queryBuilder
                            ->andWhere('r.rol <> :role_super_admin')
                            ->setParameter('role_super_admin', Security::ROLE_SUPER_ADMIN);
                    }

                    return $queryBuilder;
                },
            ])
            ->add('photo', AttachFileType::class, [
                'required' => false,
            ]);

        if ($this->security->isSuperAdmin()) {
            $builder->add('config', EntityType::class, [
                'class' => Config::class,
                'required' => false,
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
            'data_class' => Usuario::class,
        ]);
    }
}
