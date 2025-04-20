<?php

namespace App\Controller;

use App\Entity\Purchase;
use App\Form\PurchaseType;
use App\Repository\IdleRepository;
use App\Repository\PotRepository;
use App\Repository\ProductInfosRepository;
use App\Repository\PurchaseRepository;
use App\Repository\UserRepository;
use App\Services\AppService;
use App\Services\PurchaseService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

#[Route('/purchase')]
class PurchaseController extends AbstractController

{
    private EntityManagerInterface $manager;
    private PurchaseService $purchaseService;
    private $appService;
    public function __construct(EntityManagerInterface $entityManager, PurchaseService $purchaseService, AppService $appService)
    {
        $this->manager = $entityManager;
        $this->purchaseService = $purchaseService;
        $this->appService = $appService;
    }
    /**
     * @param \App\Entity\Purchase[] $purchasesBuyable
     */
    #[Route('/all/{jackpot}', name: 'app_purchase_index', methods: ['GET'])]
    public function index(PurchaseRepository $purchaseRepository, $jackpot = null): Response
    {
        if($this->appService->getMaintenance() == "true"){
            return $this->redirectToRoute('app_maintenance');
        }
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $userConnected = ["tab" => "", "nav" => ""];
        $userNotConnected = ["tab" => "show active", "nav" => "active"];
        $purchasesPossessed = [];
        if ($user) {
            if(!$user->getNature()){
                return $this->redirectToRoute('app_user_determine_nature');
            }
            $purchasesPossessed = $purchaseRepository->findByUserNull(false, $user->getId());
            // on rajoute l'aspect claimable ou pas
            foreach($purchasesPossessed as $purchaseP){
                $purchaseP->updateIsClaimable();
                $purchaseP->updateRemainedSeconds();
            }
            $userConnected = ["tab" => "show active", "nav" => "active"];
            $userNotConnected = ["tab" => "", "nav" => ""];
            // on récupère la récompense journalière
            /** @var \App\Entity\Ressource[] $ressources */
            $ressources = $user->getRessources();
            $now = new \DateTime();
            if(is_null($ressources[0]->getClaimedAt()) || $ressources[0]->getClaimedAt()->format('Y-m-d') < (new \DateTime())->format('Y-m-d')){
                foreach($ressources as $ressource){
                    $type = $ressource->getType();
                    $value = $ressource->getValue();
                    switch($type){
                        case 'lien-unité':
                            $ressource->setValue($value + 500);
                            break;
                        case 'ticket':
                            $ressource->setValue($value + 5);
                            break;
                    }
                    $ressource->setClaimedAt($now);
                }
                $this->manager->flush();
                $this->addFlash('success', "Vous avez obtenu la récompense journalière de 500 lien-unités et 5 tickets");
            }

        }
        // $user = $userRepository->find($this->getUser());
        $purchasesBuyable = $purchaseRepository->findByUserNull(true);
        // regarder si les objets achetables peuvent être achetés
        $isPurchaseBuyable = false;
        // $purchasesBuyable = $purchaseRepository->findByUserNull(true);
        foreach($purchasesBuyable as $purchase){
            if($user && $purchase->getPrice() <= $user->getMoney()){
                $isPurchaseBuyable = true;
            }
        }
        // dump($purchasesPossessed);
        return $this->render('purchase/index.html.twig', [
            // 'purchases' => $purchaseRepository->findAll(),
            'purchasesBuyable' => $purchasesBuyable,
            'purchasesPossessed' => $purchasesPossessed,
            'userNotconnected' => $userNotConnected,
            'userConnected' => $userConnected,
            'isPurchaseBuyable' => $isPurchaseBuyable,
            'jackpot' => $jackpot
        ]);
    }

    #[Route('/harvest/{id}', name: 'harvest')]
    public function harvest(Purchase $purchase)
    {
        if($this->appService->getMaintenance() == "true"){
            return $this->redirectToRoute('app_maintenance');
        }
        $gain = $purchase->getGain();
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_purchase_index');
        }
        if(!$user->getNature()){
            return $this->redirectToRoute('app_user_determine_nature');
        }
        $money = $user->getMoney();

        $isClaimable = $purchase->getIsClaimable();
        if(!$isClaimable){
                return new JsonResponse(['isClaimable' => false, 'money' => $money, 'cooldown' => $purchase->getCooldown()]);
            
        } else {
            $newMoney = $money + $gain;
            $user->setMoney($newMoney);
            $exp = $user->getExp();
            // $expPurchase = $purchase->getProduct->getExp();
            $expPurchase = 2;
            $newExp = $exp + $expPurchase;
            $user->setExp($newExp);
            $purchase->setClaimedAt(new \DateTimeImmutable());
            $this->manager->persist($user);
            $this->manager->persist($purchase);
            $this->manager->flush();
            $description = $purchase->getProduct()->getName() . " récolté, gain de " . $gain . " €, expérience " . $expPurchase . " points.";
            $this->appService->createLog($description, $purchase->getId(), "produit", "récolte", $user);
            
            return new JsonResponse(['isClaimable' => true, 'money' => $newMoney, 'cooldown' => $purchase->getCooldown(),
                                       'exp' => $newExp,                       
        ]);
        }
       
        
        // return $this->redirectToRoute('app_purchase_index');
    }

    /**
     * @param \App\Entity\Purchase $purchase
     * @param \App\Entity\User $user
     */
    #[Route('/buy/{id}', name: 'app_purchase_buy', methods: ['GET'])]
    public function buy(Purchase $purchase, EntityManagerInterface $entityManager, PotRepository $potRepository, AppService $appService): Response
    {
        if($this->appService->getMaintenance() == "true"){
            return $this->redirectToRoute('app_maintenance');
        }
        /** @var \App\Entity\User $user */
        // mettre logique d'achat avec vérification de la somme, et afficher le bouton acheter en twig que si on a la somme
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_purchase_index');
        }
        if(!$user->getNature()){
            return $this->redirectToRoute('app_user_determine_nature');
        }
        // $user = $userRepository->find($security->getUser());
        $money = $user->getMoney();
        $price = $purchase->getPrice();
        if ($money >= $price) {
            $user->setMoney($money - $price);
            $purchase->setUser($user);
            $purchase->setBoughtAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));
            $pot = $potRepository->findOneBy(['type' => 'jackpot']);
            $potGain = $pot->getGain();
            $newPotGain = $potGain + round($price / 100);
            $pot->setGain($newPotGain);
            // gérer jackpot
            $jackpotWon = $appService->winJackpot();
            $entityManager->persist($user);
            $entityManager->persist($pot);
            $entityManager->persist($purchase);
            $entityManager->flush();

            $description = $purchase->getProduct()->getName() . " acheté pour le prix de " . $price . " €. Argent restant : " . $user->getMoney() . " €";
            $this->appService->createLog($description, $purchase->getId(), "produit", "achat", $user);
            $this->addFlash('success', "Vous avez acheté " . $purchase->getProduct()->getName());
        } else {
            $this->addFlash('error', "Vous n'avez pas assez d'argent");
        }
        return $this->redirectToRoute('app_purchase_index', ['jackpot' => $jackpotWon]);
    }

    #[Route('/harvest/all/{id}', name: 'app_purchase_harvest_all')]
    public function harvestAll($id, UserRepository $userRepository): Response
    {
        if($this->appService->getMaintenance() == "true"){
            return $this->redirectToRoute('app_maintenance');
        }
        $user = $userRepository->findOneBy(['id' => $id]);
        if(!$user){
            return $this->redirectToRoute('app_login');
        }
        $this->purchaseService->harvestProduct($user, $this->appService, "récolte tous");
        return $this->redirectToRoute('app_purchase_index');
    }

    #[Route('/harvest/all/idle/{token}', name: 'app_purchase_harvest_all_idle')]
    public function harvestAllIdle(string $token, UserRepository $userRepository): Response
    {
        if($this->appService->getMaintenance() == "true"){
            return $this->redirectToRoute('app_maintenance');
        }
        // à tester
        if ($token == $_ENV['APP_TOKEN']) {
            $users = $userRepository->findAll();
            // à optimiser
            foreach($users as $user){
                $idles = $user->getIdles();
                foreach($idles as $idle){
                    if($idle->getType() == 'récolte produits' && $idle->isActive()){
                        // à chaque user je fais un flush
                        $this->purchaseService->harvestProduct($user, $this->appService, "récolte idle");
                    }
                }
                // $hasIdleActive = $idleRepository->findOneBy(['user' => $user, 'type' => 'récolte produits', 'active' => true]);
                // if($hasIdleActive){
                // }
            }
            
        }
        return $this->redirectToRoute('app_purchase_index');
    }

    #[Route('/generate/{token}', name: 'app_purchases_generate', methods: ['GET'])]
    public function generate(
        string $token,
        EntityManagerInterface $entityManager,
        PurchaseRepository $purchaseRepository,
        ProductInfosRepository $productInfosRepository,
    ): Response {
        if($this->appService->getMaintenance() == "true"){
            return $this->redirectToRoute('app_maintenance');
        }
        // tester http://localhost:8000/purchase/generate/abcde
        if ($token == $_ENV['APP_TOKEN']) {
            $productsBuyable = $purchaseRepository->findByUserNull(true);
            foreach ($productsBuyable as $product) {

                $entityManager->remove($product);
                $entityManager->flush();
            }
            $productsModel = $productInfosRepository->findAll();
            foreach ($productsModel as $model) {
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
    
    #[Route('/new', name: 'app_purchase_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if($this->appService->getMaintenance() == "true"){
            return $this->redirectToRoute('app_maintenance');
        }
        // if (!$user) {
            return $this->redirectToRoute('app_purchase_index');
        // }
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
        if($this->appService->getMaintenance() == "true"){
            return $this->redirectToRoute('app_maintenance');
        }
        return $this->redirectToRoute('app_purchase_index');
        return $this->render('purchase/show.html.twig', [
            'purchase' => $purchase,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_purchase_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Purchase $purchase, EntityManagerInterface $entityManager): Response
    {
        if($this->appService->getMaintenance() == "true"){
            return $this->redirectToRoute('app_maintenance');
        }
        return $this->redirectToRoute('app_purchase_index');
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
        if($this->appService->getMaintenance() == "true"){
            return $this->redirectToRoute('app_maintenance');
        }
        return $this->redirectToRoute('app_purchase_index');
        if ($this->isCsrfTokenValid('delete' . $purchase->getId(), $request->request->get('_token'))) {
            $entityManager->remove($purchase);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_purchase_index', [], Response::HTTP_SEE_OTHER);
    }

    
}
