<?php

declare(strict_types=1);

namespace App\Form\Users;

use App\Model\User\Message\Command\User\EditUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $roles = [
            'Admin' => 'ROLE_ADMIN',
            'User' => 'ROLE_USER'
        ];

        $activeRole = substr($options['data']->role, strpos($options['data']->role, '_') + 1);
        $activeRole = ucfirst(mb_strtolower($activeRole));
        unset($roles[$activeRole]);

        $roles = array_merge([$activeRole => $options['data']->role], $roles);

        $builder
            ->add('firstName', TextType::class, [
                'attr' => [
                    'autofocus' => null
                ]
            ])
            ->add('lastName', TextType::class)
            ->add('newEmail', EmailType::class, [
                'required' => false
            ])
            ->add('avatar', FileType::class, [
//                'constraints' => [
//                    new Image()
//                ],
                'required' => false
            ])
            ->add('newRole', ChoiceType::class, [
                'choices' => $roles
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EditUser::class
        ]);
    }
}