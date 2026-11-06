@section('title', 'Detalles del estatus del servicio')
@php
    $actionBtn = 'inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:cursor-pointer';
@endphp
<x-layout-admin>
    <section>
        <x-navigation.breadcrumbs :links="['Estatus Servicios' => route('service-statuses.index'), $serviceType->code => route('service-statuses.service-type-statuses.index', ['service_status' => $serviceType])]" />
        @if ($errors->any())
            <x-alerts.error :message="'Ocurrieron los siguientes errores:'" :errors="$errors" class="my-4" />
        @endif
        @if(session()->has('success'))
            <x-alerts.success class="mt-4" :message="session('success')" />
        @endif

        <div class="mt-4 space-y-8">
            {{-- Cabecera compacta --}}
            <header class="flex flex-wrap items-center gap-x-4 gap-y-3">
                <div class="flex size-12 flex-none items-center justify-center rounded-full border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 text-lg text-gray-500 dark:text-gray-400" aria-hidden="true">
                    <i class="fa-solid fa-list-check"></i>
                </div>
                <div class="min-w-0">
                    <h1 class="text-xl font-semibold tracking-tight text-gray-900 dark:text-gray-50 break-words">{{ $serviceTypeStatus->name }}</h1>
                    <p class="mt-0.5 flex flex-wrap items-center gap-x-2.5 gap-y-1 text-xs text-gray-500 dark:text-gray-400">
                        <span>Estatus de servicio {{ $serviceType->code }}</span>
                    </p>
                </div>
                <div class="flex flex-wrap gap-2 sm:ml-auto">
                    @can('update', $serviceType)
                        <a href="{{ route('service-statuses.service-type-statuses.edit', ['service_status' => $serviceType, 'service_type_status' => $serviceTypeStatus]) }}" class="{{ $actionBtn }}">
                            <i class="fa-regular fa-pen-to-square"></i>
                            Editar estatus
                        </a>
                    @endcan
                </div>
            </header>

            {{-- Franja de datos --}}
            <dl class="grid grid-cols-1 gap-x-7 gap-y-5 border-y border-gray-200 dark:border-lits-blue-450 py-5 sm:grid-cols-2">
                <div class="min-w-0">
                    <dt class="mb-1 text-xs font-semibold text-gray-400 dark:text-gray-500">Nombre</dt>
                    <dd class="text-sm text-gray-900 dark:text-gray-50 break-words">{{ $serviceTypeStatus->name }}</dd>
                </div>
                <div class="min-w-0">
                    <dt class="mb-1 text-xs font-semibold text-gray-400 dark:text-gray-500">Color</dt>
                    <dd class="text-sm">
                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium inset-ring {{ $serviceTypeStatus->color }}">{{ $serviceTypeStatus->name }}</span>
                    </dd>
                </div>
            </dl>

            {{-- Modos de servicio: clase x modo --}}
            @php
                // Columnas = códigos de modo distintos entre todas las clases (el nombre sale del primer modo con ese código)
                $modeColumns = $serviceType->serviceClasses
                    ->flatMap(fn ($class) => $class->serviceModes)
                    ->unique('code')
                    ->values();
            @endphp
            <div>
                <h2 class="mb-3 text-sm font-semibold text-gray-900 dark:text-gray-50">Service Modes</h2>
                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550">
                    <table class="min-w-full">
                        <thead>
                        <tr class="border-b border-gray-200 dark:border-lits-blue-450">
                            <th scope="col" class="px-5 py-3 text-left text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">Service Class</th>
                            @foreach($modeColumns as $column)
                                <th scope="col" class="px-5 py-3 text-center text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">{{ $column->code }}</th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($serviceType->serviceClasses as $serviceClass)
                            <tr class="border-b border-gray-100 dark:border-lits-blue-450/40 last:border-b-0 hover:bg-gray-50 dark:hover:bg-white/[0.03]">
                                <th scope="row" class="px-5 py-3 text-left whitespace-nowrap">
                                    <span class="inline-flex items-center gap-3">
                                        <span class="rounded-md bg-gray-100 dark:bg-white/10 px-2 py-0.5 text-xs font-semibold text-gray-900 dark:text-gray-50">{{ $serviceClass->code }}</span>
                                    </span>
                                </th>
                                @foreach($modeColumns as $column)
                                    @php $mode = $serviceClass->serviceModes->firstWhere('code', $column->code); @endphp
                                    <td class="px-5 py-3 text-center">
                                        @if(! $mode)
                                            <span class="text-gray-300 dark:text-gray-600" aria-label="No disponible">—</span>
                                        @elseif(in_array($mode->id, $modes))
                                            <span class="inline-flex size-6 items-center justify-center rounded-full bg-green-100 dark:bg-green-500/15 text-xs text-green-600 dark:text-green-400" aria-label="Aplica"><i class="fa-solid fa-check"></i></span>
                                        @else
                                            <span class="inline-flex size-6 items-center justify-center rounded-full bg-gray-100 dark:bg-white/5 text-xs text-gray-400 dark:text-gray-500" aria-label="No aplica"><i class="fa-solid fa-xmark"></i></span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</x-layout-admin>
