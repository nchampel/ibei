<?php

namespace App\Services;

class AlignementService
{
    // pas utilisé
    public function getColor(): string
    {
        /** @var \App\Entity\User $user */
        // $user = $this->getUser();
        $color = 'pas user';
        // if($user){
        //     $color = $user->getNature();
        // }
        return $color;
    }
}