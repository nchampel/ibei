<?php

namespace App\Controller;

use App\Entity\ForestResource;
use App\Repository\ForestResourceRepository;
use App\Repository\PositionRepository;
use App\Repository\RessourceRepository;
use App\Services\AppService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/foret')]
class ForestController extends AbstractController
{
    private function mergeFieldAndResources(array $formattedField, array $formattedResources): array {
        $merged = [];
    
        // Indexer les tuiles du champ de base
        foreach ($formattedField as $tile) {
            $key = $tile['x'] . ':' . $tile['y'];
            $merged[$key] = $tile;
        }
    
        // Écraser ou ajouter les ressources selon la position
        foreach ($formattedResources as $resource) {
            $key = $resource['x'] . ':' . $resource['y'];
            $merged[$key] = $resource;
        }
    
        // Revenir à un tableau classique
        return array_values($merged);
    }

    private function getCards($x, $y, $forestResourceRepository){
        $forestResources = $forestResourceRepository->findDisplayable();
        $formattedResources = [];
        foreach ($forestResources as $resource) {
            if($x - 1 <= $resource->getX() && $resource->getX() <= $x +1 &&
            $y - 1 <= $resource->getY() && $resource->getY() <= $y +1
            // ajouter si joueur sur ressource ou alors ne pas remonter les ressources avec joueurs dessus
            ){

                $formattedResources[] = [
                    'id' => $resource->getId(),
                    'gain' => $resource->getGain(),
                    'type' => $resource->getForestResource()->getGainType(),
                    'x' => $resource->getX(),
                    'y' => $resource->getY(),
                    'image_url' => $resource->getForestResource()->getImageUrl(), // ex: 'icons/wood.png'
                    'isResource' => true
                ];
            }
        }
        return $formattedResources;
    }
    private function getResources($ressourceRepository, $user){
        $resourcesBDD = $ressourceRepository->findBy(["user" => $user]);
        $resources = [];
        foreach($resourcesBDD as $resourceBDD){
            $resources[$resourceBDD->getType()] = $resourceBDD->getValue();
        }
        return $resources;
    }

    #[Route('/carte/{jackpot}', name: 'app_forest_map')]
    public function map(ForestResourceRepository $forestResourceRepository, RessourceRepository $ressourceRepository, AppService $appService, PositionRepository $positionRepository, $jackpot = null): Response
    {
        if ($appService->getConfig('maintenance') == "true") {
            return $this->redirectToRoute('app_maintenance');
        }
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        if (!$user->getNature()) {
            return $this->redirectToRoute('app_user_determine_nature');
        }
        
        // dump($forestResources);
        $formattedField = [];
        
        $forestPosition = $positionRepository->findOneBy(["type" => "forêt"]);
        $x = $forestPosition->getX();
        $y = $forestPosition->getY();
        $formattedResources = $this->getCards($x, $y, $forestResourceRepository);
        
        // on crée le sol sans ressources
        for ($xField = $x - 1 ; $xField <= $x + 1 ; $xField++){
            for ($yField = $y - 1 ; $yField <= $y + 1 ; $yField++){
                $fieldNumber = random_int(1, 10);
                $formattedField[] = [
                    'id' => null,
                    'gain' => 0,
                    'type' => "field",
                    'x' => $xField,
                    'y' => $yField,
                    'image_url' => "forest/icons/sol " . $fieldNumber . ".jpg", // ex: 'icons/wood.png'
                    'isResource' => false
                ];
            }
        }

        $finalResources = $this->mergeFieldAndResources($formattedField, $formattedResources);
        $resources = $this->getResources($ressourceRepository, $user);
        // dump($resources);

        return $this->render('forest/index.html.twig', [
            'forestResources' => $finalResources,
            'jackpot' => $jackpot,
            'resources' => $resources
        ]);
    }
    #[Route('/carte/navigation/{jackpot}/{direction}', name: 'app_forest_map_navigate')]
    public function navigateMap(ForestResourceRepository $forestResourceRepository, AppService $appService, PositionRepository $positionRepository, RessourceRepository $ressourceRepository, EntityManagerInterface $entityManager, $direction, $jackpot = null): Response{
        if ($appService->getConfig('maintenance') == "true") {
            return $this->redirectToRoute('app_maintenance');
        }
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        if (!$user->getNature()) {
            return $this->redirectToRoute('app_user_determine_nature');
        }
        $forestPosition = $positionRepository->findOneBy(["type" => "forêt"]);
        $x = $forestPosition->getX();
        $y = $forestPosition->getY();
        switch($direction){
            case "left":
                $x -= 3;
                $forestPosition->setX($x);
                break;
            case "right":
                $x += 3;
                $forestPosition->setX($x);
                break;
            case "up":
                $y -= 3;
                $forestPosition->setY($y);
                break;
            case "down":
                $y += 3;
                $forestPosition->setY($y);
                break;
        }
        $entityManager->persist($forestPosition);
        $entityManager->flush();
        $newCards = $this->getCards($x, $y, $forestResourceRepository);
        $resources = $this->getResources($ressourceRepository, $user);
        return $this->render('forest/index.html.twig', [
            'forestResources' => $newCards,
            'jackpot' => $jackpot,
            'resources' => $resources
        ]);
    }
    #[Route('/recolter/{type}/{id}', name: 'app_forest_harvest')]
    public function harvest(ForestResource $forestResource, AppService $appService, $type, RessourceRepository $ressourceRepository, EntityManagerInterface $entityManager): Response{
        if ($appService->getConfig('maintenance') == "true") {
            return $this->redirectToRoute('app_maintenance');
        }
        $gain = $forestResource->getGain();
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        if (!$user->getNature()) {
            return $this->redirectToRoute('app_user_determine_nature');
        }
        $resourceBDD = $ressourceRepository->findOneBy(['user' => $user, 'type' => $type]);
        $resourceValue = $resourceBDD->getValue();

        $isClaimable = $forestResource->getIsClaimable();
        if (!$isClaimable) {
            return new JsonResponse(['isClaimable' => false, 'value' => $resourceValue, 'cooldown' => $forestResource->getCooldown()]);
        } else {
            $newValue = $resourceValue + $gain;
            $resourceBDD->setValue($newValue);
            // $user->setMoney($newMoney);
            $exp = $user->getExp();
            // $expPurchase = $purchase->getProduct->getExp();
            $expHarvest = 2;
            $newExp = $exp + $expHarvest;
            $user->setExp($newExp);
            $forestResource->setClaimedAt(new \DateTimeImmutable());
            $entityManager->persist($user);
            $entityManager->persist($forestResource);
            $entityManager->persist($resourceBDD);
            $entityManager->flush();
            $description = $forestResource->getForestResource()->getName() . " récolté, gain de " . $gain . " " . $type . ", expérience " . $expHarvest . " points.";
            $appService->createLog($description, $forestResource->getId(),"forêt", $type, "récolte", $user);

            return new JsonResponse([
                'isClaimable' => true,
                "type" => $type,
                "typeValue" => $newValue,
                'cooldown' => $forestResource->getCooldown(),
                'exp' => $newExp,
            ]);
        }
    }
}
