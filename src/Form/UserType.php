<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('cgv' , TextareaType::class , [
                'required' => false,
                'label' => "Conditions générales de vente",
                'attr' => [
                    'placeholder' => "Ex : Acceptation des conditions générales de vente."
                ],
            ])
            ->add('company_name' , null, [
                'required' => true,
                'label' => "Raison sociale",
                'attr' => [
                    'placeholder' => "Ex : Airbus"
                ],
                'constraints' => [
                    new NotBlank(
                        message: 'Veuillez entrer une raison sociale',
                    ),
                ]
            ])
            ->add('iban', null, [
                'required' => true,
                'label' => "IBAN",
                'attr' => [
                    'placeholder' => "Ex : FR76 3000 6000 0123 4567 8901 234"
                ],
                'constraints' => [
                    new NotBlank(
                        message: 'Veuillez entrer un IBAN',
                    ),
                    new Length(
                        min: 27,
                        max: 34,
                        minMessage: 'Votre IBAN doit faire au moins {{ limit }} caractères',
                        maxMessage: 'Votre IBAN ne peut pas faire plus de {{ limit }} caractères',
                    ),
                ]
            ])
            ->add('siret', null, [
                'label' => "SIRET",
                'attr' => [
                    'placeholder' => "Ex : 123 456 789 00012"
                ],
                'required' => false,
                'constraints' => [
                    new Length(
                        min: 14,
                        max: 14,
                        exactMessage: 'Votre SIRET doit faire exactement {{ limit }} caractères',
                    ),
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
