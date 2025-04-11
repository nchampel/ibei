<?php

namespace App\Services;

use App\Entity\User;
use App\Repository\PotRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AppService
{
    private $potRepository;
    private $entity;

    public function __construct(PotRepository $potRepository, EntityManagerInterface $entity)
    {
        $this->potRepository = $potRepository;
        $this->entity = $entity;
    }

    public function getJackpot(): int
    {
        $jackpot = $this->potRepository->findOneBy(['type' => 'jackpot']);
        
        return $jackpot->getGain();
    }
    
    public function winJackpot(User $user): ?string
    {
        $jackpot = $this->potRepository->findOneBy(['type' => 'jackpot']);
        $chance = random_int(10000, 10000);
        if($chance == 10000){
            $money = $user->getMoney();
            $money += $jackpot->getGain();
            $message = "Vous avez remporté le jackpot d'une valeur de " . $jackpot->getGain() . " €";
            $user->setMoney($money);
            $jackpot->setGain(0);
            $this->entity->persist($user);
            $this->entity->persist($jackpot);
            $this->entity->flush();
            return $message;
        }
        
        return null;
    }
    
    
}
