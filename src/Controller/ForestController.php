<?php

namespace App\Controller;

use App\Repository\ForestResourceRepository;
use App\Repository\RessourceRepository;
use App\Services\AppService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    #[Route('/carte/{jackpot}', name: 'app_forest_map')]
    public function map(ForestResourceRepository $forestResourceRepository, RessourceRepository $ressourceRepository, AppService $appService, $jackpot = null): Response
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
        $forestResources = $forestResourceRepository->findDisplayable();
        // dump($forestResources);
        $formattedResources = [];
        $formattedField = [];

        $x = 50;
        $y = 50;

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

        $finalResources = $this->mergeFieldAndResources($formattedField, $formattedResources);

        $resourcesBDD = $ressourceRepository->findBy(["user" => $user]);
        $resources = [];
        foreach($resourcesBDD as $resourceBDD){
            $resources[$resourceBDD->getType()] = $resourceBDD->getValue();
        }
        // dump($resources);

        return $this->render('forest/index.html.twig', [
            'forestResources' => $finalResources,
            'jackpot' => $jackpot,
            'resources' => $resources
        ]);
    }
}
