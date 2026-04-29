<?php

namespace App\Form;

use App\Entity\Invoice;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name' , null , [
                'required' => true,
                'label' => "Nom du produit",
                'attr' => [
                    'placeholder' => "Ex : Clé USB 16GB"
                ],
            ])
            ->add('description' , null , [
                'required' => false,
                'label' => "Description",
                'attr' => [
                    'placeholder' => "Ex : Clé USB 16GB de marque SanDisk"
                ],
            ])
            ->add('price', null, [
                'required' => true,
                'label' => "Prix",
                'attr' => [
                    'placeholder' => "Ex : 19.99"
                ]
            ])
            ->add('unit', null, [
                'required' => true,
                'label' => "Unité",
                
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
