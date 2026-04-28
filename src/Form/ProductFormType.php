<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProductFormType extends AbstractType
{
    public const SIZES = [
        'XS' => 'XS',
        'S' => 'S',
        'M' => 'M',
        'L' => 'L',
        'XL' => 'XL',
        'XXL' => 'XXL',
        'Taille unique' => 'Taille unique',
    ];

    public const COLORS = [
        'Noir' => 'Noir',
        'Blanc' => 'Blanc',
        'Gris' => 'Gris',
        'Beige' => 'Beige',
        'Crème' => 'Crème',
        'Nude' => 'Nude',
        'Marron' => 'Marron',
        'Rose' => 'Rose',
        'Rouge' => 'Rouge',
        'Bordeaux' => 'Bordeaux',
        'Orange' => 'Orange',
        'Corail' => 'Corail',
        'Jaune' => 'Jaune',
        'Doré' => 'Doré',
        'Vert' => 'Vert',
        'Kaki' => 'Kaki',
        'Bleu' => 'Bleu',
        'Turquoise' => 'Turquoise',
        'Violet' => 'Violet',
        'Argenté' => 'Argenté',
    ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du produit',
            ])
            ->add('description', TextareaType::class, [
                'label'    => 'Description',
                'required' => false,
            ])
            ->add('price', NumberType::class, [
                'label' => 'Prix (€)',
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Catégorie',
                'placeholder' => 'Choisir une catégorie',
            ])
            ->add('sizes', ChoiceType::class, [
                'label' => 'Tailles disponibles',
                'choices' => self::SIZES,
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ])
            ->add('colors', ChoiceType::class, [
                'label' => 'Couleurs disponibles',
                'choices' => self::COLORS,
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ])
            ->add('imageFile', FileType::class, [
                'label'    => 'Image du produit',
                'mapped'   => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize'   => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                        'mimeTypesMessage' => 'Formats acceptés : JPG, PNG, WEBP',
                    ])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}