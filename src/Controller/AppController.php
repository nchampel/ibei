<?php

namespace App\Controller;

use App\Repository\PotRepository;
use App\Services\AppService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/app')]
class AppController extends AbstractController
{
    private EntityManagerInterface $manager;
    private $appService;

    public function __construct(EntityManagerInterface $entityManager, AppService $appService)
    {
        $this->manager = $entityManager;
        $this->appService = $appService;
    }

    #[Route('/reset-jackpot/{token}', name: 'app_reset_jackpot')]
    public function resetJackpot(Request $request, PotRepository $repo, $token): Response
    {
        if ($this->appService->getConfig('maintenance') == "true") {
            return $this->redirectToRoute('app_maintenance');
        }
        $referer = $request->headers->get('referer');
        if ($token == $_ENV['APP_TOKEN_APP']) {

            $jackpot = $repo->findOneBy(['type' => 'jackpot']);
            $jackpot->setIsClaimed(false);
            $this->manager->persist($jackpot);
            $this->manager->flush();
        }
        return $this->redirect($referer);
    }
    #[Route('/maintenance', name: 'app_maintenance')]
    public function maintenance(): Response
    {
        if ($this->appService->getConfig('maintenance') != "true") {
            return $this->redirectToRoute("app_purchase_index");
        }
        return $this->render('app/maintenance.html.twig');
    }
}
