@php
    $isOpen = $reportKey === 'open';
    $title = $isOpen ? 'Pendientes abiertos' : 'Actividad por cliente';
    $lede = $isOpen
        ? 'Lo que sigue abierto hoy y ya se pasó de tiempo: embarques con ETA vencido y órdenes que llevan demasiados días activas.'
        : 'Cuánto mueve cada cliente en el periodo y cuántos días tarda en cerrarse cada orden.';
    $filtersActive = request()->filled('client_id') || request()->filled('service_type_id') || request()->filled('service_class_id') || $report->hasColumnFilters();
    $tableParams = ['q', 'min_days', 'status', 'min_orders', 'only_active', 'min_close_days', 'sort', 'dir'];
    $controlClass = 'w-full rounded-md bg-white dark:bg-lits-blue-550 py-2 pr-8 pl-3 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-lits-red-500';
    $exportParams = array_merge(request()->query(), ['report' => $reportKey]);
    $thClass = 'px-3 py-2.5 text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500';
    $trClass = 'border-b border-gray-100 dark:border-lits-blue-450/60 hover:bg-gray-50 dark:hover:bg-lits-blue-550/60';
@endphp
@section('title', 'Reportes')
<x-layout-app>
    <section>
        <x-navigation.breadcrumbs :links="['Reportes' => route('reports.index'), $title => '#']" />
        <x-headings.without-action :title="'Reportes'" />

        {{-- Selector de reporte --}}
        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3" role="tablist" aria-label="Elige un reporte">
            <a href="{{ route('reports.index', ['report' => 'open']) }}" role="tab" aria-selected="{{ $isOpen ? 'true' : 'false' }}"
               class="relative flex items-center gap-3 rounded-xl border bg-white dark:bg-lits-blue-550 px-4 py-3.5 hover:bg-gray-50 dark:hover:bg-lits-blue-550/70 {{ $isOpen ? 'border-lits-red-500 ring-1 ring-lits-red-500' : 'border-gray-200 dark:border-lits-blue-450' }}">
                <span class="size-9 shrink-0 flex items-center justify-center rounded-lg bg-gray-100 dark:bg-lits-blue-600 text-gray-600 dark:text-gray-300"><i class="fa-regular fa-clock"></i></span>
                <span class="min-w-0 flex-1">
                    <span class="block text-[15px] font-semibold text-gray-900 dark:text-gray-50">Pendientes abiertos</span>
                    <span class="block text-xs text-gray-400 dark:text-gray-500">Embarques y órdenes que ya se pasaron de tiempo</span>
                </span>
                <span class="size-[18px] shrink-0 rounded-full flex items-center justify-center {{ $isOpen ? 'bg-lits-red-500 text-white' : 'border border-gray-300 dark:border-lits-blue-450' }}">
                    @if($isOpen)<i class="fa-solid fa-check text-[10px]"></i>@endif
                </span>
            </a>
            <a href="{{ route('reports.index', ['report' => 'activity']) }}" role="tab" aria-selected="{{ $isOpen ? 'false' : 'true' }}"
               class="relative flex items-center gap-3 rounded-xl border bg-white dark:bg-lits-blue-550 px-4 py-3.5 hover:bg-gray-50 dark:hover:bg-lits-blue-550/70 {{ $isOpen ? 'border-gray-200 dark:border-lits-blue-450' : 'border-lits-red-500 ring-1 ring-lits-red-500' }}">
                <span class="size-9 shrink-0 flex items-center justify-center rounded-lg bg-entity-orders-50 dark:bg-entity-orders/15 text-entity-orders dark:text-indigo-400"><i class="fa-regular fa-clipboard-list-check"></i></span>
                <span class="min-w-0 flex-1">
                    <span class="block text-[15px] font-semibold text-gray-900 dark:text-gray-50">Actividad por cliente</span>
                    <span class="block text-xs text-gray-400 dark:text-gray-500">Volumen y días para cerrar, por cliente</span>
                </span>
                <span class="size-[18px] shrink-0 rounded-full flex items-center justify-center {{ $isOpen ? 'border border-gray-300 dark:border-lits-blue-450' : 'bg-lits-red-500 text-white' }}">
                    @if(! $isOpen)<i class="fa-solid fa-check text-[10px]"></i>@endif
                </span>
            </a>
        </div>

        <div class="mt-7 pt-5 border-t border-gray-200 dark:border-lits-blue-450">
            <h3 class="text-xl font-bold tracking-tight text-gray-900 dark:text-gray-50">{{ $title }}</h3>
            <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400 max-w-prose">{{ $lede }}</p>
        </div>

        {{-- Filtros --}}
        <div class="mt-4" x-data="{
            showFilters: {{ $filtersActive ? 'true' : 'false' }},
            type: '{{ $report->typeId ?? '' }}',
            cls: '{{ request('service_class_id') }}',
            classes: @js($serviceClasses),
            get visible() { return this.classes.filter(c => ! this.type || c.type == this.type) },
            typeChanged() { if (! this.visible.some(c => c.id == this.cls)) this.cls = '' },
        }">
            <form id="report-form" method="GET" action="{{ route('reports.index') }}" class="flex flex-col gap-2">
                <input type="hidden" name="report" value="{{ $reportKey }}">
                @if(request()->filled('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                    <input type="hidden" name="dir" value="{{ request('dir') }}">
                @endif
                @if($isOpen)
                    <input type="hidden" name="view" value="{{ $report->view }}">
                @endif

                <div class="flex flex-col md:flex-row gap-2">
                    @if($isOpen)
                        <div class="flex-1 flex items-center gap-2.5 rounded-lg bg-white dark:bg-lits-blue-550 border border-gray-200 dark:border-lits-blue-450 px-3.5 py-2.5">
                            <label class="text-xs font-semibold text-gray-400 dark:text-gray-500 whitespace-nowrap">Situación al</label>
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-50">{{ $report->today->format('d/m/Y') }}</span>
                            <span class="text-xs text-gray-400 dark:text-gray-500">Muestra lo que sigue abierto hoy</span>
                        </div>
                    @else
                        <div class="flex-1 flex items-center gap-2.5 rounded-lg bg-white dark:bg-lits-blue-550 border border-gray-200 dark:border-lits-blue-450 px-3.5 py-2.5">
                            <label for="from" class="text-xs font-semibold text-gray-400 dark:text-gray-500">Del</label>
                            <input id="from" type="date" name="from" value="{{ $report->from->format('Y-m-d') }}" required class="flex-1 min-w-0 border-0 bg-transparent p-0 text-sm text-gray-900 dark:text-gray-50 outline-none dark:scheme-dark">
                            <span class="text-gray-400 dark:text-gray-500">–</span>
                            <label for="to" class="text-xs font-semibold text-gray-400 dark:text-gray-500">al</label>
                            <input id="to" type="date" name="to" value="{{ $report->to->format('Y-m-d') }}" required class="flex-1 min-w-0 border-0 bg-transparent p-0 text-sm text-gray-900 dark:text-gray-50 outline-none dark:scheme-dark">
                        </div>
                    @endif
                    <div class="flex items-center gap-2">
                        <button type="button" @click="showFilters = !showFilters" aria-label="Filtros" class="inline-flex items-center justify-center rounded-lg border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 px-3.5 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:cursor-pointer">
                            <i class="fa-regular fa-sliders"></i>
                        </button>
                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-lits-red-500 px-3.5 py-2.5 text-sm font-semibold text-white hover:bg-lits-red-600 hover:cursor-pointer">
                            Generar
                        </button>
                    </div>
                </div>

                <div x-cloak x-show="showFilters" x-transition class="flex flex-col gap-2 rounded-lg bg-gray-50/60 dark:bg-lits-blue-550/60 border border-gray-100 dark:border-lits-blue-450 p-2.5">
                  <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <select name="client_id" aria-label="Cliente" class="w-full rounded-md bg-white dark:bg-lits-blue-550 py-2 pr-8 pl-3 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-lits-red-500">
                        <option value="">Todos los clientes</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" @selected((int) request('client_id') === $client->id)>{{ $client->company_name }} / {{ $client->trade_name }}</option>
                        @endforeach
                    </select>
                    <select name="service_type_id" x-model="type" @change="typeChanged()" aria-label="Servicio" class="w-full rounded-md bg-white dark:bg-lits-blue-550 py-2 pr-8 pl-3 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-lits-red-500">
                        <option value="">Todos los servicios</option>
                        @foreach($serviceTypes as $type)
                            <option value="{{ $type['id'] }}">{{ $type['name'] }}</option>
                        @endforeach
                    </select>
                    <select name="service_class_id" x-model="cls" aria-label="Clase de servicio" class="w-full rounded-md bg-white dark:bg-lits-blue-550 py-2 pr-8 pl-3 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-lits-red-500">
                        <option value="">Todas las clases</option>
                        <template x-for="c in visible" :key="c.id">
                            <option :value="c.id" x-text="c.name" :selected="c.id == cls"></option>
                        </template>
                    </select>
                  </div>

                  {{-- Filtros de la tabla: tambien aplican al Excel --}}
                  <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 border-t border-gray-100 dark:border-lits-blue-450 pt-2">
                    @if($isOpen)
                        <select name="min_days" aria-label="{{ $report->isShip() ? 'Días vencido' : 'Días abierto' }}" class="{{ $controlClass }}">
                            <option value="">{{ $report->isShip() ? 'Días vencido: cualquiera' : 'Días abierto: cualquiera' }}</option>
                            @foreach([7 => 'Más de 7 días', 30 => 'Más de 30 días', 90 => 'Más de 90 días'] as $days => $daysLabel)
                                <option value="{{ $days }}" @selected((string) request('min_days') === (string) $days)>{{ $daysLabel }}</option>
                            @endforeach
                        </select>
                        @if($report->statusOptions())
                            <select name="status" aria-label="Estatus" class="{{ $controlClass }}">
                                <option value="">Estatus: todos</option>
                                @foreach($report->statusOptions() as $statusValue => $statusLabel)
                                    <option value="{{ $statusValue }}" @selected((string) request('status') === (string) $statusValue)>{{ $statusLabel }}</option>
                                @endforeach
                            </select>
                        @endif
                    @else
                        <input type="number" min="0" name="min_orders" value="{{ request('min_orders') }}" placeholder="Mínimo de órdenes" aria-label="Mínimo de órdenes" class="{{ $controlClass }}">
                        <select name="only_active" aria-label="Órdenes activas" class="{{ $controlClass }}">
                            <option value="">Todos los clientes</option>
                            <option value="1" @selected(request('only_active') === '1')>Solo con órdenes activas</option>
                        </select>
                        <input type="number" min="0" step="0.1" name="min_close_days" value="{{ request('min_close_days') }}" placeholder="Días para cerrar mayor a" aria-label="Días para cerrar mayor a" class="{{ $controlClass }}">
                    @endif
                  </div>
                </div>
            </form>
        </div>

        @if($errors->any())
            <x-alerts.error class="mt-4" :message="$errors->first()" />
        @endif

        @if($isOpen)
            @php
                $rows = $report->rows();
                $distribution = $report->distribution();
                $distTotal = collect($distribution)->sum(1);
            @endphp

            {{-- Subvista --}}
            <div class="mt-5 flex flex-wrap items-center gap-1.5" role="group" aria-label="Qué revisar">
                <span class="text-sm text-gray-400 dark:text-gray-500 mr-1">Mostrar:</span>
                @foreach($openViews as $key => $label)
                    <a href="{{ route('reports.index', array_merge(request()->except(['sort', 'dir', 'status', 'min_days']), ['report' => 'open', 'view' => $key])) }}"
                       aria-pressed="{{ $report->view === $key ? 'true' : 'false' }}"
                       class="inline-flex items-center gap-2 rounded-full border px-3.5 py-1.5 text-sm {{ $report->view === $key ? 'bg-gray-900 dark:bg-gray-50 border-gray-900 dark:border-gray-50 text-white dark:text-gray-900 font-semibold' : 'border-gray-200 dark:border-lits-blue-450 text-gray-600 dark:text-gray-300 font-medium hover:bg-gray-50 dark:hover:bg-lits-blue-550/60' }}">
                        {{ $label }}
                        <span class="tabular-nums opacity-70">{{ $openCounts[$key] }}</span>
                    </a>
                @endforeach
            </div>

            {{-- Totales --}}
            <div class="mt-5 flex flex-wrap">
                @foreach($report->summary() as $i => [$value, $label, $highlight])
                    <div class="flex-[1_1_150px] py-1 pr-4 {{ $i ? 'sm:pl-4 sm:border-l border-gray-200 dark:border-lits-blue-450' : '' }}">
                        <div class="text-3xl font-bold tracking-tight tabular-nums text-gray-900 dark:text-gray-50">{{ number_format($value) }}</div>
                        <div class="flex items-center gap-1.5 text-xs text-gray-400 dark:text-gray-500">
                            @if($highlight)<span class="size-1.5 rounded-full bg-lits-red-500"></span>@endif
                            {{ $label }}
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Antigüedad --}}
            @if($distTotal > 0)
                <div class="mt-5">
                    <h4 class="mb-2 text-[13px] font-semibold text-gray-600 dark:text-gray-300">{{ $report->isShip() ? 'Días desde el ETA' : 'Días abierta' }}</h4>
                    <div class="flex h-2.5 gap-0.5 overflow-hidden rounded-full bg-gray-200 dark:bg-lits-blue-450" role="img" aria-label="Distribución por antigüedad">
                        @foreach($distribution as [$label, $total, $color])
                            @if($total > 0)
                                <span class="block min-w-1 {{ $color }}" style="width: {{ $total / $distTotal * 100 }}%"></span>
                            @endif
                        @endforeach
                    </div>
                    <div class="mt-2 flex flex-wrap gap-x-5 gap-y-1.5 text-xs text-gray-600 dark:text-gray-300">
                        @foreach($distribution as [$label, $total, $color])
                            <span class="inline-flex items-center gap-1.5">
                                <span class="size-1.5 rounded-full {{ $color }}"></span>{{ $label }}
                                <b class="tabular-nums text-gray-900 dark:text-gray-50">{{ $total }}</b>
                            </span>
                        @endforeach
                    </div>
                </div>
                @if($report->insight())
                    <p class="mt-4 max-w-3xl rounded-r-lg border-l-2 border-gray-400 dark:border-gray-500 bg-gray-50 dark:bg-lits-blue-550/60 px-3.5 py-2.5 text-[13px] text-gray-600 dark:text-gray-300 [&_b]:text-gray-900 dark:[&_b]:text-gray-50">{!! $report->insight() !!}</p>
                @endif
            @endif
        @else
            @php
                $rows = $report->rows();
                $totals = $report->totals();
                $mix = $report->mix();
            @endphp

            {{-- Totales --}}
            <div class="mt-5 flex flex-wrap">
                @foreach([
                    [number_format($totals['orders']), 'Órdenes creadas', false],
                    [number_format($totals['clients']), 'Clientes con actividad', false],
                    [number_format($totals['services_total']), 'Servicios', false],
                    [$totals['days'] === null ? '—' : number_format($totals['days'], 1), 'Días promedio para cerrar', false],
                    [number_format($totals['active']), 'Siguen activas', true],
                ] as $i => [$value, $label, $dot])
                    <div class="flex-[1_1_150px] py-1 pr-4 {{ $i ? 'sm:pl-4 sm:border-l border-gray-200 dark:border-lits-blue-450' : '' }}">
                        <div class="text-3xl font-bold tracking-tight tabular-nums text-gray-900 dark:text-gray-50">{{ $value }}</div>
                        <div class="flex items-center gap-1.5 text-xs text-gray-400 dark:text-gray-500">
                            @if($dot)<span class="size-1.5 rounded-full bg-green-500 dark:bg-green-400"></span>@endif
                            {{ $label }}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Barra de herramientas --}}
        @php($shownCount = $rows->count())
        <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
            <span class="text-[13px] text-gray-600 dark:text-gray-300">
                @if($isOpen)
                    {{ $shownCount }}@if($report->hasTableFilters()) de {{ $report->rowsTotal() }}@endif
                    {{ ['ship' => 'embarques', 'customs' => 'aduanas', 'warehouse' => 'almacenes', 'order' => 'órdenes'][$report->view] }}
                @else
                    Del {{ $report->from->format('d/m/Y') }} al {{ $report->to->format('d/m/Y') }}
                    <span class="text-gray-400 dark:text-gray-500">· {{ $shownCount }}@if($report->hasTableFilters()) de {{ $report->rowsTotal() }}@endif clientes</span>
                @endif
            </span>
            <div class="flex flex-wrap items-center gap-2">
                <label class="flex items-center gap-2 rounded-lg bg-white dark:bg-lits-blue-550 border border-gray-200 dark:border-lits-blue-450 px-3 py-1.5">
                    <i class="fa-regular fa-magnifying-glass text-xs text-gray-400 dark:text-gray-500"></i>
                    <input form="report-form" type="search" name="q" value="{{ request('q') }}" autocomplete="off" placeholder="Buscar en la tabla" aria-label="Buscar en la tabla" class="w-44 border-0 bg-transparent p-0 text-[13px] text-gray-900 dark:text-gray-50 placeholder:text-gray-400 dark:placeholder:text-gray-500 outline-none">
                </label>
                @if($report->hasTableFilters() || request()->filled('sort'))
                    <a href="{{ route('reports.index', request()->except($tableParams)) }}" class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-[13px] font-medium text-gray-500 dark:text-gray-400 hover:text-lits-red-500">
                        <i class="fa-regular fa-xmark text-xs"></i> Limpiar
                    </a>
                @endif
                @if($rows->isNotEmpty())
                    <a href="{{ route('reports.export', $exportParams) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-[13px] font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">
                        <i class="fa-regular fa-arrow-down-to-line"></i> Excel
                    </a>
                @endif
            </div>
        </div>

        {{-- Tabla --}}
        <div class="mt-2 overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <table class="min-w-full">
                    @if($isOpen && $report->isShip())
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-lits-blue-450">
                                @include('report.partials.th', ['key' => 'code', 'first' => 'asc', 'pad' => 'pl-4', 'align' => 'text-left', 'label' => "Embarque"])
                                @include('report.partials.th', ['key' => 'client', 'first' => 'asc', 'pad' => '', 'align' => 'text-left', 'label' => "Cliente"])
                                @include('report.partials.th', ['key' => 'route', 'first' => 'asc', 'pad' => '', 'align' => 'text-left', 'label' => "Ruta"])
                                @include('report.partials.th', ['key' => 'date', 'first' => 'asc', 'pad' => '', 'align' => 'text-left', 'label' => "ETA"])
                                @include('report.partials.th', ['key' => 'days', 'first' => 'desc', 'pad' => '', 'align' => 'text-right', 'label' => "Días vencido"])
                                @include('report.partials.th', ['key' => 'status', 'first' => 'asc', 'pad' => '', 'align' => 'text-left', 'label' => "Último estatus"])
                                <th scope="col" class="{{ $thClass }} pr-4 text-center">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rows as $row)
                                @php($url = route('orders.shipments.show', ['order' => $row->order_id, 'shipment' => $row->id]))
                                <tr class="{{ $trClass }}">
                                    <td class="py-3.5 pl-4">
                                        <a href="{{ $url }}" class="tabular-nums text-sm font-semibold text-gray-900 dark:text-gray-50 hover:text-lits-red-500">{{ $row->code }}</a>
                                        <div class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $row->order_code }}</div>
                                    </td>
                                    <td class="px-3 py-3.5">@include('report.partials.client', ['name' => $row->client, 'image' => $row->image])</td>
                                    <td class="px-3 py-3.5 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">{{ $row->route }}</td>
                                    <td class="px-3 py-3.5 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">{{ $row->date->format('d/m/Y') }}</td>
                                    <td class="px-3 py-3.5 text-right tabular-nums text-sm font-semibold {{ $row->days > 30 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-gray-50' }}">{{ $row->days }}</td>
                                    <td class="px-3 py-3.5 text-sm">
                                        @if($row->status)
                                            <span class="inline-flex items-center gap-2 font-medium text-green-700 dark:text-green-400"><span class="size-2 rounded-full bg-green-500 dark:bg-green-400"></span>{{ $row->status }}</span>
                                        @else
                                            <span class="inline-flex items-center gap-2 text-gray-400 dark:text-gray-500"><span class="size-2 rounded-full bg-gray-300 dark:bg-gray-600"></span>No actualizado</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 pr-4 pl-3">
                                        <div class="flex items-center justify-center">
                                            <a href="{{ $url }}" data-tippy-content="Ver" role="button" class="size-7 shrink-0 rounded-md flex items-center justify-center text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-200"><i class="fa-regular fa-eye"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-3 py-8 text-sm text-center text-gray-500 dark:text-gray-400">No hay embarques con ETA vencido con estos filtros.</td></tr>
                            @endforelse
                        </tbody>
                    @elseif($isOpen && $report->isService())
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-lits-blue-450">
                                @include('report.partials.th', ['key' => 'code', 'first' => 'asc', 'pad' => 'pl-4', 'align' => 'text-left', 'label' => $report->view === 'customs' ? 'Aduana' : 'Almacén'])
                                @include('report.partials.th', ['key' => 'client', 'first' => 'asc', 'pad' => '', 'align' => 'text-left', 'label' => "Cliente"])
                                @include('report.partials.th', ['key' => 'reference', 'first' => 'asc', 'pad' => '', 'align' => 'text-left', 'label' => "Referencia"])
                                @include('report.partials.th', ['key' => 'date', 'first' => 'asc', 'pad' => '', 'align' => 'text-left', 'label' => "Creado"])
                                @include('report.partials.th', ['key' => 'days', 'first' => 'desc', 'pad' => '', 'align' => 'text-right', 'label' => "Días abierto"])
                                @include('report.partials.th', ['key' => 'status', 'first' => 'asc', 'pad' => '', 'align' => 'text-left', 'label' => "Estatus"])
                                <th scope="col" class="{{ $thClass }} pr-4 text-center">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rows as $row)
                                <tr class="{{ $trClass }}">
                                    <td class="py-3.5 pl-4">
                                        <a href="{{ $row->url }}" class="tabular-nums text-sm font-semibold text-gray-900 dark:text-gray-50 hover:text-lits-red-500">{{ $row->code }}</a>
                                        <div class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $row->order_code }}</div>
                                    </td>
                                    <td class="px-3 py-3.5">@include('report.partials.client', ['name' => $row->client, 'image' => $row->image])</td>
                                    <td class="px-3 py-3.5 text-sm text-gray-500 dark:text-gray-400 max-w-xs"><span class="block truncate">{{ $row->reference }}</span></td>
                                    <td class="px-3 py-3.5 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">{{ $row->date->format('d/m/Y') }}</td>
                                    <td class="px-3 py-3.5 text-right tabular-nums text-sm font-semibold {{ $row->days > 30 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-gray-50' }}">{{ $row->days }}</td>
                                    <td class="px-3 py-3.5 text-sm">
                                        <span class="inline-flex items-center gap-2 font-medium {{ $row->status->textColor() }}"><span class="size-2 rounded-full {{ $row->status->dotColor() }}"></span>{{ $row->status->label() }}</span>
                                    </td>
                                    <td class="py-3.5 pr-4 pl-3">
                                        <div class="flex items-center justify-center">
                                            <a href="{{ $row->url }}" data-tippy-content="Ver" role="button" class="size-7 shrink-0 rounded-md flex items-center justify-center text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-200"><i class="fa-regular fa-eye"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-3 py-8 text-sm text-center text-gray-500 dark:text-gray-400">No hay {{ $report->view === 'customs' ? 'aduanas' : 'almacenes' }} abiertos con estos filtros.</td></tr>
                            @endforelse
                        </tbody>
                    @elseif($isOpen)
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-lits-blue-450">
                                @include('report.partials.th', ['key' => 'code', 'first' => 'asc', 'pad' => 'pl-4', 'align' => 'text-left', 'label' => "Orden"])
                                @include('report.partials.th', ['key' => 'client', 'first' => 'asc', 'pad' => '', 'align' => 'text-left', 'label' => "Cliente"])
                                @include('report.partials.th', ['key' => 'services', 'first' => 'desc', 'pad' => '', 'align' => 'text-right', 'label' => "Servicios"])
                                @include('report.partials.th', ['key' => 'date', 'first' => 'asc', 'pad' => '', 'align' => 'text-left', 'label' => "Creada"])
                                @include('report.partials.th', ['key' => 'days', 'first' => 'desc', 'pad' => '', 'align' => 'text-right', 'label' => "Días abierta"])
                                @include('report.partials.th', ['key' => 'status', 'first' => 'asc', 'pad' => '', 'align' => 'text-left', 'label' => "Estatus"])
                                <th scope="col" class="{{ $thClass }} pr-4 text-center">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rows as $row)
                                @php($url = route('orders.show', ['order' => $row->id]))
                                <tr class="{{ $trClass }}">
                                    <td class="py-3.5 pl-4"><a href="{{ $url }}" class="tabular-nums text-sm font-semibold text-gray-900 dark:text-gray-50 hover:text-lits-red-500">{{ $row->code }}</a></td>
                                    <td class="px-3 py-3.5">@include('report.partials.client', ['name' => $row->client, 'image' => $row->image])</td>
                                    <td class="px-3 py-3.5 text-right tabular-nums text-sm text-gray-500 dark:text-gray-400">{{ $row->services }}</td>
                                    <td class="px-3 py-3.5 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">{{ $row->date->format('d/m/Y') }}</td>
                                    <td class="px-3 py-3.5 text-right tabular-nums text-sm font-semibold {{ $row->days > 30 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-gray-50' }}">{{ $row->days }}</td>
                                    <td class="px-3 py-3.5 text-sm">
                                        <span class="inline-flex items-center gap-2 font-medium {{ $row->status->textColor() }}"><span class="size-2 rounded-full {{ $row->status->dotColor() }}"></span>{{ $row->status->label() }}</span>
                                    </td>
                                    <td class="py-3.5 pr-4 pl-3">
                                        <div class="flex items-center justify-center">
                                            <a href="{{ $url }}" data-tippy-content="Ver" role="button" class="size-7 shrink-0 rounded-md flex items-center justify-center text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-200"><i class="fa-regular fa-eye"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-3 py-8 text-sm text-center text-gray-500 dark:text-gray-400">No hay órdenes activas con estos filtros.</td></tr>
                            @endforelse
                        </tbody>
                    @else
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-lits-blue-450">
                                @include('report.partials.th', ['key' => 'client', 'first' => 'asc', 'pad' => 'pl-4', 'align' => 'text-left', 'label' => "Cliente"])
                                @include('report.partials.th', ['key' => 'orders', 'first' => 'desc', 'pad' => '', 'align' => 'text-right', 'label' => "Órdenes"])
                                @include('report.partials.th', ['key' => 'active', 'first' => 'desc', 'pad' => '', 'align' => 'text-right', 'label' => "Activas"])
                                @include('report.partials.th', ['key' => 'closed', 'first' => 'desc', 'pad' => '', 'align' => 'text-right', 'label' => "Cerradas"])
                                @include('report.partials.th', ['key' => 'days', 'first' => 'desc', 'pad' => '', 'align' => 'text-right', 'label' => "Días para cerrar"])
                                @include('report.partials.th', ['key' => 'ship', 'first' => 'desc', 'pad' => '', 'align' => 'text-right', 'label' => "Embarques"])
                                @include('report.partials.th', ['key' => 'customs', 'first' => 'desc', 'pad' => '', 'align' => 'text-right', 'label' => "Aduanas"])
                                @include('report.partials.th', ['key' => 'warehouse', 'first' => 'desc', 'pad' => '', 'align' => 'text-right', 'label' => "Almacenes"])
                                @include('report.partials.th', ['key' => 'last_order', 'first' => 'desc', 'pad' => '', 'align' => 'text-left', 'label' => "Última orden"])
                                <th scope="col" class="{{ $thClass }} pr-4 text-center">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rows as $row)
                                <tr class="{{ $trClass }}">
                                    <td class="py-3.5 pl-4">@include('report.partials.client', ['name' => $row->client, 'image' => $row->image])</td>
                                    <td class="px-3 py-3.5 text-right tabular-nums text-sm text-gray-900 dark:text-gray-50">{{ number_format($row->orders) }}</td>
                                    <td class="px-3 py-3.5 text-right tabular-nums text-sm text-gray-500 dark:text-gray-400">{{ number_format($row->active) }}</td>
                                    <td class="px-3 py-3.5 text-right tabular-nums text-sm text-gray-500 dark:text-gray-400">{{ number_format($row->closed) }}</td>
                                    <td class="px-3 py-3.5 text-right tabular-nums text-sm text-gray-500 dark:text-gray-400">{{ $row->days === null ? '—' : number_format($row->days, 1) }}</td>
                                    @foreach(['ship', 'customs', 'warehouse'] as $kindKey)
                                        <td class="px-3 py-3.5 text-right tabular-nums text-sm {{ $row->services[$kindKey] ? 'text-gray-500 dark:text-gray-400' : 'text-gray-300 dark:text-gray-600' }}">{{ number_format($row->services[$kindKey]) }}</td>
                                    @endforeach
                                    <td class="px-3 py-3.5 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">{{ $row->last_order?->format('d/m/Y') }}</td>
                                    <td class="py-3.5 pr-4 pl-3">
                                        <div class="flex items-center justify-center">
                                            <a href="{{ route('clients.show', ['client' => $row->client_id]) }}" data-tippy-content="Ver" role="button" class="size-7 shrink-0 rounded-md flex items-center justify-center text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-200"><i class="fa-regular fa-eye"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="10" class="px-3 py-8 text-sm text-center text-gray-500 dark:text-gray-400">No hay órdenes en este periodo con estos filtros.</td></tr>
                            @endforelse
                        </tbody>
                        @if($rows->isNotEmpty())
                            <tfoot>
                                <tr class="border-t border-gray-200 dark:border-lits-blue-450 font-semibold">
                                    <td class="py-3.5 pl-4 text-sm text-gray-900 dark:text-gray-50">Total ({{ $totals['clients'] }} clientes)</td>
                                    <td class="px-3 py-3.5 text-right tabular-nums text-sm text-gray-900 dark:text-gray-50">{{ number_format($totals['orders']) }}</td>
                                    <td class="px-3 py-3.5 text-right tabular-nums text-sm text-gray-900 dark:text-gray-50">{{ number_format($totals['active']) }}</td>
                                    <td class="px-3 py-3.5 text-right tabular-nums text-sm text-gray-900 dark:text-gray-50">{{ number_format($totals['closed']) }}</td>
                                    <td class="px-3 py-3.5 text-right tabular-nums text-sm text-gray-900 dark:text-gray-50">{{ $totals['days'] === null ? '—' : number_format($totals['days'], 1) }}</td>
                                    @foreach(['ship', 'customs', 'warehouse'] as $kindKey)
                                        <td class="px-3 py-3.5 text-right tabular-nums text-sm text-gray-900 dark:text-gray-50">{{ number_format($totals['services'][$kindKey]) }}</td>
                                    @endforeach
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        @endif
                    @endif
                </table>
            </div>
        </div>

        {{-- Servicios por tipo, con sus clases (solo Actividad por cliente) --}}
        @if(! $isOpen && $mix->isNotEmpty())
            @php($mixMax = max(1, $mix->max('total')))
            <div class="mt-8 max-w-xl">
                <h4 class="mb-3 text-[13px] font-semibold text-gray-600 dark:text-gray-300">Servicios por tipo</h4>
                @foreach($mix as $kind)
                    <div class="{{ $loop->first ? '' : 'mt-4' }}">
                        <div class="grid grid-cols-[9.5rem_1fr_3.25rem] items-center gap-3 py-1 text-[13px]">
                            <span class="truncate font-semibold text-gray-900 dark:text-gray-50">{{ $kind->label }}</span>
                            <span class="h-2 rounded-full bg-gray-200 dark:bg-lits-blue-450 overflow-hidden"><span class="block h-full rounded-full bg-entity-shipments" style="width: {{ $kind->total / $mixMax * 100 }}%"></span></span>
                            <span class="text-right font-semibold tabular-nums text-gray-900 dark:text-gray-50">{{ number_format($kind->total) }}</span>
                        </div>
                        @foreach($kind->classes as $class)
                            <div class="grid grid-cols-[9.5rem_1fr_3.25rem] items-center gap-3 py-0.5 text-xs text-gray-500 dark:text-gray-400">
                                <span class="truncate pl-3">{{ $class->label }}</span>
                                <span></span>
                                <span class="text-right tabular-nums">{{ number_format($class->total) }}</span>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</x-layout-app>
