<?php

namespace App\Services;

class FakeInstallService
{
    private int $total;
    private int $remain;
    private static $hourDistributions = [
        0 => 2,
        1 => 1,
        2 => 1,
        3 => 1,
        4 => 1,
        5 => 1,
        6 => 1,
        7 => 3,
        8 => 5,
        9 => 7,
        10 => 8,
        11 => 9,
        12 => 6,
        13 => 5,
        14 => 5,
        15 => 6,
        16 => 6,
        17 => 4,
        18 => 5,
        19 => 5,
        20 => 5,
        21 => 5,
        22 => 5,
        23 => 3,
    ];

    public function sum($from, $to) {
        $sum = 0;
        #$count = count(self::$hourDistributions);
        for ($i = $from; $i <= $to; $i++) {
            $v = self::$hourDistributions[$i] ?? 0;
            #echo $i . " -> $v\n";
            $sum += $v;
        }

        return $sum;
    }

    public function __construct($total)
    {
        $this->total = $total;

    }

    public function getCount($currentHour = 0)
    {
        //$currentHour = (int) date('H', strtotime($currentHour));
        $percent = self::$hourDistributions[$currentHour];
        $remainPercent = $this->sum($currentHour, 23);

        $value = (int) ceil( $this->total * ($percent/$remainPercent));
        return $value;
    }
}
