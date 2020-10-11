<?php

declare(strict_types=1);

namespace App\Form\Profile;

use App\Model\User\Message\Command\Profile\EditProfile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class ProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EditProfile::class
        ]);
    }
}