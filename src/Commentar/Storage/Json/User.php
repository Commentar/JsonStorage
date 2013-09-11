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
class User
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
     * Persists the data of the user in the storage file
     *
     * @param \Commentar\DomainObject\User $user The user to store
     */
    public function persist(UserDomainObject $user)
    {
        if ($user->getId() === null) {
            $this->update($user);
        } else {
            $this->insert($user);
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
        $users['users'][$user->getId()] = [
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

        $users['autoincrement']++;

        $users['users'][$users['autoincrement']] = [
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
    private function storeAll(array $users)
    {
        file_put_contents($this->storageLocation, json_encode($users));
    }
}
