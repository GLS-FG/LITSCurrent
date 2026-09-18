@section('title', $order->code)
<x-layout-app>
    <div>
        <x-navigation.breadcrumbs :links="[__('Orders') => route('orders.index'), $order->code => '#']" />
        <div class="mt-6 flex justify-between items-center flex-wrap gap-y-2">
            <div class="flex items-center flex-wrap sm:flex-nowrap gap-1">
                @can('update', $order)
                    <a
                        href="{{route('orders.edit', ['order' => $order->id])}}"
                        data-tippy-content="{{__('shows.edit_order')}}"
                        role="button"
                        class="inline-flex items-center justify-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs inset-ring inset-ring-gray-300 hover:bg-gray-50"
                    >
                        <i class="fa-regular fa-pen-to-square"></i>
                        {{__('shows.edit')}}
                    </a>
                    <a
                        href="{{route('orders.documents.create', [ 'order' => $order->id ])}}"
                        data-tippy-content="{{__('shows.attach_file')}}"
                        role="button"
                        class="inline-flex items-center justify-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs inset-ring inset-ring-gray-300 hover:bg-gray-50"
                    >
                        <i class="fa-regular fa-paperclip"></i>
                        {{__('shows.attach')}}
                    </a>
                @endcan
                @can('update', $order)
                    <x-dropdowns.order-add-service :order="$order"/>
                @endcan
            </div>
            <div class="flex items-center flex-wrap sm:flex-nowrap gap-1">
                @can('update', $order)
                    <form action="{{ route('orders.update.urgent', ['order' => $order->id]) }}" method="POST" class="flex">
                        @csrf
                        @method('PUT')
                        <button data-tippy-content="{{$order->urgent == true ? __('shows.remove_urgent') : __('shows.make_urgent')}}" type="submit" class="flex shrink-0 items-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 outline-1 -outline-offset-1 outline-gray-300 hover:bg-gray-50 focus:relative focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 hover:cursor-pointer">
                            {{$order->urgent == true ? __('shows.remove_urgent') : __('shows.make_urgent')}}
                        </button>
                    </form>
                @endcan
                @canany(['update', 'restore'], $order)
                    <form action="{{ route('orders.update.status', ['order' => $order->id]) }}" method="POST" class="flex">
                        @csrf
                        @method('PUT')
                        <div class="-mr-px  grid grid-cols-1 focus-within:relative">
                            <select id="order_status_id" name="order_status_id" autocomplete="off" aria-label="Country" class="col-start-1 row-start-1 w-full appearance-none rounded-l-md bg-white py-1.5 pr-8 pl-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                @foreach ($statuses as $status)
                                    <option value= {{ $status->id }} @selected($order->order_status_id->value == $status->id)>{{ $status->name }}  </option>
                                @endforeach
                            </select>
                            <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 sm:text-sm"></i>
                        </div>
                        <button data-tippy-content="{{__('shows.save_status')}}" type="submit" class="flex shrink-0 items-center gap-x-1.5 rounded-r-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 outline-1 -outline-offset-1 outline-gray-300 hover:bg-gray-50 focus:relative focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 hover:cursor-pointer">
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
            <x-alerts.error :message="__('order_errors')" :errors="$errors" class="my-4" />
        @endif
        <div class="shadow-lits-card border border-gray-200 bg-white mt-2 -mx-4 sm:mx-0 lg:mx-0">
            <div class="px-4 sm:px-6 pt-4 pb-1">
                <div class="min-w-0 flex gap-x-2 items-center">
                    @if($order->urgent)
                        <i class="fa-regular fa-light-emergency-on text-3xl/7 text-red-500"></i>
                    @endif
                    <h2 class="text-2xl/7 font-bold text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">{{$order->code}}</h2>
                    <span class="inline-flex items-center rounded-md  px-2 py-1 text-sm font-medium  ring-1 ring-inset {{ $order->order_status_id->badgeColor() }}">{{ $order->order_status_id->label() }}</span>
                </div>
            </div>
            <div class="px-4 sm:px-6 pt-1 pb-8 block md:flex md:justify-between">
                <div class="flex flex-1 items-center gap-x-6">
                    <img src="{{ route('clients.logos', [ 'filename' => str_replace(".","_",str_replace("logos/", "", $order->client->image))]) }}" alt="{{$order->client->trade_name}}" class="object-contain size-16 flex-none rounded-full bg-gray-200 outline -outline-offset-1 outline-black/5" />
                    <div>
                        <h1 class="mt-1 text-base font-semibold text-gray-900">{{$order->client->trade_name}}</h1>
                        <p class="text-sm/6 text-gray-700">{{$order->contact->name}}</p>
                    </div>
                </div>
                <div>
                    <p class="text-sm/6 text-gray-700 text-left md:text-right">{{$order->reference}}</p>
                    <p class="mt-1 text-sm text-gray-500 text-left md:text-right">{{ $order->created_at->isoFormat('DD/MM/YYYY ['. __('shows.at_time') .'] h:mm a') }}</p>
                    <p class="mt-1 text-sm text-gray-500 text-left md:text-right">{{ $order->createdBy->name }}</p>
                </div>
            </div>
            <div x-data="{ activeTab: {{request()->get('activeTab', 0)}} }">
                <div class="px-4 sm:px-6 border-b border-gray-200">
                    <nav aria-label="Tabs" class="-mb-px flex">
                        <button
                            @click="activeTab = 0"
                            class="group inline-flex items-center border-l border-t border-b border-gray-200 px-3 py-2 text-sm font-medium"
                            :class="{ 'border-t-indigo-500 text-t-indigo-600 border-t-2 border-b-white': activeTab === 0, 'text-gray-500 hover:border-gray-300 hover:text-gray-700 hover:cursor-pointer': activeTab !== 0 }"
                        >
                            {{__('shows.services')}}
                        </button>
                        <button
                            @click="activeTab = 1"
                            class="group inline-flex items-center border-l border-r border-t border-b border-gray-200 px-3 py-2 text-sm font-medium"
                            :class="{ 'border-t-indigo-500 text-t-indigo-600 border-t-2 border-b-white': activeTab === 1, 'text-gray-500 hover:border-gray-300 hover:text-gray-700 hover:cursor-pointer': activeTab !== 1 }"

                        >
                            {{__('shows.files')}}
                        </button>
                    </nav>
                </div>
                <div x-cloak x-show.transition.in.opacity.duration.600="activeTab === 0">
                    <div class="px-4 py-5 sm:px-6 mb-8">
                        <ul class="space-y-4">
                            @forelse($services as $service)
                                <li class="rounded-md border p-2 border-gray-200 grid grid-cols-1 md:grid-cols-7 lg:grid-cols-10 gap-x-2 gap-y-4">
                                    <div class="col-span-1 md:col-span-2">
                                        <div class="text-sm bg-gray-100 rounded px-2.5 py-2 mb-3 flex items-center gap-x-1 font-medium text-lits-red-500">
                                            @if($service->serviceType == "Embarque")
                                                @if($service->service->serviceMode != null)
                                                    <i class="{{$service->service->serviceMode->icon}} text-lg"></i>
                                                @else
                                                    <i class="fa-regular fa-route text-lg"></i>
                                                @endif
                                            @elseif($service->serviceType == "Almacén")
                                                @if($service->service->serviceMode != null)
                                                    <i class="{{$service->service->serviceMode->icon}} text-lg"></i>
                                                @else
                                                    <i class="fa-regular fa-warehouse text-lg"></i>
                                                @endif
                                            @else
                                                @if($service->service->serviceMode != null)
                                                    <i class="{{$service->service->serviceMode->icon}} text-lg"></i>
                                                @else
                                                    <i class="fa-regular fa-person-military-pointing text-lg"></i>
                                                @endif
                                            @endif

                                            @if($service->serviceType == "Embarque")
                                            {{__('Shipment')}}
                                            @elseif($service->serviceType == "Almacén")
                                            {{__('Warehouse')}}
                                            @else
                                            {{__('Custom')}}
                                            @endif
                                        </div>
                                        <div class="px-2.5">
                                            @can('create', \App\Models\Order::class)
                                                <h1 class="text-sm font-semibold text-gray-900">Service ID</h1>
                                            @else
                                                <h1 class="text-sm font-semibold text-gray-900">Tracking Number</h1>
                                            @endcan
                                            @if($service->serviceType == "Embarque")
                                                @can('create', \App\Models\Order::class)
                                                    <h1 class="text-sm font-semibold text-gray-900">{{$service->service->tracking_code}}</h1>
                                                @else
                                                    <h1 class="text-sm font-semibold text-gray-900">{{$service->service->tracking_number}}</h1>
                                                @endcan
                                            @else
                                                <h1 class="text-sm font-semibold text-gray-900">{{$service->service->tracking_code}}</h1>
                                            @endif
                                            <p class="text-xs text-gray-500">{{__('shows.created_at')}} <time datetime="{{ $service->service->created_at }}">{{ $service->service->created_at->isoFormat('DD/MM/YYYY')  }}</time></p>
                                        </div>
                                    </div>
                                    <div class="col-span-1 md:col-span-4">
                                        <div class="bg-gray-100 rounded mb-3 flex items-center text-sm whitespace-nowrap text-truncate divide-x divide-gray-300">
                                            <div class="flex-1 text-center px-2.5 py-2">{{ $service->service->serviceClass?->code }}</div>
                                            <div class="flex-1 text-center px-2.5 py-2">{{ $service->service->serviceMode?->code }}</div>
                                            <div class="flex-1 text-center px-2.5 py-2">{{ $service->service->classType?->code }}</div>
                                            <div class="flex-1 text-center px-2.5 py-2">{{ $service->service->serviceLevel?->code }}</div>
                                        </div>
                                        <div class="px-2.5">
                                            <a
                                                href="{{route($service->route . 'show', ['order' => $service->service->order->id, $service->slug => $service->service->id])}}"
                                                class="text-sm/6 font-semibold text-blue-600 hover:text-blue-500"
                                            >
                                                {{ $service->service->reference }}
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-span-1 flex flex-col items-start gap-y-2 justify-between">
                                        <span class="inline-flex items-center rounded px-2 py-1 text-sm font-medium ring-1 ring-inset {{ $service->status->badgeColor() }}">{{ $service->status->label() }}</span>
                                        <div class="flex flex-wrap flex-none items-center justify-start gap-1.5">
                                            <a
                                                href="{{route($service->route . 'show', ['order' => $service->service->order->id, $service->slug => $service->service->id])}}"
                                                data-tippy-content="{{__(('indexes.view'))}}"
                                                role="button"
                                                class="size-7 shrink-0 rounded-md bg-blue-100 flex items-center justify-center font-semibold text-blue-500 hover:text-blue-800 hover:bg-blue-200"
                                            >
                                                <i class="fa-regular fa-eye"></i>
                                            </a>
                                            <a
                                                href="{{route($service->route . 'show', ['order' => $service->service->order->id, $service->slug => $service->service->id, 'activeTab' => 1])}}"
                                                data-tippy-content="{{__(('indexes.files'))}}"
                                                role="button"
                                                class="size-7 shrink-0 rounded-md bg-indigo-100 flex items-center justify-center font-semibold text-indigo-500 hover:text-indigo-800 hover:bg-indigo-200"
                                            >
                                                <i class="fa-regular fa-paperclip"></i>
                                            </a>
                                            @can('update', $service->service)
                                                <a
                                                    href="{{route($service->route . 'edit', ['order' => $service->service->order->id, $service->slug => $service->service->id])}}"
                                                    data-tippy-content="{{__(('indexes.edit'))}}"
                                                    role="button"
                                                    class="size-7 shrink-0 rounded-md bg-green-100 flex items-center justify-center font-semibold text-green-500 hover:text-green-800 hover:bg-green-200"
                                                >
                                                    <i class="fa-regular fa-pen-to-square"></i>
                                                </a>
                                            @endcan
                                            @can('delete', $service->service)
                                                <div
                                                    class="relative inline-block text-left"
                                                    x-data="{ openCancel: false }"
                                                >
                                                    <button
                                                        type="button"
                                                        @click="openCancel = true"
                                                        data-tippy-content="{{__(('indexes.cancel'))}}"
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
                                                                            <h3 class="text-base font-semibold text-gray-900" id="modal-title">{{__('Cancel service')}}</h3>
                                                                            <div class="mt-2">
                                                                                <p class="text-sm text-gray-500 font-normal">{{__('Are you sure to cancel the following service?')}}</p>
                                                                                <p class="text-sm text-red-500 font-medium">{{$service->service->reference}}</p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                                                        <form action="{{route($service->route . 'destroy', ['order' => $service->service->order->id, $service->slug => $service->service->id])}}" method="POST">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500 sm:ml-3 sm:w-auto">{{__('indexes.cancel')}}</button>
                                                                        </form>
                                                                        <button type="button" @click="openCancel = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs ring-1 ring-gray-300 ring-inset hover:bg-gray-50 sm:mt-0 sm:w-auto">{{__('indexes.go_back')}}</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endcan
                                        </div>
                                    </div>
                                    <div class="col-span-1 md:col-span-7 lg:col-span-3">
                                        @if($service->serviceType == "Embarque")
                                            @can('create', \App\Models\Order::class)
                                                <x-cards.order-shipment-transports-addon
                                                    :order="$order"
                                                    :shipment="$service->service"
                                                />
                                            @else
                                                <div class="flow-root h-full">
                                                    <div class="border border-gray-300 rounded h-full py-2 px-4">
                                                        <p class="text-xs text-gray-900 font-semibold">
                                                            Última actualización:
                                                        </p>
                                                        @if($service->service->latestLocation)
                                                            <p class="text-sm/6 text-gray-900">
                                                                {{$service->service->latestLocation->status->name}}
                                                            </p>
                                                            <div class="flex items-start gap-x-1">
                                                                <i class="fa-regular fa-calendar-clock text-gray-500 text-lg"></i>
                                                                <p class="py-0.5 text-xs/5 text-gray-500 text-left"><time datetime="{{$service->service->latestLocation->location_date->isoFormat('YYYY-MM-DD')}}">{{$service->service->latestLocation->location_date->isoFormat('D MMM YYYY')}}&nbsp;&nbsp;{{$service->service->latestLocation->location_date->isoFormat('h:mm a')}}</time></p>
                                                            </div>
                                                            @isset($service->service->latestLocation->name)
                                                                <div class="flex items-start gap-x-1">
                                                                    <i class="fa-regular fa-location-dot text-gray-500 text-lg"></i>
                                                                    <a
                                                                        class="hover:underline py-0.5 text-xs/5 text-blue-700 hover:text-blue-500 text-left"
                                                                        href="{{route($service->route . 'show', ['order' => $service->service->order->id, $service->slug => $service->service->id, 'activeTab' => 2])}}"
                                                                    >
                                                                        {{$service->service->latestLocation->name}}
                                                                    </a>
                                                                </div>
                                                            @endisset
                                                        @else
                                                            <p class="bg-gray-500">Aún no se han ingresado actualizaciones de estatus</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endcan
                                        @else
                                            <div class="rounded bg-gray-100 h-full w-full">

                                            </div>
                                        @endif
                                    </div>
                                </li>
                            @empty
                                <li class="text-center">
                                    <i class="fa-regular fa-folder-open mx-auto text-4xl text-gray-400"></i>
                                    <h3 class="mt-2 text-sm font-semibold text-gray-900">No hay servicios en la orden</h3>
                                    @can('update', $order)
                                        <p class="mt-1 text-sm text-gray-500">Crea uno nuevo haciendo click en <span class="font-semibold">Agregar servicio</span>.</p>
                                    @endcan
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
                <div x-cloak x-show.transition.in.opacity.duration.600="activeTab === 1">
                    <livewire:order-documents :order="$order" />
                    {{--<x-cards.documents
                        :order="$order"
                        :documents="$order->allDocuments()"
                        :create="route('orders.documents.create', [ 'order' => $order->id ])"
                        :download="route('orders.documents.index', ['order' => $order->id])"
                        :destroyRoute="'orders.documents.destroy'"
                        :destroyParams="['order' => $order]"
                    />--}}
                </div>
            </div>
        </div>
    </div>
</x-layout-app>
