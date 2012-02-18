# TNetstring

[![Build Status](https://secure.travis-ci.org/phuedx/tnetstring.png?branch=master)](http://travis-ci.org/phuedx/tnetstring)

A super-simple typed netstring encoder/decoder for PHP.

## Coder meet TNetstring. TNetstring meet coder!

```php
<?php

require_once '/path/to/tnetstring/tnetstring.php';

$data       = $_REQUEST;
$tnetstring = '';

try {
    $encoder   = new TNetstring_Encoder();
    $tnestring = $encoder->encode($data);
} catch (Exception $e) {
    // ...
}

// Alternatively
$tnestring = tnetstring_encode($data);

// Always check if there was an error. ALWAYS!
if (tnetstring_last_error()) {
    // ...
    
    tnestring_clear_last_error();
}

// It's very much the same for decoding!
try {
    $decoder = new TNetstring_Decoder();
    $data    = $decoder->decode($tnetstring);
} catch (Exception $e) {
    // ...
}

// For completeness...
$data = tnetstring_decode($tnetstring);

if (tnetstring_last_error()) {
    // ...
    
    tnetstring_clear_last_error();
}

```

## License

**TNetstring** is licensed under the MIT license and is copyright (c) 2011 Sam
Smith. See the *LICENSE* file for full copyright and license
information.
