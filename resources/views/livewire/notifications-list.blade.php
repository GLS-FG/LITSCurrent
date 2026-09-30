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
        class="relative flex items-center p-1.5 size-8 text-gray-700 dark:text-gray-300 hover:text-gray-500 text-lg group hover:cursor-pointer rounded-full"
        id="drawer-button"
        x-ref="buttonDrawer"
        @click="notificationIsOpen = true"
    >
        <i class="fa-regular fa-bell"></i>
        @if(count($notifications) > 0)
            <span class="absolute -right-0.5 top-0 rounded-full font-medium text-xs bg-red-500 text-white size-4">
                  {{count($notifications)}}
            </span>
        @endif
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
                            <div class="border-b border-gray-200 dark:border-lits-blue-450 p-6">
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
                            <ul role="list" class="flex-1 overflow-y-auto">
                                @forelse ($notifications as $notification)
                                    <li class="border-b border-gray-100 dark:border-lits-blue-450/60 px-6 py-3.5 space-y-1.5 hover:bg-gray-50 dark:hover:bg-white/5">
                                        <a href="{{route('orders.shipments.show', ['order' => $notification->orderId, 'shipment' => $notification->id])}}" class="tabular-nums text-sm font-semibold text-gray-900 dark:text-gray-50 hover:text-lits-red-500">{{ $notification->orderCode }}</a>
                                        <div class="flex items-start text-sm">
                                            <div class="size-8 shrink-0 flex items-center justify-center rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700">
                                                <img alt="{{$notification->clientName}}" src="{{ route('clients.logos', [ 'filename' => str_replace(".","_",str_replace("logos/", "", $notification->clientImage))]) }}" />
                                            </div>
                                            <p class="ml-2 text-gray-500 dark:text-gray-400 text-xs line-clamp-2">{{ $notification->contactName }}</p>
                                        </div>
                                        <div x-data="{ isExpanded: false }">
                                            <button type="button" class="flex items-center justify-start gap-1 text-lits-red-500 hover:text-lits-red-600 text-sm hover:cursor-pointer" x-on:click="isExpanded = ! isExpanded">
                                                Eventos ({{ $notification->pending }})
                                                <i class="fa-regular fa-angle-down shrink-0 transition" x-bind:class="isExpanded  ?  'rotate-180'  :  ''"></i>
                                            </button>
                                            <ul x-cloak x-show="isExpanded" class="space-y-1.5 mt-1.5">
                                                @foreach($notification->events as $event)
                                                    <li class="bg-gray-50 dark:bg-lits-blue-600/40 rounded py-1 px-2 flex space-x-2 text-sm items-center">
                                                        @if($event->done)
                                                            <i class="fa-solid fa-square-check text-green-500 dark:text-green-400"></i>
                                                            <p class="line-through text-gray-500 dark:text-gray-400">{{$event->title}}</p>
                                                        @else
                                                            <i class="fa-regular fa-square text-gray-400 dark:text-gray-500"></i>
                                                            <a href="{{route('orders.shipments.show', ['order' => $notification->orderId, 'shipment' => $notification->id, 'activeTab' => 2])}}" class="text-gray-900 dark:text-gray-50 hover:text-gray-700 hover:underline">{{ $event->title }}</a>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        {{--<div class="flex space-x-4">
                                            <button wire:click="markAsRead('{{$notification->id}}')" type="button" class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-400 hover:cursor-pointer">
                                                Marcar como leída
                                            </button>
                                            <a
                                                href="{{route('orders.shipments.show', ['order' => $notification->data['order_id'], 'shipment' => $notification->data['shipment_id'], 'activeTab' => 2])}}"
                                                class="rounded-md bg-indigo-600 px-2.5 py-1.5 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                            >
                                                Ver embarque
                                            </a>
                                        </div>--}}
                                    </li>
                                @empty
                                    <li class="text-center px-6 py-10">
                                        <i class="mt-2 text-5xl fa-regular fa-mailbox-open-empty"></i>
                                        <p class="mt-2 text-xl text-gray-900 dark:text-gray-50 font-semibold">
                                            Estás al día
                                        </p>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                            No tienes notificaciones pendientes por atender
                                        </p>
                                    </li>
                                @endforelse
                            </ul>
                            <div class="border-t border-gray-200 dark:border-lits-blue-450 p-6">
                                <div class="flex items-center justify-left space-x-4">
                                    <a
                                        href="{{route('notifications.index')}}"
                                        class="inline-flex items-center gap-x-1.5 rounded-md bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-50 shadow-xs inset-ring inset-ring-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800"
                                    >
                                        Centro de notificaciones
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
