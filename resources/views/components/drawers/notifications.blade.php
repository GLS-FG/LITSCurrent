<div
    class="relative"
    x-data="{
        notificationIsOpen: false,
        toggleDrawer() {
            if (this.notificationIsOpen) {
                return this.closeDrawer()
            }
            this.notificationIsOpen = true
            $refs.buttonCloseDrawer && $refs.buttonCloseDrawer.focus()
        },
        closeDrawer(focusAfter) {
            if (! this.notificationIsOpen) return
            this.notificationIsOpen = false
            focusAfter && focusAfter.focus()
        }
    }"
    x-id="['drawer-button']"
>
    <button
        type="button"
        class="flex items-center p-1.5 size-8 text-gray-700 dark:text-gray-300 hover:text-gray-500 text-lg group hover:cursor-pointer rounded-full"
        id="drawer-button"
        x-ref="buttonDrawer"
        @click="notificationIsOpen = true"
    >
        <i class="fa-regular fa-bell"></i>
    </button>
    <div
        x-ref="dialog"
        x-cloak
        x-show="notificationIsOpen"
        x-trap.inert.noscroll="notificationIsOpen"
        x-on:keydown.esc.window="closeDrawer($refs.buttonDrawer)"
        :id="$id('user-dropdown-button')"
        class="relative z-60"
        role="dialog"
    >
        <div
            class="fixed inset-0 bg-gray-500/75 dark:bg-gray-950/75 transition-opacity"
            x-show="notificationIsOpen"
            x-transition:enter="ease-in-out duration-500"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in-out duration-500"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        ></div>

        <div class="fixed inset-0 overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10 sm:pl-16">
                    <div
                        x-transition:enter="transition ease-in-out duration-500 sm:duration-700"
                        x-transition:enter-start="translate-x-full"
                        x-transition:enter-end="translate-x-0"
                        x-transition:leave="transition ease-in-out duration-500 sm:duration-700"
                        x-transition:leave-start=""
                        x-transition:leave-end="translate-x-full"
                        x-show="notificationIsOpen"
                        x-ref="dialogPanel"
                        @click.outside="closeDrawer($refs.buttonDrawer)"
                        class="pointer-events-auto w-screen max-w-md"
                    >
                        <div class="relative flex h-full flex-col overflow-y-auto bg-white dark:bg-lits-blue-550 shadow-xl">
                            <div class="p-6">
                                <div class="flex items-start justify-between">
                                    <div class="text-xl font-semibold text-gray-900 dark:text-gray-50">Notificaciones</div>
                                    <div class="ml-3 flex h-7 items-center">
                                        <button
                                            type="button"
                                            x-ref="buttonCloseDrawer"
                                            @click="closeDrawer($refs.buttonDrawer)"
                                            class="relative rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                        >
                                            <span class="absolute -inset-2.5" />
                                            <span class="sr-only">Cerrar notificaciones</span>
                                            <i class="fa-regular fa-xmark"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="border-b border-gray-200 dark:border-lits-blue-450">
                                <div class="px-6">
                                    <nav class="-mb-px flex space-x-6">

                                    </nav>
                                </div>
                            </div>
                            <ul role="list" class="flex-1 overflow-y-auto p-4 space-y-2">
                                @foreach ($notifications as $notification)
                                    <li class="border rounded-lg border-gray-300 dark:border-gray-600 p-2 space-y-2.5">
                                        <p class="text-sm text-gray-900 dark:text-gray-50">
                                            {{ $notification->data['message'] }}
                                        </p>
                                        <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </p>
                                        <div class="flex space-x-2">
                                            <button wire:click="markAsRead('{{$notification->id}}')" type="button" class="rounded-md bg-white dark:bg-lits-blue-550 px-2.5 py-1.5 text-sm font-semibold text-gray-900 dark:text-gray-50 shadow-xs inset-ring inset-ring-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:cursor-pointer">
                                                Marcar como leída
                                            </button>
                                            <a
                                                href="{{route('orders.shipments.show', ['order' => $notification->data['order_id'], 'shipment' => $notification->data['shipment_id'], 'activeTab' => 2])}}"
                                                class="rounded-md bg-indigo-600 px-2.5 py-1.5 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                            >
                                                Ver embarque
                                            </a>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                            <livewire:notifications-list />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
