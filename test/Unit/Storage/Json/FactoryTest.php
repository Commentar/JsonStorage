<?php

namespace CommentarTest\Unit\Storage\Json;

use Commentar\Storage\Json\Factory;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Commentar\Storage\Json\Factory::__construct
     */
    public function testConstructCorrectInterface()
    {
        $factory = new Factory(__DIR__ . '/../../../Data');

        $this->assertInstanceOf('\\Commentar\\Storage\\Datamapper\\Builder', $factory);
    }

    /**
     * @covers Commentar\Storage\Json\Factory::__construct
     */
    public function testConstructCorrectInstance()
    {
        $factory = new Factory(__DIR__ . '/../../../Data');

        $this->assertInstanceOf('\\Commentar\\Storage\\Json\\Factory', $factory);
    }

    /**
     * @covers Commentar\Storage\Json\Factory::__construct
     * @covers Commentar\Storage\Json\Factory::build
     */
    public function testBuildDefaultNamespace()
    {
        $factory = new Factory(__DIR__ . '/../../../Data');

        $this->assertInstanceOf('\\Commentar\\Storage\\Json\\User', $factory->build('User'));
    }

    /**
     * @covers Commentar\Storage\Json\Factory::__construct
     * @covers Commentar\Storage\Json\Factory::build
     */
    public function testBuildCustomNamespace()
    {
        $factory = new Factory(__DIR__ . '/../../../Data', '\\CommentarTest\Mocks');

        $this->assertInstanceOf('\\CommentarTest\\Mocks\\Mock', $factory->build('Mock'));
    }

    /**
     * @covers Commentar\Storage\Json\Factory::__construct
     * @covers Commentar\Storage\Json\Factory::build
     */
    public function testBuildCustomNamespaceWithTrailingSlash()
    {
        $factory = new Factory(__DIR__ . '/../../../Data', '\\CommentarTest\\Mocks\\');

        $this->assertInstanceOf('\\CommentarTest\\Mocks\\Mock', $factory->build('Mock'));
    }
}
