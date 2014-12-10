<?php

/**
 * This file is part of the TNetstring project and is copyright
 *
 * (c) 2011-2014 Sam Smith <git@samsmith.io>.
 *
 * Please refer the to LICENSE file that was distributed with this source code
 * for the full copyright and license information.
 */

$__tnetstring_last_error = null;

use Phuedx\TNetstring\Codec;

/**
 * Encodes the value as a tagged netstring.
 *
 * @see \Phuedx\TNetstring\Codec#encode
 *
 * @param mixed $value
 * @return string|null The encoded tagged netstring. If an error occurs during
 *   encoding, then `null` is returned. An error *should* only occur if the
 *   value can't be converted to a string, e.g. the value is a resource.
 */
function tnetstring_encode($value)
{
    global $__tnetstring_last_error;
    
    try {
        return __tnetstring_codec()->encode($value);
    } catch (Exception $e) {
        $__tnetstring_last_error = $e->getMessage();
        
        return null;
    }
}

/**
 * Decodes the value or values from the tagged netstring.
 *
 * Note well that if the tagged netstring is a single encoded value, then that
 * value will be returned. If the tagged netstring is a collection of encoded
 * values those values will be returned as an array.
 *
 * @see \Phuedx\TNetstring\Codec#decode
 *
 * @param string $tnetstring
 * @return mixed The decoded value or values. If an error occurs during
 *   decoding, then `null` is returned. An error *should* only occur if either
 *   the tagged netstring is empty or incorrectly encoded.
 */
function tnetstring_decode($tnetstring)
{
    global $__tnetstring_last_error;
    
    try {
        return __tnetstring_codec()->decode($tnetstring);
    } catch (Exception $e) {
        $__tnetstring_last_error = $e->getMessage();
        
        return null;
    }
}

function __tnetstring_codec()
{
    static $codec;

    if (! $codec) {
        $codec = new Codec();
    }

    return $codec;
}

/**
 * Gets the message associated with the last error that occurred during encoding
 * or decoding, if one occurred.
 *
 * @return string|null
 */
function tnetstring_last_error()
{
    global $__tnetstring_last_error;
    
    return $__tnetstring_last_error;
}

/**
 * Clears the message associated with the last error that occurred during
 * encoding or decoding, if one occurred.
 *
 * @return null
 */
function tnetstring_clear_last_error()
{
    global $__tnetstring_last_error;

    $__tnetstring_last_error = null;
}
