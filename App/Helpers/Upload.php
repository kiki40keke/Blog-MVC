<?php

namespace App\Helpers;

use App\Config\Config;

final class Upload
{
    public const REL_DIR = '/img/post/';

    // Dépendances injectables pour tests
    private static $postImgDir = null; // Ne pas référencer une constante non définie

    private static $imageMimeTypes = null;

    // Callbacks pour fonctions système
    private static $isUploadedFile;
    private static $moveUploadedFile;
    private static $isDir;
    private static $mkdir;
    private static $unlink;
    private static $realpath;
    private static $finfoFactory;

    // Pour les tests : setters de dépendances
    public static function setPostImgDir(string $dir): void
    {
        self::$postImgDir = $dir;
    }

    public static function setImageMimeTypes(array $types): void
    {
        self::$imageMimeTypes = $types;
    }

    public static function setSystemFunctions(array $functions): void
    {
        // ['is_uploaded_file' => callable, ...]
        self::$isUploadedFile = $functions['is_uploaded_file'] ?? 'is_uploaded_file';
        self::$moveUploadedFile = $functions['move_uploaded_file'] ?? 'move_uploaded_file';
        self::$isDir = $functions['is_dir'] ?? 'is_dir';
        self::$mkdir = $functions['mkdir'] ?? 'mkdir';
        self::$unlink = $functions['unlink'] ?? 'unlink';
        self::$realpath = $functions['realpath'] ?? 'realpath';
        self::$finfoFactory = $functions['finfo_factory'] ?? function() { return new \finfo(FILEINFO_MIME_TYPE); };
    }

    // Réinitialise les dépendances (pour isolation des tests)
    public static function resetTestOverrides(): void
    {
        self::$postImgDir = null;
        self::$imageMimeTypes = null;
        self::$isUploadedFile = 'is_uploaded_file';
        self::$moveUploadedFile = 'move_uploaded_file';
        self::$isDir = 'is_dir';
        self::$mkdir = 'mkdir';
        self::$unlink = 'unlink';
        self::$realpath = 'realpath';
        self::$finfoFactory = function() { return new \finfo(FILEINFO_MIME_TYPE); };
    }

    /**
     * Récupère le chemin du dossier d'upload (test ou prod)
     */
    private static function getPostImgDir(): string
    {
        if (self::$postImgDir !== null) {
            return self::$postImgDir;
        }
        if (defined('POST_IMG_DIR')) {
            return POST_IMG_DIR;
        }
        throw new \RuntimeException('POST_IMG_DIR is not defined and no override set');
    }

    public static function renameFile(array $image, string $slug, ?string $prefix = null): ?string
    {
        $ext = self::guessExtension($image);
        if ($ext === null) {
            return null;
        }
        $safeBase = preg_replace('/[^a-z0-9-_]/i', '-', $slug);
        $filename = $safeBase . '.' . $ext;

        return $prefix === null ? $filename : $prefix . '-' . $filename;
    }

    public static function guessExtension(array $file): ?string
    {
        $isUploadedFile = self::$isUploadedFile ?? 'is_uploaded_file';
        if (empty($file['tmp_name']) || !$isUploadedFile($file['tmp_name'])) {
            return null;
        }

        $finfoFactory = self::$finfoFactory ?? function() { return new \finfo(FILEINFO_MIME_TYPE); };
        $finfo = $finfoFactory();
        $mime = $finfo->file($file['tmp_name']);

        $types = self::$imageMimeTypes ?? Config::IMAGE_MIME_TYPES;
        return $types[$mime] ?? null;
    }

    public static function save(array $file, string $filename): bool
    {
        $ext = self::guessExtension($file);
        if ($ext === null) {
            return false;
        }

        $isDir = self::$isDir ?? 'is_dir';
        $mkdir = self::$mkdir ?? 'mkdir';
        $postImgDir = self::getPostImgDir();

        if (!$isDir($postImgDir) && !$mkdir($postImgDir, 0775, true) && !$isDir($postImgDir)) {
            return false;
        }

        $moveUploadedFile = self::$moveUploadedFile ?? 'move_uploaded_file';
        $target = $postImgDir . DIRECTORY_SEPARATOR . $filename;
        if (!$moveUploadedFile($file['tmp_name'], $target)) {
            return false;
        }

        return true;
    }

    public static function viewImage($nameImage, $class = 'img-thumbnail', $alt = '...')
    {
        if (empty($nameImage)) {
            return '';
        }
        $src = self::REL_DIR . $nameImage;
        return '<img width="150px" height="150px" src="' . htmlspecialchars($src) . '" class="' . htmlspecialchars($class) . '" alt="' . htmlspecialchars($alt) . '">';
    }

    public static function deleteFile(?string $filename): bool
    {
        if (empty($filename)) {
            return false;
        }
        $postImgDir = self::getPostImgDir();
        $realpath = self::$realpath ?? 'realpath';
        $unlink = self::$unlink ?? 'unlink';

        $target = $postImgDir . DIRECTORY_SEPARATOR . $filename;
        $filePath = $realpath($target);
        if (is_file($filePath)) {
            return $unlink($filePath);
        }
        return false;
    }
}

// Initialiser les dépendances par défaut (en prod)
\App\Helpers\Upload::resetTestOverrides();