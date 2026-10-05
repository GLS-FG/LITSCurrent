<?php

namespace App\View\Helpers;

class Navigation
{
    /** Roles que ven Reportes (el mismo criterio del grupo de rutas en routes/web.php). */
    public const REPORT_ROLES = ['Super Admin'];

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
