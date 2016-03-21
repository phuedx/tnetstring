<?php

/**
 * This file is part of the TNetstring project and is copyright
 *
 * (c) 2011-2016 Sam Smith <git@samsmith.io>.
 *
 * Please refer the to LICENSE file that was distributed with this source code
 * for the full copyright and license information.
 */

namespace Phuedx\TNetstring;

use InvalidArgumentException;
use RuntimeException;

/**
 * A tagged netstring codec.
 *
 * Example usage:
 *
 * <code>
 * $codec = new \Phuedx\TNetstring\Codec();
 *
 * try {
 *     $encoded = $codec->encode("13:I'm a teapot.,");
 *     $decoded = $codec->decode($encoded);
 * } catch (Exception $e) {
 *     // ...
 * }
 * </code>
 */
class Codec
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
    public function decode($tnetstring)
    {
        if (! $tnetstring) {
            throw new InvalidArgumentException("Can't decode an empty tnetstring.");
        }

        $remaining = $tnetstring;
        $values    = array();

        while ($remaining) {
            list($size, $data) = explode(':', $remaining, 2);
            $size              = intval($size);
            $payload           = substr($data, 0, $size);
            $remaining         = substr($data, $size);

            if (! $remaining) {
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

    protected function convertPayloadToPHPValue($payload, $type)
    {
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
                        throw new RuntimeException(
                            "The payload \"$payload\" was encoded as an integer is larger than " . PHP_INT_MAX
                        );
                    }

                    throw new RuntimeException(
                        "The payload \"$payload\" was encoded as an integer but (obviously) isn't."
                    );
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

    protected function decodeList($payload)
    {
        if (! $payload) {
            return array();
        }

        return $this->decode($payload);
    }

    protected function decodeDictionary($payload)
    {
        $list   = $this->decodeList($payload);
        $result = array();

        // Since a TNetstring dictionary is a list of key/value pairs
        if (count($list) % 2 != 0) {
            throw new RuntimeException("The dictionary \"$payload\" is missing either a key or a value.");
        }

        for ($i = 0; isset($list[$i]); $i += 2) {
            if (! is_string($list[$i])) {
                throw new RuntimeException("{$list[$i]} isn't a valid dictionary key.");
            }

            $result[$list[$i]] = $list[$i + 1];
        }

        return $result;
    }
}
