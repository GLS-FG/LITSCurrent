{{--
    Drill-down settings menu. `settingsView` lives in the `sidebar` Alpine data
    (layout.blade.php), so the main links in main-navigation / mobile-main-navigation
    can hide themselves while this panel is showing.
--}}
<div data-settings-menu x-init="settingsView = {{ $isActive ? 'true' : 'false' }}">
    <button
        type="button"
        data-settings-trigger
        x-show="!settingsView"
        @click="settingsView = true"
        class="nav-link relative group flex w-full gap-x-3 rounded-md p-1.5 text-sm/6 font-semibold items-center hover:cursor-pointer {{ $isActive ? 'is-active' : '' }}"
    >
        <span class="nav-icon"><i class="fa-regular fa-gear text-base"></i></span>
        @if($collapsible)
            <span x-show="isSidebarOpen" class="flex-1 text-left">{{__('Settings')}}</span>
            <i class="fa-regular fa-angle-right text-xs shrink-0" x-show="isSidebarOpen"></i>
            <span class="inset-0 absolute" x-show="!isSidebarOpen" data-tippy-content="{{__('Settings')}}" data-tippy-placement="right"></span>
        @else
            <span class="flex-1 text-left">{{__('Settings')}}</span>
            <i class="fa-regular fa-angle-right text-xs shrink-0"></i>
        @endif
    </button>

    <div x-show="settingsView" x-cloak>
        <button
            type="button"
            @click="settingsView = false"
            class="relative group flex w-full gap-x-3 rounded-md p-1.5 mb-2 text-sm/6 font-semibold items-center text-gray-900 dark:text-gray-50 hover:bg-slate-100 dark:hover:bg-gray-800 hover:cursor-pointer"
            :class="{ 'justify-center': {{ $collapsible ? '!isSidebarOpen' : 'false' }} }"
        >
            <span class="nav-icon"><i class="fa-regular fa-arrow-left text-base"></i></span>
            @if($collapsible)
                <span x-show="isSidebarOpen" class="flex-1 text-left">{{__('Settings')}}</span>
                <span class="inset-0 absolute" x-show="!isSidebarOpen" data-tippy-content="{{__('Back')}}" data-tippy-placement="right"></span>
            @else
                <span class="flex-1 text-left">{{__('Settings')}}</span>
            @endif
        </button>
        <div class="border-b border-gray-200 dark:border-lits-blue-450 mb-2"></div>

        <ul role="list" class="space-y-1" :class="{ 'flex flex-col items-center': {{ $collapsible ? '!isSidebarOpen' : 'false' }} }">
            @foreach($navigation as $nav)
                @php($navPrefix = str($nav->route)->beforeLast('.index')->toString())
                <li>
                    <a
                        wire:navigate
                        href="{{route($nav->route)}}"
                        class="nav-sublink relative group flex gap-x-3 rounded-md p-1.5 text-sm/6 font-semibold items-center {{ request()->routeIs($navPrefix . '*') ? 'is-active' : '' }}"
                    >
                        <span class="nav-icon"><i class="{{$nav->icon}} text-base"></i></span>
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
</div>
