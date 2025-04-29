<?php

namespace App\Controller;

use App\Repository\ForestResourceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/foret')]
class ForestController extends AbstractController
{
    #[Route('/carte/{jackpot}', name: 'app_forest_map')]
    public function map(ForestResourceRepository $forestResourceRepository, $jackpot = null): Response
    {
        $forestResources = $forestResourceRepository->findDisplayable();
        // dump($forestResources);
        $formattedResources = [];

        foreach ($forestResources as $resource) {
                $formattedResources[] = [
                    'x' => $resource->getX(),
                    'y' => $resource->getY(),
                    'image_url' => $resource->getForestResource()->getImageUrl(), // ex: 'icons/wood.png'
                ];
        }

        return $this->render('forest/index.html.twig', [
            'forestResources' => $formattedResources,
            'jackpot' => $jackpot
        ]);
    }
}
