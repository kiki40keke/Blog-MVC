<?php

namespace App\Helpers;

use App\Config\Config;



final class Upload
{
    /** Dossier web (relatif à /public) pour servir les images */
    public const REL_DIR = '/img/post/';

    public static function renameFile(array $image, string $slug, ?string $prefix = null): ?string
    {


        $ext = Upload::guessExtension($image);
        if ($ext === null) {
            return null;
        }
        // Sécuriser le nom (slug safe)
        $safeBase = preg_replace('/[^a-z0-9-_]/i', '-', $slug);
        $filename = $safeBase . '.' . $ext;

        if ($prefix === null) {
            return $filename;
        } else {
            return $prefix . '-' . $filename;
        }
    }

    /**
     * Détermine l'extension à partir du MIME réel.
     */
    public static function guessExtension(array $file): ?string
    {
        if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return null;
        }
        
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);

        // ✅ Recherche directe dans la constante Config
        return Config::IMAGE_MIME_TYPES[$mime] ?? null;
    }

    /**
     * Sauvegarde le fichier avec le nom fourni (ex: slug) et renvoie le chemin web (ex: "img/post/mon-slug.jpg").
     * Retourne null si échec ou type non autorisé.
     */
    public static function save(array $file, string $filename): bool
    {
        $ext = self::guessExtension($file);
        if ($ext === null) {
            return false; // type non supporté ou upload invalide
        }



        // Créer le dossier si nécessaire
        if (!is_dir(POST_IMG_DIR) && !mkdir(POST_IMG_DIR, 0775, true) && !is_dir(POST_IMG_DIR)) {
            return false; // impossible de créer le dossier
        }

        // Déplacer le fichier
        $target = POST_IMG_DIR . DIRECTORY_SEPARATOR . $filename;
        if (!move_uploaded_file($file['tmp_name'], $target)) {
            return false;
        }

        return true;
    }


    public static function viewImage($nameImage, $class = 'img-thumbnail', $alt = '...')
    {
        if (empty($nameImage)) {
            return '';
        }
        // Si tu veux que toutes les images soient dans /img/post/
        $src = self::REL_DIR . $nameImage;
        return '<img width="150px" height="150px" src="' . htmlspecialchars($src) . '" class="' . htmlspecialchars($class) . '" alt="' . htmlspecialchars($alt) . '">';
    }

    public static function deleteFile(?string $filename): bool
    {
        if (empty($filename)) {
            return false;
        }
        $target = POST_IMG_DIR . DIRECTORY_SEPARATOR . $filename;
        $filePath = realpath($target);
        if (is_file($filePath)) {
            return unlink($filePath);
        }
        return false;
    }
}
