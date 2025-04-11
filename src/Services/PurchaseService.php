<?php

namespace App\Services;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class PurchaseService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManagerInterface){
        $this->entityManager = $entityManagerInterface;
    }
    
    public function harvestProduct(User $user): void
    {
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
        }
        if($harvestedCount > 0){
            $user->setMoney($money);
            $user->setExp($xp);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
        
    }
}