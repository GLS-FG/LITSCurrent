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
                :objectClass="\App\Models\Order::class"
            />
        @endisset
        @if(session()->has('success'))
            <x-alerts.success class="mt-4" :message="session('success')" />
        @endif
        <div class="mt-2 flow-root">
            <div class="shadow-lits-card rounded-md bg-white">
                <div class="px-4 py-5 sm:p-4 border-b border-gray-200">
                    <form method="GET" action="{{ $index }}" class="w-full block md:flex items-center gap-2">
                        <div class="flex-1 md:flex items-between gap-2">
                            <div class="-mr-px flex items-center px-0 mt-2 md:mt-0">
                                <div class="grid w-full grid-cols-1 relative">
                                    <input type="text" value="{{ request('code_search') }}" autocomplete="off" name="code_search" placeholder="{{__('indexes.search_orders')}}" class="col-start-1 row-start-1 block w-full rounded-md bg-white py-1.5 pr-10 pl-10 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    <i class="fa-regular fa-magnifying-glass text-gray-400 pointer-events-none col-start-1 row-start-1 ml-3 self-center"></i>
                                    @if(request('code_search'))
                                        <a href="{{ $index }}" class="absolute right-3 size-5 self-center text-gray-400 hover:text-gray-600" aria-hidden="true" data-slot="icon">
                                            <i class="fa-regular fa-xmark"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div class="-mr-px flex items-center px-0 mt-2 md:mt-0">
                                <div class="grid w-full grid-cols-1 relative">
                                    <input type="text" value="{{ request('client_search') }}" autocomplete="off" name="client_search" placeholder="{{__('indexes.search_clients')}}" class="col-start-1 row-start-1 block w-full rounded-md bg-white py-1.5 pr-10 pl-10 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    <i class="fa-regular fa-user-magnifying-glass text-gray-400 pointer-events-none col-start-1 row-start-1 ml-3 self-center"></i>
                                    @if(request('client_search'))
                                        <a href="{{ $index }}" class="absolute right-3 size-5 self-center text-gray-400 hover:text-gray-600" aria-hidden="true" data-slot="icon">
                                            <i class="fa-regular fa-xmark"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div class="-mr-px grow shrink-0 flex items-center px-0 mt-2 md:mt-0">
                                <div class="grid w-full grid-cols-1 relative">
                                    <input type="text" value="{{ request('reference_search') }}" autocomplete="off" name="reference_search" placeholder="{{__('indexes.search_references')}}" class="col-start-1 row-start-1 block w-full rounded-md bg-white py-1.5 pr-10 pl-10 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    <i class="fa-regular fa-file-magnifying-glass text-gray-400 pointer-events-none col-start-1 row-start-1 ml-3 self-center"></i>
                                    @if(request('reference_search'))
                                        <a href="{{ $index }}" class="absolute right-3 size-5 self-center text-gray-400 hover:text-gray-600" aria-hidden="true" data-slot="icon">
                                            <i class="fa-regular fa-xmark"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div class="-mr-px flex items-center px-0 mt-2 md:mt-0">
                                <div class="grid w-full grid-cols-1 relative">
                                    <input type="text" value="{{ request('service_search') }}" autocomplete="off" name="service_search" placeholder="Service Type" class="col-start-1 row-start-1 block w-full rounded-md bg-white py-1.5 pr-10 pl-10 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    <i class="fa-regular fa-magnifying-glass text-gray-400 pointer-events-none col-start-1 row-start-1 ml-3 self-center"></i>
                                    @if(request('service_search'))
                                        <a href="{{ $index }}" class="absolute right-3 size-5 self-center text-gray-400 hover:text-gray-600" aria-hidden="true" data-slot="icon">
                                            <i class="fa-regular fa-xmark"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="w-full sm:w-auto mt-2 md:mt-0 flex flex-nowrap gap-2">
                            <button type="submit" class="inline-flex w-full justify-center items-center gap-x-1.5 rounded-md  px-3 py-2 text-sm  ring-1  ring-inset bg-white text-gray-900 ring-gray-300 hover:bg-gray-50 hover:cursor-pointer">
                                {{__('indexes.search')}}
                                <i class="fa-regular fa-magnifying-glass -mr-1"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="overflow-x-auto">
                    <div class="inline-block min-w-full align-middle">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="">
                                <tr>
                                    <th scope="col" class="py-3.5 pl-4 text-left text-sm font-semibold whitespace-nowrap text-gray-900 sm:pl-6"></th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold whitespace-nowrap text-gray-900">{{__("indexes.order")}}</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold whitespace-nowrap text-gray-900">{{__("indexes.client")}}</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold whitespace-nowrap text-gray-900">{{__("indexes.reference")}}</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold whitespace-nowrap text-gray-900">{{__("indexes.request_date")}}</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold whitespace-nowrap text-gray-900">{{__("indexes.status")}}</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold whitespace-nowrap text-gray-900">{{__("indexes.services")}}</th>
                                    <th scope="col" class="relative py-3.5 pr-4 pl-3 sm:pr-6"><span class="sr-only">{{__('Actions')}}</span></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 ">
                            @forelse($orders as $order)
                                <tr>
                                    <td class="py-4 pl-4 sm:pl-6">
                                        @if($order->urgent)
                                            <i class="fa-regular fa-light-emergency-on text-2xl text-red-500"></i>
                                        @endif
                                    </td>
                                    <td class="px-3 py-4 text-sm whitespace-nowrap ">
                                        <a href="{{route('orders.show', ['order' => $order->id])}}" class="text-indigo-600 hover:text-indigo-900">{{ $order->code }}</a>
                                        <div class="text-gray-500 text-xs">{{ $order->createdBy->name }}</div>
                                    </td>
                                    <td class="px-3 py-4 text-gray-900 ">
                                        <div class="flex items-center">
                                            <div class="size-8 shrink-0 flex items-center justify-center rounded-full overflow-hidden bg-gray-200">
                                                <img alt="{{$order->client->trade_name}}" src="{{ route('clients.logos', [ 'filename' => str_replace(".","_",str_replace("logos/", "", $order->client->image))]) }}" />
                                            </div>
                                            <div class="ml-2">
                                                <div class="text-sm font-medium text-gray-900">{{ $order->client->trade_name }}</div>
                                                <div class="text-gray-500 text-xs">{{ $order->contact->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-4 text-sm text-gray-500">
                                        {{ $order->reference }}
                                    </td>
                                    <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-900">
                                        {{ $order->created_at->isoFormat('DD/MM/YYYY') }}
                                    </td>
                                    <td class="px-3 py-4 text-sm whitespace-nowrap ">
                                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium  ring-1 ring-inset {{ $order->order_status_id->badgeColor() }}">{{ $order->order_status_id->label() }}</span>
                                    </td>
                                    <td class="py-4 pr-4 pl-3 text-sm whitespace-nowrap sm:pr-6">
                                        <ul class="flex items-center space-x-1">
                                            @foreach($order->shipments as $service)
                                                <a
                                                    href="{{route('orders.shipments.show', ['order' => $order, 'shipment' => $service])}}"
                                                    data-tippy-content="{{$service->reference}}"
                                                    role="button"
                                                    class="shrink-0 flex items-center justify-center font-semibold @if($service->status == \App\Enums\OrderShipmentStatusEnum::CLOSED) text-blue-500 hover:text-blue-600 @else text-lits-red-500 hover:text-lits-red-600 @endif"
                                                >
                                                    @if($service->serviceMode != null)
                                                        <i class="{{$service->serviceMode->icon}} text-lg"></i>
                                                    @else
                                                        <i class="fa-regular fa-route text-lg"></i>
                                                    @endif
                                                </a>
                                            @endforeach
                                            @foreach($order->imports as $service)
                                                <a
                                                    href="{{route('orders.imports.show', ['order' => $order, 'import' => $service])}}"
                                                    data-tippy-content="{{$service->reference}}"
                                                    role="button"
                                                    class="shrink-0 flex items-center justify-center font-semibold @if($service->status == \App\Enums\OrderImportStatusEnum::CLOSED) text-blue-500 hover:text-blue-600 @else text-lits-red-500 hover:text-lits-red-600 @endif"
                                                >
                                                    @if($service->serviceMode != null)
                                                        <i class="{{$service->serviceMode->icon}} text-lg"></i>
                                                    @else
                                                        <i class="fa-regular fa-person-military-pointing text-lg"></i>
                                                    @endif
                                                </a>
                                            @endforeach
                                            @foreach($order->storages as $service)
                                                <a
                                                    href="{{route('orders.warehouse-storages.show', ['order' => $order, 'warehouse_storage' => $service])}}"
                                                    data-tippy-content="{{$service->reference}}"
                                                    role="button"
                                                    class="shrink-0 flex items-center justify-center font-semibold @if($service->status == \App\Enums\WarehouseStorageStatusEnum::CLOSED) text-blue-500 hover:text-blue-600 @else text-lits-red-500 hover:text-lits-red-600 @endif"
                                                >
                                                    @if($service->serviceMode != null)
                                                        <i class="{{$service->serviceMode->icon}} text-lg"></i>
                                                    @else
                                                        <i class="fa-regular fa-warehouse text-lg"></i>
                                                    @endif
                                                </a>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td class="relative py-4 pr-4 pl-3 text-sm font-medium whitespace-nowrap sm:pr-6">
                                        <div class="flex items-center gap-x-1">
                                            <a
                                                href="{{route('orders.show', ['order' => $order->id])}}"
                                                data-tippy-content="{{__('indexes.view')}}"
                                                role="button"
                                                class="size-7 shrink-0 rounded-md bg-blue-100 flex items-center justify-center font-semibold text-blue-500 hover:text-blue-800 hover:bg-blue-200"
                                            >
                                                <i class="fa-regular fa-eye"></i>
                                            </a>
                                            <a
                                                href="{{route('orders.show', ['order' => $order->id, 'activeTab' => 1])}}"
                                                data-tippy-content="{{__('indexes.files')}}"
                                                role="button"
                                                class="size-7 shrink-0 rounded-md bg-indigo-100 flex items-center justify-center font-semibold text-indigo-500 hover:text-indigo-800 hover:bg-indigo-200"
                                            >
                                                <i class="fa-regular fa-paperclip"></i>
                                            </a>
                                            @can('update', $order)
                                            <a
                                                href="{{route('orders.edit', ['order' => $order->id])}}"
                                                data-tippy-content="{{__('indexes.edit')}}"
                                                role="button"
                                                class="size-7 shrink-0 rounded-md bg-green-100 flex items-center justify-center font-semibold text-green-500 hover:text-green-800 hover:bg-green-200"
                                            >
                                                <i class="fa-regular fa-pen-to-square"></i>
                                            </a>
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
                                                        class="size-7 shrink-0 rounded-md bg-red-100 flex items-center justify-center font-semibold text-red-500 hover:text-red-800 hover:bg-red-200 hover:cursor-pointer"
                                                    >
                                                        <i class="fa-regular fa-trash-can"></i>
                                                    </button>
                                                    <div x-cloak x-show="openCancel" class="relative z-100" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                                        <div
                                                            class="fixed inset-0 bg-gray-500/75 transition-opacity"
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
                                                                    class="relative transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6"
                                                                >
                                                                    <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
                                                                        <button type="button" @click="openCancel = false" class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-hidden">
                                                                            <span class="sr-only">Close</span>
                                                                            <i class="fa-regular fa-xmark"></i>
                                                                        </button>
                                                                    </div>
                                                                    <div class="sm:flex sm:items-start">
                                                                        <div class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:size-10">
                                                                            <i class="fa-regular fa-triangle-exclamation text-lg text-red-600"></i>
                                                                        </div>
                                                                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left whitespace-normal">
                                                                            <h3 class="text-base font-semibold text-gray-900" id="modal-title">
                                                                                {{__('Cancel service')}}
                                                                            </h3>
                                                                            <div class="mt-2">
                                                                                <p class="text-sm text-gray-500 font-normal">
                                                                                    {{__('Are you sure to cancel the following order?')}}
                                                                                </p>
                                                                                <p class="text-sm text-red-500 font-medium">{{$order->code}}</p>
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
                                                                        <button type="button" @click="openCancel = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs ring-1 ring-gray-300 ring-inset hover:bg-gray-50 sm:mt-0 sm:w-auto">
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
                                    <td colspan="6" class="px-3 py-4 text-sm text-center text-gray-500">
                                        No hay órdenes de servicio en la base de datos
                                        @can('create', \App\Models\Order::class)
                                        <span>, define una nueva haciendo <a href="{{route('orders.create')}}" class="text-indigo-600 hover:text-indigo-900">click aquí</a></span>
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
                    <nav>
                        {{ $orders->links() }}
                    </nav>
                @endif
            </div>
        </div>
    </section>
</x-layout-app>
