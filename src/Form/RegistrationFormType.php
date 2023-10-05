<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('pseudo', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false, // le champs ne sera pas stocké en BDD
                'label' => "Cochez cette case pour accepter les conditions d'utilisations",
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez acceptez les conditions.', // message d'erreur si la case n'est pas cochée
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                "mapped" => false, // le champs ne sera pas stocké en BDD
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passes doivent être identiques.', // message d'erreur si les mdp correspondents pas
                'options' => ['attr' => ['class' => 'password-field, form-control']],
                'required' => true, // le mdp est obligatoire
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmation du mot de passe'],
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
