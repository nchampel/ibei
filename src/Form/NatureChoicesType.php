<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NatureChoicesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $base = ["sora" => "Sora : Peuple altruiste",
        "nano" => "Nano : Tribu ingénieuse",
        "altheron" => "Althéron : Civilisation pacisfiste",
        "sotoc" => "Sotoc : La confrérie des braves",
        "flumia" => "Flumia : La communauté des respectueux"];
        
        $keysToFilter = $options['choices'];

        // Recouper les deux tableaux pour garder les clés qui sont dans $keysToFilter
        $filteredChoices = array_intersect_key($base, array_flip($keysToFilter));

        $builder
            ->add('nature', ChoiceType::class, [
                'label' => 'Choisissez votre alignement',
                'choices' => array_flip($filteredChoices),
                'expanded' => false, // => dropdown (select)
                'multiple' => false,
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // L’entité associée ici si tu en as une
            'data_class' => null,
            'choices' => null
        ]);
    }
}
