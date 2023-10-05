<?php

namespace App\Form;

use App\Entity\Student;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\Email;

class StudentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('lastname', TextType::class, [
            'label' => 'Nom',
            'attr' => [
                'class' => 'form-control'
            ],
            "required" => true
        ])
        ->add('firstname', TextType::class, [
            'label' => 'Prénom',
            'attr' => [
                'class' => 'form-control'
            ],
            "required" => true
        ])
        ->add('sexe', TextType::class, [
            'attr' => [
                'class' => 'form-control'
            ],
            "required" => true
        ])
        ->add('birthDate', DateType::class, [
            'label' => 'Date de naissance',
            'widget' => 'single_text',
            'attr' => [
                'class' => 'form-control'
            ]
        ])
        ->add('city', TextType::class, [
            'label' => 'Ville',
            'attr' => [
                'class' => 'form-control'
            ]
        ])
        ->add('email', EmailType::class, [
            'attr' => [
                'class' => 'form-control'
            ]
        ])
        ->add('phone', TextType::class, [
            'label' => 'Numéro de téléphone',
            'attr' => [
                'class' => 'form-control'
            ]
        ])
        ->add('valider', SubmitType::class, [
            'attr' => [
                'class' => 'btn btn-lg btn-primary submit'
            ]
        ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Student::class,
        ]);
    }
}
