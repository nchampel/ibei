<?php

namespace App\Controller;

use App\Entity\Purchase;
use App\Form\PurchaseType;
use App\Repository\IdleRepository;
use App\Repository\ProductInfosRepository;
use App\Repository\PurchaseRepository;
use App\Repository\UserRepository;
use App\Services\PurchaseService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/purchase')]
class PurchaseController extends AbstractController

{
    private EntityManagerInterface $manager;
    private PurchaseService $purchaseService;
    private $params;
    public function __construct(EntityManagerInterface $entityManager, PurchaseService $purchaseService, ParameterBagInterface $params)
    {
        $this->manager = $entityManager;
        $this->purchaseService = $purchaseService;
        $this->params = $params;
    }
    /**
     * @param \App\Entity\Purchase[] $purchasesBuyable
     */
    #[Route('/', name: 'app_purchase_index', methods: ['GET'])]
    public function index(PurchaseRepository $purchaseRepository): Response
    {
        /** @var User $user */
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
            'isPurchaseBuyable' => $isPurchaseBuyable
        ]);
    }

    #[Route('/harvest/{id}', name: 'harvest')]
    public function harvest(Purchase $purchase, PurchaseRepository $purchaseRepository)
    {
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
    public function buy(Purchase $purchase, EntityManagerInterface $entityManager): Response
    {

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
            $entityManager->persist($user);
            $entityManager->persist($purchase);
            $entityManager->flush();
            $this->addFlash('success', "Vous avez acheté " . $purchase->getProduct()->getName());
        } else {
            $this->addFlash('error', "Vous n'avez pas assez d'argent");
        }
        return $this->redirectToRoute('app_purchase_index');
    }

    #[Route('/harvest/all/{id}', name: 'app_purchase_harvest_all')]
    public function harvestAll($id, PurchaseRepository $purchaseRepository, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $userRepository->findOneBy(['id' => $id]);
        if(!$user){
            return $this->redirectToRoute('app_login');
        }
        $money = $user->getMoney();
        $xp = $user->getExp();
        $purchasesHarvestable = $purchaseRepository->findBy(['user' => $user]);
        if(count($purchasesHarvestable) > 0){
            $harvestedCount = 0;
            foreach($purchasesHarvestable as $purchase){
                if($purchase->updateIsClaimable()){
                    $harvestedCount++;
                    $gain = $purchase->getGain();
                    $money += $gain;
                    // mettre l'xp moins forte que récolte normale
                    $xp += 1;
                    $purchase->setClaimedAt(new \DateTime('now'));
                    $entityManager->persist($purchase);
                }
            }
            if($harvestedCount > 0){
                $user->setMoney($money);
                $user->setExp($xp);
                $entityManager->persist($user);
                $entityManager->flush();
            }
        }
        return $this->redirectToRoute('app_purchase_index');
    }

    #[Route('/harvest/all/idle/{token}', name: 'app_purchase_harvest_all_idle')]
    public function harvestAllIdle(string $token, IdleRepository $idleRepository, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        if ($token == "abcde") {
        $users = $userRepository->findAll();
        // à optimiser
        foreach($users as $user){

            $hasIdleActive = $idleRepository->findOneBy(['user' => $user, 'type' => 'récolte produits', 'active' => true]);
            if($hasIdleActive){

            }
        }
        
    }
        return $this->redirectToRoute('app_purchase_index');
    }

    #[Route('/generate/{token}', name: 'app_purchases_generate', methods: ['GET'])]
    public function generate(
        Request $request,
        string $token,
        EntityManagerInterface $entityManager,
        PurchaseRepository $purchaseRepository,
        ProductInfosRepository $productInfosRepository,
    ): Response {
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
        return $this->redirectToRoute('app_purchase_index');
        return $this->render('purchase/show.html.twig', [
            'purchase' => $purchase,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_purchase_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Purchase $purchase, EntityManagerInterface $entityManager): Response
    {
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
        return $this->redirectToRoute('app_purchase_index');
        if ($this->isCsrfTokenValid('delete' . $purchase->getId(), $request->request->get('_token'))) {
            $entityManager->remove($purchase);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_purchase_index', [], Response::HTTP_SEE_OTHER);
    }

    
}
