<?php

/**
 * This file is part of the TNetstring project and is copyright
 * 
 * (c) 2011-2014 Sam Smith <git@samsmith.io>.
 *
 * Please refer the to LICENSE file that was distributed with this source code
 * for the full copyright and license information.
 */

/**
 * A tagged netstring decoder.
 *
 * Example usage:
 *
 * <code>
 * $decoder = new TNetstring_Decoder();
 *
 * try {
 *     $decoder->decode("13:I'm a teapot.,");
 * } catch (Exception $e) {
 *     // ...
 * }
 * </code>
 */
class TNetstring_Decoder
{
    /**
     * Decodes the value or values from the tagged netstring.
     *
     * Note well that if the tagged netstring is a single encoded value, then
     * that value will be returned. If the tagged netstring is a collection of
     * encoded values those values will be returned as an array.
     *
     * @param string $tnetstring
     * @return mixed The decoded value or values
     * @throws InvalidArgumentException If the tagged netstring is an empty
     *   string
     * @throws RuntimeException If the tagged netstring is incorrectly encoded
     */
    public function decode($tnetstring) {
        if ( ! $tnetstring) {
            throw new InvalidArgumentException("Can't decode an empty tnetstring.");
        }

        $remaining = $tnetstring;
        $values    = array();
        
        while ($remaining) {  
            list($size, $data) = explode(':', $remaining, 2);
            $size              = intval($size);
            $payload           = substr($data, 0, $size);
            $remaining         = substr($data, $size);

            if ( ! $remaining) {
                throw new RuntimeException(sprintf(
                    'The size of the payload "%s" (%d) isn\'t the same as specified (%d).',
                    $payload,
                    strlen($payload),
                    $size
                ));
            }
        
            $payloadType = $remaining[0];
            $remaining   = substr($remaining, 1);
            $values[]    = $this->convertPayloadToPHPValue($payload, $payloadType);
        }
        
        return count($values) > 1 ? $values : $values[0];
    }

    protected function convertPayloadToPHPValue($payload, $type) {
        // ++$complexityRequiredToConvertValue;
        switch ($type) {
            case '^':
                return floatval($payload);
            case '!':
                return strcasecmp($payload, 'true') == 0;
            case '~':
                if ($payload) {
                    throw new RuntimeException("\"$payload\" is encoded as null but (obviously) isn't.");
                }

                return null;
            case '#':
                $result = intval($payload);

                if (strcmp($result, $payload) != 0) {
                    // Overflow?
                    if ($result == PHP_INT_MAX) {
                        throw new RuntimeException("The payload \"$payload\" was encoded as an integer is larger than " . PHP_INT_MAX);
                    }

                    throw new RuntimeException("The payload \"$payload\" was encoded as an integer but (obviously) isn't.");
                }

                return intval($payload);

            case ']':
                return $this->decodeList($payload);
            case '}':
                return $this->decodeDictionary($payload);
        }
        
        // throw new RuntimeException("");
        return $payload;
    }
    
    protected function decodeList($payload) {
        if ( ! $payload) {
            return array();
        }
    
        return $this->decode($payload);
    }
    
    protected function decodeDictionary($payload) {
        $list   = $this->decodeList($payload);
        $result = array();
        
        // Since a TNetstring dictionary is a list of key/value pairs
        if (count($list) % 2 != 0) {
            throw new RuntimeException("The dictionary \"$payload\" is missing either a key or a value.");
        }
        
        for ($i = 0; isset($list[$i]); $i += 2) {
            if ( ! is_string($list[$i])) {
                throw new RuntimeException("{$list[$i]} isn't a valid dictionary key.");
            }
            
            $result[$list[$i]] = $list[$i + 1];
        }
        
        return $result;
    }
}
