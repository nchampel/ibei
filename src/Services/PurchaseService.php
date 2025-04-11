<?php

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;

class PurchaseService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManagerInterface){
        $this->entityManager = $entityManagerInterface;
    }
    
    public function harvestProduct($user): void
    {
        /** @var \App\Entity\User $user */
        $money = $user->getMoney();
        $xp = $user->getExp();
        $purchases = $user->getPurchases();
        $harvestedCount = 0;
        foreach($purchases as $purchase){
            if($purchase->updateIsClaimable()){
                $harvestedCount++;
                $gain = $purchase->getGain();
                $money += $gain;
                // mettre l'xp moins forte que récolte normale
                $xp += 1;
                $purchase->setClaimedAt(new \DateTime('now'));
                $this->entityManager->persist($purchase);
            }
            if($harvestedCount > 0){
                $user->setMoney($money);
                $user->setExp($xp);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
            }
        }
        
    }
}