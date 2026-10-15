@section('title', 'Detalles del agente aduanal')
@php
    $actionBtn = 'inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:cursor-pointer';
    $iconBtn = 'size-7 shrink-0 rounded-md flex items-center justify-center text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-200';
    $iconBtnDanger = 'size-7 shrink-0 rounded-md flex items-center justify-center text-gray-400 dark:text-gray-500 hover:bg-red-50 dark:hover:bg-red-500/15 hover:text-red-500 dark:hover:text-red-400 hover:cursor-pointer';
@endphp
<x-layout-admin>
    <section>
        <x-navigation.breadcrumbs :links="['Agentes' => route('custom-agents.index'), 'Agente aduanal' => '#']" />
        @if ($errors->any() && ! old('_drawer'))
            <x-alerts.error :message="'Ocurrieron los siguientes errores:'" :errors="$errors" class="my-4" />
        @endif
        @if(session()->has('success'))
            <x-alerts.success class="mt-4" :message="session('success')" />
        @endif

        <div class="mt-4 space-y-8">
            {{-- Cabecera compacta --}}
            <header class="flex flex-wrap items-center gap-x-4 gap-y-3">
                <div class="flex size-12 flex-none items-center justify-center rounded-full border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 text-lg text-gray-500 dark:text-gray-400" aria-hidden="true">
                    <i class="fa-regular fa-user-tie"></i>
                </div>
                <div class="min-w-0">
                    <h1 class="text-xl font-semibold tracking-tight text-gray-900 dark:text-gray-50 break-words">{{ $agent->company_name }}</h1>
                </div>
                <div class="flex flex-wrap gap-2 sm:ml-auto">
                    @can('update', $agent)
                        <button type="button" @click="window.dispatchEvent(new CustomEvent('open-edit-custom-agent-modal'))" class="{{ $actionBtn }}">
                            <i class="fa-regular fa-pen-to-square"></i>
                            Editar agente
                        </button>
                    @endcan
                </div>
            </header>

            {{-- Franja de datos --}}
            <dl class="grid grid-cols-2 gap-x-7 gap-y-5 border-y border-gray-200 dark:border-lits-blue-450 py-5 md:grid-cols-4">
                <div class="min-w-0">
                    <dt class="mb-1 text-xs font-semibold text-gray-400 dark:text-gray-500">Razón social</dt>
                    <dd class="text-sm text-gray-900 dark:text-gray-50 break-words">{{ $agent->company_name }}</dd>
                </div>
                <div class="min-w-0">
                    <dt class="mb-1 text-xs font-semibold text-gray-400 dark:text-gray-500">Nombre</dt>
                    <dd class="text-sm text-gray-900 dark:text-gray-50 break-words">{{ $agent->name }} {{ $agent->last_name }}</dd>
                </div>
                <div class="min-w-0">
                    <dt class="mb-1 text-xs font-semibold text-gray-400 dark:text-gray-500">Patente</dt>
                    <dd class="text-sm text-gray-900 dark:text-gray-50 break-words">{{ $agent->patent }}</dd>
                </div>
                <div class="min-w-0">
                    <dt class="mb-1 text-xs font-semibold text-gray-400 dark:text-gray-500">Teléfono</dt>
                    <dd class="text-sm break-words"><a href="tel:{{ $agent->phone1 }}" class="text-lits-red-500 hover:underline">{{ $agent->phone1 }}</a></dd>
                </div>
            </dl>

            {{-- Direcciones --}}
            <section>
                <div class="mb-2">
                    <h2 class="text-[15px] font-semibold text-gray-900 dark:text-gray-50">Direcciones<span class="ml-1.5 font-medium text-gray-400 dark:text-gray-500">{{ $agent->addresses->count() }}</span></h2>
                    <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">Crea o edita las direcciones del agente.</p>
                </div>
                @if($agent->addresses->isNotEmpty())
                    <div class="grid grid-cols-1 gap-x-7 md:grid-cols-2">
                        @foreach($agent->addresses as $address)
                            <article class="flex min-w-0 justify-between gap-3 border-b border-gray-100 dark:border-lits-blue-450/60 py-4">
                                <div class="min-w-0 text-[13px]/6 text-gray-500 dark:text-gray-400 break-words">
                                    <p class="mb-0.5 text-sm font-semibold text-gray-900 dark:text-gray-50">{{ $address->address_type->label() }}</p>
                                    <p>{{ $address->street_name }} {{ $address->street_no }}, {{ $address->neighborhood }}, C.P. {{ $address->postal_code }}, {{ $address->city->name }}, {{ $address->state->name }}, {{ $address->country->name }}</p>
                                </div>
                                <div class="flex items-start gap-0.5">
                                    @can('update', $agent)
                                        <button
                                            type="button"
                                            @click="window.dispatchEvent(new CustomEvent('request-edit-custom-agent-address', { detail: @js([
                                                'id' => $address->id,
                                                'update_url' => route('custom-agents.addresses.update', ['custom_agent' => $agent->id, 'address' => $address->id]),
                                                'type_label' => $address->address_type->label(),
                                                'country_id' => $address->country_id,
                                                'state_id' => $address->state_id,
                                                'city_id' => $address->city_id,
                                                'street_name' => $address->street_name,
                                                'street_no' => $address->street_no,
                                                'neighborhood' => $address->neighborhood,
                                                'postal_code' => $address->postal_code,
                                            ]) }))"
                                            data-tippy-content="Editar"
                                            class="{{ $iconBtn }}"
                                        >
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </button>
                                    @endcan
                                    @can('delete', $agent)
                                        <div
                                            class="relative inline-block text-left"
                                            x-data="{ openCancel: false }"
                                        >
                                            <button
                                                type="button"
                                                @click="openCancel = true"
                                                data-tippy-content="Desactivar"
                                                class="{{ $iconBtnDanger }}"
                                            >
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                            <div x-cloak x-show="openCancel" @keydown.escape.window="openCancel = false" class="relative z-100" aria-labelledby="modal-title-address-{{ $address->id }}" role="alertdialog" aria-modal="true">
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
                                                    @click="openCancel = false"
                                                ></div>

                                                <div class="fixed inset-0 z-100 w-screen overflow-y-auto">
                                                    <div class="flex min-h-full items-end justify-center p-4 sm:items-center sm:p-6">
                                                        <div
                                                            x-show="openCancel"
                                                            x-transition:enter="ease-out duration-300"
                                                            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                            x-transition:leave="ease-in duration-200"
                                                            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                                            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                            class="relative w-full overflow-hidden rounded-xl border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 text-left whitespace-normal sm:max-w-md"
                                                        >
                                                            <div class="flex items-center gap-3 border-b border-gray-200 dark:border-lits-blue-450 px-6 py-4">
                                                                <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-lits-red-50 dark:bg-lits-red-500/15 text-lits-red-500">
                                                                    <i class="fa-regular fa-triangle-exclamation"></i>
                                                                </div>
                                                                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-50" id="modal-title-address-{{ $address->id }}">Desactivar dirección</h3>
                                                            </div>
                                                            <div class="px-6 pt-4 pb-2 text-sm/6 font-normal text-gray-500 dark:text-gray-400">
                                                                ¿Quieres desactivar esta dirección del agente aduanal?
                                                                <b class="block font-semibold text-gray-900 dark:text-gray-50">{{ $address->street_name }} {{ $address->street_no }}, {{ $address->neighborhood }}, C.P. {{ $address->postal_code }}, {{ $address->city->name }}, {{ $address->state->name }}, {{ $address->country->name }}</b>
                                                            </div>
                                                            <div class="flex items-center justify-end gap-2 border-t border-gray-200 dark:border-lits-blue-450 px-6 py-3.5 mt-4">
                                                                <button type="button" @click="openCancel = false" class="rounded-md px-3 py-2 text-sm font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-50 hover:cursor-pointer">Cancelar</button>
                                                                <form action="{{route('custom-agents.addresses.destroy', [ 'custom_agent' => $agent->id, 'address' => $address->id ])}}" method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="rounded-md bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Desactivar</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endcan
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <p class="border-b border-gray-100 dark:border-lits-blue-450/60 py-6 text-sm text-gray-500 dark:text-gray-400">
                        No hay direcciones del agente aduanal en la base de datos.
                    </p>
                @endif
                <button type="button" @click="window.dispatchEvent(new CustomEvent('open-add-custom-agent-address-modal'))" class="mt-3.5 inline-flex items-center gap-1.5 text-sm font-semibold text-lits-red-500 hover:underline hover:cursor-pointer"><i class="fa-regular fa-plus text-xs"></i> Agregar dirección</button>
            </section>
        </div>
    </section>
    <x-modals.add-custom-agent-address :agent="$agent" :countries="$countries" />
    @can('update', $agent)
        <x-modals.custom-agent-form mode="edit" :agent="$agent" />
        <x-modals.custom-agent-address-form :agent="$agent" :countries="$countries" />
    @endcan
</x-layout-admin>
