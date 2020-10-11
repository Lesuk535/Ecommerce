<?php

declare(strict_types=1);

namespace App\Form\Category;

use App\Model\Category\Message\Command\CreateCategory;
use App\ReadModel\Category\CategoryFetcher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateCategoryFormType extends AbstractType
{
    private CategoryFetcher $fetcher;

    public function __construct(CategoryFetcher $fetcher)
    {
        $this->fetcher = $fetcher;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'autofocus' => null
                ]
            ])
            ->add('image', FileType::class, ['required' => false])
            ->add('description', TextareaType::class, ['required' => false])
            ->add('children', ChoiceType::class, [
                'choices' => array_flip($this->fetcher->getCategoriesWithoutParent()),
                'expanded' => true,
                'multiple' => true,
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CreateCategory::class
        ]);
    }
}