<?php

namespace App\Form;

use App\Enum\QuestionChoicesEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NatureType extends AbstractType
{
    private function shuffle_assoc($list) {
        if (!is_array($list)) return $list;
    
        $keys = array_keys($list);
        shuffle($keys);
    
        $random = [];
        foreach ($keys as $key) {
            $random[$key] = $list[$key];
        }
        return $random;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices1 = QuestionChoicesEnum::QUESTION1;
        $choices2 = QuestionChoicesEnum::QUESTION2;
        $choices3 = QuestionChoicesEnum::QUESTION3;
        $choices4 = QuestionChoicesEnum::QUESTION4;
        $this->shuffle_assoc($choices1);
        $this->shuffle_assoc($choices2);
        $this->shuffle_assoc($choices3);
        $this->shuffle_assoc($choices4);
        $builder
        ->add('question1', ChoiceType::class, [
                'label' => 'Vous voyez un bébé chat.',
                'choices' => array_flip($choices1),
                'expanded' => true,
            ])
            ->add('question2', ChoiceType::class, [
                'label' => "Vous êtes en retard pour un rendez-vous. En cherchant
                          quelqu'un à qui demander votre route, vous voyez une
                          fille qui pleure.",
                'choices' => array_flip($choices2),
                'expanded' => true,
            ])
            ->add('question3', ChoiceType::class, [
                'label' => 'Votre ami veut vous confier un lourd secret le
                          concernant.',
                'choices' => array_flip($choices3),
                'expanded' => true,
            ])
            ->add('question4', ChoiceType::class, [
                'label' => 'Votre moitié veut rompre avec vous.',
                'choices' => array_flip($choices4),
                'expanded' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // L’entité associée ici si tu en as une
            'data_class' => null,
        ]);
    }
}
