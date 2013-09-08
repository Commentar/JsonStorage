JsonStorage
=

[![Build Status](https://travis-ci.org/Commentar/JsonStorage.png?branch=master)](https://travis-ci.org/Commentar/JsonStorage) [![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/Commentar/JsonStorage/badges/quality-score.png?s=de8f464b0b29483baecf1751883781e40840a621)](https://scrutinizer-ci.com/g/Commentar/JsonStorage/) [![Code Coverage](https://scrutinizer-ci.com/g/Commentar/JsonStorage/badges/coverage.png?s=c0b35e140c622cd80b88b8b9882ad228c253ae73)](https://scrutinizer-ci.com/g/Commentar/JsonStorage/) [![Latest Stable Version](https://poser.pugx.org/Commentar/json-storage/v/stable.png)](https://packagist.org/packages/Commentar/json-storage) [![Total Downloads](https://poser.pugx.org/Commentar/json-storage/downloads.png)](https://packagist.org/packages/Commentar/json-storage)

Storage mechanism for the [Commentar][commentar] project. This storage mechanism requires no database, but rather stores the data encoded in the JSON format on the filesystem.

Installation
-

Add the storage mechanism to the project's `composer.json` file:

    "require": {
        "commentar/json-storage": "0.0.*",
    }

Setup the storage in the bootstrap file:

    $storage = new \Commentar\Storage\Json();

[commentar]:https://github.com/Commentar/Commentar
