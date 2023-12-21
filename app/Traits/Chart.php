<?php

namespace App\Traits;

trait Chart
{

    /**
     * Retrieve statistics of new recipes for each month
     */
    public static function statisticsPerMonthForYear($year)
    {
        return self::selectRaw('MONTH(created_at) as label, COUNT(id) as Count')
            ->whereYear('created_at', $year)
            ->groupBy('label');
    }

}
