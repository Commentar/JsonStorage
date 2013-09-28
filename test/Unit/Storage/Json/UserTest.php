<?php

namespace CommentarTest\Unit\Storage\Json;

use Commentar\Storage\Json\User;

class UserTest extends \PHPUnit_Framework_TestCase
{
    private function resetUsers()
    {
        $originalData = [
            'autoincrement' => 0,
            'users' => new \StdClass(),
        ];

        file_put_contents(__DIR__ . '/../../../Data/users.json', json_encode($originalData));
    }

    /**
     * @covers Commentar\Storage\Json\User::__construct
     */
    public function testConstructCorrectInterface()
    {
        $user = new User(__DIR__ . '/../../../Data');

        $this->assertInstanceOf('\\Commentar\\Storage\\Datamapper\\UserMappable', $user);
    }

    /**
     * @covers Commentar\Storage\Json\User::__construct
     */
    public function testConstructCorrectInstance()
    {
        $user = new User(__DIR__ . '/../../../Data');

        $this->assertInstanceOf('\\Commentar\\Storage\\Json\\User', $user);
    }

    /**
     * @covers Commentar\Storage\Json\User::__construct
     * @covers Commentar\Storage\Json\User::fetchById
     * @covers Commentar\Storage\Json\User::getAll
     */
    public function testFetchByIdDoesNotExist()
    {
        $user = new User(__DIR__ . '/../../../Data');

        $domainObject = new \Commentar\DomainObject\User();

        $this->assertNull($user->fetchById($domainObject, 1));
        $this->assertNull($domainObject->getUsername());
    }

    /**
     * @covers Commentar\Storage\Json\User::__construct
     * @covers Commentar\Storage\Json\User::fetchById
     * @covers Commentar\Storage\Json\User::getAll
     */
    public function testFetchByIdDoesNotExistWithTrailingSlashForStorage()
    {
        $user = new User(__DIR__ . '/../../../Data/');

        $this->assertNull($user->fetchById(new \Commentar\DomainObject\User(), 1));
    }

    /**
     * @covers Commentar\Storage\Json\User::__construct
     * @covers Commentar\Storage\Json\User::fetchById
     * @covers Commentar\Storage\Json\User::getAll
     */
    public function testFetchByIdExists()
    {
        $user = new User(__DIR__ . '/../../../Data');

        $data = [
            'autoincrement' => 1,
            'users' => [
                '10' => [
                    'id'           => 10,
                    'username'     => 'PeeHaa',
                    'email'        => 'peehaa@example.com',
                    'isHellbanned' => false,
                    'isAdmin'      => true,
                ],
            ],
        ];

        file_put_contents(__DIR__ . '/../../../Data/users.json', json_encode($data));

        $domainObject = new \Commentar\DomainObject\User();

        $this->assertNull($user->fetchById($domainObject, 10));
        $this->assertSame('PeeHaa', $domainObject->getUsername());

        $this->resetUsers();
    }

    /**
     * @covers Commentar\Storage\Json\User::__construct
     * @covers Commentar\Storage\Json\User::fetchById
     * @covers Commentar\Storage\Json\User::getAll
     */
    public function testFetchByIdThrowsUpOnInvalidStorage()
    {
        $this->setExpectedException('\\Commentar\\Storage\\InvalidStorageException');

        $user = new User(__DIR__ . '/../../../DataInvalid');

        $domainObject = new \Commentar\DomainObject\User();

        $user->fetchById($domainObject, 1);
    }

    /**
     * @covers Commentar\Storage\Json\User::__construct
     * @covers Commentar\Storage\Json\User::fetchByUsername
     * @covers Commentar\Storage\Json\User::getAll
     */
    public function testFetchByUsernameDoesNotExist()
    {
        $user = new User(__DIR__ . '/../../../Data');

        $domainObject = new \Commentar\DomainObject\User();
        $domainObject->fill([
            'username' => 'PeeHaa',
        ]);

        $this->assertNull($user->fetchByUsername($domainObject));
        $this->assertNull($domainObject->getEmail());
    }

    /**
     * @covers Commentar\Storage\Json\User::__construct
     * @covers Commentar\Storage\Json\User::fetchByUsername
     * @covers Commentar\Storage\Json\User::getAll
     */
    public function testFetchByUsernameExists()
    {
        $user = new User(__DIR__ . '/../../../Data');

        $data = [
            'autoincrement' => 1,
            'users' => [
                '10' => [
                    'id'           => 10,
                    'username'     => 'PeeHaa',
                    'email'        => 'peehaa@example.com',
                    'isHellbanned' => false,
                    'isAdmin'      => true,
                    'password'     => 'x',
                ],
            ],
        ];

        file_put_contents(__DIR__ . '/../../../Data/users.json', json_encode($data));

        $domainObject = new \Commentar\DomainObject\User();
        $domainObject->fill([
            'username' => 'PeeHaa',
        ]);

        $this->assertNull($user->fetchByUsername($domainObject));
        $this->assertSame('peehaa@example.com', $domainObject->getEmail());

        $this->resetUsers();
    }

    /**
     * @covers Commentar\Storage\Json\User::__construct
     * @covers Commentar\Storage\Json\User::fetchByUsername
     * @covers Commentar\Storage\Json\User::getAll
     */
    public function testFetchByUsernameExistsCaseInsensitive()
    {
        $user = new User(__DIR__ . '/../../../Data');

        $data = [
            'autoincrement' => 1,
            'users' => [
                '10' => [
                    'id'           => 10,
                    'username'     => 'PeeHaa',
                    'email'        => 'peehaa@example.com',
                    'isHellbanned' => false,
                    'isAdmin'      => true,
                    'password'     => 'x',
                ],
            ],
        ];

        file_put_contents(__DIR__ . '/../../../Data/users.json', json_encode($data));

        $domainObject = new \Commentar\DomainObject\User();
        $domainObject->fill([
            'username' => 'peehaa',
        ]);

        $this->assertNull($user->fetchByUsername($domainObject));
        $this->assertSame('peehaa@example.com', $domainObject->getEmail());

        $this->resetUsers();
    }

    /**
     * @covers Commentar\Storage\Json\User::__construct
     * @covers Commentar\Storage\Json\User::persist
     * @covers Commentar\Storage\Json\User::insert
     * @covers Commentar\Storage\Json\User::getAll
     * @covers Commentar\Storage\Json\User::storeAll
     * @covers Commentar\Storage\Json\User::fetchById
     */
    public function testPersistInsert()
    {
        $user = new User(__DIR__ . '/../../../Data');

        $domainObject = new \Commentar\DomainObject\User();
        $domainObject->fill([
            'username'     => 'PeeHaa',
            'email'        => 'peehaa@example.com',
            'isHellbanned' => false,
            'isAdmin'      => true,
            'password'     => 'x',
        ]);

        $user->persist($domainObject);

        $storedDomainObject = new \Commentar\DomainObject\User();
        $storedDomainObject->fill([
            'id' => 1,
        ]);

        $user->fetchById($storedDomainObject, 1);

        $this->assertSame('PeeHaa', $storedDomainObject->getUsername());

        $this->resetUsers();
    }

    /**
     * @covers Commentar\Storage\Json\User::__construct
     * @covers Commentar\Storage\Json\User::persist
     * @covers Commentar\Storage\Json\User::insert
     * @covers Commentar\Storage\Json\User::update
     * @covers Commentar\Storage\Json\User::getAll
     * @covers Commentar\Storage\Json\User::storeAll
     * @covers Commentar\Storage\Json\User::fetchById
     */
    public function testPersistUpdate()
    {
        $user = new User(__DIR__ . '/../../../Data');

        $domainObject = new \Commentar\DomainObject\User();
        $domainObject->fill([
            'username'     => 'PeeHaa',
            'email'        => 'peehaa@example.com',
            'isHellbanned' => false,
            'isAdmin'      => true,
            'password'     => 'x',
        ]);

        $user->persist($domainObject);

        $domainObject = new \Commentar\DomainObject\User();
        $domainObject->fill([
            'id'           => 1,
            'username'     => 'NotPeeHaa',
            'email'        => 'peehaa@example.com',
        ]);

        $user->persist($domainObject);

        $updatedDomainObject = new \Commentar\DomainObject\User();

        $user->fetchById($updatedDomainObject, 1);

        $this->assertSame('NotPeeHaa', $updatedDomainObject->getUsername());

        $this->resetUsers();
    }
}
