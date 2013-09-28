<?php
/**
 * User storage for the JSON storage mechanism
 *
 * PHP version 5.4
 *
 * @category   Commentar
 * @package    Storage
 * @subpackage Json
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2013 Pieter Hordijk
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    1.0.0
 */
namespace Commentar\Storage\Json;

use Commentar\Storage\Datamapper\UserMappable;
use Commentar\DomainObject\User as UserDomainObject;
use Commentar\Storage\InvalidStorageException;

/**
 * User storage for the JSON storage mechanism
 *
 * @category   Commentar
 * @package    Storage
 * @subpackage Json
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 */
class User implements UserMappable
{
    /**
     * @var string The location of the user storage file
     */
    private $storageLocation;

    /**
     * Creates instance
     *
     * @param string The base storage location
     */
    public function __construct($storageLocation)
    {
        $this->storageLocation = rtrim($storageLocation, '/') . '/users.json';
    }

    /**
     * Gets the user based on the id
     *
     * @param int $id The id of the user
     *
     * @return null|array The user
     */
    public function fetchById(UserDomainObject $user, $id)
    {
        $users = $this->getAll();

        if (!property_exists($users->users, $id)) {
            return null;
        }

        $userData = $users->users->{$id};

        $user->fill([
            'id'           => $userData->id,
            'username'     => $userData->username,
            'email'        => $userData->email,
            'isHellbanned' => $userData->isHellbanned,
            'isAdmin'      => $userData->isAdmin,
        ]);
    }

    /**
     * Gets the user based on the username
     *
     * @param \Commentar\DomainObject\User $user The user domain object
     */
    public function fetchByUsername(UserDomainObject $user)
    {
        $users = $this->getAll();

        foreach ($users->users as $id => $userData) {
            if (strtolower($userData->username) !== strtolower($user->getUsername())) {
                continue;
            }

            $user->fill([
                'id'           => $userData->id,
                'username'     => $userData->username,
                'password'     => $userData->password,
                'email'        => $userData->email,
                'isHellbanned' => $userData->isHellbanned,
                'isAdmin'      => $userData->isAdmin,
            ]);

            return;
        }
    }

    /**
     * Persists the data of the user in the storage file
     *
     * @param \Commentar\DomainObject\User $user The user to store
     */
    public function persist(UserDomainObject $user)
    {
        if ($user->getId() === null) {
            $this->insert($user);
        } else {
            $this->update($user);
        }
    }

    /**
     * Updates the data of the user in the storage file
     *
     * @param \Commentar\DomainObject\User $user The user to update
     */
    private function update(UserDomainObject $user)
    {
        $users = $this->getAll();
        $users->users->{$user->getId()} = [
            'id'           => $user->getId(),
            'username'     => $user->getUsername(),
            'email'        => $user->getEmail(),
            'isHellbanned' => $user->isHellbanned(),
            'isAdmin'      => $user->isAdmin(),
        ];

        $this->storeAll($users);
    }

    /**
     * Inserts the data of the user in the storage file
     *
     * @param \Commentar\DomainObject\User $user The user to insert
     */
    private function insert(UserDomainObject $user)
    {
        $users = $this->getAll();

        $users->autoincrement++;

        $users->users->{$users->autoincrement} = [
            'id'           => $user->getId(),
            'username'     => $user->getUsername(),
            'email'        => $user->getEmail(),
            'isHellbanned' => $user->isHellbanned(),
            'isAdmin'      => $user->isAdmin(),
        ];

        $this->storeAll($users);
    }

    /**
     * Gets all the user data in the storage
     *
     * @return array All the users from the storage
     * @throws \Commentar\Storage\InvalidStorageException When the storage file could not be read
     */
    private function getAll()
    {
        if (!file_exists($this->storageLocation)) {
            throw new InvalidStorageException(
                'Could not access the user storage file (`' . $this->storageLocation . '`)'
            );
        }

        return json_decode(file_get_contents($this->storageLocation));
    }

    /**
     * Stores all the user data in the storage
     *
     * @param array The users to store
     */
    private function storeAll($users)
    {
        file_put_contents($this->storageLocation, json_encode($users));
    }
}
