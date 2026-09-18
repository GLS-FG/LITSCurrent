<?php

namespace App\View\Helpers;

use Illuminate\Database\Eloquent\Collection;

class DashboardStat
{
    public string $title;
    public int $stat;
    public string $route;
    public string $show;
    public string $slug;
    public string $svg;
    public Collection $rows;

    public function __construct(string $title, int $stat, string $route, Collection $rows, string $slug, string $show, string $svg)
    {
        $this->title = $title;
        $this->stat = $stat;
        $this->route = $route;
        $this->rows = $rows;
        $this->slug = $slug;
        $this->show = $show;
        $this->svg = $svg;
    }
}
