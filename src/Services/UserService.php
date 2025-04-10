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
}