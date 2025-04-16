<?php

namespace App\Services;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserService
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function getConnectedUser(): ?UserInterface
    {
        $token = $this->tokenStorage->getToken();
        if ($token && $token->getUser() instanceof UserInterface) {
            return $token->getUser();
        }

        return null;  // Si l'utilisateur n'est pas connecté, retourne null
    }
    public function getNature(): ?string
    {
        $token = $this->tokenStorage->getToken();
        if ($token && $token->getUser() instanceof UserInterface) {
            /** @var \App\Entity\User $user */
            $user = $token->getUser();
            return $user->getNature();
        }

        return null;  // Si l'utilisateur n'est pas connecté, retourne null
    }

    public function getPseudo(): ?string
    {
        $token = $this->tokenStorage->getToken();
        if ($token && $token->getUser() instanceof UserInterface) {
            /** @var \App\Entity\User $user */
            $user = $token->getUser();
            return $user->getPseudo();
        }

        return null;  // Si l'utilisateur n'est pas connecté, retourne null
    }

    public function getMoney(): ?string
    {
        $token = $this->tokenStorage->getToken();
        if ($token && $token->getUser() instanceof UserInterface) {
            /** @var \App\Entity\User $user */
            $user = $token->getUser();
            return $user->getMoney();
        }

        return null;  // Si l'utilisateur n'est pas connecté, retourne null
    }

    public function getExp(): ?string
    {
        $token = $this->tokenStorage->getToken();
        if ($token && $token->getUser() instanceof UserInterface) {
            /** @var \App\Entity\User $user */
            $user = $token->getUser();
            return $user->getExp();
        }

        return null;  // Si l'utilisateur n'est pas connecté, retourne null
    }
    public function getResourceByType(string $type): ?int
    {
        $token = $this->tokenStorage->getToken();
        if ($token && $token->getUser() instanceof UserInterface) {
            /** @var \App\Entity\User $user */
            $user = $token->getUser();
            return $user->getRessourceByType($type)->getValue();
        }

        return null;  // Si l'utilisateur n'est pas connecté, retourne null
    }
    
    public function getDevise(): ?string
    {
        $nature = $this->getNature();
        if ($nature) {
            $devises = [
                "sotoc" => "ça c'est plus fort que le roquefort",
                "altheron" => "il faut manger pour vivre, et non pas vivre pour manger",
                "sora" => "aujourd'hui, on a plus le droit, ni d'avoir faim ni d'avoir froid",
                "flumia" => "loués soient la Terre Mère et le Ciel Père",
                "nano" => "et voilà qui voilà inspecteur Gadget",
            ];
            return $devises[$nature];
        }

        return null;
    }
    public function getDescription(): ?string
    {
        $nature = $this->getNature();
        if ($nature) {
            $descriptions = [
                "sotoc" => "la confrérie des braves au summum de l'arrogance",
                "altheron" => "civilisation non violente à la limite du stoïcisme",
                "sora" => "peuple altruiste avec tendance à l'abnégation",
                "flumia" => "la communauté des respectueux craintifs",
                "nano" => "tribu ingénieuse mais autiste",
            ];
            return $descriptions[$nature];
        }

        return null;
    }
}
