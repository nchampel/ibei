<?php

namespace App\Services;

use App\Entity\User;
use App\Repository\RessourceRepository;
use Doctrine\ORM\EntityManagerInterface;

class PurchaseService
{
    private EntityManagerInterface $entityManager;
    private RessourceRepository $ressourceRepository;

    public function __construct(EntityManagerInterface $entityManagerInterface, RessourceRepository $ressourceRepository){
        $this->entityManager = $entityManagerInterface;
        $this->ressourceRepository = $ressourceRepository;
    }
    
    public function harvestProduct(User $user, $appService, $category): void
    {
        $moneyResource = $this->ressourceRepository->findOneBy(['user' => $user, 'type' => 'argent']);
        $money = $moneyResource->getValue();
        // $money = $user->getMoney();
        $xp = $user->getExp();
        $purchases = $user->getPurchases();
        $harvestedCount = 0;
        $gainTotal = 0;
        $xpTotal = 0;
        foreach($purchases as $purchase){
            if($purchase->updateIsClaimable()){
                $harvestedCount++;
                $gain = $purchase->getGain();
                $gainTotal += $gain;
                $money += $gain;
                // mettre l'xp moins forte que récolte normale
                $xp += 1;
                $xpTotal +=1;
                $purchase->setClaimedAt(new \DateTime('now'));
                $this->entityManager->persist($purchase);
            }
        }
        if($harvestedCount > 0){
            $moneyResource->setValue($money);
            // $user->setMoney($money);
            $user->setExp($xp);
            $this->entityManager->persist($user);
            $this->entityManager->persist($moneyResource);
            $this->entityManager->flush();
            $description = $harvestedCount . " produit(s) récolté(s), gain de " . $gainTotal . " €, expérience " . $xpTotal . " points.";
            $appService->createLog($description, null, "produit", $category, $user);
        }
        
    }
}