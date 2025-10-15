<?php

use PHPUnit\Framework\TestCase;
use App\Models\Repository\UserRepository;
use App\Models\Entity\User;

class UserRepositoryTest extends TestCase
{
    public function testFindUserCallsFindByWithUsername()
    {
        // Mock PDO pour le constructeur
        $pdoMock = $this->createMock(PDO::class);

        // Mock UserRepository, en passant le mock PDO au constructeur
        $repo = $this->getMockBuilder(UserRepository::class)
            ->setConstructorArgs([$pdoMock])
            ->onlyMethods(['findBy'])
            ->getMock();

        $expectedUser = new User();
        $repo->expects($this->once())
            ->method('findBy')
            ->with('username', 'toto')
            ->willReturn($expectedUser);

        $result = $repo->findUser('toto');
        $this->assertSame($expectedUser, $result);
    }
}