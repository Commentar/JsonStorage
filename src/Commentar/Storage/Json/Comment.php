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

use Commentar\Storage\Datamapper\CommentMappable;
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
class Comment implements CommentMappable
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
     * Creates the storage file
     *
     * @param mixed $id The id
     */
    public function createStore($id)
    {
        $comments = [
            'autoincrement' => 0,
            'comments' => [],
        ];

        file_put_contents($this->storageLocation . $id . '.json', json_encode($comments));
    }

    /**
     * Fetches all comments based on the post id
     *
     * @param mixed $postId The id of the post of which to fetch the comments
     *
     * @return array List of all the comments on the post
     */
    public function fetchByPostId($id)
    {
        $commentsInfo = $this->getAll($id);
        $comments     = $commentsInfo->comments;

        $parsedComments = [];
        foreach ($comments as $id => $comment) {
            $parsedComments[$id] = [
                'id'          => $comment->id,
                'postId'      => $comment->postId,
                'userId'      => $comment->userId,
                'parent'      => $comment->parent,
                'content'     => $comment->content,
                'timestamp'   => new \DateTime($comment->timestamp),
                'updated'     => null,
                'score'       => $comment->score,
                'isReviewed'  => $comment->isReviewed,
                'isModerated' => $comment->isModerated,
            ];

            if ($comment->updated !== null) {
                $parsedComments[$id]['updated'] = new \DateTime($comment->updated);
            }
        }

        return $parsedComments;
    }

    /**
     * Persists the data of the comment in the storage file
     *
     * @param \Commentar\DomainObject\Comment $comment The comment to store
     */
    public function persist(CommentDomainObject $comment)
    {
        if ($comment->getId() === null) {
            $this->insert($comment);
        } else {
            $this->update($comment);
        }
    }

    /**
     * Inserts the data of the comment in the storage file
     *
     * @param \Commentar\DomainObject\Comment $comment The comment to insert
     */
    private function insert(CommentDomainObject $comment)
    {
        $comments = $this->getAll($comment->getPostId());

        $comments->autoincrement++;

        if (is_array($comments->comments)) {
            $comments->comments = new \StdClass();
        }

        $comments->comments->{$comments->autoincrement} = [
            'id'          => $comments->autoincrement,
            'postId'      => $comment->getPostId(),
            'userId'      => $comment->getUser()->getId(),
            'parent'      => $comment->getParent(),
            'content'     => $comment->getContent(),
            'timestamp'   => $comment->getTimestamp()->format('Y-m-d H:i:s'),
            'isReviewed'  => $comment->isReviewed(),
            'isModerated' => $comment->isModerated(),
            'score'       => $comment->getScore(),
            'updated'     => $comment->getUpdated(),
        ];

        $this->storeAll($comment->getPostId(), $comments);
    }

    /**
     * Updates the data of the comment in the storage file
     *
     * @param \Commentar\DomainObject\Comment $comment The comment to update
     */
    private function update(CommentDomainObject $comment)
    {
        $comments = $this->getAll($comment->getPostId());

        $comments->comments->{$comment->getId()} = [
            'id'          => $comment->getId(),
            'postId'      => $comment->getPostId(),
            'userId'      => $comment->getUser()->getId(),
            'parent'      => $comment->getParent(),
            'content'     => $comment->getContent(),
            'timestamp'   => $comment->getTimestamp()->format('Y-m-d H:i:s'),
            'isReviewed'  => $comment->isReviewed(),
            'isModerated' => $comment->isModerated(),
            'score'       => $comment->getScore(),
            'updated'     => $comment->getUpdated(),
        ];

        $this->storeAll($comment->getPostId(), $comments);
    }

    /**
     * Gets all the comments data in the storage
     *
     * @param mixed $id The id of the post
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
     * @param object The users to store
     */
    private function storeAll($id, $comments)
    {
        $storageLocation = $this->storageLocation . $id . '.json';

        file_put_contents($storageLocation, json_encode($comments));
    }
}
