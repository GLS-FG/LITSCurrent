@section('title', 'Detalles de almacén')
@section('custom_script')
    @parent
    <script type="module">
        window.editLocation = function(location) {
            const { id, comments, location_date, service_type_status_id } = location;
            $('#edit-location_date').val(location_date);
            $('#edit-service_type_status_id').val(service_type_status_id);
            $('#edit-comments').val(comments);
            $('#edit-location-form').attr('action', "{{request()->getSchemeAndHttpHost()}}/orders/{{$order->id}}/warehouse_storage/{{$storage->id}}/locations/" + id);
        };
    </script>
@endsection
<x-layout-app>
    <div>
        <x-navigation.breadcrumbs :links="['Ordenes' => route('orders.index'), $order->code => route('orders.show', ['order' => $order->id]), 'Almacenamiento' => '#']" />

        <div class="mt-5 flex items-start justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-2.5 flex-wrap">
                <div class="size-9 shrink-0 rounded-lg flex items-center justify-center bg-entity-warehouses-50 dark:bg-entity-warehouses/15 text-entity-warehouses">
                    <i class="fa-regular fa-warehouse text-base"></i>
                </div>
                @if($storage->urgent)
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-lits-red-50 dark:bg-lits-red-500/15 px-2.5 py-1 text-xs font-bold text-lits-red-600 dark:text-lits-red-400">
                        <i class="fa-regular fa-light-emergency-on"></i>
                        {{__('Urgent')}}
                    </span>
                @endif
                <h1 class="text-xl font-bold tracking-tight text-gray-900 dark:text-gray-50">
                    {{$storage->tracking_code}}
                </h1>
                <span class="inline-flex items-center gap-1.5 text-xs font-medium {{ $storage->warehouse_storage_status_id->textColor() }}">
                    <span class="size-1.5 rounded-full {{ $storage->warehouse_storage_status_id->dotColor() }}"></span>
                    {{ $storage->warehouse_storage_status_id->label() }}
                </span>
            </div>
            <div class="flex items-center gap-1.5 flex-wrap">
                @can('update', $storage)
                    <a
                        href="{{route('orders.warehouse-storages.edit', [ 'order' => $order->id, 'warehouse_storage' => $storage->id ])}}"
                        data-tippy-content="{{__('shows.edit_service')}}"
                        role="button"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800"
                    >
                        <i class="fa-regular fa-pen-to-square"></i>
                        {{__('shows.edit')}}
                    </a>
                    <a
                        href='{{route('orders.warehouse-storages.documents.create', [ 'order' => $storage->order, 'warehouse_storage' => $storage ])}}'
                        data-tippy-content="{{__('shows.attach_file')}}"
                        role="button"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800"
                    >
                        <i class="fa-regular fa-paperclip"></i>
                        {{__('shows.attach_file')}}
                    </a>
                @endcan
                @canany(['update', 'restore'], $storage)
                    <form action="{{ route('orders.warehouse-storages.update.status', ['order' => $order->id, 'warehouse_storage' => $storage->id]) }}" method="POST" class="flex">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 focus-within:relative">
                            <select id="warehouse_storage_status_id" name="warehouse_storage_status_id" autocomplete="off" aria-label="{{__('indexes.status')}}" class="col-start-1 row-start-1 w-full appearance-none rounded-l-lg border border-r-0 border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 py-2 pr-8 pl-3 text-sm text-gray-900 dark:text-gray-50 outline-none focus:ring-2 focus:ring-indigo-600">
                                @foreach ($statuses as $status)
                                    <option value= {{ $status->id }} @selected($storage->warehouse_storage_status_id->value == $status->id)>{{ $status->name }}  </option>
                                @endforeach
                            </select>
                            <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2.5 self-center justify-self-end text-xs text-gray-400 dark:text-gray-500"></i>
                        </div>
                        <button data-tippy-content="{{__('shows.save_status')}}" type="submit" class="flex items-center gap-x-1.5 rounded-r-lg border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:cursor-pointer">
                            <i class="fa-regular fa-floppy-disk"></i>
                        </button>
                    </form>
                @endcanany
            </div>
        </div>

        @if(session()->has('success'))
            <x-alerts.success class="mt-4" :message="session('success')" />
        @endif
        @if ($errors->any())
            <x-alerts.error :message="'Para editar la órden soluciona los siguientes errores:'" :errors="$errors" class="my-4" />
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
                    <div class="text-sm font-medium text-gray-700 dark:text-gray-300 max-w-xs break-words">{{$storage->reference}}</div>
                </div>
                <div>
                    <div class="text-[11px] text-gray-400 dark:text-gray-500 mb-0.5">{{__('shows.created_at')}}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $storage->created_at->isoFormat('DD/MM/YYYY ['. __('shows.at_time') .'] h:mm a') }}</div>
                </div>
                <div>
                    <div class="text-[11px] text-gray-400 dark:text-gray-500 mb-0.5">Creada por</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $storage->order->createdBy->name }}</div>
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
                    {{__('shows.services')}}
                </button>
                <button
                    @click="activeTab = 1"
                    class="py-3 text-sm border-b-2 hover:cursor-pointer"
                    :class="{ 'border-lits-red-500 font-semibold text-gray-900 dark:text-gray-50': activeTab === 1, 'border-transparent font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200': activeTab !== 1 }"
                >
                    {{__('shows.files')}}
                </button>
                <button
                    @click="activeTab = 2"
                    class="py-3 text-sm border-b-2 hover:cursor-pointer"
                    :class="{ 'border-lits-red-500 font-semibold text-gray-900 dark:text-gray-50': activeTab === 2, 'border-transparent font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200': activeTab !== 2 }"
                >
                    {{__('shows.status')}}
                </button>
                @can('checklist', $storage)
                    <button
                        @click="activeTab = 3"
                        class="py-3 text-sm border-b-2 hover:cursor-pointer"
                        :class="{ 'border-lits-red-500 font-semibold text-gray-900 dark:text-gray-50': activeTab === 3, 'border-transparent font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200': activeTab !== 3 }"
                    >
                        {{__('shows.internal_control')}}
                    </button>
                @endcan
            </div>
            <div class="border-b border-gray-200 dark:border-lits-blue-450 -mt-px mb-5"></div>

            <div x-cloak x-show.transition.in.opacity.duration.600="activeTab === 0" class="pt-2">
                <div class="flex flex-col gap-6">
                    <div class="flex gap-1.5 flex-wrap">
                        @foreach([
                            'Service Class' => $storage->serviceClass?->code,
                            'Service Mode' => $storage->serviceMode?->code,
                            'Class Type' => $storage->classType?->code,
                            'Service Level' => $storage->serviceLevel?->code,
                        ] as $label => $code)
                            @if($code)
                                <span data-tippy-content="{{ $label }}" class="text-[10.5px] font-semibold px-1.5 py-0.5 rounded border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 text-gray-400 dark:text-gray-500 hover:cursor-help">{{ $code }}</span>
                            @endif
                        @endforeach
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-5 gap-6">
                        <div>
                            <div class="text-[11px] text-gray-400 dark:text-gray-500 mb-1">Almacén</div>
                            <p class="text-sm text-gray-900 dark:text-gray-50 font-medium">{{$storage->warehouse->name}}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{$storage->warehouse->contact_name}}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{$storage->warehouse->phone1}}</p>
                        </div>
                        <div>
                            <div class="text-[11px] text-gray-400 dark:text-gray-500 mb-1">Recibido de</div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                @isset($storage->receipt)
                                    {{$storage->receipt}}
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">No especificado</span>
                                @endisset
                            </p>
                        </div>
                        <div>
                            <div class="text-[11px] text-gray-400 dark:text-gray-500 mb-1">Fecha de recibo</div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                @isset($storage->receipt_date)
                                    <time dateTime="{{ $storage->receipt_date->isoFormat('YYYY-MM-DD') }}">{{ $storage->receipt_date->isoFormat('DD/MM/YYYY') }}</time>
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">No especificada</span>
                                @endisset
                            </p>
                        </div>
                        <div>
                            <div class="text-[11px] text-gray-400 dark:text-gray-500 mb-1">Documento</div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                @isset($storage->document)
                                    {{$storage->document}}
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">No especificado</span>
                                @endisset
                            </p>
                        </div>
                        <div>
                            <div class="text-[11px] text-gray-400 dark:text-gray-500 mb-1">Fecha de documento</div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                @isset($storage->document_date)
                                    <time dateTime="{{ $storage->document_date->isoFormat('YYYY-MM-DD') }}">{{ $storage->document_date->isoFormat('DD/MM/YYYY') }}</time>
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">No especificada</span>
                                @endisset
                            </p>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-200 dark:border-lits-blue-450/60">
                        <div class="text-[11px] text-gray-400 dark:text-gray-500 mb-1">{{__('shows.comments')}}</div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $storage->comments == null || $storage->comments == "" ? "Sin comentarios" : $storage->comments }}
                        </p>
                    </div>

                    <div class="pt-6 border-t border-gray-200 dark:border-lits-blue-450/60">
                        <livewire:products-section :order="$storage" />
                    </div>
                </div>
            </div>

            <div x-cloak x-show.transition.in.opacity.duration.600="activeTab === 1">
                <livewire:documents :order="$storage" />
            </div>

            <div x-cloak x-show.transition.in.opacity.duration.600="activeTab === 2">
                <div class="pt-2">
                    <div x-data="{ mode: 1 }">
                        <div x-cloak x-show.transition.in.opacity.duration.600="mode === 1">
                            <div class="flex flex-col gap-6">
                                <div class="flex items-center justify-between gap-3 flex-wrap">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-50">Seguimiento de almacén</p>
                                    @can('update', $storage)
                                        <button data-tippy-content="Agregar geolocalización" @click="mode = 2" class="inline-flex items-center gap-1.5 rounded-lg bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">
                                            <i class="fa-regular fa-location-dot"></i>
                                            Agregar
                                        </button>
                                    @endcan
                                </div>
                                <div class="rounded-lg border border-gray-200 dark:border-lits-blue-450 p-4">
                                    <div x-data="{ collapsed: false }" @collapse="collapsed = !collapsed" class="flex justify-end mb-3">
                                        <button
                                            @click="$dispatch('collapse', !collapsed)"
                                            class="text-xs font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:cursor-pointer"
                                            x-text="collapsed ? 'Expandir todo' : 'Minimizar todo'"
                                        ></button>
                                    </div>
                                    <ul role="list">
                                        @forelse($storage->locations as $location)
                                            <li class="relative flex gap-x-2 @if (!$loop->last) pb-6 @endif">
                                                @if (!$loop->last)
                                                    <div class="absolute top-0 -bottom-0 left-0 flex w-6 justify-center">
                                                        <div class="w-px bg-gray-200 dark:bg-lits-blue-450"></div>
                                                    </div>
                                                @endif
                                                @if ($location->service_type_status_id == 20)
                                                    <div class="relative flex size-6 flex-none items-center justify-center bg-body dark:bg-lits-blue-600">
                                                        <i class="fa-solid fa-circle-check text-lg text-indigo-600 dark:text-indigo-400"></i>
                                                    </div>
                                                @else
                                                    <div class="relative flex size-6 flex-none items-center justify-center bg-body dark:bg-lits-blue-600">
                                                        <div class="size-1.5 rounded-full {{ $loop->last ? 'bg-entity-warehouses shadow-[0_0_8px_2px_#B45309]' : 'bg-gray-400 dark:bg-gray-600' }}"></div>
                                                    </div>
                                                @endif
                                                <div class="flex-1 min-w-0">
                                                    <div x-data="{ collapsed: false }" @collapse.window="collapsed = $event.detail">
                                                        <div class="flex items-center gap-x-1 text-gray-500 dark:text-gray-400 flex-wrap">
                                                            <p class="text-sm/6 text-gray-900 dark:text-gray-50 font-medium">
                                                                {{$location->status->name}}
                                                            </p>
                                                            <button x-cloak x-show.transition.in.opacity.duration.600="collapsed === false" @click="collapsed = true" data-tippy-content="Minimizar" class="size-7 shrink-0 rounded-md flex items-center justify-center text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-200">
                                                                <i class="fa-regular fa-angle-up text-xs"></i>
                                                            </button>
                                                            <button x-cloak x-show.transition.in.opacity.duration.600="collapsed === true" @click="collapsed = false" data-tippy-content="Expandir" class="size-7 shrink-0 rounded-md flex items-center justify-center text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-200">
                                                                <i class="fa-regular fa-angle-down text-xs"></i>
                                                            </button>
                                                            <div class="ml-auto flex items-center gap-1">
                                                                <button type="button" disabled data-tippy-content="Mapa próximamente" class="size-7 shrink-0 rounded-md flex items-center justify-center text-gray-300 dark:text-gray-600 cursor-not-allowed">
                                                                    <i class="fa-regular fa-location-dot text-xs"></i>
                                                                </button>
                                                                @can('delete', $storage)
                                                                    <button @click="mode = 3; editLocation({{$location}})" data-tippy-content="Editar estatus" class="size-7 shrink-0 rounded-md flex items-center justify-center text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-200">
                                                                        <i class="fa-regular fa-pen-to-square text-xs"></i>
                                                                    </button>
                                                                    <div
                                                                        class="relative inline-block text-left"
                                                                        x-data="{ openCancel: false }"
                                                                    >
                                                                        <button
                                                                            type="button"
                                                                            @click="openCancel = true"
                                                                            data-tippy-content="Eliminar estatus"
                                                                            class="size-7 shrink-0 rounded-md flex items-center justify-center text-gray-400 dark:text-gray-500 hover:bg-red-50 dark:hover:bg-red-500/15 hover:text-red-600 dark:hover:text-red-400 hover:cursor-pointer"
                                                                        >
                                                                            <i class="fa-regular fa-trash-can text-xs"></i>
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
                                                                                                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-50" id="modal-title">Eliminar estatus</h3>
                                                                                                <div class="mt-2">
                                                                                                    <p class="text-sm text-gray-500 dark:text-gray-400 font-normal">¿Estás seguro de eliminar el estatus?</p>
                                                                                                    <p class="text-sm text-red-500 dark:text-red-400 font-medium">{{$location->status->name}}</p>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                                                                            <form action="{{route('orders.warehouse-storages.locations.destroy', ['order' => $order->id, 'warehouse_storage' => $storage->id, 'location' => $location])}}" method="POST">
                                                                                                @csrf
                                                                                                @method('DELETE')
                                                                                                <button type="submit" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500 sm:ml-3 sm:w-auto">Eliminar</button>
                                                                                            </form>
                                                                                            <button type="button" @click="openCancel = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-50 shadow-xs ring-1 ring-gray-300 dark:ring-gray-600 ring-inset hover:bg-gray-50 dark:hover:bg-gray-800 sm:mt-0 sm:w-auto">Regresar</button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endcan
                                                            </div>
                                                        </div>
                                                        <div x-cloak x-show.transition.in.opacity.duration.600="collapsed === false" class="mt-1">
                                                            <div class="flex items-start gap-x-1">
                                                                <i class="fa-regular fa-calendar-clock text-gray-400 dark:text-gray-500 text-xs mt-0.5"></i>
                                                                <p class="text-xs text-gray-500 dark:text-gray-400 text-left"><time datetime="{{$location->location_date->isoFormat('YYYY-MM-DD')}}">{{$location->location_date->isoFormat('D MMM YYYY')}}&nbsp;&nbsp;{{$location->location_date->isoFormat('h:mm a')}}</time></p>
                                                            </div>
                                                            @isset($location->name)
                                                                <div class="flex items-start gap-x-1 mt-0.5">
                                                                    <i class="fa-regular fa-location-dot text-gray-400 dark:text-gray-500 text-xs mt-0.5"></i>
                                                                    <p class="text-xs text-gray-500 dark:text-gray-400 text-left">{{$location->name}}</p>
                                                                </div>
                                                            @endisset
                                                            @isset($location->comments)
                                                                <div class="flex items-start gap-x-1 mt-0.5">
                                                                    <i class="fa-regular fa-message-lines text-gray-400 dark:text-gray-500 text-xs mt-0.5"></i>
                                                                    <p class="text-xs text-gray-500 dark:text-gray-400 text-left">
                                                                        {{$location->comments}}
                                                                    </p>
                                                                </div>
                                                            @endisset
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        @empty
                                            <li>
                                                <div class="rounded-md bg-yellow-50 dark:bg-yellow-500/10 p-3">
                                                    <div class="flex">
                                                        <div class="shrink-0">
                                                            <i class="fa-solid fa-triangle-exclamation text-yellow-400"></i>
                                                        </div>
                                                        <div class="ml-3">
                                                            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-400">Sin actualizaciones</h3>
                                                            <div class="mt-1 text-sm text-yellow-700 dark:text-yellow-400">
                                                                <p>Aún no se han agregado actualizaciones de estatus.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @can('update', $storage)
                            <div x-cloak x-show.transition.in.opacity.duration.600="mode === 2">
                                <form action="{{ route('orders.warehouse-storages.locations.store', ['order' => $order->id, 'warehouse_storage' => $storage->id]) }}" method="POST" class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-4" autocomplete="off">
                                    @csrf
                                    <div class="sm:col-span-full">
                                        <h2 class="text-base/7 font-semibold text-gray-900 dark:text-gray-50">Nuevo estatus</h2>
                                        <p class="mt-1 text-sm/6 text-gray-600 dark:text-gray-400">Agrega un estatus al almacén, puedes buscar una dirección para ingresar una nueva geolocalización.</p>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label for="location_date" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Fecha</label>
                                        <div class="mt-2">
                                            <input id="location_date" required value="{{old('location_date', $now->format('Y-m-d\TH:i'))}}" name="location_date" type="datetime-local" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                        </div>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label for="service_type_status_id" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Estatus</label>
                                        <div class="mt-2 grid grid-cols-1">
                                            <select id="service_type_status_id" name="service_type_status_id" required autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                <option value="" selected disabled>Selecciona un nuevo estatus</option>
                                                @if($storage->serviceMode)
                                                    @foreach ($storage->serviceMode->statuses as $status)
                                                        <option value="{{ $status->id }}">{{ $status->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 dark:text-gray-400 sm:text-sm"></i>
                                        </div>
                                    </div>
                                    <div class="sm:col-span-full">
                                        <label for="comments" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Comentarios</label>
                                        <div class="mt-2">
                                            <textarea id="comments" name="comments" autocomplete="off" class="@error('comments') outline-red-400 @else outline-gray-300 dark:outline-gray-600 @enderror block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">{{old('comments')}}</textarea>
                                        </div>
                                    </div>
                                    <div class="sm:col-span-full">
                                        <div class="flex items-center justify-end gap-x-6">
                                            <button
                                                class="text-sm/6 font-semibold text-gray-900 dark:text-gray-50 hover:cursor-pointer hover:text-gray-500"
                                                @click="mode = 1"
                                                type="button"
                                            >
                                                Cancelar
                                            </button>
                                            <button type="submit" class="inline-flex items-center justify-center gap-x-1.5 rounded-md bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">
                                                <i class="fa-regular fa-location-dot"></i>
                                                Agregar
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            @isset($storage->latestLocation)
                                <div x-cloak x-show.transition.in.opacity.duration.600="mode === 3">
                                    <form id="edit-location-form" action="" method="POST" class="grid grid-cols-1 gap-x-4 gap-y-4 lg:grid-cols-4" autocomplete="off">
                                        @csrf
                                        @method('PUT')
                                        <div class="sm:col-span-full">
                                            <h2 class="text-base/7 font-semibold text-gray-900 dark:text-gray-50">Editar estatus</h2>
                                            <p class="mt-1 text-sm/6 text-gray-600 dark:text-gray-400">Edita el último estatus del almacén, puedes buscar una dirección para editar la geolocalización.</p>
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label for="edit-location_date" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Fecha</label>
                                            <div class="mt-2">
                                                <input id="edit-location_date" required name="location_date" type="datetime-local" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                            </div>
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label for="edit-service_type_status_id" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Estatus</label>
                                            <div class="mt-2 grid grid-cols-1">
                                                <select id="edit-service_type_status_id" name="service_type_status_id" required autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                    @foreach ($storage->serviceMode?->statuses as $status)
                                                        <option value= {{ $status->id }}>{{ $status->name }}</option>
                                                    @endforeach
                                                </select>
                                                <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 dark:text-gray-400 sm:text-sm"></i>
                                            </div>
                                        </div>
                                        <div class="sm:col-span-full">
                                            <label for="edit-comments" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Comentarios</label>
                                            <div class="mt-2">
                                                <textarea id="edit-comments" name="comments" autocomplete="off" class="@error('comments') outline-red-400 @else outline-gray-300 dark:outline-gray-600 @enderror block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"></textarea>
                                            </div>
                                        </div>
                                        <div class="sm:col-span-full">
                                            <div class="flex items-center justify-end gap-x-6">
                                                <button
                                                    class="text-sm/6 font-semibold text-gray-900 dark:text-gray-50 hover:cursor-pointer hover:text-gray-500"
                                                    @click="mode = 1"
                                                    type="button"
                                                >
                                                    Cancelar
                                                </button>
                                                <button type="submit" class="inline-flex items-center justify-center gap-x-1.5 rounded-md bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">
                                                    <i class="fa-regular fa-location-dot"></i>
                                                    Editar
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            @endisset
                        @endcan
                    </div>
                    <div class="relative inline-block text-left" x-data>
                        <div x-cloak x-show="$store.map.openMap" class="relative z-100" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                            <div
                                class="fixed inset-0 bg-gray-500/75 dark:bg-gray-950/75 transition-opacity"
                                aria-hidden="true"
                                x-show="$store.map.openMap"
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
                                        x-show="$store.map.openMap"
                                        x-transition:enter="ease-out duration-300"
                                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                        x-transition:leave="ease-in duration-200"
                                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                        class="relative transform overflow-hidden rounded-lg bg-white dark:bg-lits-blue-550 px-4 pt-5 pb-4 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-xl sm:p-6"
                                    >
                                        <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
                                            <button type="button" @click="$store.map.openMap = false" class="rounded-md bg-white dark:bg-lits-blue-550 text-gray-400 dark:text-gray-500 hover:text-gray-500 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-hidden">
                                                <span class="sr-only">Close</span>
                                                <i class="fa-regular fa-xmark"></i>
                                            </button>
                                        </div>
                                        <div class="mt-10 sm:mt-8">
                                            <div class="h-full w-full aspect-square" id="grand-map"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @can('checklist', $storage)
                <div x-cloak x-show.transition.in.opacity.duration.600="activeTab === 3">
                    <div class="pt-2">
                        <div class="mb-2 md:flex md:items-center md:justify-between">
                            <div class="">
                                <h2 class="text-2xl/7 font-bold text-gray-900 dark:text-gray-50 sm:truncate sm:text-3xl sm:tracking-tight">Checklist</h2>
                            </div>
                            <div class="mt-3 flex sm:mt-0 sm:ml-4">
                                @can('updateChecklist', $storage)
                                    <a
                                        href='{{route('orders.warehouse-storages.privates.create', [ 'order' => $storage->order, 'warehouse_storage' => $storage ])}}'
                                        data-tippy-content="Adjuntar archivo interno"
                                        role="button"
                                        class="mr-3 inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 whitespace-nowrap"
                                    >
                                        <i class="fa-regular fa-paperclip"></i>
                                        Adjuntar Interno
                                    </a>
                                    <div
                                        class="relative inline-block text-left"
                                        x-data="{ openComments: false }"
                                    >
                                        <button
                                            type="button"
                                            x-on:click="openComments = true"
                                            class="mr-3 inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:cursor-pointer"
                                        >
                                            Comentarios
                                        </button>
                                        <div x-cloak x-show="openComments" class="relative z-100" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                            <div
                                                class="fixed inset-0 bg-gray-500/75 dark:bg-gray-950/75 transition-opacity"
                                                aria-hidden="true"
                                                x-show="openComments"
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
                                                        x-show="openComments"
                                                        x-transition:enter="ease-out duration-300"
                                                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                        x-transition:leave="ease-in duration-200"
                                                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                        class="relative transform overflow-hidden rounded-lg bg-white dark:bg-lits-blue-550 px-4 pt-5 pb-4 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6"
                                                    >
                                                        <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
                                                            <button type="button" @click="openComments = false" class="rounded-md bg-white dark:bg-lits-blue-550 text-gray-400 dark:text-gray-500 hover:text-gray-500 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-hidden">
                                                                <span class="sr-only">Close</span>
                                                                <i class="fa-regular fa-xmark"></i>
                                                            </button>
                                                        </div>
                                                        <form action="{{route('orders.warehouse-storages.update.checklist', ['order' => $order, 'warehouse_storage' => $storage])}}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="mt-3 text-center sm:mt-0 sm:text-left whitespace-normal">
                                                                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-50" id="modal-title">Editar checklist</h3>
                                                                <div class="mt-2">
                                                                    <p class="text-sm text-gray-500 dark:text-gray-400 font-normal">Edita los comentarios del checklist</p>
                                                                    <div class="col-span-full">
                                                                        <label for="checklist_comments" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Comentarios</label>
                                                                        <div class="mt-2">
                                                                            <textarea id="checklist_comments" name="checklist_comments" autocomplete="off" class=" block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">{{old('checklist_comments', $storage->checklist_comments)}}</textarea>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                                                <button type="submit" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500 sm:ml-3 sm:w-auto">Guardar</button>
                                                                <button type="button" @click="openComments = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-50 shadow-xs ring-1 ring-gray-300 dark:ring-gray-600 ring-inset hover:bg-gray-50 dark:hover:bg-gray-800 sm:mt-0 sm:w-auto">Cancelar</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endcan
                                <a
                                    href="{{route('orders.warehouse-storages.printCL', [ 'order' => $order->id, 'warehouse_storage' => $storage->id ])}}"
                                    data-tippy-content="Descargar checklist"
                                    role="button"
                                    class="mr-3 inline-flex items-center gap-x-1.5 rounded-lg bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer"
                                >
                                    <i class="fa-regular fa-arrow-down-to-bracket"></i>
                                    Descargar
                                </a>
                            </div>
                        </div>
                        <div class="mt-8">
                            <div class="-mx-4 sm:mx-0 overflow-auto">
                                <div class="bg-white shadow-lits-card w-fit h-fit mx-auto border border-gray-200" style="width: 816px;padding: 25px;">
                                    <x-cards.checklist :order="$order" :service="$storage" :documentTypes="$documentTypes" :milestones="$milestones" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <x-cards.privatedocuments
                        :order="$storage"
                        :documents="$storage->privateDocuments"
                        :create="route('orders.warehouse-storages.privates.create', [ 'order' => $storage->order, 'warehouse_storage' => $storage ])"
                        :download="route('orders.warehouse-storages.privates.index', ['order' => $order->id, 'warehouse_storage' => $storage->id])"
                        :destroyRoute="'orders.privates.destroy'"
                        :destroyParams="['order' => $order->id]"
                    />
                </div>
            @endcan
        </div>
    </div>
</x-layout-app>
