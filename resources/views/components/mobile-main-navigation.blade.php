<nav class="flex flex-1 flex-col">
    <ul role="list" class="flex flex-1 flex-col gap-y-7">
        <li>
            <ul role="list" class="-mx-2 space-y-1">
                @foreach ($navigation as $nav)
                    <li>
                        <a href="{{route($nav->route)}}" class="group flex gap-x-3 rounded-md  p-2 text-sm/6 font-semibold items-center {{ Request::routeIs($nav->route) ? 'bg-slate-100 text-red-600' : 'text-gray-700 hover:text-red-600 hover:bg-slate-100' }}">
                            <i class="{{$nav->icon}} text-lg shrink-0"></i>
                            {{$nav->title}}
                        </a>
                    </li>
                @endforeach
            </ul>
        </li>
        @hasanyrole([App\Enums\RolesEnum::SUPERADMIN, App\Enums\RolesEnum::OPERATORADMIN, App\Enums\RolesEnum::OPERATOR, App\Enums\RolesEnum::WAREHOUSE, App\Enums\RolesEnum::BILLING])
        <li class="mt-auto">
            <a href="{{route("clients.index")}}" class="group -mx-2 flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold items-center text-gray-700 hover:text-red-600 hover:bg-slate-100">
                <i class="fa-regular fa-gear text-lg shrink-0"></i>
                {{__('Settings')}}
            </a>
        </li>
        @endhasanyrole
    </ul>
</nav>
