@section('title', $order->code)
<x-layout-app>
    <div>
        <x-navigation.breadcrumbs :links="[__('Orders') => route('orders.index')]" />

        <div class="mt-5 flex items-start justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-2.5 flex-wrap">
                <h1 class="text-xl font-bold tracking-tight text-gray-900 dark:text-gray-50">{{$order->code}}</h1>
                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset {{ $order->order_status_id->badgeColor() }}">
                    <span class="size-1.5 rounded-full {{ $order->order_status_id->dotColor() }}"></span>
                    {{ $order->order_status_id->label() }}
                </span>
                @if($order->urgent)
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-lits-red-50 dark:bg-lits-red-500/15 px-2.5 py-1 text-xs font-bold text-lits-red-600 dark:text-lits-red-400">
                        <i class="fa-regular fa-light-emergency-on"></i>
                        {{__('Urgent')}}
                    </span>
                @endif
            </div>
            <div class="flex items-center gap-1.5 flex-wrap">
                @can('update', $order)
                    <button
                        type="button"
                        @click="window.dispatchEvent(new CustomEvent('open-edit-order-drawer'))"
                        data-tippy-content="{{__('shows.edit_order')}}"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:cursor-pointer"
                    >
                        <i class="fa-regular fa-pen-to-square"></i>
                        {{__('shows.edit')}}
                    </button>
                    <a
                        href="{{route('orders.documents.create', [ 'order' => $order->id ])}}"
                        data-tippy-content="{{__('shows.attach_file')}}"
                        role="button"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800"
                    >
                        <i class="fa-regular fa-paperclip"></i>
                        {{__('shows.attach')}}
                    </a>
                    <form action="{{ route('orders.update.urgent', ['order' => $order->id]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button
                            data-tippy-content="{{$order->urgent == true ? __('shows.remove_urgent') : __('shows.make_urgent')}}"
                            type="submit"
                            class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-2 text-sm font-medium hover:cursor-pointer {{ $order->urgent ? 'border-transparent bg-lits-red-50 dark:bg-lits-red-500/15 text-lits-red-600 dark:text-lits-red-400 hover:bg-lits-red-100 dark:hover:bg-lits-red-500/25' : 'border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800' }}"
                        >
                            {{$order->urgent == true ? __('shows.remove_urgent') : __('shows.make_urgent')}}
                        </button>
                    </form>
                @endcan
                @canany(['update', 'restore'], $order)
                    <form action="{{ route('orders.update.status', ['order' => $order->id]) }}" method="POST" class="flex">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 focus-within:relative">
                            <select id="order_status_id" name="order_status_id" autocomplete="off" aria-label="{{__('indexes.status')}}" class="col-start-1 row-start-1 w-full appearance-none rounded-l-lg border border-r-0 border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 py-2 pr-8 pl-3 text-sm text-gray-900 dark:text-gray-50 outline-none focus:ring-2 focus:ring-indigo-600">
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->id }}" @selected($order->order_status_id->value == $status->id)>{{ $status->name }}</option>
                                @endforeach
                            </select>
                            <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2.5 self-center justify-self-end text-xs text-gray-400 dark:text-gray-500"></i>
                        </div>
                        <button data-tippy-content="{{__('shows.save_status')}}" type="submit" class="flex items-center gap-x-1.5 rounded-r-lg border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:cursor-pointer">
                            <i class="fa-regular fa-floppy-disk"></i>
                        </button>
                    </form>
                @endcanany
                @can('update', $order)
                    <x-dropdowns.order-add-service :order="$order"/>
                @endcan
            </div>
        </div>

        @if(session()->has('success'))
            <x-alerts.success class="mt-4" :message="session('success')" />
        @endif
        {{--
            Errors from the "Agregar embarque" drawer's form also land in this
            page's $errors bag (Laravel redirects failed validation back to the
            referring page). The drawer already shows them itself, so this
            page's own generic alert is suppressed for that case to avoid
            showing the same errors twice.
        --}}
        @if ($errors->any() && ! ($errors->has('ship_from') || $errors->has('service_class_id')) && ! old('_drawer'))
            <x-alerts.error :message="__('order_errors')" :errors="$errors" class="my-4" />
        @endif

        <div class="mt-5 flex items-start justify-between gap-4 flex-wrap py-4 border-t border-b border-gray-200 dark:border-lits-blue-450">
            <div class="flex items-center gap-3">
                <img src="{{ route('clients.logos', [ 'filename' => str_replace(".","_",str_replace("logos/", "", $order->client->image))]) }}" alt="{{$order->client->trade_name}}" class="object-contain size-11 flex-none rounded-full bg-gray-200 dark:bg-gray-700 outline -outline-offset-1 outline-black/5" />
                <div>
                    <div class="text-sm font-bold text-gray-900 dark:text-gray-50">{{$order->client->trade_name}}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{$order->contact->name}}</div>
                </div>
            </div>
            <div class="flex items-start gap-6 flex-wrap">
                <div>
                    <div class="text-[11px] text-gray-400 dark:text-gray-500 mb-0.5">{{__('indexes.reference')}}</div>
                    <div class="text-sm font-medium text-gray-700 dark:text-gray-300 max-w-xs break-words">{{$order->reference}}</div>
                </div>
                <div>
                    <div class="text-[11px] text-gray-400 dark:text-gray-500 mb-0.5">{{__('shows.created_at')}}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $order->created_at->isoFormat('DD/MM/YYYY ['. __('shows.at_time') .'] h:mm a') }}</div>
                </div>
                <div>
                    <div class="text-[11px] text-gray-400 dark:text-gray-500 mb-0.5">Creada por</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $order->createdBy->name }}</div>
                </div>
            </div>
        </div>

        <div x-data="{ activeTab: {{request()->get('activeTab', 0)}} }" class="mt-6">
            <div class="flex items-center gap-6">
                <button
                    @click="activeTab = 0"
                    class="py-3 text-sm border-b-2 hover:cursor-pointer"
                    :class="{ 'border-lits-red-500 font-semibold text-gray-900 dark:text-gray-50': activeTab === 0, 'border-transparent font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200': activeTab !== 0 }"
                >
                    {{__('shows.services')}} <span class="text-xs text-gray-400 dark:text-gray-500">{{ count($services) }}</span>
                </button>
                <button
                    @click="activeTab = 1"
                    class="py-3 text-sm border-b-2 hover:cursor-pointer"
                    :class="{ 'border-lits-red-500 font-semibold text-gray-900 dark:text-gray-50': activeTab === 1, 'border-transparent font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200': activeTab !== 1 }"
                >
                    {{__('shows.files')}}
                </button>
            </div>
            <div class="border-b border-gray-200 dark:border-lits-blue-450 -mt-px mb-5"></div>

            <div x-cloak x-show.transition.in.opacity.duration.600="activeTab === 0">
                @forelse($services as $service)
                    @php
                        $entitySlug = match ($service->serviceType) {
                            'Embarque' => 'shipment',
                            'Almacén' => 'warehouse',
                            default => 'customs',
                        };
                        $entityLabel = match ($service->serviceType) {
                            'Embarque' => __('Shipment'),
                            'Almacén' => __('Warehouse'),
                            default => __('Custom'),
                        };
                        $entityIcon = $service->service->serviceMode->icon ?? match ($service->serviceType) {
                            'Embarque' => 'fa-regular fa-route',
                            'Almacén' => 'fa-regular fa-warehouse',
                            default => 'fa-regular fa-person-military-pointing',
                        };
                    @endphp
                    <div class="flex items-start gap-3.5 py-4 border-b border-gray-200 dark:border-lits-blue-450/60 last:border-b-0">
                        <div class="size-9 shrink-0 rounded-lg flex items-center justify-center
                            @if($entitySlug === 'shipment') bg-entity-shipments-50 dark:bg-entity-shipments/15 text-entity-shipments
                            @elseif($entitySlug === 'warehouse') bg-entity-warehouses-50 dark:bg-entity-warehouses/15 text-entity-warehouses
                            @else bg-entity-customs-50 dark:bg-entity-customs/15 text-entity-customs
                            @endif
                        ">
                            <i class="{{ $entityIcon }} text-base"></i>
                        </div>
                        <div class="flex-1 min-w-0 grid grid-cols-1 sm:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)_minmax(0,1fr)_auto] lg:grid-cols-[minmax(0,1.4fr)_minmax(0,1.2fr)_minmax(0,1fr)_minmax(0,1fr)_auto] items-start gap-4">
                            <div>
                                <div class="flex flex-wrap items-center gap-x-2 gap-y-1.5">
                                    <div class="inline-flex items-center gap-2 rounded-lg px-2.5 py-1.5
                                        @if($entitySlug === 'shipment') bg-entity-shipments-50 dark:bg-entity-shipments/15
                                        @elseif($entitySlug === 'warehouse') bg-entity-warehouses-50 dark:bg-entity-warehouses/15
                                        @else bg-entity-customs-50 dark:bg-entity-customs/15
                                        @endif
                                    ">
                                    <span class="w-[4.75rem] shrink-0 text-[11px] font-semibold uppercase tracking-wide
                                        @if($entitySlug === 'shipment') text-entity-shipments
                                        @elseif($entitySlug === 'warehouse') text-entity-warehouses
                                        @else text-entity-customs
                                        @endif
                                    ">{{ $entityLabel }}</span>
                                    @foreach([
                                        'Service Class' => $service->service->serviceClass?->code,
                                        'Service Mode' => $service->service->serviceMode?->code,
                                        'Class Type' => $service->service->classType?->code,
                                        'Service Level' => $service->service->serviceLevel?->code,
                                    ] as $label => $code)
                                        @if($code)
                                            <span data-tippy-content="{{ $label }}" class="w-[2.75rem] text-center text-[10.5px] font-semibold px-1.5 py-0.5 rounded border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 text-gray-400 dark:text-gray-500 hover:cursor-help">{{ $code }}</span>
                                        @else
                                            <span aria-hidden="true" class="w-[2.75rem]"></span>
                                        @endif
                                    @endforeach
                                    </div>
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset {{ $service->status->badgeColor() }}">
                                        <span class="size-1.5 rounded-full {{ $service->status->dotColor() }}"></span>
                                        {{ $service->status->label() }}
                                    </span>
                                </div>
                                <a
                                    href="{{route($service->route . 'show', ['order' => $service->service->order->id, $service->slug => $service->service->id])}}"
                                    class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 hover:underline mt-0.5 inline-block"
                                >
                                    @if($service->serviceType == 'Embarque')
                                        @can('create', \App\Models\Order::class)
                                            {{ $service->service->tracking_code }}
                                        @else
                                            {{ $service->service->tracking_number }}
                                        @endcan
                                    @else
                                        {{ $service->service->tracking_code }}
                                    @endif
                                </a>
                                <div class="text-sm font-medium text-gray-700 dark:text-gray-300 mt-0.5 max-w-xs break-words">{{ $service->service->reference }}</div>
                                <div class="text-[11px] text-gray-400 dark:text-gray-500 mt-1">{{__('shows.created_at')}} {{ $service->service->created_at->isoFormat('DD/MM/YYYY') }}</div>
                            </div>

                            <div class="hidden lg:block min-w-0">
                                @if($service->serviceType == 'Embarque')
                                    <div class="text-[11px] text-gray-400 dark:text-gray-500 mb-1">Ruta</div>
                                    <div data-tippy-content="{{ $service->service->ship_from }}" class="flex items-center gap-1.5">
                                        <i class="fa-solid fa-location-dot text-[11px] text-gray-400 dark:text-gray-500"></i>
                                        <span class="text-xs text-gray-700 dark:text-gray-300 line-clamp-1">{{ $service->service->originCity?->name }}, {{ $service->service->originState?->short_name }}</span>
                                    </div>
                                    <div data-tippy-content="{{ $service->service->ship_to }}" class="flex items-center gap-1.5 mt-1">
                                        <i class="fa-solid fa-flag text-[11px] text-gray-400 dark:text-gray-500"></i>
                                        <span class="text-xs text-gray-700 dark:text-gray-300 line-clamp-1">{{ $service->service->destinationCity?->name }}, {{ $service->service->destinationState?->short_name }}</span>
                                    </div>
                                @endif
                            </div>

                            <div>
                                @if($service->serviceType == 'Embarque')
                                  @can('create', \App\Models\Order::class)
                                    <div class="text-[11px] text-gray-400 dark:text-gray-500 mb-1">Transportista</div>
                                    @if($service->service->transportations->isNotEmpty())
                                        <div class="text-xs font-semibold">
                                            @foreach($service->service->transportations as $transportation)
                                                @continue(! $transportation->agency?->company_name)
                                                <div x-data="{ openTransport: false, confirmDelete: false }" x-init="$watch('openTransport', value => { if (! value) confirmDelete = false })" @keydown.escape.window="openTransport = false">
                                                    <button
                                                        type="button"
                                                        @click="openTransport = true"
                                                        class="text-left font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 hover:underline cursor-pointer"
                                                    >{{ $transportation->agency->company_name }}</button>

                                                    <template x-teleport="body">
                                                        <div x-cloak x-show="openTransport" class="relative z-100" role="dialog" aria-modal="true">
                                                            <div
                                                                class="fixed inset-0 bg-gray-500/75 dark:bg-gray-950/75 transition-opacity"
                                                                aria-hidden="true"
                                                                x-show="openTransport"
                                                                x-transition:enter="ease-out duration-300"
                                                                x-transition:enter-start="opacity-0"
                                                                x-transition:enter-end="opacity-100"
                                                                x-transition:leave="ease-in duration-200"
                                                                x-transition:leave-start="opacity-100"
                                                                x-transition:leave-end="opacity-0"
                                                            ></div>

                                                            <div class="fixed inset-0 z-100 w-screen overflow-y-auto">
                                                                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0" @click.self="openTransport = false">
                                                                    <div
                                                                        x-show="openTransport"
                                                                        x-transition:enter="ease-out duration-300"
                                                                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                                        x-transition:leave="ease-in duration-200"
                                                                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                                                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                                        class="relative transform overflow-hidden rounded-lg bg-white dark:bg-lits-blue-550 px-4 pt-5 pb-4 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6"
                                                                    >
                                                                        <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
                                                                            <button type="button" @click="openTransport = false" class="rounded-md bg-white dark:bg-lits-blue-550 text-gray-400 dark:text-gray-500 hover:text-gray-500 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-hidden">
                                                                                <span class="sr-only">Close</span>
                                                                                <i class="fa-regular fa-xmark"></i>
                                                                            </button>
                                                                        </div>

                                                                        <div x-show="! confirmDelete">
                                                                            <h3 class="text-base font-semibold text-gray-900 dark:text-gray-50">{{ $transportation->agency->company_name }}</h3>
                                                                            <div class="mt-5 flex justify-center">
                                                                                <table>
                                                                                    <thead>
                                                                                    <tr>
                                                                                        <th class="border bg-yellow-100 dark:bg-yellow-500/15 px-3 py-1 text-blue-900 dark:text-blue-300">{{__('ECO NUMBER')}}</th>
                                                                                        <th class="border bg-yellow-100 dark:bg-yellow-500/15 px-3 py-1 text-blue-900 dark:text-blue-300">{{ $transportation->unit_eco_number }}</th>
                                                                                    </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                    <tr>
                                                                                        <td class="border px-3 py-1 text-blue-900 dark:text-blue-300">{{__('PLATES')}}</td>
                                                                                        <td class="border px-3 py-1 text-blue-900 dark:text-blue-300">{{ $transportation->plates }}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="border px-3 py-1 text-blue-900 dark:text-blue-300">CAAT</td>
                                                                                        <td class="border px-3 py-1 text-blue-900 dark:text-blue-300">{{ $transportation->agency->caat_code }}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="border px-3 py-1 text-blue-900 dark:text-blue-300">SCAC</td>
                                                                                        <td class="border px-3 py-1 text-blue-900 dark:text-blue-300">{{ $transportation->agency->scac_code }}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="border px-3 py-1 text-blue-900 dark:text-blue-300">{{__('SERIES')}}</td>
                                                                                        <td class="border px-3 py-1 text-blue-900 dark:text-blue-300">{{ $transportation->vehicle?->serial_number }}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="border px-3 py-1 text-blue-900 dark:text-blue-300">{{__('UNIT')}}</td>
                                                                                        <td class="border px-3 py-1 text-blue-900 dark:text-blue-300">{{ $transportation->vehicle?->vehicle_type }}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="border px-3 py-1 text-blue-900 dark:text-blue-300">{{__('BRAND AND LINE')}}</td>
                                                                                        <td class="border px-3 py-1 text-blue-900 dark:text-blue-300">{{ $transportation->vehicle?->vehicle_brand }}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="border px-3 py-1 text-blue-900 dark:text-blue-300">{{__('MODEL')}}</td>
                                                                                        <td class="border px-3 py-1 text-blue-900 dark:text-blue-300">{{ $transportation->vehicle?->vehicle_model }}</td>
                                                                                    </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>

                                                                            <div class="mt-5 flex flex-wrap items-center justify-center gap-2">
                                                                                @if($transportation->tracking_link != null)
                                                                                    <a href="{{ $transportation->tracking_link }}" target="_blank" class="inline-flex items-center gap-1.5 rounded-md px-3 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200 ring-1 ring-gray-300 dark:ring-gray-600 ring-inset hover:bg-gray-50 dark:hover:bg-gray-800">
                                                                                        <i class="fa-regular fa-link"></i> Ver enlace
                                                                                    </a>
                                                                                @endif
                                                                                @can('update', $service->service)
                                                                                    <a href="{{ route('orders.shipments.transportations.edit', ['order' => $order->id, 'shipment' => $service->service->id, 'transportation' => $transportation->id]) }}" class="inline-flex items-center gap-1.5 rounded-md px-3 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200 ring-1 ring-gray-300 dark:ring-gray-600 ring-inset hover:bg-gray-50 dark:hover:bg-gray-800">
                                                                                        <i class="fa-regular fa-pen-to-square"></i> {{__('indexes.edit')}}
                                                                                    </a>
                                                                                @endcan
                                                                                @can('delete', $service->service)
                                                                                    <button type="button" @click="confirmDelete = true" class="inline-flex items-center gap-1.5 rounded-md px-3 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200 ring-1 ring-gray-300 dark:ring-gray-600 ring-inset hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-500/15 dark:hover:text-red-400 cursor-pointer">
                                                                                        <i class="fa-regular fa-trash-can"></i> {{__('Delete')}}
                                                                                    </button>
                                                                                @endcan
                                                                                <button type="button" @click="openTransport = false" class="inline-flex rounded-md px-3 py-2 text-sm font-semibold text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer">Cerrar</button>
                                                                            </div>
                                                                        </div>

                                                                        @can('delete', $service->service)
                                                                            <div x-cloak x-show="confirmDelete">
                                                                                <div class="sm:flex sm:items-start">
                                                                                    <div class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-500/15 sm:mx-0 sm:size-10">
                                                                                        <i class="fa-regular fa-triangle-exclamation text-red-600 dark:text-red-400 text-lg"></i>
                                                                                    </div>
                                                                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left whitespace-normal">
                                                                                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-50">{{__('Delete transport')}}</h3>
                                                                                        <div class="mt-2">
                                                                                            <p class="text-sm text-gray-500 dark:text-gray-400 font-normal">{{__('Are you sure to remove the following transport from the shipping order?')}}</p>
                                                                                            <p class="text-sm text-red-500 dark:text-red-400 font-medium">{{ $transportation->agency->name }} - {{ $transportation->transportation_type }}</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                                                                    <form action="{{ route('orders.shipments.transportations.destroy', ['order' => $order->id, 'shipment' => $service->service->id, 'transportation' => $transportation->id]) }}" method="POST">
                                                                                        @csrf
                                                                                        @method('DELETE')
                                                                                        <button type="submit" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500 sm:ml-3 sm:w-auto cursor-pointer">{{__('Delete')}}</button>
                                                                                    </form>
                                                                                    <button type="button" @click="confirmDelete = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-50 shadow-xs ring-1 ring-gray-300 dark:ring-gray-600 ring-inset hover:bg-gray-50 dark:hover:bg-gray-800 sm:mt-0 sm:w-auto cursor-pointer">{{__('Go back')}}</button>
                                                                                </div>
                                                                            </div>
                                                                        @endcan
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-xs text-gray-400 dark:text-gray-500">Sin transportista asignado</p>
                                    @endif
                                  @endcan
                                @endif
                            </div>
                            <div>
                                @if($service->serviceType == 'Embarque')
                                    <div class="text-[11px] text-gray-400 dark:text-gray-500 mb-1">Último estatus</div>
                                    @if($service->service->latestLocation)
                                        <div class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 dark:text-gray-400">
                                            <span class="size-1.5 rounded-full bg-gray-400 dark:bg-gray-500"></span>
                                            {{$service->service->latestLocation->status->name}}
                                        </div>
                                        <div class="text-[11.5px] text-gray-400 dark:text-gray-500 mt-0.5">{{$service->service->latestLocation->location_date->isoFormat('D MMM YYYY, h:mm a')}}</div>
                                        @isset($service->service->latestLocation->name)
                                            <a
                                                href="{{route($service->route . 'show', ['order' => $service->service->order->id, $service->slug => $service->service->id, 'activeTab' => 2])}}"
                                                class="text-[11.5px] text-blue-600 dark:text-blue-400 hover:underline"
                                            >{{$service->service->latestLocation->name}}</a>
                                        @endisset
                                    @else
                                        <p class="text-xs text-gray-400 dark:text-gray-500">Aún no se han ingresado actualizaciones de estatus</p>
                                    @endif
                                @endif
                            </div>

                            <div class="flex flex-col items-end gap-2 shrink-0">
                                <div class="flex items-center gap-1">
                                    <a
                                        href="{{route($service->route . 'show', ['order' => $service->service->order->id, $service->slug => $service->service->id, 'activeTab' => 1])}}"
                                        data-tippy-content="{{__(('indexes.files'))}}"
                                        role="button"
                                        class="size-7 shrink-0 rounded-md flex items-center justify-center text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-200"
                                    >
                                        <i class="fa-regular fa-paperclip"></i>
                                    </a>
                                    @can('update', $service->service)
                                        <a
                                            href="{{route($service->route . 'edit', ['order' => $service->service->order->id, $service->slug => $service->service->id])}}"
                                            data-tippy-content="{{__(('indexes.edit'))}}"
                                            role="button"
                                            class="size-7 shrink-0 rounded-md flex items-center justify-center text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-200"
                                        >
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </a>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-10 text-center">
                        <i class="fa-regular fa-folder-open mx-auto text-4xl text-gray-300 dark:text-gray-600"></i>
                        <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-50">No hay servicios en la orden</h3>
                        @can('update', $order)
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Crea uno nuevo haciendo click en <span class="font-semibold">{{__('shows.add_service')}}</span>.</p>
                        @endcan
                    </div>
                @endforelse
            </div>
            <div x-cloak x-show.transition.in.opacity.duration.600="activeTab === 1" class="pt-2">
                <livewire:order-documents :order="$order" />
            </div>
        </div>
    </div>
    @can('update', $order)
        <x-drawers.edit-order :order="$order" :clients="$clients" />
        <x-drawers.add-shipment
            :order="$order"
            :cloneFrom="$cloneFrom"
            :serviceClasses="$serviceClasses"
            :defaultServiceClass="$defaultServiceClass"
            :instructionsOne="$instructionsOne"
            :instructionsTwo="$instructionsTwo"
        />
        <x-drawers.add-import
            :order="$order"
            :serviceClasses="$importServiceClasses"
            :defaultServiceClass="$defaultImportServiceClass"
        />
        <x-drawers.warehouse-form
            mode="create"
            :order="$order"
            :warehouses="$warehouses"
            :serviceClasses="$warehouseServiceClasses"
            :defaultServiceClass="$defaultWarehouseServiceClass"
        />
    @endcan
</x-layout-app>
