<?php

declare(strict_types = 1);

use AsciiChart\AsciiChart;

include __DIR__ . '/../vendor/autoload.php';

echo AsciiChart::plot([1,2,3,2,1,2,3,0,-1,-2,-3,-1,0]);
echo PHP_EOL;
echo AsciiChart::plot(iterator_to_array(getFibonacci(20)), ['height'=> 10]);
echo AsciiChart::plot(iterator_to_array(getSine(M_PI * 4)), ['height'=> 10]);
echo AsciiChart::plot(iterator_to_array(getCosine(M_PI * 4)), ['height'=> 10, 'offset' => 4]);
$series = iterator_to_array(getLog(100));
unset($series[0]);
echo AsciiChart::plot($series, ['height'=> 10]);


function getSine($max) {
    $i = 0;
    while (M_PI * ($i/10) <= $max) {
        yield sin(M_PI * ($i++/10) );
    }
}

function getCosine($max) {
    $i = 0;
    while (M_PI * ($i/10) <= $max) {
        yield cos(M_PI * ($i++/10) );
    }
}

function getLog($max) {
    $i = 0;
    while ($i <= $max) {
        yield log10($i++);
    }
}

function getFibonacci($maxRounds)
{
    $rounds = 0;
    $i = 0;
    $k = 1; //first fibonacci value
    yield $k;
    while($rounds < $maxRounds)
    {
        $rounds++;
        $k = $i + $k;
        $i = $k - $i;
        yield $k;
    }
};
