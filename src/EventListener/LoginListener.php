<?php
namespace App\EventListener;

use App\Services\AppService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class LoginListener
{
    private AppService $appService;
    private RequestStack $requestStack;

    public function __construct(AppService $appService, RequestStack $requestStack)
    {
        $this->appService = $appService;
        $this->requestStack = $requestStack;
    }

    public function __invoke(LoginSuccessEvent $event): void
    {
        /** @var \App\Entity\User $user */
        $user = $event->getUser();

        $request = $this->requestStack->getCurrentRequest();
        $ip = $request?->getClientIp();

        $description = "Connexion réussie de " . $user->getPseudo() . " depuis l'ip : " . $ip .".";
        $this->appService->createLog($description, $user->getId(), null, "utilisateur", "connexion", $user);
    }
}