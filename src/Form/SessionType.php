<?php

namespace App\Form;

use App\Entity\Session;
use App\Entity\Formation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class SessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', TextType::class, [
            'attr' => [
                'class' => 'form-control'
            ],
            "required" => true
        ])
        ->add('startDate', DateType::class, [
            'widget' => 'single_text',
            'attr' => [
                'class' => 'form-control'
            ],
            "required" => true
        ])
        ->add('endDate', DateType::class, [
            'widget' => 'single_text',
            'attr' => [
                'class' => 'form-control'
            ],
            "required" => true
        ])
        ->add('nbPlaces', IntegerType::class, [
            'attr' => [
                'class' => 'form-control'
            ],
            "required" => true
        ])
        ->add('formation', EntityType::class, [
            'class' => Formation::class,
            'attr' => [
                'class' => 'btn btn-success'
            ]
        ])
        ->add('valider', SubmitType::class, [
            'attr' => [
                'class' => 'btn btn-success'
            ]
        ])
    ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Session::class,
        ]);
    }
}
