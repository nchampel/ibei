<?php

namespace App\Controller;

use App\Repository\PotRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/app')]
class AppController extends AbstractController
{
    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->manager = $entityManager;
    }

    #[Route('/reset-jackpot/{token}', name: 'app_reset_jackpot')]
    public function resetJackpot(Request $request, PotRepository $repo, $token): Response
    {
        $referer = $request->headers->get('referer');
        if($token == $_ENV['APP_TOKEN_APP']){

            $jackpot = $repo->findOneBy(['type' => 'jackpot']);
            $jackpot->setIsClaimed(false);
            $this->manager->persist($jackpot);
            $this->manager->flush();
        }
        return $this->redirect($referer);
    }
}
