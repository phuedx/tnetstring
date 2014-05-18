<?php

/**
 * This file is part of the TNetstring project and is copyright
 * 
 * (c) 2011-2014 Sam Smith <git@samsmith.io>.
 *
 * Please refer the to LICENSE file that was distributed with this source code
 * for the full copyright and license information.
 */
 
require_once dirname(__FILE__) . '/src/TNetstring/Decoder.php';
require_once dirname(__FILE__) . '/src/TNetstring/Encoder.php';

$__tnetstring_last_error = false;

function tnetstring_encode($value) {
    global $__tnetstring_last_error;    
    static $encoder;
    
    if ( ! $encoder) {
        $encoder = new TNetstring_Encoder();
    }
    
    try {
        return $encoder->encode($value);
    } catch (Exception $e) {
        $__tnetstring_last_error = $e->getMessage();
        
        return null;
    }
}

function tnetstring_decode($tnetstring) {
    global $__tnetstring_last_error;
    static $decoder;
    
    if ( ! $decoder) {
        $decoder = new TNetstring_Decoder();
    }
    
    try {
        return $decoder->decode($tnetstring);
    } catch (Exception $e) {
        $__tnetstring_last_error = $e->getMessage();
        
        return null;
    }
}

function tnetstring_last_error() {
    global $__tnetstring_last_error;
    
    return $__tnetstring_last_error;
}

function tnetstring_clear_last_error() {
    global $__tnetstring_last_error;

    $__tnetstring_last_error = null;
}
