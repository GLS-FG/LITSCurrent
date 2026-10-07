<nav class="flex flex-1 flex-col">
    <ul role="list" class="flex flex-1 flex-col gap-y-7">
        <li x-show="!settingsView">
            <ul role="list" class="-mx-2 space-y-1">
                @foreach ($navigation as $nav)
                    <li>
                        <a wire:navigate href="{{route($nav->route)}}" class="nav-link group flex gap-x-3 rounded-md p-1.5 text-sm/6 font-semibold items-center {{ Request::routeIs($nav->route) ? 'is-active' : '' }}">
                            <span class="nav-icon"><i class="{{$nav->icon}} text-base"></i></span>
                            {{$nav->title}}
                        </a>
                    </li>
                @endforeach
            </ul>
        </li>
        @hasanyrole([App\Enums\RolesEnum::SUPERADMIN, App\Enums\RolesEnum::OPERATORADMIN, App\Enums\RolesEnum::OPERATOR, App\Enums\RolesEnum::WAREHOUSE, App\Enums\RolesEnum::BILLING])
        <li class="-mx-2" :class="{ 'mt-auto': !settingsView }">
            <x-dropdowns.settings-menu :collapsible="false" />
        </li>
        @endhasanyrole
    </ul>
</nav>
