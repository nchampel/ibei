<?php

namespace App\Controller;

use App\Entity\ForestResource;
use App\Repository\ForestResourceInfosRepository;
use App\Repository\ForestResourceRepository;
use App\Services\AppService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/foret')]
class ForestResourceController extends AbstractController
{
    #[Route('/ressources/generer/{token}', name: 'app_forest_resource_daily_generate')]
    public function index(
        string $token,
        EntityManagerInterface $entityManager,
        ForestResourceRepository $forestResourceRepository,
        ForestResourceInfosRepository $forestResourceInfosRepository,
        AppService $appService
    ): Response {
        if ($appService->getConfig('maintenance') == "true") {
            return $this->redirectToRoute('app_maintenance');
        }
        if ($token == $_ENV['APP_TOKEN']) {
            $freeForestResource = $forestResourceRepository->findByUserNull(true);
            foreach ($freeForestResource as $resource) {

                $entityManager->remove($resource);
                // $entityManager->flush();
            }
            $forestResourceModel = $forestResourceInfosRepository->findAll();
            foreach ($forestResourceModel as $model) {
                for($i = 0; $i < $model->getFactor(); $i++){

                    $forestResourceGenerated = new ForestResource();
                    $gainModel = $model->getGain();
                    // $priceModel = $model->getPrice();
                    $cooldownModel = $model->getCooldown();
                    $forestResourceGenerated->setGain(random_int(round($gainModel * 0.8), round($gainModel * 1.2)));
                    // $forestResourceGenerated->setPrice(random_int(round($priceModel * 0.8), round($priceModel * 1.2)));
                    $forestResourceGenerated->setCooldown(random_int(round($cooldownModel * 0.8), round($cooldownModel * 1.2)));
                    $forestResourceGenerated->setForestResource($model);
                    $forestResourceGenerated->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));
                    $forestResourceGenerated->setX(random_int(1, 100));
                    $forestResourceGenerated->setY(random_int(1, 100));
                    // $forestResourceGenerated->setType("");
                    $entityManager->persist($forestResourceGenerated);
                }
                    
            }
            $entityManager->flush();
            $this->addFlash('success', "Les nouvelles ressources de la forêt de la journée ont été générés");
        }

        return $this->redirectToRoute('app_forest_map');
    }
}
