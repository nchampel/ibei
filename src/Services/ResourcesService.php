<?php

namespace App\Services;

use App\Entity\Ressource;
use App\Entity\User;
use App\Repository\ConfigRepository;
use App\Repository\PotRepository;
use App\Repository\RessourceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class ResourcesService
{
    private ?User $user;
    private RessourceRepository $ressourceRepository;
    private $manager;

    public function __construct(Security $security, EntityManagerInterface $manager, RessourceRepository $ressourceRepository)
    {
        $this->manager = $manager;
        $this->user = $security->getUser();
        $this->ressourceRepository = $ressourceRepository;
    }

    public function getResource($type): int
    {
        $resourceBDD = $this->ressourceRepository->findOneBy(['user' => $this->user, 'type' => $type]);
        if(!$resourceBDD){
            $resource = new Ressource();
            $resource->setValue(0);
            $resource->setType($type);
            $this->manager->persist($resource);
            $this->manager->flush();
            return 0;
        }
        return $resourceBDD->getValue();
    }
    
}
