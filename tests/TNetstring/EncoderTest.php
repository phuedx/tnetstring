<?php

/**
 * This file is part of the TNetstring project and is copyright
 * 
 * (c) 2011-2014 Sam Smith <git@samsmith.io>.
 *
 * Please refer the to LICENSE file that was distributed with this source code
 * for the full copyright and license information.
 */

class TNetstring_EncoderTest extends PHPUnit_Framework_TestCase
{
    protected $encoder;
    
    public function setUp() {
        $this->encoder = new TNetstring_Encoder();
    }
    
    public static function encodeDataProvider() {
        return array(
            array(array(), '0:]'),
            array(
                array('hello' => array(12345, 'this', true, null, "\x00\x00\x00\x00")),
                "44:5:hello,32:5:12345#4:this,4:true!0:~4:\x00\x00\x00\x00,]}",
            ),
            array(12345, '5:12345#'),
            array('this is cool', '12:this is cool,'),
            array('', '0:,'),
            array(null, '0:~'),
            array(true, '4:true!'),
            array(false, '5:false!'),
            array("\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", "10:\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00,"),
            array(array(12345, 67890, 'xxxxx'), '24:5:12345#5:67890#5:xxxxx,]'),
        );
    }
    
    /**
     * @dataProvider encodeDataProvider
     */
    public function testEncode($value, $expected) {
        $this->assertEquals($expected, $this->encoder->encode($value));
    }
}
