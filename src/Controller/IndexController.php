<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if($user){

            if($user->getNature()){
    
                return $this->redirectToRoute('app_purchase_index');
            } else {
                return $this->redirectToRoute('app_user_determine_nature');
            }
        } else {
            return $this->redirectToRoute('app_purchase_index');
        }
        // return $this->render('test/index.html.twig', [
        //     'controller_name' => 'TestController',
        // ]);
    }
}
