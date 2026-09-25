<div x-data="{ settingsMenuOpen: {{ $isActive ? 'true' : 'false' }} }" data-settings-menu>
    <button
        type="button"
        data-settings-trigger
        @click="settingsMenuOpen = !settingsMenuOpen"
        :aria-expanded="settingsMenuOpen"
        class="nav-link relative group flex w-full gap-x-3 rounded-md p-2 text-sm/6 font-semibold items-center hover:cursor-pointer {{ $isActive ? 'is-active' : '' }}"
    >
        <i class="fa-regular fa-gear text-lg shrink-0"></i>
        @if($collapsible)
            <span x-show="isSidebarOpen" class="flex-1 text-left">{{__('Settings')}}</span>
            <i class="fa-regular fa-angle-up text-xs shrink-0" x-show="isSidebarOpen" :class="{ 'rotate-180': !settingsMenuOpen }"></i>
            <span class="inset-0 absolute" x-show="!isSidebarOpen" data-tippy-content="{{__('Settings')}}" data-tippy-placement="right"></span>
        @else
            <span class="flex-1 text-left">{{__('Settings')}}</span>
            <i class="fa-regular fa-angle-up text-xs shrink-0" :class="{ 'rotate-180': !settingsMenuOpen }"></i>
        @endif
    </button>

    <ul
        x-show="settingsMenuOpen"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak
        role="list"
        class="mt-1 space-y-1"
        :class="{ 'flex flex-col items-center': {{ $collapsible ? '!isSidebarOpen' : 'false' }} }"
    >
        @foreach($navigation as $nav)
            @php($navPrefix = str($nav->route)->beforeLast('.index')->toString())
            <li>
                <a
                    wire:navigate
                    href="{{route($nav->route)}}"
                    class="nav-sublink relative group flex gap-x-3 rounded-md p-2 text-sm/6 font-medium items-center {{ request()->routeIs($navPrefix . '*') ? 'is-active' : '' }}"
                >
                    <i class="{{$nav->icon}} shrink-0"></i>
                    @if($collapsible)
                        <span x-show="isSidebarOpen">{{$nav->title}}</span>
                        <span class="inset-0 absolute" x-show="!isSidebarOpen" data-tippy-content="{{$nav->title}}" data-tippy-placement="right"></span>
                    @else
                        <span>{{$nav->title}}</span>
                    @endif
                </a>
            </li>
        @endforeach
    </ul>
</div>
