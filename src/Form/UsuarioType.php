<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Form;

use CarlosChininin\App\Infrastructure\Security\Security;
use CarlosChininin\AttachFile\Form\AttachFileRender;
use CarlosChininin\AttachFile\Form\AttachFileType;
use Doctrine\ORM\EntityRepository;
use Pidia\Apps\Demo\Entity\Config;
use Pidia\Apps\Demo\Entity\Usuario;
use Pidia\Apps\Demo\Entity\UsuarioRol;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
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
        $usuarioId = $builder->getData()->getId(0);

        $builder
            ->add('fullName', TextType::class)
            ->add('username', TextType::class)
            ->add('email', EmailType::class)
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Las contraseñas no coinciden',
                'required' => !$usuarioId,
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
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
                'render' => AttachFileRender::IMAGE,
            ]);

        if ($this->security->isSuperAdmin()) {
            $builder->add('config', EntityType::class, [
                'class' => Config::class,
                'required' => false,
                'placeholder' => 'Seleccione ...',
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
