<?php

namespace App\Form;

use App\Services\MarketServices;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProductsType extends AbstractType
{

    public function __construct(private MarketServices $marketServices)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Nombre',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('details', TextType::class, [
                'label' => 'Detalles',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('stock', IntegerType::class, [
                'attr' => [
                    'min' => 0,
                    'class' => 'form-control'
                ],
                'label' => 'stock'
            ])
            ->add('picture', FileType::class, [
                'label' => 'imagen',
                'attr' => [
                    'class' => 'custom-file-input'
                ],
                'label_attr' => [
                  'class' => 'custom-file-label'
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpg',
                            'image/jpeg',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF document',
                    ])
            ]
            ])
            ->add('category', ChoiceType::class, [
                'label' => 'Categorías',
                'placeholder' => 'Seleccione categoría',
                'choices' => $this->getCategories(),
                'attr' => [
                    'class' => 'custom-select'
                ]

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }

    private function getCategories(): array
    {
        $categories = $this->marketServices->getCategories();
        $choices = [];
            foreach ($categories as $category) {
                $choices[$category->title] = $category->identifier;
            }
            return $choices;
    }




}
