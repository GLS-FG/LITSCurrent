<?php

namespace App\View\Helpers;

class DashboardChartData
{
    public array $labels;
    public array $datasets;
    public function __construct(array $labels, array $datasets)
    {
        $this->labels = $labels;
        $this->datasets = $datasets;
    }

}
