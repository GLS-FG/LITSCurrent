@section('title', __('Service orders'))
<x-layout-app>
    <section>
        <x-navigation.breadcrumbs :links="[__('Orders') => '#']" />
        @isset($history)
            <x-headings.without-action
                :title="__('Service orders history')"
            />
        @else
            <x-headings.with-one-action
                :title="__('Service orders')"
                :buttonLabel="__('Add service')"
                :buttonAction="route('orders.create')"
                buttonEvent="open-add-order-drawer"
                :objectClass="\App\Models\Order::class"
            />
        @endisset
        @if(session()->has('success'))
            <x-alerts.success class="mt-4" :message="session('success')" />
        @endif

        <div class="mt-5" x-data="{ showFilters: false }">
            <form method="GET" action="{{ $index }}" class="flex flex-col gap-2">
                <div class="flex flex-col md:flex-row gap-2">
                    <div class="flex-1 flex items-center gap-2 rounded-lg bg-white dark:bg-lits-blue-550 border border-gray-200 dark:border-lits-blue-450 px-3.5 py-2.5">
                        <i class="fa-regular fa-magnifying-glass text-gray-400 dark:text-gray-500"></i>
                        <input type="text" value="{{ request('code_search') }}" autocomplete="off" name="code_search" placeholder="{{__('indexes.search_orders')}}" class="flex-1 border-0 bg-transparent p-0 text-sm text-gray-900 dark:text-gray-50 placeholder:text-gray-400 dark:placeholder:text-gray-500 outline-none">
                        @if(request('code_search'))
                            <a href="{{ $index }}" class="text-gray-400 dark:text-gray-500 hover:text-gray-600" aria-hidden="true"><i class="fa-regular fa-xmark"></i></a>
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

                <div x-cloak x-show="showFilters" x-transition class="grid grid-cols-1 sm:grid-cols-3 gap-2 rounded-lg bg-gray-50/60 dark:bg-lits-blue-550/60 border border-gray-100 dark:border-lits-blue-450 p-2.5">
                    <div class="relative">
                        <input type="text" value="{{ request('client_search') }}" autocomplete="off" name="client_search" placeholder="{{__('indexes.search_clients')}}" class="w-full rounded-md bg-white dark:bg-lits-blue-550 py-2 pr-8 pl-3 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">
                        @if(request('client_search'))
                            <a href="{{ $index }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 hover:text-gray-600"><i class="fa-regular fa-xmark text-xs"></i></a>
                        @endif
                    </div>
                    <div class="relative">
                        <input type="text" value="{{ request('reference_search') }}" autocomplete="off" name="reference_search" placeholder="{{__('indexes.search_references')}}" class="w-full rounded-md bg-white dark:bg-lits-blue-550 py-2 pr-8 pl-3 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">
                        @if(request('reference_search'))
                            <a href="{{ $index }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 hover:text-gray-600"><i class="fa-regular fa-xmark text-xs"></i></a>
                        @endif
                    </div>
                    <div class="relative">
                        <input type="text" value="{{ request('service_search') }}" autocomplete="off" name="service_search" placeholder="Service Type" class="w-full rounded-md bg-white dark:bg-lits-blue-550 py-2 pr-8 pl-3 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">
                        @if(request('service_search'))
                            <a href="{{ $index }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 hover:text-gray-600"><i class="fa-regular fa-xmark text-xs"></i></a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        @unless($history ?? false)
            <div class="mt-4 flex items-center gap-5 border-b border-gray-200 dark:border-lits-blue-450">
                <a
                    href="{{ route('orders.index', request()->except(['urgent', 'page'])) }}"
                    class="inline-flex items-center gap-1.5 pb-2.5 text-sm border-b-2 {{ $onlyUrgent ? 'border-transparent font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' : 'border-lits-red-500 font-semibold text-gray-900 dark:text-gray-50' }}"
                >
                    {{__('indexes.active')}}
                    <span class="text-xs text-gray-400 dark:text-gray-500">{{ $activeCount }}</span>
                </a>
                <a
                    href="{{ route('orders.index', array_merge(request()->except(['urgent', 'page']), ['urgent' => 1])) }}"
                    class="inline-flex items-center gap-1.5 pb-2.5 text-sm border-b-2 {{ $onlyUrgent ? 'border-lits-red-500 font-semibold text-gray-900 dark:text-gray-50' : 'border-transparent font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}"
                >
                    <span class="size-1.5 rounded-full bg-lits-red-500"></span>
                    {{__('indexes.urgent')}}
                    <span class="text-xs text-gray-400 dark:text-gray-500">{{ $urgentCount }}</span>
                </a>
            </div>
        @endunless

        <div class="mt-4 overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-lits-blue-450">
                            <th scope="col" class="py-2.5 pl-2 text-left text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">{{__("indexes.order")}}</th>
                            <th scope="col" class="px-3 py-2.5 text-left text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">{{__("indexes.client")}}</th>
                            <th scope="col" class="px-3 py-2.5 text-left text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">{{__("indexes.reference")}}</th>
                            <th scope="col" class="px-3 py-2.5 text-left text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">{{__("indexes.request_date")}}</th>
                            <th scope="col" class="px-3 py-2.5 text-left text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">{{__("indexes.status")}}</th>
                            <th scope="col" class="px-3 py-2.5 text-left text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">{{__("indexes.services")}}</th>
                            <th scope="col" class="py-2.5 pr-2 pl-3 text-center text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">{{__('Actions')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($orders as $order)
                        <tr class="group border-b border-gray-100 dark:border-lits-blue-450/60 {{ $order->urgent ? 'bg-lits-red-50/50 dark:bg-lits-red-500/10 shadow-[inset_3px_0_0_var(--color-lits-red-500)] hover:bg-lits-red-50 dark:hover:bg-lits-red-500/15' : 'hover:bg-gray-50 dark:hover:bg-lits-blue-550/60' }}">
                            <td class="py-3.5 pl-4">
                                <a href="{{route('orders.show', ['order' => $order->id])}}" class="tabular-nums text-sm font-semibold text-gray-900 dark:text-gray-50 hover:text-lits-red-500">{{ $order->code }}</a>
                                <div class="text-gray-400 dark:text-gray-500 text-xs mt-0.5">{{ $order->createdBy->name }}</div>
                            </td>
                            <td class="px-3 py-3.5">
                                <div class="flex items-center">
                                    <div class="size-8 shrink-0 flex items-center justify-center rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700">
                                        <img alt="{{$order->client->trade_name}}" src="{{ route('clients.logos', [ 'filename' => str_replace(".","_",str_replace("logos/", "", $order->client->image))]) }}" />
                                    </div>
                                    <div class="ml-2">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-50">{{ $order->client->trade_name }}</div>
                                        <div class="text-gray-400 dark:text-gray-500 text-xs">{{ $order->contact->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-3.5 text-sm text-gray-500 dark:text-gray-400 max-w-xs">
                                <span class="block truncate">{{ $order->reference }}</span>
                            </td>
                            <td class="px-3 py-3.5 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                {{ $order->created_at->isoFormat('DD/MM/YYYY') }}
                            </td>
                            <td class="px-3 py-3.5 text-sm whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium {{ $order->order_status_id->textColor() }}">
                                    <span class="size-1.5 rounded-full {{ $order->order_status_id->dotColor() }}"></span>
                                    {{ $order->order_status_id->label() }}
                                </span>
                            </td>
                            <td class="py-3.5 pr-4 pl-3 text-sm whitespace-nowrap">
                                @if($order->shipments->isEmpty() && $order->imports->isEmpty() && $order->storages->isEmpty())
                                    <span class="text-xs text-gray-300 dark:text-gray-600">&mdash;</span>
                                @else
                                    <ul class="flex items-center gap-1">
                                        @foreach($order->shipments as $service)
                                            <a
                                                href="{{route('orders.shipments.show', ['order' => $order, 'shipment' => $service])}}"
                                                data-tippy-content="{{$service->reference}}"
                                                role="button"
                                                class="shrink-0 flex size-6 items-center justify-center rounded-md @if($service->status == \App\Enums\OrderShipmentStatusEnum::CLOSED) text-gray-400 dark:text-gray-500 @else text-entity-shipments bg-entity-shipments-50 dark:bg-entity-shipments/15 @endif"
                                            >
                                                @if($service->serviceMode != null)
                                                    <i class="{{$service->serviceMode->icon}} text-xs"></i>
                                                @else
                                                    <i class="fa-regular fa-route text-xs"></i>
                                                @endif
                                            </a>
                                        @endforeach
                                        @foreach($order->imports as $service)
                                            <a
                                                href="{{route('orders.imports.show', ['order' => $order, 'import' => $service])}}"
                                                data-tippy-content="{{$service->reference}}"
                                                role="button"
                                                class="shrink-0 flex size-6 items-center justify-center rounded-md @if($service->status == \App\Enums\OrderImportStatusEnum::CLOSED) text-gray-400 dark:text-gray-500 @else text-entity-customs bg-entity-customs-50 dark:bg-entity-customs/15 @endif"
                                            >
                                                @if($service->serviceMode != null)
                                                    <i class="{{$service->serviceMode->icon}} text-xs"></i>
                                                @else
                                                    <i class="fa-regular fa-person-military-pointing text-xs"></i>
                                                @endif
                                            </a>
                                        @endforeach
                                        @foreach($order->storages as $service)
                                            <a
                                                href="{{route('orders.warehouse-storages.show', ['order' => $order, 'warehouse_storage' => $service])}}"
                                                data-tippy-content="{{$service->reference}}"
                                                role="button"
                                                class="shrink-0 flex size-6 items-center justify-center rounded-md @if($service->status == \App\Enums\WarehouseStorageStatusEnum::CLOSED) text-gray-400 dark:text-gray-500 @else text-entity-warehouses bg-entity-warehouses-50 dark:bg-entity-warehouses/15 @endif"
                                            >
                                                @if($service->serviceMode != null)
                                                    <i class="{{$service->serviceMode->icon}} text-xs"></i>
                                                @else
                                                    <i class="fa-regular fa-warehouse text-xs"></i>
                                                @endif
                                            </a>
                                        @endforeach
                                    </ul>
                                @endif
                            </td>
                            <td class="relative py-3.5 pr-4 pl-3 text-sm font-medium whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1">
                                    <a
                                        href="{{route('orders.show', ['order' => $order->id])}}"
                                        data-tippy-content="{{__('indexes.view')}}"
                                        role="button"
                                        class="size-7 shrink-0 rounded-md flex items-center justify-center text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-200"
                                    >
                                        <i class="fa-regular fa-eye"></i>
                                    </a>
                                    <a
                                        href="{{route('orders.show', ['order' => $order->id, 'activeTab' => 1])}}"
                                        data-tippy-content="{{__('indexes.files')}}"
                                        role="button"
                                        class="size-7 shrink-0 rounded-md flex items-center justify-center text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-200"
                                    >
                                        <i class="fa-regular fa-paperclip"></i>
                                    </a>
                                    @can('update', $order)
                                    <button
                                        type="button"
                                        @click="window.dispatchEvent(new CustomEvent('request-edit-order', { detail: { url: '{{ route('orders.edit-data', ['order' => $order->id]) }}' } }))"
                                        data-tippy-content="{{__('indexes.edit')}}"
                                        class="size-7 shrink-0 rounded-md flex items-center justify-center text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-200 hover:cursor-pointer"
                                    >
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </button>
                                    @endcan
                                    @can('delete', $order)
                                        <div
                                            class="relative inline-block text-left"
                                            x-data="{ openCancel: false }"
                                        >
                                            <button
                                                type="button"
                                                @click="openCancel = true"
                                                data-tippy-content="{{__('indexes.cancel')}}"
                                                class="size-7 shrink-0 rounded-md flex items-center justify-center text-gray-400 dark:text-gray-500 hover:bg-red-50 dark:hover:bg-red-500/15 hover:text-red-600 dark:hover:text-red-400 hover:cursor-pointer"
                                            >
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                            <div x-cloak x-show="openCancel" class="relative z-100" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                                <div
                                                    class="fixed inset-0 bg-gray-500/75 dark:bg-gray-950/75 transition-opacity"
                                                    aria-hidden="true"
                                                    x-show="openCancel"
                                                    x-transition:enter="ease-out duration-300"
                                                    x-transition:enter-start="opacity-0"
                                                    x-transition:enter-end="opacity-100"
                                                    x-transition:leave="ease-in duration-200"
                                                    x-transition:leave-start="opacity-100"
                                                    x-transition:leave-end="opacity-0"
                                                ></div>

                                                <div class="fixed inset-0 z-100 w-screen overflow-y-auto">
                                                    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                                                        <div
                                                            x-show="openCancel"
                                                            x-transition:enter="ease-out duration-300"
                                                            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                            x-transition:leave="ease-in duration-200"
                                                            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                                            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                            class="relative transform overflow-hidden rounded-lg bg-white dark:bg-lits-blue-550 px-4 pt-5 pb-4 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6"
                                                        >
                                                            <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
                                                                <button type="button" @click="openCancel = false" class="rounded-md bg-white dark:bg-lits-blue-550 text-gray-400 dark:text-gray-500 hover:text-gray-500 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-hidden">
                                                                    <span class="sr-only">Close</span>
                                                                    <i class="fa-regular fa-xmark"></i>
                                                                </button>
                                                            </div>
                                                            <div class="sm:flex sm:items-start">
                                                                <div class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-500/15 sm:mx-0 sm:size-10">
                                                                    <i class="fa-regular fa-triangle-exclamation text-lg text-red-600 dark:text-red-400"></i>
                                                                </div>
                                                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left whitespace-normal">
                                                                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-50" id="modal-title">
                                                                        {{__('Cancel service')}}
                                                                    </h3>
                                                                    <div class="mt-2">
                                                                        <p class="text-sm text-gray-500 dark:text-gray-400 font-normal">
                                                                            {{__('Are you sure to cancel the following order?')}}
                                                                        </p>
                                                                        <p class="text-sm text-red-500 dark:text-red-400 font-medium">{{$order->code}}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                                                <form action="{{route('orders.destroy', ['order' => $order->id])}}" method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500 sm:ml-3 sm:w-auto">
                                                                        {{__('indexes.cancel')}}
                                                                    </button>
                                                                </form>
                                                                <button type="button" @click="openCancel = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-50 shadow-xs ring-1 ring-gray-300 dark:ring-gray-600 ring-inset hover:bg-gray-50 dark:hover:bg-gray-800 sm:mt-0 sm:w-auto">
                                                                    {{__('indexes.go_back')}}
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-8 text-sm text-center text-gray-500 dark:text-gray-400">
                                No hay órdenes de servicio en la base de datos
                                @can('create', \App\Models\Order::class)
                                <span>, define una nueva haciendo <button type="button" @click="window.dispatchEvent(new CustomEvent('open-add-order-drawer'))" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 hover:cursor-pointer">click aquí</button></span>
                                @endcan
                                .
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($orders->count())
            <nav class="mt-2">
                {{ $orders->links() }}
            </nav>
        @endif
    </section>
    @if($orders->contains(fn ($order) => auth()->user()->can('update', $order)))
        <x-drawers.edit-order :clients="$clients" />
    @endif
    @unless(isset($history))
        @can('create', \App\Models\Order::class)
            <x-drawers.add-order :clients="$clients" />
        @endcan
    @endunless
</x-layout-app>
