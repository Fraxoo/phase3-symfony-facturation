<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email' , null, [
                'label' => "Adresse Email",
                'attr' => [
                    'placeholder' => "Ex : john.doe@example.com"
                ]
            ])
            ->add('first_name' , null, [
                'label' => "Prénom",
                'attr' => [
                    'placeholder' => "Prénom"
                ]
            ])
            ->add('last_name' , null, [
                'label' => "Nom",
                'attr' => [
                    'placeholder' => "Nom"
                ]
            ])
            ->add('company_name' , null, [
                'label' => "Raison sociale",
                'attr' => [
                    'placeholder' => "Ex : Airbus"
                ]
            ])
            ->add('iban' , null, [
                'label' => "IBAN",
                'attr' => [
                    'placeholder' => "Ex : FR76 3000 6000 0112 3456 7890 189"
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
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'label' => "Mot de passe",
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password' , 'placeholder' => "******"],
                'constraints' => [
                    new NotBlank(
                        message: 'Veuillez entrer un mot de passe',
                    ),
                    new Length(
                        min: 6,
                        minMessage: 'Votre mot de passe doit faire au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        max: 4096,
                    ),
                ],
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
