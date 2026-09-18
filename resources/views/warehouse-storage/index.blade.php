@section('title', 'Almacenes')
<x-layout-app>
    <section>
        <x-navigation.breadcrumbs :links="['Almacenes' => '#']" />
        <x-headings.without-action :title="'Almacenes'" />
        @if(session()->has('success'))
            <x-alerts.success class="mt-4" :message="session('success')" />
        @endif
        <div class="mt-2 flow-root">
            <div class="shadow-lits-card rounded bg-white">
                <div class="px-4 py-5 sm:p-4 border-b border-gray-200">
                    <form method="GET" action="{{ route('warehouse-storages.index') }}" class="w-full block sm:flex items-center gap-2">
                        <div class="flex-1 md:flex items-between gap-2">
                            <div class="-mr-px flex items-center px-0 mt-2 md:mt-0">
                                <div class="grid w-full grid-cols-1 relative">
                                    <input type="text" value="{{ request('code_search') }}" autocomplete="off" name="code_search" placeholder="{{__('indexes.search_orders')}}" class="col-start-1 row-start-1 block w-full rounded-md bg-white py-1.5 pr-10 pl-10 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    <i class="fa-regular fa-magnifying-glass text-gray-400 pointer-events-none col-start-1 row-start-1 ml-3 self-center"></i>
                                    @if(request('code_search'))
                                        <a href="{{ route('warehouse-storages.index') }}" class="absolute right-3 size-5 self-center text-gray-400 hover:text-gray-600" aria-hidden="true" data-slot="icon">
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
                                        <a href="{{ route('warehouse-storages.index') }}" class="absolute right-3 size-5 self-center text-gray-400 hover:text-gray-600" aria-hidden="true" data-slot="icon">
                                            <i class="fa-regular fa-xmark"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div class="-mr-px grow flex-1/3 shrink-0 flex items-center px-0 mt-2 md:mt-0">
                                <div class="grid w-full grid-cols-1 relative">
                                    <input type="text" value="{{ request('reference_search') }}" autocomplete="off" name="reference_search" placeholder="{{__('indexes.search_references')}}" class="col-start-1 row-start-1 block w-full rounded-md bg-white py-1.5 pr-10 pl-10 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    <i class="fa-regular fa-file-magnifying-glass text-gray-400 pointer-events-none col-start-1 row-start-1 ml-3 self-center"></i>
                                    @if(request('reference_search'))
                                        <a href="{{ route('warehouse-storages.index') }}" class="absolute right-3 size-5 self-center text-gray-400 hover:text-gray-600" aria-hidden="true" data-slot="icon">
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
                                        <a href="{{ route('warehouse-storages.index') }}" class="absolute right-3 size-5 self-center text-gray-400 hover:text-gray-600" aria-hidden="true" data-slot="icon">
                                            <i class="fa-regular fa-xmark"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div class="-mr-px flex items-center px-0 mt-2 md:mt-0">
                                <div class="grid w-full grid-cols-1 relative">
                                    <input type="text" value="{{ request('status_search') }}" autocomplete="off" name="status_search" placeholder="{{__('indexes.search_status')}}" class="col-start-1 row-start-1 block w-full rounded-md bg-white py-1.5 pr-10 pl-10 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    <i class="fa-solid fa-bars-progress text-gray-400 pointer-events-none col-start-1 row-start-1 ml-3 self-center"></i>
                                    @if(request('status_search'))
                                        <a href="{{ route('warehouse-storages.index') }}" class="absolute right-3 size-5 self-center text-gray-400 hover:text-gray-600" aria-hidden="true" data-slot="icon">
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
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold whitespace-nowrap text-gray-900">Orden</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold whitespace-nowrap text-gray-900">Cliente</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold whitespace-nowrap text-gray-900">Referencia</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold whitespace-nowrap text-gray-900">Almacén</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold whitespace-nowrap text-gray-900">Fecha solicitud</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold whitespace-nowrap text-gray-900">Estatus</th>
                                <th scope="col" class="relative py-3.5 pr-4 pl-3 sm:pr-6"><span class="sr-only">Acciones</span></th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 ">
                            @forelse($storages as $storage)
                                <tr>
                                    <td class="py-4 pl-4 sm:pl-6">
                                        @if($storage->urgent)
                                            <i class="fa-regular fa-light-emergency-on text-2xl text-red-500"></i>
                                        @endif
                                    </td>
                                    <td class="px-3 py-4 text-sm whitespace-nowrap ">
                                        <a href="{{route('orders.warehouse-storages.show', ['order' => $storage->order->id, 'warehouse_storage' => $storage->id])}}" class="text-indigo-600 hover:text-indigo-900">{{ $storage->tracking_code }}</a>
                                        <p class="text-gray-500 text-xs">{{ $storage->order->code }}</p>
                                    </td>
                                    <td class="px-3 py-4 text-gray-900">
                                        <div class="flex items-center">
                                            <div class="size-8 shrink-0 flex items-center justify-center rounded-full overflow-hidden bg-gray-200">
                                                <img alt="{{$storage->order->client->trade_name}}" src="{{ route('clients.logos', [ 'filename' => str_replace(".","_",str_replace("logos/", "", $storage->order->client->image))]) }}" />
                                            </div>
                                            <div class="ml-2">
                                                <div class="text-sm font-medium text-gray-900">{{ $storage->order->client->trade_name }}</div>
                                                <div class="text-gray-500 text-xs">{{$storage->order->contact->name}}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-4 text-sm text-gray-500">
                                        {{ $storage->reference }}
                                    </td>
                                    <td class="px-3 py-4 text-sm text-gray-500">
                                        {{ $storage->warehouse->name }}
                                    </td>
                                    <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-900">
                                        {{ $storage->created_at->isoFormat('DD/MM/YYYY') }}
                                    </td>
                                    <td class="px-3 py-4 text-sm">
                                        <a
                                            href="{{route('orders.warehouse-storages.show', ['order' => $storage->order->id, 'warehouse_storage' => $storage->id, 'activeTab' => 2])}}"
                                            data-tippy-content="Ir a estatus"
                                            role="button"
                                        >
                                            @if($storage->latestLocation != null)
                                                <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 inset-ring inset-ring-gray-500/10">{{$storage->latestLocation->status->name}}</span>
                                            @else
                                                <span class="whitespace-nowrap inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 inset-ring inset-ring-yellow-600/20">No actualizado</span>
                                            @endif
                                        </a>
                                    </td>
                                    <td class="py-4 pr-4 pl-3 text-sm whitespace-nowrap sm:pr-6">
                                        <div class="flex items-center gap-x-1">
                                            <a
                                                href="{{route('orders.warehouse-storages.show', ['order' => $storage->order->id, 'warehouse_storage' => $storage->id])}}"
                                                data-tippy-content="Ver"
                                                role="button"
                                                class="size-7 shrink-0 rounded-md bg-blue-100 flex items-center justify-center font-semibold text-blue-500 hover:text-blue-800 hover:bg-blue-200"
                                            >
                                                <i class="fa-regular fa-eye"></i>
                                            </a>
                                            @can('update', $storage)
                                                <a
                                                    href="{{route('orders.warehouse-storages.edit', ['order' => $storage->order->id, 'warehouse_storage' => $storage->id])}}"
                                                    data-tippy-content="Editar"
                                                    role="button"
                                                    class="size-7 shrink-0 rounded-md bg-green-100 flex items-center justify-center font-semibold text-green-500 hover:text-green-800 hover:bg-green-200"
                                                >
                                                    <i class="fa-regular fa-pen-to-square"></i>
                                                </a>
                                            @endcan
                                            @can('delete', $storage)
                                                <div
                                                    class="relative inline-block text-left"
                                                    x-data="{ openCancel: false }"
                                                    @keydown.escape.prevent.stop="close($refs.buttonDropdown)"
                                                    @focusin.window="! $refs.panel.contains($event.target) && close()"
                                                    x-id="['dropdown-button-{{$storage->id}}']"
                                                    @confirm.window="{{$storage->id}} == $event.detail && $refs['delete-row-' + $event.detail].submit()"
                                                >
                                                    <button
                                                        type="button"
                                                        @click="openCancel = true"
                                                        data-tippy-content="Cancelar"
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
                                                                            <h3 class="text-base font-semibold text-gray-900" id="modal-title">Cancelar almacén</h3>
                                                                            <div class="mt-2">
                                                                                <p class="text-sm text-gray-500 font-normal">¿Estás seguro de cancelar la siguiente orden de almacén?</p>
                                                                                <p class="text-sm text-red-500 font-medium">{{$storage->reference}}</p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                                                        <form action="{{route('orders.warehouse-storages.destroy', ['order' => $storage->order->id, 'warehouse_storage' => $storage->id])}}" method="POST">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500 sm:ml-3 sm:w-auto">Cancelar</button>
                                                                        </form>
                                                                        <button type="button" @click="openCancel = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs ring-1 ring-gray-300 ring-inset hover:bg-gray-50 sm:mt-0 sm:w-auto">Regresar</button>
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
                                        @can('create', \App\Models\WarehouseStorage::class)
                                            <span>, para crear una debes ingresar a una <a href="{{route('orders.index')}}" class="text-indigo-600 hover:text-indigo-900">órden de servicio</a>, hacer click en "Agregar servicio", seguido de "Almacén"</span>
                                        @endcan
                                        .
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($storages->count())
                    <nav>
                        {{ $storages->links() }}
                    </nav>
                @endif
            </div>
        </div>
    </section>
</x-layout-app>
