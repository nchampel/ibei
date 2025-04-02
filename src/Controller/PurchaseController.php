<?php

namespace App\Controller;

use App\Entity\Purchase;
use App\Form\PurchaseType;
use App\Repository\ProductInfosRepository;
use App\Repository\PurchaseRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/purchase')]
class PurchaseController extends AbstractController
{
    #[Route('/', name: 'app_purchase_index', methods: ['GET'])]
    public function index(PurchaseRepository $purchaseRepository, UserRepository $userRepository): Response
    {
        $user = $userRepository->find(9);
        $purchasesBuyable = $purchaseRepository->findByUserNull(true);
        $purchasesPossessed = $purchaseRepository->findByUserNull(false, $user);
        return $this->render('purchase/index.html.twig', [
            // 'purchases' => $purchaseRepository->findAll(),
            'purchasesBuyable' => $purchasesBuyable,
            'purchasesPossessed' => $purchasesPossessed,
        ]);
    }

    #[Route('/buy/{id}', name: 'app_purchase_buy', methods: ['GET'])]
    public function buy(Purchase $purchase, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        // il faudra changer l'user
        // mettre logique d'achat avec vérification de la somme, et afficher le bouton acheter en twig que si on a la somme
        $user = $userRepository->find(9);
        $money = $user->getMoney();
        $price = $purchase->getPrice();
        if($money >= $price){
            $user->setMoney($money - $price);
            $purchase->setUser($user);
            $entityManager->persist($user);
            $entityManager->persist($purchase);
            $entityManager->flush();
            $this->addFlash('success', "Vous avez acheté " . $purchase->getProduct()->getName());
        } else {
            $this->addFlash('error', "Vous n'avez pas assez d'argent");
        }
        return $this->redirectToRoute('app_purchase_index');
        
    }

    #[Route('/new', name: 'app_purchase_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $purchase = new Purchase();
        $form = $this->createForm(PurchaseType::class, $purchase);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($purchase);
            $entityManager->flush();

            return $this->redirectToRoute('app_purchase_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('purchase/new.html.twig', [
            'purchase' => $purchase,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_purchase_show', methods: ['GET'])]
    public function show(Purchase $purchase): Response
    {
        return $this->render('purchase/show.html.twig', [
            'purchase' => $purchase,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_purchase_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Purchase $purchase, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PurchaseType::class, $purchase);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_purchase_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('purchase/edit.html.twig', [
            'purchase' => $purchase,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_purchase_delete', methods: ['POST'])]
    public function delete(Request $request, Purchase $purchase, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$purchase->getId(), $request->request->get('_token'))) {
            $entityManager->remove($purchase);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_purchase_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/generate/{token}', name: 'app_purchases_generate', methods: ['GET'])]
    public function generate(Request $request, string $token, EntityManagerInterface $entityManager, PurchaseRepository $purchaseRepository,
    ProductInfosRepository $productInfosRepository): Response
    {
        // tester http://localhost:8000/purchase/generate/abcde
        if ($token == "abcde") {
            $productsBuyable = $purchaseRepository->findByUserNull(true);
            foreach($productsBuyable as $product){
                
                $entityManager->remove($product);
                $entityManager->flush();
            }
            $productsModel = $productInfosRepository->findAll();
            foreach($productsModel as $model){
                $productGenerated = new Purchase();
                $gainModel = $model->getGain();
                $priceModel = $model->getPrice();
                $cooldownModel = $model->getCooldown();
                $productGenerated->setGain(random_int(round($gainModel * 0.8), round($gainModel * 1.2)));
                $productGenerated->setPrice(random_int(round($priceModel * 0.8), round($priceModel * 1.2)));
                $productGenerated->setCooldown(random_int(round($cooldownModel * 0.8), round($cooldownModel * 1.2)));
                $productGenerated->setProduct($model);
                $productGenerated->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));
                $productGenerated->setType("");
                $entityManager->persist($productGenerated);
            }
            $entityManager->flush();
            $this->addFlash('success', "Les nouveaux produits ont été générés");
        }

        return $this->redirectToRoute('app_purchase_index');
    }
}
