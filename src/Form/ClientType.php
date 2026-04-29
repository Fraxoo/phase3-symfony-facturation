<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'required' => true,
                'label' => "Nom du client",
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'label' => "Email",
            ])
            ->add('phone', TelType::class, [
                'attr' => [
                    'pattern' => '^\+?[0-9\s\-]{10,15}$'
                ],
                'required' => true,
                'label' => "Téléphone",
            ])
            ->add('adress', null, [
                'required' => true,
                'label' => "Adresse",
            ])
            ->add('siret', null, [
                'required' => false,
                'label' => "SIRET (Optionnel)",
            ])
            ->add('rib', null, [
                'required' => false,
                'label' => "RIB (Optionnel)",
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
