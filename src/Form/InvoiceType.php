<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Invoice;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class InvoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('created_at')
            ->add('client_id', EntityType::class, [
                'class' => Client::class,
                'choice_label' => 'name',
            ])
            ->add('total_ttc' , HiddenType::class )
            ->add('invoiceItems', CollectionType::class, [
                'entry_type' => InvoiceItemType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
            ])
            ->add('saveDump', SubmitType::class, [
                'label' => 'Enregistrer en brouillon',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer et valider',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Invoice::class,
        ]);
    }
}
