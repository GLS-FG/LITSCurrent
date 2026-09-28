@section('title', 'Estatus del Servicio')
<x-layout-admin>
    <section>
        <x-navigation.breadcrumbs :links="['Estatus Servicios' => route('service-statuses.index'), $serviceType->code => '#' ]" />
        <x-headings.with-one-action
            :title="'Estatus de ' . $serviceType->name"
            :buttonLabel="'Nuevo estatus'"
            :buttonAction="route('service-statuses.service-type-statuses.create', ['service_status' => $serviceType->id])"
            :objectClass="\App\Models\ServiceClass::class"
        />
        @if(session()->has('success'))
            <x-alerts.success class="mt-4" :message="session('success')" />
        @endif
        <div class="mt-5 overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <table class="min-w-full">
                    <thead>
                    <tr>
                        <th scope="col" rowspan="2" class="py-2.5 pl-2 text-left text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">Nombre</th>
                        <th scope="col" rowspan="2" class="py-2.5 px-3 text-left text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">Color</th>
                        @foreach($serviceType->serviceClasses as $serviceClass)
                            <th scope="col" colspan="{{count($serviceClass->serviceModes)}}" class="bg-gray-50 dark:bg-white/[0.02] border-r border-l border-b border-gray-200 dark:border-lits-blue-450 px-3 py-1.5 text-center text-xs font-semibold whitespace-nowrap text-gray-500 dark:text-gray-400">{{$serviceClass->code}}</th>
                        @endforeach
                        <th scope="col" rowspan="2" class="py-2.5 pr-2 pl-3 text-center text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">Acciones</th>
                    </tr>
                    <tr class="border-b border-gray-200 dark:border-lits-blue-450">
                        @foreach($serviceType->serviceClasses as $serviceClass)
                            @foreach($serviceClass->serviceModes as $serviceMode)
                                <th scope="col" class="bg-gray-50 dark:bg-white/[0.02] border-r border-l border-gray-200 dark:border-lits-blue-450 px-3 py-1.5 text-center text-xs font-semibold whitespace-nowrap text-gray-500 dark:text-gray-400">{{$serviceMode->code}}</th>
                            @endforeach
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($statuses as $status)
                        <tr class="border-b border-gray-100 dark:border-lits-blue-450/60 hover:bg-gray-50 dark:hover:bg-lits-blue-550/60">
                            <td class="py-3.5 pl-2">
                                <a href="{{route('service-statuses.service-type-statuses.show', ['service_status' => $serviceType, 'service_type_status' => $status])}}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 text-sm font-medium">{{ $status->name }}</a>
                            </td>
                            <td class="py-3.5 px-3">
                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium inset-ring {{$status->color}}">COLOR</span>
                            </td>
                            @foreach($serviceType->serviceClasses as $serviceClass)
                                @foreach($serviceClass->serviceModes as $serviceMode)
                                    <td class="px-3 py-3.5 border-l border-r border-gray-200 dark:border-lits-blue-450 text-center whitespace-nowrap">
                                        @if($status->modes->where('id', $serviceMode->id)->first() != null)
                                            <i class="fa-solid fa-square-check text-green-500 dark:text-green-400"></i>
                                        @else
                                            <i class="fa-solid fa-square-xmark text-gray-300 dark:text-gray-600"></i>
                                        @endif
                                    </td>
                                @endforeach
                            @endforeach
                            <td class="py-3.5 pr-2 pl-3 text-sm font-medium whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1">
                                <a
                                    href="{{route('service-statuses.service-type-statuses.show', ['service_status' => $serviceType, 'service_type_status' => $status])}}"
                                    data-tippy-content="Ver"
                                    role="button"
                                    class="size-7 shrink-0 rounded-md flex items-center justify-center text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-200"
                                >
                                    <i class="fa-regular fa-eye"></i>
                                </a>
                                @can('update', $serviceType)
                                    @if (!$loop->first)
                                        <form action="{{route('service-statuses.service-type-statuses.up', ['service_status' => $serviceType, 'service_type_status' => $status])}}" method="POST">
                                            @csrf
                                            <button
                                                type="submit"
                                                class="size-7 shrink-0 rounded-md flex items-center justify-center text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-200 hover:cursor-pointer"
                                                data-tippy-content="Mover a arriba"
                                            >
                                                <i class="fa-regular fa-arrow-up"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if (!$loop->last)
                                        <form action="{{route('service-statuses.service-type-statuses.down', ['service_status' => $serviceType, 'service_type_status' => $status])}}" method="POST">
                                            @csrf
                                            <button
                                                type="submit"
                                                class="size-7 shrink-0 rounded-md flex items-center justify-center text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-200 hover:cursor-pointer"
                                                data-tippy-content="Mover a abajo"
                                            >
                                                <i class="fa-regular fa-arrow-down"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <a
                                        href="{{route('service-statuses.service-type-statuses.edit', ['service_status' => $serviceType, 'service_type_status' => $status])}}"
                                        data-tippy-content="Editar"
                                        role="button"
                                        class="size-7 shrink-0 rounded-md flex items-center justify-center text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-200"
                                    >
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </a>
                                @endcan
                                @can('delete', $serviceType)
                                    <div
                                        class="relative inline-block text-left"
                                        x-data="{ openCancel: false }"
                                        @keydown.escape.prevent.stop="close($refs.buttonDropdown)"
                                        @focusin.window="! $refs.panel.contains($event.target) && close()"
                                        x-id="['dropdown-button-{{$status->id}}']"
                                        @confirm.window="{{$status->id}} == $event.detail && $refs['delete-row-' + $event.detail].submit()"
                                    >
                                        <button
                                            type="button"
                                            @click="openCancel = true"
                                            data-tippy-content="Eliminar"
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
                                                                        <i class="fa-regular fa-triangle-exclamation text-red-600 dark:text-red-400 text-lg"></i>
                                                                    </div>
                                                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left whitespace-normal">
                                                                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-50" id="modal-title">Eliminar estatus</h3>
                                                                        <div class="mt-2">
                                                                            <p class="text-sm text-gray-500 dark:text-gray-400 font-normal">¿Estás seguro de eliminar el estatus?</p>
                                                                            <p class="text-sm text-red-500 dark:text-red-400 font-medium">{{$status->name}}</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                                                    <form action="{{route('service-statuses.service-type-statuses.destroy', ['service_status' => $serviceType, 'service_type_status' => $status])}}" method="POST">
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
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="100%" class="px-3 py-4 text-sm text-center text-gray-500 dark:text-gray-400">
                                        No hay estatus del service type en la base de datos
                                        @can('create', \App\Models\TransportationAgency::class)
                                            <span>, define uno nuevo haciendo <a href="{{route('service-statuses.service-type-statuses.create', ['service_status' => $serviceType->id])}}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900">click aquí</a></span>
                                        @endcan
                                        .
                                    </td>
                                </tr>
                            @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</x-layout-admin>
