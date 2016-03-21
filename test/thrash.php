<?php

/**
 * This file is part of the TNetstring project and is copyright
 *
 * (c) 2011-2016 Sam Smith <git@samsmith.io>.
 *
 * Please refer the to LICENSE file that was distributed with this source code
 * for the full copyright and license information.
 */

define('ITERATIONS', 1048576);

require_once dirname(__FILE__) . '/../vendor/autoload.php';

function thrash()
{
    $tnetstring = "51:5:hello,39:11:12345678901#4:this,4:true!0:~4:\x00\x00\x00\x00,]}";

    $result = tnetstring_decode($tnetstring);
    tnetstring_encode($result);
}

$startTime = microtime(true);

for ($i = 0; $i < ITERATIONS; ++$i) {
    thrash();
}

$endTime = microtime(true);

$timeTaken             = $endTime - $startTime;
$averageThrashDuration = $timeTaken / ITERATIONS;

echo sprintf(
    "It took %s (s) to perform %d thrashes. That's %s (s) per thrash!\n",
    number_format($timeTaken, 6),
    ITERATIONS,
    number_format($averageThrashDuration, 6)
);
