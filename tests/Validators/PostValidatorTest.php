<?php

use PHPUnit\Framework\TestCase;
use App\Validators\PostValidator;
use App\Models\Repository\PostRepository;

class PostValidatorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Correction pour PHP 8.1+ : deux arguments pour setValue sur une propriété statique
        $ref = new ReflectionProperty(\App\Validators\ValidatorBase::class, 'rulesRegistered');
        $ref->setAccessible(true);
        $ref->setValue(null, false);
    }

    private function getRepoMock($existsCallback = null)
    {
        $repo = $this->getMockBuilder(PostRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['exists'])
            ->getMock();

        if ($existsCallback) {
            $repo->method('exists')->willReturnCallback($existsCallback);
        } else {
            $repo->method('exists')->willReturn(false);
        }
        return $repo;
    }

    public function testValidPostPasses()
    {
        $repo = $this->getRepoMock();
        $categoriesIds = [1 => 'Tech', 2 => 'PHP'];

        $data = [
            'name'        => 'Mon titre',
            'slug'        => 'mon-titre',
            'content'     => str_repeat('Lorem ipsum ', 3),
            'created_at'  => date('Y-m-d H:i:s'),
            'categories_id' => [1, 2],
            // On enlève le champ 'image' pour éviter le souci de is_uploaded_file en test unitaire
        ];

        $validator = new PostValidator($data, $repo, $categoriesIds);

        $result = $validator->validate();
        $this->assertTrue($result, 'Erreurs de validation : ' . var_export($validator->errors(), true));
    }

    public function testEmptyNameFails()
    {
        $repo = $this->getRepoMock();
        $categoriesIds = [1 => 'Tech'];

        $data = [
            'name'        => '',
            'slug'        => 'slug-valide',
            'content'     => str_repeat('Lorem ipsum ', 3),
            'created_at'  => date('Y-m-d H:i:s'),
            'categories_id' => [1],
        ];

        $validator = new PostValidator($data, $repo, $categoriesIds);
        $validator->validate();
        $this->assertArrayHasKey('name', $validator->errors());
    }

    public function testShortContentFails()
    {
        $repo = $this->getRepoMock();
        $categoriesIds = [1 => 'Tech'];

        $data = [
            'name'        => 'Titre',
            'slug'        => 'titre',
            'content'     => 'Trop court',
            'created_at'  => date('Y-m-d H:i:s'),
            'categories_id' => [1],
        ];

        $validator = new PostValidator($data, $repo, $categoriesIds);
        $validator->validate();
        $this->assertArrayHasKey('content', $validator->errors());
    }

    public function testInvalidSlugFails()
    {
        $repo = $this->getRepoMock();
        $categoriesIds = [1 => 'Tech'];

        $data = [
            'name'        => 'Titre',
            'slug'        => 'Titre_invalide!', // mauvais format
            'content'     => str_repeat('Lorem ipsum ', 3),
            'created_at'  => date('Y-m-d H:i:s'),
            'categories_id' => [1],
        ];

        $validator = new PostValidator($data, $repo, $categoriesIds);
        $validator->validate();
        $this->assertArrayHasKey('slug', $validator->errors());
    }

    public function testNotUniqueNameFails()
    {
        $repo = $this->getRepoMock(function($conditions) {
            // Retourne true seulement si on teste la clé 'name' avec la valeur 'Titre'
            return isset($conditions['name']) && $conditions['name'] === 'Titre';
        });
        $categoriesIds = [1 => 'Tech'];

        $data = [
            'name'        => 'Titre',
            'slug'        => 'titre',
            'content'     => str_repeat('Lorem ipsum ', 3),
            'created_at'  => date('Y-m-d H:i:s'),
            'categories_id' => [1],
        ];

        $validator = new PostValidator($data, $repo, $categoriesIds);
        $validator->validate();
        $this->assertArrayHasKey('name', $validator->errors(), 'Erreurs de validation : ' . var_export($validator->errors(), true));
    }

    public function testEmptyCategoryFails()
    {
        $repo = $this->getRepoMock();
        $categoriesIds = [1 => 'Tech', 2 => 'PHP'];

        $data = [
            'name'        => 'Titre',
            'slug'        => 'titre',
            'content'     => str_repeat('Lorem ipsum ', 3),
            'created_at'  => date('Y-m-d H:i:s'),
            'categories_id' => [],
        ];

        $validator = new PostValidator($data, $repo, $categoriesIds);
        $validator->validate();
        $this->assertArrayHasKey('categories_id', $validator->errors());
    }
}