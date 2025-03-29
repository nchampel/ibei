<?php

namespace App\Form;

use App\Entity\Purchase;
use App\Repository\PurchaseRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseType extends AbstractType
{
    // private PurchaseRepository $purchaseRepository;

    // public function __construct (PurchaseRepository $purchaseRepository){
    //     $this->purchaseRepository = $purchaseRepository;
    // }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // $product = $this->purchaseRepository->findOneBy(['id' => 1]);
        // $nameProduct = $product->getName();

        $builder
            ->add('type')
            ->add('claimedAt')
            ->add('createdAt')
            // ->add('product')
            // ->add('user')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Purchase::class,
        ]);
    }
}
