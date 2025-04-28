<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shop')]
class ShopController extends AbstractController
{
    #[Route('/home//{jackpot}', name: 'app_shop_home')]
    public function index($jackpot = null): Response
    {
        return $this->render('shop/index.html.twig', [
            'controller_name' => 'ShopController',
            'jackpot' => $jackpot
        ]);
    }
}
