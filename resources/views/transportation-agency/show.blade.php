@section('title', 'Detalles de el transportista')
<x-layout-admin>
    <section>
        <x-navigation.breadcrumbs :links="['Transportistas' => route('transportation-agencies.index'), $agency->name => '#']" />
        <x-headings.without-action
            :title="'Detalles de el transportista'"
        />
        @if ($errors->any())
            <x-alerts.error :message="'Ocurrieron los siguientes errores:'" :errors="$errors" class="my-4" />
        @endif
        @if(session()->has('success'))
            <x-alerts.success class="mt-4" :message="session('success')" />
        @endif
        <div class="mt-2 space-y-8">
            <div class="divide-y divide-gray-200 rounded bg-white shadow-lits-card">
                <div class="px-4 py-5 sm:px-6">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Transportista</h3>
                            <p class="mt-1 max-w-2xl text-sm/6 text-gray-500">Información de el transportista y su contacto.</p>
                        </div>
                        <div class="flex shrink-0 space-x-5">
                            @can('update', $agency)
                                <a
                                    href='{{route('transportation-agencies.edit', [ 'transportation_agency' => $agency->id ])}}'
                                    class="inline-flex items-center gap-1 rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer"
                                >
                                    <i class="fa-regular fa-pen-to-square mr-1.5 -ml-0.5"></i>
                                    Editar transportista
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="px-4 py-5 sm:p-6 divide-y divide-gray-200">
                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                        <dt class="text-sm/6 font-medium text-gray-900">Razon social</dt>
                        <dd class="mt-1 text-sm/6 text-gray-700 sm:col-span-2 sm:mt-0">
                            {{$agency->name}}
                        </dd>
                    </div>
                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                        <dt class="text-sm/6 font-medium text-gray-900">Nombre comercial</dt>
                        <dd class="mt-1 text-sm/6 text-gray-700 sm:col-span-2 sm:mt-0">
                            {{$agency->company_name}}
                        </dd>
                    </div>
                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                        <dt class="text-sm/6 font-medium text-gray-900">CAAT</dt>
                        <dd class="mt-1 text-sm/6 text-gray-700 sm:col-span-2 sm:mt-0">
                            {{$agency->caat_code}}
                        </dd>
                    </div>
                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                        <dt class="text-sm/6 font-medium text-gray-900">SCAC</dt>
                        <dd class="mt-1 text-sm/6 text-gray-700 sm:col-span-2 sm:mt-0">
                            {{$agency->scac_code}}
                        </dd>
                    </div>
                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                        <dt class="text-sm/6 font-medium text-gray-900">RFC</dt>
                        <dd class="mt-1 text-sm/6 text-gray-700 sm:col-span-2 sm:mt-0">
                            {{$agency->rfc}}
                        </dd>
                    </div>
                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                        <dt class="text-sm/6 font-medium text-gray-900">Persona de contacto</dt>
                        <dd class="mt-1 text-sm/6 text-gray-700 sm:col-span-2 sm:mt-0">
                            {{$agency->contact_name}}
                        </dd>
                    </div>
                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                        <dt class="text-sm/6 font-medium text-gray-900">Teléfono 1</dt>
                        <dd class="mt-1 text-sm/6 text-gray-700 sm:col-span-2 sm:mt-0">
                            {{$agency->phone1}}
                        </dd>
                    </div><div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                        <dt class="text-sm/6 font-medium text-gray-900">Teléfono 2</dt>
                        <dd class="mt-1 text-sm/6 text-gray-700 sm:col-span-2 sm:mt-0">
                            {{$agency->phone2}}
                        </dd>
                    </div>

                </div>
            </div>
            <div class="divide-y divide-gray-200 rounded bg-white shadow-lits-card">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-semibold text-gray-900">Unidades de transporte</h3>
                    <p class="mt-1 max-w-2xl text-sm/6 text-gray-500">Crea o edita nuevas unidades del transportista</p>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex space-x-2">
                        @can('update', $agency)
                        <div class="w-full flex justify-end mb-6">
                            <a
                                href='{{route('transportation-agencies.vehicles.create', [ 'transportation_agency' => $agency->id ])}}'
                                class="inline-flex items-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs inset-ring inset-ring-gray-300 hover:bg-gray-50"
                            >
                                Agregar unidad
                            </a>
                        </div>
                        @endcan
                    </div>
                    <div class="overflow-x-auto border border-gray-300 rounded">
                        <div class="inline-block min-w-full align-middle">
                            <table class="relative min-w-full divide-y divide-gray-300">
                                <thead>
                                    <tr class="divide-x divide-gray-200">
                                        <th scope="col" class="py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-gray-900 sm:pl-3">Número económico</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Placas</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Tipo</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">N.Serie</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Origen</th>
                                        <th scope="col" class="py-3.5 pr-4 pl-3 text-left text-sm font-semibold text-gray-900 sm:pr-3">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-300">
                                @forelse($agency->vehicles as $vehicle)
                                    <tr class="even:bg-gray-50 divide-x divide-gray-200">
                                        <td class="py-4 pr-3 pl-4 text-xs text-gray-900 sm:pl-3 font-medium">
                                            {{$vehicle->eco_number}}
                                        </td>
                                        <td class="px-3 py-4 text-xs text-gray-500">{{$vehicle->plates}}</td>
                                        <td class="px-3 py-4 text-xs text-gray-500">{{$vehicle->vehicle_type}}</td>
                                        <td class="px-3 py-4 text-xs text-gray-500">{{$vehicle->serial_number}}</td>
                                        <td class="px-3 py-4 text-xs text-gray-500">{{$vehicle->origin}}</td>
                                        <td class="py-4 pr-4 pl-3 text-right text-xs font-medium whitespace-nowrap sm:pr-3">
                                            <div class="flex items-center justify-center gap-x-1">
                                                @can('update', $agency)
                                                    <a
                                                        href="{{route('transportation-agencies.vehicles.edit', [ 'transportation_agency' => $agency->id, 'vehicle' => $vehicle->id ])}}"
                                                        data-tippy-content="Editar"
                                                        role="button"
                                                        class="size-7 shrink-0 rounded-md bg-green-100 flex items-center justify-center font-semibold text-green-500 hover:text-green-800 hover:bg-green-200"
                                                    >
                                                        <i class="fa-regular fa-pen-to-square"></i>
                                                    </a>
                                                @endcan
                                                @can('delete', $agency)
                                                    <div
                                                        class="relative inline-block text-left"
                                                        x-data="{ openCancel: false }"
                                                        @keydown.escape.prevent.stop="close($refs.buttonDropdown)"
                                                        @focusin.window="! $refs.panel.contains($event.target) && close()"
                                                        x-id="['dropdown-button-{{$agency->id}}']"
                                                        @confirm.window="{{$agency->id}} == $event.detail && $refs['delete-row-' + $event.detail].submit()"
                                                    >
                                                        <button
                                                            type="button"
                                                            @click="openCancel = true"
                                                            data-tippy-content="Desactivar"
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
                                                                                <i class="fa-regular fa-triangle-exclamation text-red-600 text-lg"></i>
                                                                            </div>
                                                                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left whitespace-normal">
                                                                                <h3 class="text-base font-semibold text-gray-900" id="modal-title">Eliminar unidad de transporte</h3>
                                                                                <div class="mt-2">
                                                                                    <p class="text-sm text-gray-500 font-normal">¿Estás seguro de eliminar la siguiente unidad?</p>
                                                                                    <p class="text-sm text-red-500 font-medium">{{ $vehicle->eco_number }}</p>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                                                            <form action="{{route('transportation-agencies.vehicles.destroy', [ 'transportation_agency' => $agency->id, 'vehicle' => $vehicle->id ])}}" method="POST">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <button type="submit" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500 sm:ml-3 sm:w-auto">Eliminar</button>
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
                                    <tr class="text-sm text-center text-gray-400 py-5 px-4">
                                        <td colspan="6" class="py-4 px-4 text-xs text-gray-500">
                                            No hay unidades del transportista en la base de datos
                                            @can('update', $agency)
                                                <span>, define una nueva haciendo <a href="{{route('transportation-agencies.vehicles.create', [ 'transportation_agency' => $agency->id ])}}" class="text-indigo-600 hover:text-indigo-900">click aquí</a></span>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="divide-y divide-gray-200 rounded bg-white shadow-lits-card">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-semibold text-gray-900">Expediente</h3>
                    <p class="mt-1 max-w-2xl text-sm/6 text-gray-500">Agrega archivos al expediente del transportista</p>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex space-x-2">
                        @can('update', $agency)
                            <div class="w-full flex justify-end mb-6">
                                <a
                                    href='{{route('transportation-agencies.documents.create', [ 'transportation_agency' => $agency->id ])}}'
                                    class="inline-flex items-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs inset-ring inset-ring-gray-300 hover:bg-gray-50"
                                >
                                    Adjuntar
                                </a>
                            </div>
                        @endcan
                    </div>
                    <x-cards.transportationdocs
                        :documents="$agency->documents"
                        :create="route('transportation-agencies.documents.create', [ 'transportation_agency' => $agency ])"
                        :download="route('transportation-agencies.documents.index', [ 'transportation_agency' => $agency])"
                        :destroyRoute="'transportation-agencies.documents.destroy'"
                        :destroyParams="['transportation_agency' => $agency]"
                    />
                </div>
            </div>
        </div>
    </section>
</x-layout-admin>
