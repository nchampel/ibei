<?php

namespace App\Services;

use App\Entity\User;
use App\Repository\PotRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class AppService
{
    private $jackpot;
    private $entity;
    private ?User $user;

    public function __construct(PotRepository $potRepository, EntityManagerInterface $entity, Security $security)
    {
        $this->jackpot = $potRepository->findOneBy(['type' => 'jackpot']);
        $this->entity = $entity;
        $this->user = $security->getUser();
    }

    public function getJackpot(): int
    {
        // $jackpot = $this->potRepository->findOneBy(['type' => 'jackpot']);
        
        return $this->jackpot->getGain();
    }
    
    public function winJackpot(): ?string
    {
        // $jackpot = $this->potRepository->findOneBy(['type' => 'jackpot']);
        $chance = random_int(10000, 10000);
        if($chance == 10000){
            $money = $this->user->getMoney();
            $money += $this->jackpot->getGain();
            $message = "Vous avez remporté le jackpot d'une valeur de " . $this->jackpot->getGain() . " €";
            $this->user->setMoney($money);
            $this->jackpot->setGain(0);
            $this->jackpot->setClaimedAt(new \DateTime('now'));
            $this->jackpot->setIsClaimed(true);
            $this->entity->persist($this->user);
            $this->entity->persist($this->jackpot);
            $this->entity->flush();
            return $message;
        }
        
        return null;
    }

    public function getJackpotWon(){
        return $this->jackpot->isClaimed();
    }

    public function getJackpotWonDate(): ?string
    {
        // setlocale(LC_TIME, 'fr_FR.UTF-8');
        $formatter = new DateFormatterService();
        if($this->jackpot->getClaimedAt()){
            return $formatter->formatToLongFrench($this->jackpot->getClaimedAt()) ;
        }
        return null;
    }
    
    
}
