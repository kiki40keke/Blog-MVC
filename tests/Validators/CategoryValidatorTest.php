<?php

use PHPUnit\Framework\TestCase;
use App\Validators\CategoryValidator;
use App\Models\Repository\CategoryRepository;

class CategoryValidatorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Correction pour PHP 8.1+ : deux arguments pour setValue sur une propriété statique
        $ref = new ReflectionProperty(\App\Validators\ValidatorBase::class, 'rulesRegistered');
        $ref->setAccessible(true);
        $ref->setValue(null, false);
    }

    private function getRepoMock($existsReturn = false)
    {
        $repo = $this->getMockBuilder(CategoryRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['exists'])
            ->getMock();

        $repo->method('exists')->willReturn($existsReturn);
        return $repo;
    }

    public function testValidCategoryPasses()
    {
        $repo = $this->getRepoMock(false);

        $data = [
            'name' => 'Développement',
            'slug' => 'developpement',
        ];

        $validator = new CategoryValidator($data, $repo);
        $this->assertTrue($validator->validate());
    }

    public function testShortNameFails()
    {
        $repo = $this->getRepoMock(false);

        $data = [
            'name' => 'ab',
            'slug' => 'abc',
        ];

        $validator = new CategoryValidator($data, $repo);
        $this->assertFalse($validator->validate());
        $this->assertArrayHasKey('name', $validator->errors());
    }

    public function testInvalidSlugFails()
    {
        $repo = $this->getRepoMock(false);

        $data = [
            'name' => 'Test',
            'slug' => 'Slug Invalide',
        ];

        $validator = new CategoryValidator($data, $repo);
        $this->assertFalse($validator->validate());
        $this->assertArrayHasKey('slug', $validator->errors());
    }

    public function testNotUniqueSlugFails()
    {
        $repo = $this->getRepoMock(true);

        $data = [
            'name' => 'Test',
            'slug' => 'test',
        ];

        $validator = new CategoryValidator($data, $repo);
        $this->assertFalse($validator->validate());
        $this->assertArrayHasKey('slug', $validator->errors());
    }
}