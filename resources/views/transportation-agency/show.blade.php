@section('title', 'Detalles del transportista')
@php
    $actionBtn = 'inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:cursor-pointer';
    $dataFields = [
        'Nombre comercial' => $agency->company_name,
        'Contacto' => $agency->contact_name,
        'Teléfono' => $agency->phone1,
        'Otro teléfono' => $agency->phone2,
        'RFC' => $agency->rfc,
        'CAAT' => $agency->caat_code,
        'SCAC' => $agency->scac_code,
    ];
    $tabClass = 'inline-flex items-baseline gap-1.5 border-b-2 pb-2.5 text-sm hover:cursor-pointer';
@endphp
<x-layout-admin>
    <section>
        <x-navigation.breadcrumbs :links="['Transportistas' => route('transportation-agencies.index')]" />
        @if ($errors->any() && ! old('_drawer'))
            <x-alerts.error :message="'Ocurrieron los siguientes errores:'" :errors="$errors" class="my-4" />
        @endif
        @if(session()->has('success'))
            <x-alerts.success class="mt-4" :message="session('success')" />
        @endif

        <div class="mt-4 space-y-8">
            {{-- Cabecera compacta --}}
            <header class="flex flex-wrap items-center gap-x-4 gap-y-3">
                <div class="flex size-12 flex-none items-center justify-center rounded-xl bg-gray-100 dark:bg-lits-blue-550 text-lg text-gray-500 dark:text-gray-400" aria-hidden="true">
                    <i class="fa-regular fa-truck"></i>
                </div>
                <div class="min-w-0">
                    <h1 class="text-xl font-semibold tracking-tight text-gray-900 dark:text-gray-50 break-words">{{ $agency->name }}</h1>
                    <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">Transportista desde {{ $agency->created_at->isoFormat('D MMM YYYY') }}</p>
                </div>
                <div class="flex flex-wrap gap-2 sm:ml-auto">
                    @can('update', $agency)
                        <button type="button" @click="window.dispatchEvent(new CustomEvent('open-edit-transportation-agency-modal'))" class="{{ $actionBtn }}">
                            <i class="fa-regular fa-pen-to-square"></i>
                            Editar transportista
                        </button>
                    @endcan
                </div>
            </header>

            {{-- Franja de datos --}}
            <dl class="grid grid-cols-2 gap-x-7 gap-y-5 border-y border-gray-200 dark:border-lits-blue-450 py-5 md:grid-cols-4 lg:grid-cols-7">
                @foreach($dataFields as $label => $value)
                    <div class="min-w-0">
                        <dt class="mb-1 text-xs font-semibold text-gray-400 dark:text-gray-500">{{ $label }}</dt>
                        <dd class="text-sm break-words {{ filled($value) ? 'text-gray-900 dark:text-gray-50' : 'text-gray-400 dark:text-gray-500' }}">{{ filled($value) ? $value : '—' }}</dd>
                    </div>
                @endforeach
            </dl>

            {{-- Pestañas: la activa se recuerda en la URL (?activeTab=) para que un redirect o recargar no regrese a la primera --}}
            <div
                x-data="{ activeTab: Number(new URLSearchParams(window.location.search).get('activeTab')) === 1 ? 1 : 0 }"
                x-init="$watch('activeTab', value => { const url = new URL(window.location); url.searchParams.set('activeTab', value); history.replaceState(null, '', url) })"
            >
                <div class="flex items-center gap-6 border-b border-gray-200 dark:border-lits-blue-450" role="tablist">
                    <button type="button" role="tab" @click="activeTab = 0" :aria-selected="activeTab === 0" :class="activeTab === 0 ? 'border-lits-red-500 font-semibold text-gray-900 dark:text-gray-50' : 'border-transparent font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'" class="{{ $tabClass }}">
                        Unidades
                        <span class="text-xs font-medium text-gray-400 dark:text-gray-500">{{ $agency->vehicles->count() }}</span>
                    </button>
                    <button type="button" role="tab" @click="activeTab = 1" :aria-selected="activeTab === 1" :class="activeTab === 1 ? 'border-lits-red-500 font-semibold text-gray-900 dark:text-gray-50' : 'border-transparent font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'" class="{{ $tabClass }}">
                        Expediente
                        <span class="text-xs font-medium text-gray-400 dark:text-gray-500">{{ $agency->documents->count() }}</span>
                    </button>
                </div>

                {{-- Unidades --}}
                <div x-cloak x-show="activeTab === 0" role="tabpanel">
                    <div class="flex flex-wrap items-center justify-between gap-3 pt-4 pb-2">
                        <h2 class="text-xs font-semibold text-gray-400 dark:text-gray-500">Unidades de transporte</h2>
                        @can('update', $agency)
                            <button type="button" @click="window.dispatchEvent(new CustomEvent('open-add-vehicle-modal'))" class="{{ $actionBtn }} py-1.5">
                                <i class="fa-regular fa-plus"></i>
                                Agregar unidad
                            </button>
                        @endcan
                    </div>
                    <div class="overflow-x-auto">
                        <div class="inline-block min-w-full align-middle">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="border-b border-gray-200 dark:border-lits-blue-450">
                                        <th scope="col" class="py-2.5 pr-3 text-left text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">Número económico</th>
                                        <th scope="col" class="px-3 py-2.5 text-left text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">Placas</th>
                                        <th scope="col" class="px-3 py-2.5 text-left text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">Tipo</th>
                                        <th scope="col" class="px-3 py-2.5 text-left text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">N. serie</th>
                                        <th scope="col" class="px-3 py-2.5 text-left text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">Origen</th>
                                        <th scope="col" class="py-2.5 pr-3 pl-3 text-center text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @forelse($agency->vehicles as $vehicle)
                                    <tr class="border-b border-gray-100 dark:border-lits-blue-450/60 hover:bg-gray-50 dark:hover:bg-lits-blue-550/60">
                                        <td class="py-3.5 pr-3 text-sm font-medium text-gray-900 dark:text-gray-50">{{ $vehicle->eco_number }}</td>
                                        <td class="px-3 py-3.5 text-sm text-gray-500 dark:text-gray-400">{{ $vehicle->plates }}</td>
                                        <td class="px-3 py-3.5 text-sm text-gray-500 dark:text-gray-400">{{ $vehicle->vehicle_type }}</td>
                                        <td class="px-3 py-3.5 text-sm text-gray-500 dark:text-gray-400">{{ $vehicle->serial_number }}</td>
                                        <td class="px-3 py-3.5 text-sm text-gray-500 dark:text-gray-400">{{ $vehicle->origin }}</td>
                                        <td class="py-3.5 pr-3 pl-3 text-sm font-medium whitespace-nowrap">
                                            <div class="flex items-center justify-center gap-1">
                                                @can('update', $agency)
                                                    <button
                                                        type="button"
                                                        @click="window.dispatchEvent(new CustomEvent('request-edit-vehicle', { detail: @js([
                                                            'id' => $vehicle->id,
                                                            'update_url' => route('transportation-agencies.vehicles.update', [ 'transportation_agency' => $agency->id, 'vehicle' => $vehicle->id ]),
                                                            'eco_number' => $vehicle->eco_number,
                                                            'plates' => $vehicle->plates,
                                                            'vehicle_type' => $vehicle->vehicle_type,
                                                            'serial_number' => $vehicle->serial_number,
                                                            'vehicle_brand' => $vehicle->vehicle_brand,
                                                            'vehicle_model' => $vehicle->vehicle_model,
                                                            'origin' => $vehicle->origin,
                                                        ]) }))"
                                                        data-tippy-content="Editar"
                                                        class="size-7 shrink-0 rounded-md flex items-center justify-center text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-200 hover:cursor-pointer"
                                                    >
                                                        <i class="fa-regular fa-pen-to-square"></i>
                                                    </button>
                                                @endcan
                                                @can('delete', $agency)
                                                    <div
                                                        class="relative inline-block text-left"
                                                        x-data="{ openCancel: false }"
                                                        @keydown.escape.prevent.stop="close($refs.buttonDropdown)"
                                                        @focusin.window="! $refs.panel.contains($event.target) && close()"
                                                        x-id="['dropdown-button-{{ $agency->id }}']"
                                                        @confirm.window="{{ $agency->id }} == $event.detail && $refs['delete-row-' + $event.detail].submit()"
                                                    >
                                                        <button
                                                            type="button"
                                                            @click="openCancel = true"
                                                            data-tippy-content="Desactivar"
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
                                                                                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-50" id="modal-title">Eliminar unidad de transporte</h3>
                                                                                <div class="mt-2">
                                                                                    <p class="text-sm text-gray-500 dark:text-gray-400 font-normal">¿Estás seguro de eliminar la siguiente unidad?</p>
                                                                                    <p class="text-sm text-red-500 dark:text-red-400 font-medium">{{ $vehicle->eco_number }}</p>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                                                            <form action="{{ route('transportation-agencies.vehicles.destroy', [ 'transportation_agency' => $agency->id, 'vehicle' => $vehicle->id ]) }}" method="POST">
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
                                        <td colspan="6" class="px-3 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                            No hay unidades del transportista en la base de datos
                                            @can('update', $agency)
                                                <span>, define una nueva haciendo <button type="button" @click="window.dispatchEvent(new CustomEvent('open-add-vehicle-modal'))" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 hover:cursor-pointer">click aquí</button></span>
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

                {{-- Expediente --}}
                <div x-cloak x-show="activeTab === 1" role="tabpanel">
                    <div class="flex flex-wrap items-center justify-between gap-3 pt-4 pb-2">
                        <h2 class="text-xs font-semibold text-gray-400 dark:text-gray-500">Expediente del transportista</h2>
                        @can('update', $agency)
                            <a href="{{ route('transportation-agencies.documents.create', [ 'transportation_agency' => $agency->id ]) }}" class="{{ $actionBtn }} py-1.5">
                                <i class="fa-regular fa-paperclip"></i>
                                Adjuntar
                            </a>
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
    @can('update', $agency)
        <x-modals.transportation-agency-form mode="edit" :agency="$agency" />
        <x-modals.vehicle-form mode="create" :agency="$agency" />
        @if($agency->vehicles->isNotEmpty())
            <x-modals.vehicle-form mode="edit" :agency="$agency" />
        @endif
    @endcan
</x-layout-admin>
