<?php

namespace App\EventListener;

use App\Services\AppService;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class LoginFailureListener
{
    private AppService $appService;
    private RequestStack $requestStack;

    public function __construct(AppService $appService, RequestStack $requestStack)
    {
        $this->appService = $appService;
        $this->requestStack = $requestStack;
    }

    public function __invoke(LoginFailureEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $ip = $request?->getClientIp();

        $identifier = $event->getPassport()?->getBadge(UserBadge::class)?->getUserIdentifier() ?? 'inconnu';
        $exceptionMessage = $event->getException()?->getMessage() ?? 'Erreur inconnue';

        $description = sprintf(
            "Échec de connexion pour l'identifiant '%s' depuis IP %s. Raison : %s",
            $identifier,
            $ip ?? 'IP inconnue',
            $exceptionMessage
        );

        $this->appService->createLog($description, null, "utilisateur", "connexion échouée", null);
    }
}
