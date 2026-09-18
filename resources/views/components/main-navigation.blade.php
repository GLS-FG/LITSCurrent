<nav class="flex flex-1 flex-col">
    <ul role="list" class="flex flex-1 flex-col gap-y-7">
        <li>
            <ul role="list" class="-mx-2 space-y-1">
                @foreach ($navigation as $nav)
                    <li class="relative" :class="{ 'flex justify-center': !isSidebarOpen }">
                        <a href="{{route($nav->route)}}" class="relative group flex gap-x-3 rounded-md  p-2 text-sm/6 font-semibold items-center {{ Request::routeIs($nav->route) ? 'bg-slate-100 text-red-600' : 'text-gray-700 hover:text-red-600 hover:bg-slate-100' }}">
                            <i class="{{$nav->icon}} text-lg shrink-0"></i>
                            <span x-show="isSidebarOpen">{{$nav->title}}</span>
                            <span class="inset-0 absolute" x-show="!isSidebarOpen" data-tippy-content="{{$nav->title}}" data-tippy-placement="right"></span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </li>
        @hasanyrole([App\Enums\RolesEnum::SUPERADMIN, App\Enums\RolesEnum::OPERATORADMIN, App\Enums\RolesEnum::OPERATOR, App\Enums\RolesEnum::WAREHOUSE, App\Enums\RolesEnum::BILLING])
            <li class="mt-auto" :class="{ 'flex justify-center': !isSidebarOpen }">
                <a href="{{route("clients.index")}}" class="relative group -mx-2 flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold items-center text-gray-700 hover:text-red-600 hover:bg-slate-100">
                    <i class="fa-regular fa-gear text-lg shrink-0"></i>
                    <span x-show="isSidebarOpen">{{__('Settings')}}</span>
                    <span class="inset-0 absolute" x-show="!isSidebarOpen" data-tippy-content="{{__('Settings')}}" data-tippy-placement="right"></span>
                </a>
            </li>
        @endhasanyrole
    </ul>
</nav>
