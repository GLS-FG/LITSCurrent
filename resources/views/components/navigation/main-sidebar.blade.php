
<div class="flex grow flex-col gap-y-5 overflow-y-auto border-r border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 pb-4" :class="{ 'px-6': isSidebarOpen, 'px-2': !isSidebarOpen }">
    <div class="flex h-16 shrink-0 items-center" :class="{ 'justify-center': !isSidebarOpen }">
        <img
            class="h-14 w-auto"
            x-show="isSidebarOpen"
            src="{{ asset('/images/lits.png') }}"
            alt="LITS GLS Group"
        />
        <img
            class="h-10 w-auto"
            x-show="!isSidebarOpen"
            src="{{ asset('/images/mundo.png') }}"
            alt="LITS GLS Group"
        />
    </div>
    {{ $slot }}
</div>
