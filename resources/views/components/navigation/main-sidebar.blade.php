
<div class="flex grow flex-col gap-y-5 overflow-y-auto border-r border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 pb-4" :class="{ 'px-6': isSidebarOpen, 'px-2': !isSidebarOpen }">
    <div class="flex h-16 shrink-0 items-center" :class="{ 'justify-center': !isSidebarOpen }">
        <div x-show="isSidebarOpen">
            <img
                class="h-14 w-auto dark:hidden"
                src="{{ asset('/images/lits.png') }}"
                alt="LITS GLS Group"
            />
            <img
                class="hidden h-14 w-auto dark:block"
                src="{{ asset('/images/lits-white.png') }}"
                alt="LITS GLS Group"
            />
        </div>
        <div x-show="!isSidebarOpen">
            <img
                class="h-10 w-auto dark:hidden"
                src="{{ asset('/images/mundo.png') }}"
                alt="LITS GLS Group"
            />
            <img
                class="hidden h-10 w-auto dark:block"
                src="{{ asset('/images/mundo-white.png') }}"
                alt="LITS GLS Group"
            />
        </div>
    </div>
    {{ $slot }}
</div>
