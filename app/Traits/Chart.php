<?php

namespace App\Traits;

use Carbon\Carbon;

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


    /**
     * Reconstructing the data to be adapted for displaying charts
     * @param $data
     * @return mixed
     */
    protected function reconstructDataForMonthlyCharts($data, $year): mixed
    {
        for ($i = 1; $i <= 12; $i++) {
            if (empty($data->where('label', $i)->first())) {
                $data[] = [
                    'label' => Carbon::createFromDate($year, $i, 1)->format('m/Y'),
                    'Count' => 0
                ];
            } else {
                $data->where('label', $i)->first()->label = Carbon::createFromDate($year, $i, 1)->format('m/Y');
            }
        }
        return $data->sortBy('label')->pluck('Count');
    }

}
