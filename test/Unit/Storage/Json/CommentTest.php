<?php

namespace CommentarTest\Unit\Storage\Json;

use Commentar\Storage\Json\Comment;

class CommentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Commentar\Storage\Json\Comment::__construct
     */
    public function testConstructCorrectInterface()
    {
        $comment = new Comment(__DIR__ . '/../../../Data');

        $this->assertInstanceOf('\\Commentar\\Storage\\Datamapper\\CommentMappable', $comment);
    }

    /**
     * @covers Commentar\Storage\Json\Comment::__construct
     */
    public function testConstructCorrectInstance()
    {
        $comment = new Comment(__DIR__ . '/../../../Data');

        $this->assertInstanceOf('\\Commentar\\Storage\\Json\\Comment', $comment);
    }

    /**
     * @covers Commentar\Storage\Json\Comment::__construct
     * @covers Commentar\Storage\Json\Comment::createStore
     */
    public function testCreateStore()
    {
        $comment = new Comment(__DIR__ . '/../../../Data');

        $this->assertNull($comment->createStore(1));
        $this->assertTrue(file_exists(__DIR__ . '/../../../Data/comments/1.json'));

        unlink(__DIR__ . '/../../../Data/comments/1.json');
        $this->assertFalse(file_exists(__DIR__ . '/../../../Data/comments/1.json'));
    }

    /**
     * @covers Commentar\Storage\Json\Comment::__construct
     * @covers Commentar\Storage\Json\Comment::createStore
     */
    public function testCreateStoreWithTrailingSlashInStorageLocation()
    {
        $comment = new Comment(__DIR__ . '/../../../Data/');

        $this->assertNull($comment->createStore(1));
        $this->assertTrue(file_exists(__DIR__ . '/../../../Data/comments/1.json'));

        unlink(__DIR__ . '/../../../Data/comments/1.json');
        $this->assertFalse(file_exists(__DIR__ . '/../../../Data/comments/1.json'));
    }

    /**
     * @covers Commentar\Storage\Json\Comment::__construct
     * @covers Commentar\Storage\Json\Comment::createStore
     */
    public function testCreateStoreCorrectlyInitialized()
    {
        $comment = new Comment(__DIR__ . '/../../../Data');

        $this->assertNull($comment->createStore(1));
        $data = json_decode(file_get_contents(__DIR__ . '/../../../Data/comments/1.json'));

        $this->assertSame(0, $data->autoincrement);
        $this->assertSame(0, count($data->comments));

        unlink(__DIR__ . '/../../../Data/comments/1.json');
        $this->assertFalse(file_exists(__DIR__ . '/../../../Data/comments/1.json'));
    }

    /**
     * @covers Commentar\Storage\Json\Comment::__construct
     * @covers Commentar\Storage\Json\Comment::fetchByPostId
     * @covers Commentar\Storage\Json\Comment::getAll
     */
    public function testFetchByPostIdCommentsExists()
    {
        $comment = new Comment(__DIR__ . '/../../../Data');

        $data = [
            'autoincrement' => 1,
            'comments' => [
                '1' => [
                    'id'          => 1,
                    'postId'      => 1,
                    'userId'      => 1,
                    'parent'      => null,
                    'content'     => 'My super awesome content',
                    'timestamp'   => '2013-01-01 00:00:00',
                    'updated'     => null,
                    'score'       => 1,
                    'isReviewed'  => true,
                    'isModerated' => false,
                ],
            ],
        ];

        file_put_contents(__DIR__ . '/../../../Data/comments/1.json', json_encode($data));

        $comments = $comment->fetchByPostId(1);

        $this->assertSame(1, count($comments));

        unlink(__DIR__ . '/../../../Data/comments/1.json');
        $this->assertFalse(file_exists(__DIR__ . '/../../../Data/comments/1.json'));
    }

    /**
     * @covers Commentar\Storage\Json\Comment::__construct
     * @covers Commentar\Storage\Json\Comment::fetchByPostId
     * @covers Commentar\Storage\Json\Comment::getAll
     */
    public function testFetchByPostIdCommentsExistsAndIsUpdated()
    {
        $comment = new Comment(__DIR__ . '/../../../Data');

        $data = [
            'autoincrement' => 1,
            'comments' => [
                '1' => [
                    'id'          => 1,
                    'postId'      => 1,
                    'userId'      => 1,
                    'parent'      => null,
                    'content'     => 'My super awesome content',
                    'timestamp'   => '2013-01-01 00:00:00',
                    'updated'     => '2013-01-01 00:00:00',
                    'score'       => 1,
                    'isReviewed'  => true,
                    'isModerated' => false,
                ],
            ],
        ];

        file_put_contents(__DIR__ . '/../../../Data/comments/1.json', json_encode($data));

        $comments = $comment->fetchByPostId(1);

        $this->assertSame(1, count($comments));

        unlink(__DIR__ . '/../../../Data/comments/1.json');
        $this->assertFalse(file_exists(__DIR__ . '/../../../Data/comments/1.json'));
    }

    /**
     * @covers Commentar\Storage\Json\Comment::__construct
     * @covers Commentar\Storage\Json\Comment::fetchByPostId
     * @covers Commentar\Storage\Json\Comment::getAll
     */
    public function testFetchByPostIdThrowsUpOnInvalidStorage()
    {
        $this->setExpectedException('\\Commentar\\Storage\\InvalidStorageException');

        $comment = new Comment(__DIR__ . '/../../../Data');

        $comments = $comment->fetchByPostId(1);
    }

    /**
     * @covers Commentar\Storage\Json\Comment::__construct
     * @covers Commentar\Storage\Json\Comment::fetchByPostId
     * @covers Commentar\Storage\Json\Comment::getAll
     */
    public function testFetchByPostIdCommentsDoesNotExist()
    {
        $comment = new Comment(__DIR__ . '/../../../Data');

        $data = [
            'autoincrement' => 0,
            'comments' => [],
        ];

        file_put_contents(__DIR__ . '/../../../Data/comments/1.json', json_encode($data));

        $comments = $comment->fetchByPostId(1);

        $this->assertSame(0, count($comments));

        unlink(__DIR__ . '/../../../Data/comments/1.json');
        $this->assertFalse(file_exists(__DIR__ . '/../../../Data/comments/1.json'));
    }

    /**
     * @covers Commentar\Storage\Json\Comment::__construct
     * @covers Commentar\Storage\Json\Comment::persist
     * @covers Commentar\Storage\Json\Comment::insert
     * @covers Commentar\Storage\Json\Comment::getAll
     * @covers Commentar\Storage\Json\Comment::storeAll
     * @covers Commentar\Storage\Json\Comment::fetchByPostId
     */
    public function testPersistInsert()
    {
        $comment = new Comment(__DIR__ . '/../../../Data');

        $data = [
            'autoincrement' => 0,
            'comments' => [],
        ];

        file_put_contents(__DIR__ . '/../../../Data/comments/1.json', json_encode($data));

        $user = new \Commentar\DomainObject\User();
        $user->fill(['id' => 1]);

        $domainObject = new \Commentar\DomainObject\Comment();
        $domainObject->fill([
            'postId'    => 1,
            'user'      => $user,
            'timestamp' => new \DateTime(),
        ]);

        $comment->persist($domainObject);

        $comments = $comment->fetchByPostId(1);

        $this->assertSame(1, count($comments));

        unlink(__DIR__ . '/../../../Data/comments/1.json');
        $this->assertFalse(file_exists(__DIR__ . '/../../../Data/comments/1.json'));
    }

    /**
     * @covers Commentar\Storage\Json\Comment::__construct
     * @covers Commentar\Storage\Json\Comment::persist
     * @covers Commentar\Storage\Json\Comment::update
     * @covers Commentar\Storage\Json\Comment::getAll
     * @covers Commentar\Storage\Json\Comment::storeAll
     * @covers Commentar\Storage\Json\Comment::fetchByPostId
     */
    public function testPersistUpdate()
    {
        $comment = new Comment(__DIR__ . '/../../../Data');

        $data = [
            'autoincrement' => 1,
            'comments' => [
                '1' => [
                    'id'          => 1,
                    'postId'      => 1,
                    'userId'      => 1,
                    'parent'      => null,
                    'content'     => 'My super awesome content',
                    'timestamp'   => '2013-01-01 00:00:00',
                    'updated'     => null,
                    'score'       => 1,
                    'isReviewed'  => true,
                    'isModerated' => false,
                ],
            ],
        ];

        file_put_contents(__DIR__ . '/../../../Data/comments/1.json', json_encode($data));

        $user = new \Commentar\DomainObject\User();
        $user->fill(['id' => 1]);

        $domainObject = new \Commentar\DomainObject\Comment();
        $domainObject->fill([
            'id'        => 1,
            'postId'    => 1,
            'user'      => $user,
            'timestamp' => new \DateTime(),
            'content'   => 'Fresh content',
        ]);

        $comment->persist($domainObject);

        $comments = $comment->fetchByPostId(1);

        $this->assertSame(1, count($comments));
        $this->assertSame('Fresh content', $comments[1]['content']);

        unlink(__DIR__ . '/../../../Data/comments/1.json');
        $this->assertFalse(file_exists(__DIR__ . '/../../../Data/comments/1.json'));
    }
}
