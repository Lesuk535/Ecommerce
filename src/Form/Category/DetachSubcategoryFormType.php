<?php

declare(strict_types=1);

namespace App\Form\Category;

use App\Model\Category\Message\Command\DetachSubcategories;
use App\ReadModel\Category\CategoryFetcher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DetachSubcategoryFormType extends AbstractType
{
    private CategoryFetcher $fetcher;

    public function __construct(CategoryFetcher $fetcher)
    {
        $this->fetcher = $fetcher;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('children', ChoiceType::class, [
                'choices' => array_flip($this->fetcher->findByParent($options['data']->id)),
                'expanded' => true,
                'multiple' => true,
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DetachSubcategories::class
        ]);
    }
}