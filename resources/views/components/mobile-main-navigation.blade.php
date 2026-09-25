<nav class="flex flex-1 flex-col">
    <ul role="list" class="flex flex-1 flex-col gap-y-7">
        <li>
            <ul role="list" class="-mx-2 space-y-1">
                @foreach ($navigation as $nav)
                    <li>
                        <a wire:navigate href="{{route($nav->route)}}" class="nav-link group flex gap-x-3 rounded-md  p-2 text-sm/6 font-semibold items-center {{ Request::routeIs($nav->route) ? 'is-active' : '' }}">
                            <i class="{{$nav->icon}} text-lg shrink-0"></i>
                            {{$nav->title}}
                        </a>
                    </li>
                @endforeach
            </ul>
        </li>
        @hasanyrole([App\Enums\RolesEnum::SUPERADMIN, App\Enums\RolesEnum::OPERATORADMIN, App\Enums\RolesEnum::OPERATOR, App\Enums\RolesEnum::WAREHOUSE, App\Enums\RolesEnum::BILLING])
        <li class="mt-auto -mx-2">
            <x-dropdowns.settings-menu :collapsible="false" />
        </li>
        @endhasanyrole
    </ul>
</nav>
