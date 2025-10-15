<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Helpers\Upload;

final class UploadTest extends TestCase
{
    private string $tempDir;

    protected function setUp(): void
    {
        // Crée un dossier temporaire pour les tests
        $this->tempDir = sys_get_temp_dir() . '/upload_test_' . uniqid();
        mkdir($this->tempDir, 0777, true);

        // Dépendances simulées
        Upload::setPostImgDir($this->tempDir);
        Upload::setImageMimeTypes([
            'image/jpeg' => 'jpg',
            'image/png'  => 'png'
        ]);
        Upload::setSystemFunctions([
            'is_uploaded_file' => fn($file) => true,
            'move_uploaded_file' => function ($src, $dst) { return copy($src, $dst); },
            'is_dir' => fn($dir) => is_dir($dir),
            'mkdir' => fn($dir, $mode, $rec) => mkdir($dir, $mode, $rec),
            'unlink' => fn($file) => unlink($file),
            'realpath' => fn($file) => realpath($file),
            'finfo_factory' => fn() => new class {
                public function file($f) { return 'image/jpeg'; }
            },
        ]);
    }

    protected function tearDown(): void
    {
        // Nettoyage du dossier temporaire
        array_map('unlink', glob($this->tempDir . '/*'));
        rmdir($this->tempDir);
        Upload::resetTestOverrides();
    }

    public function testRenameFileReturnsExpectedName()
    {
        $image = ['tmp_name' => __FILE__];
        $this->assertSame(
            'post-slug.jpg',
            Upload::renameFile($image, 'post slug')
        );
        $this->assertSame(
            'prefix-post-slug.jpg',
            Upload::renameFile($image, 'post slug', 'prefix')
        );
    }

    public function testGuessExtensionReturnsNullIfNotUploaded()
    {
        // Simule un upload non valide
        Upload::setSystemFunctions([
            'is_uploaded_file' => fn($f) => false,
            'finfo_factory' => fn() => new class { public function file($f) { return 'image/jpeg'; } },
        ]);
        $this->assertNull(Upload::guessExtension(['tmp_name' => 'whatever']));
    }

    public function testSaveReturnsTrueAndMovesFile()
    {
        $src = tempnam($this->tempDir, 'src');
        file_put_contents($src, 'data');
        $file = ['tmp_name' => $src];

        $this->assertTrue(Upload::save($file, 'test.jpg'));
        $this->assertFileExists($this->tempDir . DIRECTORY_SEPARATOR . 'test.jpg');
    }

    public function testSaveReturnsFalseIfNotImage()
    {
        Upload::setSystemFunctions([
            'is_uploaded_file' => fn($f) => true,
            'finfo_factory' => fn() => new class { public function file($f) { return 'application/pdf'; } },
        ]);
        $file = ['tmp_name' => __FILE__];
        $this->assertFalse(Upload::save($file, 'wrong.ext'));
    }

    public function testViewImageReturnsImgTag()
    {
        $html = Upload::viewImage('foo.jpg', 'img-class', 'desc');
        $this->assertStringContainsString('<img', $html);
        $this->assertStringContainsString('foo.jpg', $html);
        $this->assertStringContainsString('img-class', $html);
        $this->assertStringContainsString('desc', $html);
    }

    public function testDeleteFileRemovesFile()
    {
        $path = $this->tempDir . '/todelete.jpg';
        file_put_contents($path, 'delete me');
        $this->assertFileExists($path);
        $this->assertTrue(Upload::deleteFile('todelete.jpg'));
        $this->assertFileDoesNotExist($path);
    }

    public function testDeleteFileReturnsFalseOnEmptyName()
    {
        $this->assertFalse(Upload::deleteFile(''));
        $this->assertFalse(Upload::deleteFile(null));
    }
}