<?php

/**
 * This file is part of the TNetstring project and is copyright
 *
 * (c) 2011-2014 Sam Smith <git@samsmith.io>.
 *
 * Please refer the to LICENSE file that was distributed with this source code
 * for the full copyright and license information.
 */

namespace Phuedx\TNetstring;

use InvalidArgumentException;

/**
 * A tagged netstring encoder.
 *
 * Example usage:
 *
 * <code>
 * $encoder = new \Phuedx\TNetstring\Encoder();
 *
 * try {
 *     $tnetstring = $encoder->encode("I'm a teapot.");
 * } catch (Exception $e) {
 *     // ...
 * }
 * </pre>
 */
class Encoder
{
    const T_NULL       = '~';
    const T_BOOL       = '!';
    const T_INT        = '#';
    const T_FLOAT      = '^';
    const T_STRING     = ',';
    const T_LIST       = ']';
    const T_DICTIONARY = '}';
    
    /**
     * Encodes the value as a tagged netstring.
     *
     * @see TNetstring_Encoder#encode
     *
     * @param mixed $value
     * @return string
     * @throws InvalidArgumentException If the value can't be converted to a
     *   string, e.g. the value is a resource
     */
    public function encode($value)
    {
        switch (true) {
            case is_null($value):
                return $this->encodeString('', self::T_NULL);
            case is_bool($value):
                return $this->encodeString($value ? 'true' : 'false', self::T_BOOL);
            case is_int($value):
                return $this->encodeString($value, self::T_INT);
            case is_float($value):
                return $this->encodeString($value, self::T_FLOAT);
            case is_array($value):
                return $this->encodePHPArray($value);

            case is_resource($value):
                throw new InvalidArgumentException("You can't encode a PHP resource as a tagged netstring.");

            default:
                return $this->encodeString($value);
        }
    }
    
    protected function encodeString($value, $type = self::T_STRING)
    {
        $value = (string) $value;
        
        return sprintf('%d:%s%s', strlen($value), $value, $type);
        
    }
    
    protected function encodePHPArray($array)
    {
        $isList = true;
        $result = '';
    
        foreach ($array as $key => $value) {
            if (is_string($key)) {
                $isList = false;
                
                break;
            }
        }
        
        foreach ($array as $key => $value) {
            $result .= $isList
                ? $this->encode($value)
                : $this->encodeString($key) . $this->encode($value);
        }
        
        $result = $this->encodeString($result, $isList ? self::T_LIST : self::T_DICTIONARY);
        
        return $result;
    }
}
