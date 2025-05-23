<?php

namespace App\Controller;

use App\Entity\Pot;
use App\Entity\Ressource;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Services\AppService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        AppService $appService,
    ): Response {
        if ($appService->getConfig('maintenance') == "true") {
            return $this->redirectToRoute('app_maintenance');
        }
        if ($appService->getConfig('test') == 'true') {
            return $this->redirectToRoute('app_login');
        }
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $currentDate = new \DateTime();
            $currentDate->setTimezone(new \DateTimeZone('Europe/Paris'));
            $user->setCreatedAt($currentDate);
            // $moneyResource = new Ressource();
            // $moneyResource->setValue(0);
            // $moneyResource->setType('argent');
            // $moneyResource->setUser($user);
            $user->setExp(0);
            $pot = new Pot();
            $pot->setType("utilisateur");
            $pot->setUser($user);
            $pot->setGain(0);
            $pot->setIsClaimed(false);
            $ressources = ['lien-unité', 'ticket', 'argent', 'champignon'];
            foreach ($ressources as $ressourceType) {
                $resource = new Ressource();
                $resource->setType($ressourceType);
                $resource->setUser($user);
                if($ressourceType == "argent") {

                    $resource->setValue(50);
                } else {

                    $resource->setValue(0);
                }
                $entityManager->persist($resource);
            }
            // $entityManager->persist($moneyResource);
            $entityManager->persist($pot);
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            // return $this->redirectToRoute('app_purchase_index');
            return $this->redirectToRoute('app_user_determine_nature');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
