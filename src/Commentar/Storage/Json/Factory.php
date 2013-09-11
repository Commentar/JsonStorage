<?php
/**
 * Datamapper factory for the JSON storage mechanism
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

/**
 * Datamapper factory for the JSON storage mechanism
 *
 * @category   Commentar
 * @package    Storage
 * @subpackage Json
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 */
class Factory
{
    /**
     * @var string The base storage location
     */
    private $storageLocation;

    /**
     * @var string The base namespace of the datamappers
     */
    private $namespace;

    /**
     * Creates instance
     *
     * @param string $storageLocation The base storage location
     * @param string $namespace       The namespace in which the datamappers reside
     */
    public function __construct($storageLocation, $namespace = '\\Commentar\\Storage\\Json')
    {
        $this->storageLocation = $storageLocation;
        $this->namespace       = rtrim($namespace, '\\') . '\\';
    }

    /**
     * Builds the requested datamapper
     *
     * @param string $name The name of the data mapper to build
     *
     * @return object The datamapper
     */
    public function build($name)
    {
        $fullyQualifiedName = $this->namespace . $name;

        return new $fullyQualifiedName($this->storageLocation);
    }
}
