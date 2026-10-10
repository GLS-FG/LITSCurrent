@section('title', __('Notification Center'))
<x-layout-app>
    <section>
        <x-navigation.breadcrumbs :links="[__('Notification Center') => '#']" />
        <x-headings.without-action
            :title="__('Notification Center') . ' (' . $activeCount . ')'"
        />
        @if(session()->has('success'))
            <x-alerts.success class="mt-4" :message="session('success')" />
        @endif

        <div class="mt-5" x-data="{ showFilters: false }">
            <form method="GET" action="{{ route('notifications.index') }}" class="flex flex-col gap-2">
                <div class="flex flex-col md:flex-row gap-2">
                    <div class="flex-1 flex items-center gap-2 rounded-lg bg-white dark:bg-lits-blue-550 border border-gray-200 dark:border-lits-blue-450 px-3.5 py-2.5">
                        <i class="fa-regular fa-magnifying-glass text-gray-400 dark:text-gray-500"></i>
                        <input type="text" value="{{ request('code_search') }}" autocomplete="off" name="code_search" placeholder="{{__('indexes.search_orders')}}" class="flex-1 border-0 bg-transparent p-0 text-sm text-gray-900 dark:text-gray-50 placeholder:text-gray-400 dark:placeholder:text-gray-500 outline-none">
                        @if(request('code_search'))
                            <a href="{{ route('notifications.index') }}" class="text-gray-400 dark:text-gray-500 hover:text-gray-600" aria-hidden="true"><i class="fa-regular fa-xmark"></i></a>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="showFilters = !showFilters" aria-label="{{__('indexes.search')}}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 px-3.5 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:cursor-pointer">
                            <i class="fa-regular fa-sliders"></i>
                        </button>
                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-lits-red-500 px-3.5 py-2.5 text-sm font-semibold text-white hover:bg-lits-red-600 hover:cursor-pointer">
                            {{__('indexes.search')}}
                        </button>
                    </div>
                </div>

                <div x-cloak x-show="showFilters" x-transition class="grid grid-cols-1 sm:grid-cols-4 gap-2 rounded-lg bg-gray-50/60 dark:bg-lits-blue-550/60 border border-gray-100 dark:border-lits-blue-450 p-2.5">
                    <div class="relative">
                        <input type="text" value="{{ request('client_search') }}" autocomplete="off" name="client_search" placeholder="{{__('indexes.search_clients')}}" class="w-full rounded-md bg-white dark:bg-lits-blue-550 py-2 pr-8 pl-3 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">
                        @if(request('client_search'))
                            <a href="{{ route('notifications.index') }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 hover:text-gray-600"><i class="fa-regular fa-xmark text-xs"></i></a>
                        @endif
                    </div>
                    <div class="relative">
                        <input type="text" value="{{ request('reference_search') }}" autocomplete="off" name="reference_search" placeholder="{{__('indexes.search_references')}}" class="w-full rounded-md bg-white dark:bg-lits-blue-550 py-2 pr-8 pl-3 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">
                        @if(request('reference_search'))
                            <a href="{{ route('notifications.index') }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 hover:text-gray-600"><i class="fa-regular fa-xmark text-xs"></i></a>
                        @endif
                    </div>
                    <div class="relative">
                        <input type="text" value="{{ request('service_search') }}" autocomplete="off" name="service_search" placeholder="Service Type" class="w-full rounded-md bg-white dark:bg-lits-blue-550 py-2 pr-8 pl-3 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">
                        @if(request('service_search'))
                            <a href="{{ route('notifications.index') }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 hover:text-gray-600"><i class="fa-regular fa-xmark text-xs"></i></a>
                        @endif
                    </div>
                    <div class="relative">
                        <input type="text" value="{{ request('status_search') }}" autocomplete="off" name="status_search" placeholder="{{__('indexes.search_status')}}" class="w-full rounded-md bg-white dark:bg-lits-blue-550 py-2 pr-8 pl-3 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">
                        @if(request('status_search'))
                            <a href="{{ route('notifications.index') }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 hover:text-gray-600"><i class="fa-regular fa-xmark text-xs"></i></a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <div class="mt-4 flex items-center gap-5 border-b border-gray-200 dark:border-lits-blue-450">
            <a
                href="{{ route('notifications.index', request()->except(['urgent'])) }}"
                class="inline-flex items-center gap-1.5 pb-2.5 text-sm border-b-2 {{ $onlyUrgent ? 'border-transparent font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' : 'border-lits-red-500 font-semibold text-gray-900 dark:text-gray-50' }}"
            >
                {{__('indexes.active')}}
                <span class="text-xs text-gray-400 dark:text-gray-500">{{ $activeCount }}</span>
            </a>
            <a
                href="{{ route('notifications.index', array_merge(request()->except(['urgent']), ['urgent' => 1])) }}"
                class="inline-flex items-center gap-1.5 pb-2.5 text-sm border-b-2 {{ $onlyUrgent ? 'border-lits-red-500 font-semibold text-gray-900 dark:text-gray-50' : 'border-transparent font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}"
            >
                <span class="size-1.5 rounded-full bg-lits-red-500"></span>
                {{__('indexes.urgent')}}
                <span class="text-xs text-gray-400 dark:text-gray-500">{{ $urgentCount }}</span>
            </a>
        </div>

        <div class="mt-4 overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-lits-blue-450">
                            <th scope="col" class="py-2.5 pl-2 text-left text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">Orden</th>
                            <th scope="col" class="px-3 py-2.5 text-left text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">Cliente</th>
                            <th scope="col" class="px-3 py-2.5 text-left text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">Referencia</th>
                            <th scope="col" class="px-3 py-2.5 text-left text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">Estatus</th>
                            <th scope="col" class="px-3 py-2.5 text-left text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">Eventos</th>
                            <th scope="col" class="py-2.5 pr-2 pl-3 text-center text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">{{__('Actions')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($notifications as $notification)
                        <tr class="group border-b border-gray-100 dark:border-lits-blue-450/60 {{ $notification->service->urgent ? 'bg-lits-red-50/50 dark:bg-lits-red-500/10 shadow-[inset_3px_0_0_var(--color-lits-red-500)] hover:bg-lits-red-50 dark:hover:bg-lits-red-500/15' : 'hover:bg-gray-50 dark:hover:bg-lits-blue-550/60' }}">
                            <td class="py-3.5 pl-4 whitespace-nowrap">
                                <a href="{{route('orders.shipments.show', ['order' => $notification->service->order->id, 'shipment' => $notification->service->id, 'activeTab' => 2])}}" class="tabular-nums text-sm font-semibold text-gray-900 dark:text-gray-50 hover:text-lits-red-500">{{ $notification->service->order->code }}</a>
                                <div class="text-gray-400 dark:text-gray-500 text-xs mt-0.5">{{ $notification->service->order->createdBy?->name }}</div>
                            </td>
                            <td class="px-3 py-3.5">
                                <div class="flex items-center">
                                    <div class="size-8 shrink-0 flex items-center justify-center rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700">
                                        <img alt="{{$notification->service->order->client->trade_name}}" src="{{ route('clients.logos', [ 'filename' => str_replace(".","_",str_replace("logos/", "", $notification->service->order->client->image))]) }}" />
                                    </div>
                                    <div class="ml-2">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-50">{{ $notification->service->order->client->trade_name }}</div>
                                        <div class="text-gray-400 dark:text-gray-500 text-xs">{{$notification->service->order->contact->name}}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-3.5 text-sm text-gray-500 dark:text-gray-400 max-w-xs">
                                <span class="block truncate">{{ $notification->service->reference }}</span>
                            </td>
                            <td class="px-3 py-3.5 text-sm">
                                <a
                                    href="{{route('orders.shipments.show', ['order' => $notification->service->order->id, 'shipment' => $notification->service->id, 'activeTab' => 2])}}"
                                    data-tippy-content="Ir a estatus"
                                    role="button"
                                >
                                    @if($notification->service->latestLocation != null)
                                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium inset-ring {{$notification->service->latestLocation->status->color}} dark:bg-gray-800 dark:text-gray-300 dark:inset-ring-gray-600">{{$notification->service->latestLocation->status->name}}</span>
                                    @else
                                        <span class="whitespace-nowrap text-gray-400 dark:text-gray-500 text-xs">No actualizado</span>
                                    @endif
                                </a>
                            </td>
                            <td class="px-3 py-3.5 text-sm whitespace-nowrap text-gray-900 dark:text-gray-50">
                                <ul class="space-y-1">
                                    @foreach($notification->events as $event)
                                        <li class="flex gap-2 text-sm items-center">
                                            @if($event->done)
                                                <i class="fa-solid fa-square-check text-green-500 dark:text-green-400"></i>
                                                <p class="line-through text-gray-400 dark:text-gray-500">{{$event->title}}</p>
                                            @else
                                                <i class="fa-regular fa-square text-gray-400 dark:text-gray-500"></i>
                                                <a href="{{route('orders.shipments.show', ['order' => $notification->service->order->id, 'shipment' => $notification->service->id, 'activeTab' => 2])}}" class="text-gray-700 dark:text-gray-300 hover:text-lits-red-500 hover:underline">{{ $event->title }}</a>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="relative py-3.5 pr-4 pl-3 text-sm font-medium whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1">
                                    <a
                                        href="{{route('orders.shipments.show', ['order' => $notification->service->order->id, 'shipment' => $notification->service->id, 'activeTab' => 2])}}"
                                        data-tippy-content="Ver"
                                        role="button"
                                        class="size-7 shrink-0 rounded-md flex items-center justify-center text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-200"
                                    >
                                        <i class="fa-regular fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-8 text-sm text-center text-gray-500 dark:text-gray-400">
                                No hay notificaciones pendientes.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</x-layout-app>
