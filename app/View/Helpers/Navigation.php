<?php

namespace App\View\Helpers;

class Navigation
{
    public string $route;
    public string $title;
    public ?string $icon;

    public function __construct($route, $title, $icon)
    {
        $this->route = $route;
        $this->title = $title;
        $this->icon = $icon;
    }
}
