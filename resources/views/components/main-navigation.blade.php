<nav class="flex flex-1 flex-col">
    <ul role="list" class="flex flex-1 flex-col gap-y-7">
        <li>
            <ul role="list" class="-mx-2 space-y-1">
                @foreach ($navigation as $nav)
                    <li class="relative" :class="{ 'flex justify-center': !isSidebarOpen }">
                        <a wire:navigate href="{{route($nav->route)}}" class="nav-link relative group flex gap-x-3 rounded-md  p-2 text-sm/6 font-semibold items-center {{ Request::routeIs($nav->route) ? 'is-active' : '' }}">
                            <i class="{{$nav->icon}} text-lg shrink-0"></i>
                            <span x-show="isSidebarOpen">{{$nav->title}}</span>
                            <span class="inset-0 absolute" x-show="!isSidebarOpen" data-tippy-content="{{$nav->title}}" data-tippy-placement="right"></span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </li>
        @hasanyrole([App\Enums\RolesEnum::SUPERADMIN, App\Enums\RolesEnum::OPERATORADMIN, App\Enums\RolesEnum::OPERATOR, App\Enums\RolesEnum::WAREHOUSE, App\Enums\RolesEnum::BILLING])
            <li class="mt-auto -mx-2" :class="{ 'flex justify-center': !isSidebarOpen }">
                <x-dropdowns.settings-menu :collapsible="true" />
            </li>
        @endhasanyrole
    </ul>
</nav>
