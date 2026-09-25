<?php

namespace App\View\Components\Dropdowns;

use App\View\Helpers\AdminNavigation;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SettingsMenu extends Component
{
    public array $navigation;
    public bool $isActive;

    public function __construct(public bool $collapsible = true)
    {
        // 'dashboard' is already shown as its own link in the main sidebar
        // above this menu, so it's left out here to avoid showing it twice.
        $this->navigation = array_values(array_filter(
            AdminNavigation::items(),
            fn ($item) => $item->route !== 'dashboard'
        ));
        $this->isActive = collect($this->navigation)->contains(function ($item) {
            $prefix = str($item->route)->beforeLast('.index')->toString();
            return request()->routeIs($prefix . '*');
        });
    }

    public function render(): View|Closure|string
    {
        return view('components.dropdowns.settings-menu');
    }
}
