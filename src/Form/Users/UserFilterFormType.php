<?php

declare(strict_types=1);

namespace App\Form\Users;

use App\Model\User\Domain\ValueObject\Role;
use App\Model\User\Domain\ValueObject\Status;
use App\ReadModel\User\DTO\UserFilter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserFilterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Name'
                ]
            ])
            ->add('email', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Email'
                ]
            ])
            ->add('status', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'Active' => Status::STATUS_ACTIVE,
                    'Wait' => Status::STATUS_WAIT,
                    'Banned' => Status::STATUS_BAN
                ],
                'placeholder' => 'All status'
            ])
            ->add('role', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'User' => Role::USER,
                    'Admin' => Role::ADMIN
                ],
                'placeholder' => 'All roles'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserFilter::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }
}