<?php

namespace App\Controller;

use App\Entity\Nature;
use App\Form\NatureChoicesType;
use App\Form\NatureType;
use App\Services\AppService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private $appService;

    public function __construct(AppService $appService)
    {
        $this->appService = $appService;
    }
    #[Route('/determine/nature', name: 'app_user_determine_nature')]
    public function nature(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->appService->getConfig('maintenance') == "true") {
            return $this->redirectToRoute('app_maintenance');
        }
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_purchase_index');
        }
        if ($user->getNature()) {
            return $this->redirectToRoute('app_purchase_index');
        }
        $form = $this->createForm(NatureType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $answers = $form->getData();
            $values = array_values($answers);
            $sotoc_score = 0;
            $flumia_score = 0;
            $sora_score = 0;
            $nano_score = 0;
            $altheron_score = 0;
            $now = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
            foreach ($values as $key => $answer) {
                $natureEntry = new Nature();
                $natureEntry->setAnswer($answer);
                $natureEntry->setQuestion($key + 1);
                $natureEntry->setUser($user);
                $natureEntry->setCreatedAt($now);
                $entityManager->persist($natureEntry);

                switch ($answer) {
                    case 'rb':
                    case 'rm':
                        $sotoc_score += 1;
                        break;
                    case 'eb':
                    case 'em':
                        $flumia_score += 1;
                        break;
                    case 'ob':
                    case 'om':
                        $sora_score += 1;
                        break;
                    case 'ab':
                    case 'am':
                        $nano_score += 1;
                        break;
                    case 'sb':
                    case 'sm':
                        $altheron_score += 1;
                        break;
                }
            }
            // $entityManager->flush();
            $score = [
                'sotoc' => $sotoc_score,
                'flumia' => $flumia_score,
                'sora' => $sora_score,
                'nano' => $nano_score,
                'altheron' => $altheron_score
            ];

            $valeur_max = max($score);
            $clefs_max = array_keys($score, $valeur_max);

            // $correspondances = ['s' => 'altheron', 'a' => 'nano', 'o' => 'sora', 'e' =>'flumia', 'r'=>'sotoc'];
            if (count($clefs_max) == 1) {
                $nature = $clefs_max[0];
                $user->setNature($nature);
                $entityManager->persist($user);
                $entityManager->flush();
                return $this->redirectToRoute('app_purchase_index');
            } else {
                $natures = [];
                foreach ($clefs_max as $clef_max) {
                    $natures[] = $clef_max;
                }
                $entityManager->flush();
                // rediriger vers le choix d'un alignement avec les descriptions 
                return $this->redirectToRoute('app_user_determine_nature_choices', [
                    'choices' => json_encode($natures) // Tu peux encoder le tableau en JSON pour le passer en paramètre
                ]);
            }
            // do anything else you need here, like send an email

            // return $this->redirectToRoute('app_purchase_index');
        }
        return $this->render('nature/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/determine/nature/choice', name: 'app_user_determine_nature_choices', methods: ['GET', 'POST'])]
    public function natures(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->appService->getConfig('maintenance') == "true") {
            return $this->redirectToRoute('app_maintenance');
        }
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_purchase_index');
        }
        if ($user->getNature()) {
            return $this->redirectToRoute('app_purchase_index');
        }
        $choices = json_decode($request->query->get('choices'), true);
        $form = $this->createForm(NatureChoicesType::class, null, ['choices' => $choices]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $answer = $form->getData();
            $user->setNature($answer['nature']);

            $entityManager->persist($user);

            $entityManager->flush();

            return $this->redirectToRoute('app_purchase_index');
        }

        /*sora: "Peuple altruiste avec tendance à l'abnégation",
    nano: "Tribu ingénieuse mais autiste",
    altheron: "Civilisation non violente à la limite du stoïcisme",
    sotoc: "La confrérie des braves au summum de l'arrogance",
    flumia: "La communauté des respectueux craintifs",*/
        // }
        // do anything else you need here, like send an email

        // return $this->redirectToRoute('app_user_determine_nature_choices');
        // return $this->redirectToRoute('app_user_determine_nature');

        return $this->render('nature/choices.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
