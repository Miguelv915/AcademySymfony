<?php

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Form;

use CarlosChininin\App\Infrastructure\Security\Security;
use CarlosChininin\FileUpload\Form\ImageFileUploadFormType;
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
use Symfony\Component\Validator\Constraints\File;

class UsuarioType extends AbstractType
{
    public function __construct(private Security $security)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
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
                        ->where('r.activo = TRUE')
                        ->orderBy('r.nombre', 'ASC');
                    if (!$this->security->isSuperAdmin()) {
                        $queryBuilder
                            ->andWhere('r.rol <> :role_super_admin')
                            ->setParameter('role_super_admin', Security::ROLE_SUPER_ADMIN)
                        ;
                    }

                    return $queryBuilder;
                },
            ])
            ->add('foto', ImageFileUploadFormType::class, [
                'required' => false,
                'folder' => '/usuarios',
                'validators' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'maxSizeMessage' => 'El archivo es demasiado grande. El tamaño máximo permitido es de 1024Kb',
                        'mimeTypesMessage' => 'Subir imagen en formato png o jpg',
                    ]),
                ],
            ])
        ;

        if ($this->security->isSuperAdmin()) {
            $builder->add('config', EntityType::class, [
                'class' => Config::class,
                'required' => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('config')
                        ->where('config.activo = TRUE')
                        ->orderBy('config.nombreCorto', 'ASC');
                },
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
        ]);
    }
}
