<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TwigExtensions extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('duration_format', [$this, 'formatDuration']),
            new TwigFilter('format_number', [$this, 'formatNumber']),
        ];
    }

    public function formatDuration(int $seconds): string
    {
        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $remainingSeconds = $seconds % 60;

        // if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $remainingSeconds);
        // }

        // return sprintf('%02d:%02d', $minutes, $remainingSeconds);
    }
     // Filtre pour formater les nombres (ajouter un espace comme séparateur de milliers)
     public function formatNumber($number): string
     {
         // Vérifie que c'est un nombre, puis formate
         if (is_numeric($number)) {
             return number_format($number, 0, ',', ' ');
         }
 
         return $number; // Retourne la valeur sans changement si ce n'est pas un nombre
     }
    
}
