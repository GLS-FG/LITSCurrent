<?php

namespace App\View\Components;

use App\View\Helpers\Navigation;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class MainNavigation extends Component
{
    public $navigation;

    public function __construct()
    {
        $dashboard = new Navigation('dashboard', 'Dashboard', 'fa-regular fa-house');
        $orders = new Navigation('orders.index', __('Orders'), 'fa-regular fa-clipboard-list-check');
        $imports = new Navigation('imports.index', __('Customs'), 'fa-regular fa-person-military-pointing');
        $shipments = new Navigation('shipments.index', __('Shipments'), 'fa-regular fa-route');
        $warehouse = new Navigation('warehouse-storages.index', __('Warehouses'), 'fa-regular fa-warehouse');
        $history = new Navigation('orders.history', __('History'), 'fa-regular fa-clock-rotate-left');
        $this->navigation = array($dashboard, $orders, $shipments, $imports, $warehouse, $history);

        // Reportes: solo Super Admin
        if (auth()->user()?->hasAnyRole(Navigation::REPORT_ROLES)) {
            $this->navigation[] = new Navigation('reports.index', __('Reports'), 'fa-regular fa-chart-column');
        }
    }

    public function render(): View|Closure|string
    {
        return view('components.main-navigation');
    }
}
