<?php

namespace App\Services;

use App\Entity\Log;
use App\Entity\User;
use App\Repository\ConfigRepository;
use App\Repository\PotRepository;
use App\Repository\RessourceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class AppService
{
    private $jackpot;
    private $manager;
    private ?User $user;
    private $configRepository;
    private RessourceRepository $ressourceRepository;

    public function __construct(PotRepository $potRepository, ConfigRepository $configRepository, Security $security, EntityManagerInterface $manager, RessourceRepository $ressourceRepository)
    {
        $this->jackpot = $potRepository->findOneBy(['type' => 'jackpot']);
        $this->manager = $manager;
        $this->user = $security->getUser();
        $this->configRepository = $configRepository;
        $this->ressourceRepository = $ressourceRepository;
    }

    public function getJackpot(): int
    {
        // $jackpot = $this->potRepository->findOneBy(['type' => 'jackpot']);
        
        return $this->jackpot->getGain();
    }
    
    public function winJackpot(): ?string
    {
        // $jackpot = $this->potRepository->findOneBy(['type' => 'jackpot']);
        $chance = random_int(1, 10000);
        if($chance == 10000 && !$this->jackpot->isClaimed()){
            $moneyResource = $this->ressourceRepository->findOneBy(['user' => $this->user, 'type' => 'argent']);
            // $money = $this->user->getMoney();
            $money = $moneyResource->getValue();
            $money += $this->jackpot->getGain();
            $message = "Vous avez remporté le jackpot d'une valeur de " . $this->jackpot->getGain() . " €";
            // $this->user->setMoney($money);
            $moneyResource->setValue($money);
            $this->jackpot->setGain(0);
            $this->jackpot->setClaimedAt(new \DateTime('now'));
            $this->jackpot->setIsClaimed(true);
            $this->jackpot->setUser($this->user);
            // $this->manager->persist($this->user);
            $this->manager->persist($this->jackpot);
            $this->manager->persist($moneyResource);
            $this->manager->flush();
            return $message;
        }
        
        return null;
    }

    public function getJackpotWon(){
        return $this->jackpot->isClaimed();
    }
    
    public function getJackpotWinner(){
        if(is_null($this->jackpot->getUser())){
            return null;
        }
        return $this->jackpot->getUser()->getPseudo();
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
    
    public function createLog(string $description, ?int $target, string $type, string $category, ?User $user){
        $log = new Log();
        $log->setDescription($description);
        $log->setCategory($category);
        $log->setTarget($target);
        $log->setType($type);
        $log->setUser($user);
        $log->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));
        $this->manager->persist($log);
        $this->manager->flush();
    }

    public function getAppTest(){
        return  $_ENV['APP_TEST'];
    }
    public function getConfig($type){
        return  $this->configRepository->findOneBy(['name' => $type])->getValue();
    }
    
}
