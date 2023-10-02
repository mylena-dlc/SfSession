<?php

namespace App\Form;

use App\Entity\Module;
use App\Entity\Program;
use App\Entity\Session;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class ProgramType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('session', HiddenType::class) // on cache la session car elle sera automatiquement récupéré dans la session
            ->add('module', EntityType::class, [
                'class' => Module::class,
                'label' => 'Module',
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'btn btn-success'
                ]
            ])            
            ->add('nbDays', IntegerType::class, [
                'label' => 'Nombre de jours',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1,
                    'max' => 100
                ],
                "required" => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Program::class,
        ]);
    }
}
