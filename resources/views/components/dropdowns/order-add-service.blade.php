<div
    class="relative inline-block text-left"
    x-data="{
        dropdownIsOpen: false,
        toggleDropdown() {
            if (this.dropdownIsOpen) {
                return this.closeDropdown()
            }

            this.$refs.buttonDropdown.focus()

            this.dropdownIsOpen = true
        },
        closeDropdown(focusAfter) {
            if (! this.dropdownIsOpen) return

            this.dropdownIsOpen = false

            focusAfter && focusAfter.focus()
        }
    }"
    @keydown.escape.prevent.stop="closeDropdown($refs.buttonDropdown)"
    @focusin.window="! $refs.panel.contains($event.target) && closeDropdown()"
    x-id="['order-add-service-dropdown-button']"
>
    <div>
        <button
            type="button"
            class="inline-flex w-full items-center justify-center gap-x-1.5 rounded-md bg-lits-red-500 px-3 py-2 text-sm text-white shadow-xs transition-colors duration-150 ease-in-out hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-350 whitespace-nowrap hover:cursor-pointer"
            id="user-menu-button"
            aria-haspopup="true"
            x-ref="buttonDropdown"
            @click="toggleDropdown()"
            :aria-expanded="dropdownIsOpen"
            :aria-controls="$id('order-add-service-dropdown-button')"
        >
            {{__('shows.add_service')}}
            <i class="-mr-1 fa-regular fa-plus"></i>
        </button>
    </div>
    <div
        x-ref="panel"
        x-show="dropdownIsOpen"
        @click.outside="closeDropdown($refs.button)"
        :id="$id('order-add-service-dropdown-button')"
        x-cloak
        class="absolute right-0 z-10 mt-2 w-56 origin-top-right divide-y divide-gray-100 dark:divide-gray-800 rounded-md bg-white dark:bg-lits-blue-550 shadow-lg ring-1 ring-black/5 dark:ring-white/10 focus:outline-hidden"
        role="menu"
        aria-orientation="vertical"
        aria-labelledby="user-menu-button"
        tabindex="-1"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
    >
        <div class="py-1" role="none">
            <a href="{{route('orders.shipments.create', ['order' => $order->id])}}" class="group flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem" tabindex="-1" id="menu-item-1">
                <i class="fa-regular fa-route text-lg mr-3"></i>
                {{__('Shipment')}}
            </a>
            <a href="{{route('orders.imports.create', ['order' => $order->id])}}" class="group flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem" tabindex="-1" id="menu-item-1">
                <i class="fa-regular fa-person-military-pointing text-lg mr-3"></i>
                {{__('Custom')}}
            </a>
            <a href="{{route('orders.warehouse-storages.create', ['order' => $order->id])}}" class="group flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem" tabindex="-1" id="menu-item-0">
                <i class="fa-regular fa-warehouse text-lg mr-3"></i>
                {{__('Warehouse')}}
            </a>
        </div>
    </div>
</div>
