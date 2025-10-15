<?php

use PHPUnit\Framework\TestCase;
use App\Validators\UserValidator;
use App\Models\Repository\UserRepository;

class UserValidatorTest extends TestCase
{
    private function getRepoMock($hash = null)
    {
        $repo = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findOne'])
            ->getMock();

        // findOne doit retourner le hash si on veut un login valide
        $repo->method('findOne')->willReturn($hash ? ['password' => $hash] : []);
        return $repo;
    }

    public function testValidUserPasses()
    {
        $password = 'supersecret';
        $hash = password_hash($password, PASSWORD_BCRYPT);

        $repo = $this->getRepoMock($hash);

        $data = [
            'username' => 'bob',
            'password' => $password,
            'login'    => true, // le champ peut être n'importe quoi, il doit exister pour la règle
        ];

        $validator = new UserValidator($data, $repo);
        $this->assertTrue($validator->validate());
    }

    public function testMissingUsernameFails()
    {
        $repo = $this->getRepoMock();

        $data = [
            'username' => '',
            'password' => 'hello',
            'login'    => true,
        ];

        $validator = new UserValidator($data, $repo);
        $this->assertFalse($validator->validate());
        $this->assertArrayHasKey('username', $validator->errors());
    }

    public function testInvalidLoginFails()
    {
        $repo = $this->getRepoMock(null); // findOne retourne vide (user inconnu)

        $data = [
            'username' => 'bob',
            'password' => 'wrongpassword',
            'login'    => true,
        ];

        $validator = new UserValidator($data, $repo);
        $this->assertFalse($validator->validate());
        $this->assertArrayHasKey('login', $validator->errors());
    }
}