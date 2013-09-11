<?php
/**
 * Comment storage for the JSON storage mechanism
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

use Commentar\DomainObject\Comment as CommentDomainObject;
use Commentar\Storage\InvalidStorageException;

/**
 * Comment storage for the JSON storage mechanism
 *
 * @category   Commentar
 * @package    Storage
 * @subpackage Json
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 */
class Comment
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
        $this->storageLocation = rtrim($storageLocation, '/') . '/comments/';
    }

    /**
     * Persists the data of the comment in the storage file
     *
     * @param \Commentar\DomainObject\Comment $comment The comment to store
     */
    public function persist(CommentDomainObject $comment)
    {
        if ($comment->getId() === null) {
            $this->update($comment);
        } else {
            $this->insert($comment);
        }
    }

    /**
     * Updates the data of the comment in the storage file
     *
     * @param \Commentar\DomainObject\Comment $comment The comment to update
     */
    private function update(CommentDomainObject $comment)
    {
        $comments = $this->getAll($comment->getPostId());
        $comments['comments'][$comment->getId()] = [
            'id'          => $comment->getId(),
            'postId'      => $comment->getPostId(),
            'userId'      => $comment->getUser()->getId(),
            'parent'      => $comment->getParent(),
            'content'     => $comment->getContent(),
            'timestamp'   => $comment->getTimestamp()->fomat('Y-m-d H:i:s'),
            'isReviewed'  => $comment->isReviewed(),
            'isModerated' => $comment->isModerated(),
        ];

        $this->storeAll($comments);
    }

    /**
     * Inserts the data of the comment in the storage file
     *
     * @param \Commentar\DomainObject\Comment $comment The comment to insert
     */
    private function insert(CommentDomainObject $comment)
    {
        $comments = $this->getAll();

        $comments['autoincrement']++;

        $comments['users'][$comments['autoincrement']] = [
            'id'          => $comment->getId(),
            'postId'      => $comment->getPostId(),
            'userId'      => $comment->getUser()->getId(),
            'parent'      => $comment->getParent(),
            'content'     => $comment->getContent(),
            'timestamp'   => $comment->getTimestamp()->fomat('Y-m-d H:i:s'),
            'isReviewed'  => $comment->isReviewed(),
            'isModerated' => $comment->isModerated(),
        ];

        $this->storeAll($comment->getPostId(), $comments);
    }

    /**
     * Gets all the comments data in the storage
     *
     * @param mixed $id The is of the post
     *
     * @return array All the comments from the storage
     * @throws \Commentar\Storage\InvalidStorageException When the storage file could not be read
     */
    private function getAll($id)
    {
        $storageLocation = $this->storageLocation . $id . '.json';

        if (!file_exists($storageLocation)) {
            throw new InvalidStorageException(
                'Could not access the comment storage file (`' . $storageLocation . '`)'
            );
        }

        return json_decode(file_get_contents($storageLocation));
    }

    /**
     * Stores all the user data in the storage
     *
     * @param mixed $id The is of the post
     * @param array The users to store
     */
    private function storeAll($id, array $comments)
    {
        $storageLocation = $this->storageLocation . $id . '.json';

        file_put_contents($storageLocation, json_encode($comments));
    }
}
