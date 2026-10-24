@section('title', 'Detalles de la dirección')
@php
    $actionBtn = 'inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:cursor-pointer';
@endphp
<x-layout-admin>
    <section>
        <x-navigation.breadcrumbs :links="['Libreta de direcciones' => route('addresses.index')]" />
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
                    <i class="fa-regular fa-location-dot"></i>
                </div>
                <div class="min-w-0">
                    <h1 class="text-xl font-semibold tracking-tight text-gray-900 dark:text-gray-50 break-words">{{ $address->name }}</h1>
                    <p class="mt-0.5 flex flex-wrap items-center gap-x-2.5 gap-y-1 text-xs text-gray-500 dark:text-gray-400">
                        @if($address->trade_name)
                            <span>{{ $address->trade_name }}</span>
                        @endif
                        <span>Alta {{ $address->created_at->isoFormat('D MMM YYYY') }}</span>
                    </p>
                </div>
                <div class="flex flex-wrap gap-2 sm:ml-auto">
                    @can('update', $address)
                        <button type="button" @click="window.dispatchEvent(new CustomEvent('open-edit-address-modal'))" class="{{ $actionBtn }}">
                            <i class="fa-regular fa-pen-to-square"></i>
                            Editar dirección
                        </button>
                    @endcan
                </div>
            </header>

            {{-- Franja de datos --}}
            <dl class="grid grid-cols-2 gap-x-7 gap-y-5 border-y border-gray-200 dark:border-lits-blue-450 py-5 md:grid-cols-3">
                <div class="min-w-0">
                    <dt class="mb-1 text-xs font-semibold text-gray-400 dark:text-gray-500">Persona de contacto</dt>
                    <dd class="text-sm text-gray-900 dark:text-gray-50 break-words">{{ $address->contact_name }}</dd>
                </div>
                <div class="min-w-0">
                    <dt class="mb-1 text-xs font-semibold text-gray-400 dark:text-gray-500">Email</dt>
                    <dd class="text-sm text-gray-900 dark:text-gray-50 break-words">{{ $address->email ?: '—' }}</dd>
                </div>
                <div class="min-w-0">
                    <dt class="mb-1 text-xs font-semibold text-gray-400 dark:text-gray-500">Teléfono</dt>
                    <dd class="text-sm text-gray-900 dark:text-gray-50 break-words">{{ $address->phone ?: '—' }}</dd>
                </div>
                <div class="col-span-2 md:col-span-3 border-t border-gray-200 dark:border-lits-blue-450"></div>
                <div class="min-w-0 col-span-2 md:col-span-3">
                    <dt class="mb-1 text-xs font-semibold text-gray-400 dark:text-gray-500">Dirección</dt>
                    <dd class="text-sm text-gray-900 dark:text-gray-50 break-words">
                        {{ $address->address }}@if($address->neighborhood), {{ $address->neighborhood }}@endif @if($address->postal_code), C.P. {{ $address->postal_code }}@endif
                        <br>
                        {{ $address->city->name }}, {{ $address->state->name }}, {{ $address->country->name }}
                    </dd>
                </div>
                <div class="min-w-0 col-span-2 md:col-span-3">
                    <dt class="mb-1 text-xs font-semibold text-gray-400 dark:text-gray-500">Ubicación en maps</dt>
                    <dd class="text-sm text-gray-900 dark:text-gray-50 break-words">{{ $address->location_name }}</dd>
                </div>
                @if($address->link)
                    <div class="min-w-0 col-span-2 md:col-span-3">
                        <dt class="mb-1 text-xs font-semibold text-gray-400 dark:text-gray-500">Link de compartir ubicación en maps</dt>
                        <dd class="text-sm break-all">
                            <a href="{{ $address->link }}" target="_blank" rel="noopener" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">{{ $address->link }}</a>
                        </dd>
                    </div>
                @endif
            </dl>
        </div>
    </section>
    @can('update', $address)
        <x-modals.address-form mode="edit" :address="$address" :countries="$countries" />
    @endcan
</x-layout-admin>
