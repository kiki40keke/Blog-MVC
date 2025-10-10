<?php

namespace App\Validators;

use Valitron\Validator as ValitronValidator;
use App\Models\Repository\Repository;
use App\Config\Config;
use finfo;

abstract class ValidatorBase
{
    protected ValitronValidator $validator;
    protected Repository $Repository;
    protected static bool $rulesRegistered = false;
    private const DEFAULT_DATE_FORMATS = [
        'Y-m-d H:i:s',
        'Y-m-d H:i',
        'd/m/Y H:i',
        'm/d/Y H:i',
        'Y-m-d\TH:i',
        'Y-m-d\TH:i:s',
    ];

    public function __construct(array $data, Repository $Repository)
    {
        $this->Repository = $Repository;
        ValitronValidator::lang('fr');

        $this->validator = new ValitronValidator($data);

        // Charger les règles custom une seule fois
        self::registerRules($this->Repository, $data);
    }

    /**
     * Enregistre les règles personnalisées
     */
    protected static function registerRules(Repository $Repository, array $data): void
    {
        // Slug format
        ValitronValidator::addRule(
            'slugFormat',
            function (string $field, $value): bool {
                if (!is_string($value)) return false;
                return (bool) preg_match('/^(?:[a-z0-9]+(?:-[a-z0-9]+)*)$/', $value);
            },
            'invalide (a-z, 0-9 et tirets uniquement).'
        );


        // $data = $_POST;  // ou la source de tes valeurs
        ValitronValidator::addRule(
            'unique',
            function (string $field, $value, array $params) use ($Repository, $data): bool {
                $columnsSpec = $params[0] ?? $field;
                $excludeId   = isset($params[1]) ? (int)$params[1] : null;

                $conditions  = [];
                $valuesToCheck = [];

                // Cas string
                if (is_string($columnsSpec)) {
                    $conditions[$columnsSpec] = $value;
                    $valuesToCheck[] = $value;
                }
                // Cas array
                elseif (is_array($columnsSpec)) {
                    $isList = array_keys($columnsSpec) === range(0, count($columnsSpec) - 1);

                    if ($isList) {
                        foreach ($columnsSpec as $col) {
                            $val = ($col === $field) ? $value : ($data[$col] ?? null);
                            $conditions[$col] = $val;
                            $valuesToCheck[]  = $val;
                        }
                    } else {
                        foreach ($columnsSpec as $col => $srcField) {
                            $val = ($srcField === $field) ? $value : ($data[$srcField] ?? null);
                            $conditions[$col] = $val;
                            $valuesToCheck[]  = $val;
                        }
                    }
                } else {
                    return false; // type non supporté => on échoue
                }

                // >>> Garde-fou : si un champ est null ou '', on échoue
                foreach ($valuesToCheck as $v) {
                    if ($v === null || $v === '') {
                        return false;
                    }
                }

                // Tous présents -> on peut vérifier en BDD
                return !$Repository->exists($conditions, $excludeId);
            },
            ' est déjà utilisée.'
        );

        ValitronValidator::addRule(
            'existsInDb',
            function (string $field, $value, array $params) use ($Repository, $data): bool {
                $columnsSpec   = $params[0] ?? $field;
                $excludeId     = isset($params[1]) ? (int)$params[1] : null;

                $conditions    = [];
                $valuesToCheck = [];

                if (is_string($columnsSpec)) {
                    $conditions[$columnsSpec] = $value;
                    $valuesToCheck[] = $value;
                } elseif (is_array($columnsSpec)) {
                    $isList = array_keys($columnsSpec) === range(0, count($columnsSpec) - 1);
                    if ($isList) {
                        foreach ($columnsSpec as $col) {
                            $val = ($col === $field) ? $value : ($data[$col] ?? null);
                            $conditions[$col] = $val;
                            $valuesToCheck[]  = $val;
                        }
                    } else {
                        foreach ($columnsSpec as $col => $srcField) {
                            $val = ($srcField === $field) ? $value : ($data[$srcField] ?? null);
                            $conditions[$col] = $val;
                            $valuesToCheck[]  = $val;
                        }
                    }
                } else {
                    return false; // type non supporté => échec
                }

                // Garde-fou : si un champ est vide, échec
                foreach ($valuesToCheck as $v) {
                    if ($v === null || $v === '') return false;
                }

                // Valide si ça existe (inverse de unique)
                return $Repository->exists($conditions, $excludeId);
            },
            " n'existe pas."
        );


        // Au moins N éléments sélectionnés (par défaut 1)
        ValitronValidator::addRule(
            'minCount',
            function (string $field, $value, array $params): bool {
                $min = (int) ($params[0] ?? 1);
                return is_array($value)
                    && count(array_filter($value, fn($v) => $v !== '' && $v !== null)) >= $min;
            },
            'sélectionnez au moins {0} élément(s).'
        );
        $defaultFormats = self::DEFAULT_DATE_FORMATS;

        // Chaque valeur doit être un entier > 0
        ValitronValidator::addRule(
            'eachInt',
            function (string $field, $value): bool {
                if (!is_array($value)) return false;
                foreach ($value as $v) {
                    $i = filter_var($v, FILTER_VALIDATE_INT);
                    if ($i === false || $i <= 0) return false;
                }
                return true;
            },
            'contient des valeurs invalides.'
        );

        ValitronValidator::addRule(
            'dateAnyOf',
            function (string $field, $value, array $params) use ($defaultFormats): bool {
                if (!is_string($value) || $value === '') return false;

                // Si on reçoit [[...]] (un seul paramètre qui est un array), aplatir
                if (count($params) === 1 && is_array($params[0])) {
                    $params = $params[0];
                }

                // Formats à utiliser : ceux fournis, sinon les défauts
                $formats = !empty($params) ? $params : $defaultFormats;

                foreach ($formats as $fmt) {
                    if (!is_string($fmt)) continue;
                    $dt = \DateTime::createFromFormat($fmt, $value);
                    if ($dt && $dt->format($fmt) === $value) {
                        return true;
                    }
                }
                return false;
            },
            'a un format de date invalide.'
        );

        ValitronValidator::addRule(
            'validLogin',
            function (string $field, $value, array $params) use ($Repository, $data): bool {
                $usernameField = $params[0] ?? 'username'; // champs FORM
                $passwordField = $params[1] ?? 'password'; // champs FORM

                // colonnes SQL (si différentes)
                $usernameCol   = $params[2] ?? $usernameField;
                $passwordCol   = $params[3] ?? 'password';

                // <<< ne dépend plus de $field
                $username = isset($data[$usernameField]) ? trim((string)$data[$usernameField]) : null;
                $password = isset($data[$passwordField]) ? (string)$data[$passwordField] : null;
                //dd($usernameCol, $username, $passwordCol, $password);

                // Laisse 'required' gérer si vide
                if ($username === null || $username === '' || $password === null || $password === '') {
                    return true;
                }

                // Récupère uniquement le hash
                $row = $Repository->findOne([$usernameCol => $username], [$passwordCol]);
                if (!$row || empty($row[$passwordCol])) {
                    return false; // pseudo inconnu
                }

                return password_verify($password, $row[$passwordCol]);
            },
            'Pseudo ou mot de passe incorrect.'
        );

        ValitronValidator::addRule('imageValid', function ($field, $value, array $params) {
            $isEdit = isset($params[0]) && $params[0] !== null && $params[0] !== '';
            // En édition : image optionnelle
            if ($isEdit) {
                if (empty($value['size'])) return true;
            } else { // En création : image obligatoire
                if (empty($value['size'])) return false;
            }

            $mimes = Config::IMAGE_MIME_TYPES;
            if (empty($value['size'])) return true;
            if (empty($value['tmp_name']) || !is_uploaded_file($value['tmp_name'])) return false;

            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($value['tmp_name']);

            // ✅ Validation basée sur la même constante
            return in_array($mime, array_keys(Config::IMAGE_MIME_TYPES), true);
        }, " une image valide est requise.");
    }

    public function validate(): bool
    {
        return $this->validator->validate();
    }

    public function errors(): array
    {
        return $this->validator->errors();
    }
}
