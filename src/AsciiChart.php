<?php declare(strict_types = 1);

namespace AsciiChart;

use Exception;

class AsciiChart
{

    public static function plot($series, $cfg = [])
    {
        $series = array_values($series);
        $min = $series[0];
        $max = $series[0];

        $seriesCount = \count($series);
        foreach ($series as $value) {
            $min = min($min, $value);
            $max = max($max, $value);
        }
        if (is_infinite($min) || is_infinite($max)) {
            throw new Exception('Cannot plot infinity, check your data');
        }

        $range = abs($max - $min);
        $cfg = $cfg ?? [];
        $offset = 2 + ($cfg['offset'] ?? 1);
        $padding = str_repeat(' ', $cfg['padding'] ?? 11);
        $height = $cfg['height'] ?? $range;
        $ratio = $height / $range;
        $min2 = round($min * $ratio);
        $max2 = round($max * $ratio);
        $rows = abs($max2 - $min2);
        $width = $seriesCount + $offset;
        $format = $cfg['format'] ?? function ($x) use ($padding) {
                return substr($padding . round($x, 2), -1 * mb_strlen($padding));
            };

        $result = []; // empty space
        for ($i = 0; $i <= $rows; $i++) {
            $result[$i] = [];
            for ($j = 0; $j < $width; $j++) {
                $result[$i][$j] = ' ';
            }
        }

        for ($y = $min2; $y <= $max2; ++$y) { // axis + labels
            $label = $format ($max - ($y - $min2) * $range / $rows, $y - $min2);
            $result[$y - $min2][max($offset - mb_strlen($label), 0)] = $label;
            $result[$y - $min2][$offset - 1] = ($y == 0) ? '┼' : '┤';
        }

        $y0 = round($series[0] * $ratio) - $min2;
        $result[$rows - $y0][$offset - 1] = '┼'; // first value

        for ($x = 0; $x < $seriesCount - 1; $x++) { // plot the line
            $y0 = round($series[$x + 0] * $ratio) - $min2;
            $y1 = round($series[$x + 1] * $ratio) - $min2;
            if ($y0 == $y1) {
                $result[$rows - $y0][$x + $offset] = '─';
            } else {
                $result[$rows - $y1][$x + $offset] = ($y0 > $y1) ? '╰' : '╭';
                $result[$rows - $y0][$x + $offset] = ($y0 > $y1) ? '╮' : '╯';
                $from = min($y0, $y1);
                $to = max($y0, $y1);
                for ($y = $from + 1; $y < $to; $y++) {
                    $result[$rows - $y][$x + $offset] = '│';
                }
            }
        }

        return implode(PHP_EOL, array_map(function ($x) {
                return implode('', $x);
            }, $result)) . PHP_EOL;
    }
}
