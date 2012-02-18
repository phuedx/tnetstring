# TNetstring [![Build Status](https://secure.travis-ci.org/phuedx/tnetstring.png?branch=master)](http://travis-ci.org/phuedx/tnetstring)

A tagged netstring encoder/decoder for PHP.

## Example Usage

```php
<?php

require_once '/path/to/tnetstring/tnetstring.php';

// Let's play a quick game of TNetstring Whispers!

$payload = array(
    'authors' => array(
        array(
            'name'     => 'Sam Smith',
            'email'    => 'samuel.david.smith@gmail.com',
            'homepage' => 'https://github.com/phuedx',
        ),
    ),
);

$encoder    = new TNetstring_Encoder();
$tnetstring = $encoder->encode($payload);
// <=> $tnetstring = tnetstring_encode($payload);

$decoder           = new TNetstring_Decoder();
$equivalentPayload = $decoder->decode($tnetstring);
// <=> $equivalentPayload = tnetstring_decode($tnetstring);
```

## License

**TNetstring** is licensed under the MIT license and is copyright (c) 2011 Sam Smith. See the *LICENSE* file for full copyright and license information.
