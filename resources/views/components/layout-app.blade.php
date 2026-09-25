<x-layout>
    <div x-data="sidebar">
        @persist('sidebar')
            <div x-cloak class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:flex-col" :class="{ 'lg:w-56': isSidebarOpen, 'lg:w-14': !isSidebarOpen }">
                <x-navigation.main-sidebar>
                    <x-main-navigation />
                    <button @click="toggle" class="absolute top-1/2 -right-5 z-10 bg-white dark:bg-lits-blue-550 rounded-r-lg text-gray-600 dark:text-gray-400 py-4 border border-gray-200 dark:border-lits-blue-450 border-l-white hover:bg-gray-100 dark:hover:bg-gray-700 hover:cursor-pointer">
                        <i x-show="isSidebarOpen" class="fa-regular fa-angle-left"></i>
                        <i x-show="!isSidebarOpen" class="fa-regular fa-angle-right"></i>
                    </button>
                </x-navigation.main-sidebar>
            </div>
        @endpersist

        <div x-cloak :class="{ 'lg:pl-56': isSidebarOpen, 'lg:pl-14': !isSidebarOpen }">
            @persist('topbar')
                <div class="sticky top-0 z-50 shadow-xs bg-white dark:bg-lits-blue-550">
                    <div class="flex h-16 items-center gap-x-4 lg:mx-auto px-4 shadow-xs sm:gap-x-6 sm:px-6 lg:shadow-none lg:px-8">
                        <x-navigation.mobile-sidebar>
                            <x-navigation.mobile-main-sidebar class="flex grow flex-col gap-y-5 overflow-y-auto bg-white dark:bg-lits-blue-550 px-6 pb-4">
                                <x-mobile-main-navigation />
                            </x-navigation.mobile-main-sidebar>
                        </x-navigation.mobile-sidebar>

                        <x-navigation.user-navbar />
                    </div>
                </div>
            @endpersist

            <main class="py-2 sm:py-4">
                <div class="mx-auto px-4 sm:px-6 lg:px-8">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
</x-layout>
