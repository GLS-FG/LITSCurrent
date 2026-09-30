@section('title', $order->code)
<x-layout-app>
    <div>
        <x-navigation.breadcrumbs :links="[__('Orders') => route('orders.index'), $order->code => '#']" />

        <div class="mt-5 flex items-start justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-2.5 flex-wrap">
                @if($order->urgent)
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-lits-red-50 dark:bg-lits-red-500/15 px-2.5 py-1 text-xs font-bold text-lits-red-600 dark:text-lits-red-400">
                        <i class="fa-regular fa-light-emergency-on"></i>
                        {{__('Urgent')}}
                    </span>
                @endif
                <h1 class="text-xl font-bold tracking-tight text-gray-900 dark:text-gray-50">{{$order->code}}</h1>
                <span class="inline-flex items-center gap-1.5 text-xs font-medium {{ $order->order_status_id->textColor() }}">
                    <span class="size-1.5 rounded-full {{ $order->order_status_id->dotColor() }}"></span>
                    {{ $order->order_status_id->label() }}
                </span>
            </div>
            <div class="flex items-center gap-1.5 flex-wrap">
                @can('update', $order)
                    <a
                        href="{{route('orders.edit', ['order' => $order->id])}}"
                        data-tippy-content="{{__('shows.edit_order')}}"
                        role="button"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800"
                    >
                        <i class="fa-regular fa-pen-to-square"></i>
                        {{__('shows.edit')}}
                    </a>
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
                        <div class="flex-1 min-w-0 grid grid-cols-1 sm:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)_minmax(0,1fr)_auto] items-start gap-4">
                            <div>
                                <div class="text-[11px] font-semibold uppercase tracking-wide
                                    @if($entitySlug === 'shipment') text-entity-shipments
                                    @elseif($entitySlug === 'warehouse') text-entity-warehouses
                                    @else text-entity-customs
                                    @endif
                                ">{{ $entityLabel }}</div>
                                <a
                                    href="{{route($service->route . 'show', ['order' => $service->service->order->id, $service->slug => $service->service->id])}}"
                                    class="text-sm font-semibold text-gray-900 dark:text-gray-50 hover:text-lits-red-500 mt-0.5 inline-block"
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
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 max-w-xs break-words">{{ $service->service->reference }}</div>
                                <div class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{__('shows.created_at')}} {{ $service->service->created_at->isoFormat('DD/MM/YYYY') }}</div>
                                <div class="flex gap-1 mt-2">
                                    @foreach([
                                        'Service Class' => $service->service->serviceClass?->code,
                                        'Service Mode' => $service->service->serviceMode?->code,
                                        'Class Type' => $service->service->classType?->code,
                                        'Service Level' => $service->service->serviceLevel?->code,
                                    ] as $label => $code)
                                        @if($code)
                                            <span data-tippy-content="{{ $label }}" class="text-[10.5px] font-semibold px-1.5 py-0.5 rounded border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 text-gray-400 dark:text-gray-500 hover:cursor-help">{{ $code }}</span>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                @if($service->serviceType == 'Embarque')
                                    <div class="text-[11px] text-gray-400 dark:text-gray-500 mb-1">Transportista</div>
                                    @if($service->service->transportations->isNotEmpty())
                                        <div class="text-xs font-semibold text-gray-700 dark:text-gray-300">
                                            {{ $service->service->transportations->pluck('agency.company_name')->filter()->implode(', ') }}
                                        </div>
                                    @else
                                        <p class="text-xs text-gray-400 dark:text-gray-500">Sin transportista asignado</p>
                                    @endif
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
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium {{ $service->status->textColor() }}">
                                    <span class="size-1.5 rounded-full {{ $service->status->dotColor() }}"></span>
                                    {{ $service->status->label() }}
                                </span>
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
        <x-drawers.add-shipment
            :order="$order"
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
