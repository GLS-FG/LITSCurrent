@section('title', 'Detalles del almacen')
@php
    $actionBtn = 'inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:cursor-pointer';
@endphp
<x-layout-admin>
    <section>
        <x-navigation.breadcrumbs :links="['Almacenes' => route('warehouses.index')]" />
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
                    <i class="fa-regular fa-warehouse"></i>
                </div>
                <div class="min-w-0">
                    <h1 class="text-xl font-semibold tracking-tight text-gray-900 dark:text-gray-50 break-words">{{ $warehouse->name }}</h1>
                    <p class="mt-0.5 flex flex-wrap items-center gap-x-2.5 gap-y-1 text-xs text-gray-500 dark:text-gray-400">
                        <span>Alta {{ $warehouse->created_at->isoFormat('D MMM YYYY') }}</span>
                    </p>
                </div>
                <div class="flex flex-wrap gap-2 sm:ml-auto">
                    @can('update', $warehouse)
                        <button type="button" @click="window.dispatchEvent(new CustomEvent('open-edit-warehouse-modal'))" class="{{ $actionBtn }}">
                            <i class="fa-regular fa-pen-to-square"></i>
                            Editar almacén
                        </button>
                    @endcan
                </div>
            </header>

            {{-- Franja de datos --}}
            <dl class="grid grid-cols-2 gap-x-7 gap-y-5 border-y border-gray-200 dark:border-lits-blue-450 py-5 md:grid-cols-3">
                <div class="min-w-0 col-span-2 md:col-span-1">
                    <dt class="mb-1 text-xs font-semibold text-gray-400 dark:text-gray-500">Persona de contacto</dt>
                    <dd class="text-sm text-gray-900 dark:text-gray-50 break-words">{{ $warehouse->contact_name }}</dd>
                </div>
                <div class="min-w-0">
                    <dt class="mb-1 text-xs font-semibold text-gray-400 dark:text-gray-500">Teléfono</dt>
                    <dd class="text-sm text-gray-900 dark:text-gray-50 break-words">{{ $warehouse->phone1 }}</dd>
                </div>
                <div class="min-w-0">
                    <dt class="mb-1 text-xs font-semibold text-gray-400 dark:text-gray-500">Otro teléfono</dt>
                    <dd class="text-sm text-gray-900 dark:text-gray-50 break-words">{{ $warehouse->phone2 ?: '—' }}</dd>
                </div>
            </dl>
        </div>
    </section>
    @can('update', $warehouse)
        <x-modals.warehouse-form mode="edit" :warehouse="$warehouse" />
    @endcan
</x-layout-admin>
