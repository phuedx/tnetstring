# TNetstring [![Build Status](https://secure.travis-ci.org/phuedx/tnetstring.png?branch=master)](http://travis-ci.org/phuedx/tnetstring)

A tagged netstring codec for PHP.

## Installation

The recommended way to install TNetstring is with [Composer](https://getcomposer.org/).

```
composer.phar require phuedx/tnetstring
```

## Usage

```php
<?php

require_once __DIR__.'/vendor/autoload.php';

$payload = array(
    'authors' => array(
        array(
            'name'     => 'Sam Smith',
            'email'    => 'yo@samsmith.io',
            'homepage' => 'https://github.com/phuedx',
        ),
    ),
);

$codec = new \Phuedx\TNetstring\Codec();
$encoded = $codec->encode($payload);
$decoded = $codec->decode($encoded);
```

There's also a JSON-like API:

```php
$encoded = tnetstring_encode($payload);
$decoded = tnetstring_decode($encoded);
```

## Resources

[tnetstrings.org](http://web.archive.org/web/20140701085126/http://tnetstrings.org/) _had_ all of the information you'll need to get acquainted with tagged netstrings.

## License

**TNetstring** is licensed under the MIT license and is copyright (c) 2011-2016 Sam Smith. See [LICENSE](./LICENSE) for full copyright and license information.
