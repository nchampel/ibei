<?php

namespace App\Services;

class DateFormatterService
{
    public function formatToLongFrench(\DateTimeInterface $date): string
    {
        $formatter = new \IntlDateFormatter(
            'fr_FR',
            \IntlDateFormatter::LONG,
            \IntlDateFormatter::NONE,
            'Europe/Paris',
            \IntlDateFormatter::GREGORIAN,
            'd MMMM yyyy'
        );

        return $formatter->format($date); // ex: 12 avril 2025
    }
}
