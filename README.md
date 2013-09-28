JsonStorage
=

[![Build Status](https://travis-ci.org/Commentar/JsonStorage.png?branch=master)](https://travis-ci.org/Commentar/JsonStorage) [![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/Commentar/JsonStorage/badges/quality-score.png?s=de8f464b0b29483baecf1751883781e40840a621)](https://scrutinizer-ci.com/g/Commentar/JsonStorage/) [![Code Coverage](https://scrutinizer-ci.com/g/Commentar/JsonStorage/badges/coverage.png?s=c0b35e140c622cd80b88b8b9882ad228c253ae73)](https://scrutinizer-ci.com/g/Commentar/JsonStorage/) [![Latest Stable Version](https://poser.pugx.org/Commentar/json-storage/v/stable.png)](https://packagist.org/packages/Commentar/json-storage) [![Total Downloads](https://poser.pugx.org/Commentar/json-storage/downloads.png)](https://packagist.org/packages/Commentar/json-storage)

Storage mechanism for the [Commentar][commentar] project. This storage mechanism requires no database, but rather stores the data encoded in the JSON format on the filesystem.

This should only be used for development purposes to have a portable way of introducing some persistent storage. It should *never* be used in production.

Installation
-

Add the storage mechanism to the project's `composer.json` file:

    "require": {
        "commentar/json-storage": "0.0.*",
    }

Add the default admin user in the `/data/users.json` file:

    {
        "autoincrement": 1,
        "users": {
            "1": {
                "id": 1,
                "username": "PeeHaa",
                "password": "$2y$14$Usk4vuNbzowQihbscOZjcu6RRzPBK3zIn79F8wn.bjczbElrqzbJu",
                "email": "your@mail.com",
                "isHellbanned": false,
                "isAdmin": true
            }
        }
    }

The password should be hashed using PHP's native password hashing function (`password_hash()`). The easiest way to generate the password hash is by either using [this service][hash-service] or by manually running the password hashing function: `echo password_hash('Your super secret password', PASSWORD_DEFAULT, ['cost' => 14]);`.

To start using the storage you will have to start using the provided datamapper factory by this library. An example of retrieving the comment tree of a thread is:

    $domainObjectFactory = new \Commentar\DomainObject\Factory();
    $datamapperFactory   = new \Commentar\Storage\Json\Factory(__DIR__ . '/vendor/commentar/json-storage/data');
    $commentService      = new \Commentar\Service\Comment($domainObjectFactory, $datamapperFactory);

    $commentTree = $commentService->getTree(1);

[commentar]:https://github.com/Commentar/Commentar
[hash-service]:https://passhash.pieterhordijk.com
