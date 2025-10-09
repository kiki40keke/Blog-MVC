<?php

namespace App\Helpers;

use DateTime;
use DateTimeZone;
use InvalidArgumentException;
use RuntimeException;

final class Date
{
    /**
     * Normalise une date entrée en texte vers 'Y-m-d H:i:s'
     * en utilisant le fuseau défini par la constante APP_TIMEZONE.
     *
     * @throws InvalidArgumentException si le format est invalide
     * @throws RuntimeException si APP_TIMEZONE n'est pas défini/valide
     */
    public static function normalizeCreatedAt(string $input): string
    {
        if (!defined('APP_TIMEZONE') || !is_string(APP_TIMEZONE) || APP_TIMEZONE === '') {
            throw new RuntimeException('Constante APP_TIMEZONE non définie.');
        }

        try {
            $tz = new DateTimeZone(APP_TIMEZONE);
        } catch (\Throwable $e) {
            throw new RuntimeException('APP_TIMEZONE invalide : ' . APP_TIMEZONE);
        }

        $input = trim($input);
        if ($input === '') {
            throw new InvalidArgumentException('Date vide.');
        }

        // Formats acceptés (ajoute/retire selon ton formulaire)
        $formats = [
            'Y-m-d H:i:s',
            'Y-m-d H:i',
            'd/m/Y H:i',
            'm/d/Y H:i',
            'Y-m-d\TH:i',
            'Y-m-d\TH:i:s',
        ];

        foreach ($formats as $fmt) {
            $dt = DateTime::createFromFormat($fmt, $input, $tz);
            if ($dt && $dt->format($fmt) === $input) {
                // Si pas de secondes fournies, force :00
                if (strpos($fmt, 's') === false) {
                    $dt->setTime((int)$dt->format('H'), (int)$dt->format('i'), 0);
                }
                return $dt->format('Y-m-d H:i:s'); // <- sortie uniformisée
            }
        }

        throw new InvalidArgumentException('Format de date invalide.');
    }

    public static function toMysql(?string $value): ?string
    {
        if (empty($value)) return null;

        $value = str_replace('T', ' ', $value);
        if (strlen($value) === 16) $value .= ':00';

        return $value;
    }
}
