@section('title', 'Service Classes')
<x-layout-admin>
    <section>
        <x-navigation.breadcrumbs :links="['Service Types' => route('service-types.index'), $serviceType->code => '#' ]" />
        <x-headings.with-one-action
            :title="'Service Classes'"
            :buttonLabel="'Nuevo service class'"
            :buttonAction="route('service-types.service-classes.create', ['service_type' => $serviceType->id])"
            :objectClass="\App\Models\ServiceClass::class"
        />
        @if(session()->has('success'))
            <x-alerts.success class="mt-4" :message="session('success')" />
        @endif
        <div class="mt-2 flow-root">
            <div class="shadow-lits-card rounded bg-white dark:bg-lits-blue-550">
                <div class="overflow-x-auto">
                    <div class="inline-block min-w-full align-middle">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="">
                            <tr>
                                <th scope="col" class="py-3.5 pr-3 pl-4 text-left text-sm font-semibold whitespace-nowrap text-gray-900 dark:text-gray-50 sm:pl-6">Código</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold whitespace-nowrap text-gray-900 dark:text-gray-50">Nombre</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold whitespace-nowrap text-gray-900 dark:text-gray-50">Fecha alta</th>
                                <th scope="col" class="relative py-3.5 pr-4 pl-3 sm:pr-6"><span class="sr-only">Detalles</span></th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 ">
                            @forelse($services as $service)
                                <tr>
                                    <td class="py-4 pr-3 pl-4 text-gray-900 dark:text-gray-50 sm:pl-6 whitespace-nowrap">
                                        <a href="{{route('service-types.service-classes.service-modes.index', ['service_type' => $serviceType, 'service_class' => $service])}}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 text-sm font-medium">{{ $service->code }}</a>
                                    </td>
                                    <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-900 dark:text-gray-50">
                                        {{ $service->name }}
                                    </td>
                                    <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-900 dark:text-gray-50">
                                        {{ $service->created_at->isoFormat('D MMM YYYY') }}
                                    </td>
                                    <td class="relative py-4 pr-4 pl-3 text-sm font-medium whitespace-nowrap sm:pr-6 flex items-center justify-end gap-x-1">
                                        <a
                                            href="{{route('service-types.service-classes.service-modes.index', ['service_type' => $serviceType, 'service_class' => $service])}}"
                                            data-tippy-content="Ver"
                                            role="button"
                                            class="size-7 shrink-0 rounded-md bg-blue-100 dark:bg-blue-500/15 flex items-center justify-center font-semibold text-blue-500 dark:text-blue-400 hover:text-blue-800 hover:bg-blue-200"
                                        >
                                            <i class="fa-regular fa-eye"></i>
                                        </a>
                                        @can('update', $serviceType)
                                            <a
                                                href="{{route('service-types.service-classes.edit', ['service_type' => $serviceType, 'service_class' => $service])}}"
                                                data-tippy-content="Editar"
                                                role="button"
                                                class="size-7 shrink-0 rounded-md bg-green-100 dark:bg-green-500/15 flex items-center justify-center font-semibold text-green-500 dark:text-green-400 hover:text-green-800 hover:bg-green-200"
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
                                                x-id="['dropdown-button-{{$service->id}}']"
                                                @confirm.window="{{$service->id}} == $event.detail && $refs['delete-row-' + $event.detail].submit()"
                                            >
                                                <button
                                                    type="button"
                                                    @click="openCancel = true"
                                                    data-tippy-content="Eliminar"
                                                    class="size-7 shrink-0 rounded-md bg-red-100 dark:bg-red-500/15 flex items-center justify-center font-semibold text-red-500 dark:text-red-400 hover:text-red-800 hover:bg-red-200 hover:cursor-pointer"
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
                                                                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-50" id="modal-title">Eliminar service class</h3>
                                                                        <div class="mt-2">
                                                                            <p class="text-sm text-gray-500 dark:text-gray-400 font-normal">¿Estás seguro de eliminar el service class?</p>
                                                                            <p class="text-sm text-red-500 dark:text-red-400 font-medium">{{$service->name}}</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                                                    <form action="{{route('service-types.service-classes.destroy', ['service_type' => $serviceType, 'service_class' => $service])}}" method="POST">
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
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-3 py-4 text-sm text-center text-gray-500 dark:text-gray-400">
                                        No hay service classes en la base de datos
                                        @can('create', \App\Models\TransportationAgency::class)
                                            <span>, define uno nuevo haciendo <a href="{{route('service-types.service-classes.create', ['service_type' => $serviceType->id])}}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900">click aquí</a></span>
                                        @endcan
                                        .
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layout-admin>
