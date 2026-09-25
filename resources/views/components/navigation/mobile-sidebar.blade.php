<div
    x-data="{
        sidebarIsOpen: false,
        toggleSidebar() {
            if (this.sidebarIsOpen) {
                return this.closeSidebar()
            }

            this.$refs.buttonSidebar.focus()

            this.sidebarIsOpen = true
        },
        closeSidebar(focusAfter) {
            if (! this.sidebarIsOpen) return

            this.sidebarIsOpen = false

            focusAfter && focusAfter.focus()
        }
    }"
    @keydown.escape.prevent.stop="closeSidebar($refs.buttonSidebar)"
    @focusin.window="! $refs.dialog.contains($event.target) && closeSidebar()"
    x-id="['sidebar-button']"
    class="relative z-50 lg:hidden"
    role="dialog"
    aria-modal="true"
>
    <button
        type="button"
        class="-m-2.5 p-2.5 text-gray-700 dark:text-gray-300 lg:hidden"
        id="sidebar-menu-button"
        x-ref="buttonSidebar"
        @click="toggleSidebar()"
        :aria-expanded="sidebarIsOpen"
        :aria-controls="$id('sidebar-button')"
    >
        <span class="sr-only">Open sidebar</span>
        <i class="fa-regular fa-bars text-lg"></i>
    </button>

    <div
        x-show="sidebarIsOpen"
        class="fixed inset-0 bg-gray-900/80"
        aria-hidden="true"
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    ></div>

    <div
        x-ref="dialog"
        x-show="sidebarIsOpen"
        :id="$id('sidebar-button')"
        x-cloak
        class="fixed inset-0 flex"
        x-transition:enter="transition ease-in-out duration-300 transform"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in-out duration-300 transform"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
    >
        <div
            class="relative mr-16 flex w-full max-w-xs flex-1"
            @click.outside="closeSidebar($refs.buttonSidebar)"
            x-transition:enter="ease-in-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in-out duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <div class="absolute top-0 left-full flex w-16 justify-center pt-5">
                <button
                    type="button" class="-m-2.5 p-2.5"
                    @click="toggleSidebar()"
                    :aria-expanded="sidebarIsOpen"
                    :aria-controls="$id('sidebar-button')"
                >
                    <span class="sr-only">Close sidebar</span>
                    <i class="fa-regular fa-xmark"></i>
                </button>
            </div>

            {{ $slot }}
        </div>
    </div>
</div>
