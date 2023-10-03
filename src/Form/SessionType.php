<?php

namespace App\Form;

use App\Entity\Session;
use App\Entity\Formation;
use App\Form\ProgramType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class SessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', TextType::class, [
            'label' => 'Nom',
            'attr' => [
                'class' => 'form-control'
            ],
            "required" => true
        ])
        ->add('startDate', DateType::class, [
            'label' => 'Date de début',
            'widget' => 'single_text',
            'attr' => [
                'class' => 'form-control'
            ],
            "required" => true
        ])
        ->add('endDate', DateType::class, [
            'label' => 'Date de fin',
            'widget' => 'single_text',
            'attr' => [
                'class' => 'form-control'
            ],
            "required" => true
        ])
        ->add('nbPlaces', IntegerType::class, [
            'label' => 'Nombre de places',
            'attr' => [
                'class' => 'form-control'
            ],
            "required" => true
        ])
        ->add('formation', EntityType::class, [
            'class' => Formation::class,
            'label' => 'Centre de formation',
            'attr' => [
                'class' => 'btn btn-success'
            ]
        ])
        ->add('programs', CollectionType::class, [
            // la collection attend l'élément qui entrera dans le form, ce n'est pas obligatoire que ce soit un autre form
            'entry_type' => ProgramType::class,
            'prototype' => true,
            // on autorise l'ajout de nouveau élément dans l'entité Session qui seront persister grâce au cascade persist sur l'élément program
            // ça va activer un data prototype qui sera un attribut html qu'on pourra manipuler en JS
            'allow_add' => true, // permet d'ajouter des éléments
            'allow_delete' => true, // permet de supprimer des éléments
            'by_reference' => false, // il est obligatoire car Session n'a pas de setProgram mais c'est Program qui contient setSession
            // Program est propriétaire de la relation
            // pour éviter un mapping false, on est obligé de ajouter un by reference false
            ])

        ->add('valider', SubmitType::class, [
            'attr' => [
                'class' => 'btn btn-lg btn-primary submit'
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
