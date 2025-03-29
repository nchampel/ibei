<?php

namespace App\Enum;

// enum PurchaseType: string
// {
//     case A = 'achetable';
//     case B = 'acheté';

    // public function getLabel(): string
    // {
    //     return match ($this) {
    //         self::Available => 'Disponible',
    //         self::Borrowed => 'Emprunté',
    //         self::Unavailable => 'Indisponible',
    //     };
    // }
// }
class PurchaseType
{
    const A = 'achetable';
    const B = 'acheté';
}