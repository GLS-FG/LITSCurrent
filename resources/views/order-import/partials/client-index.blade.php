{{--
    Listado de Aduanas para el rol Cliente: resumen, operaciones (etapas + filtros + tabla) y análisis.
    Recibe: $dashboard (OrderImportController::clientDashboard), $imports, $stage, $stageLabels,
    $onlyUrgent, $customFilter.
--}}
@php
    // Filtros activos que se conservan al cambiar de etapa / urgentes
    $keep = fn (array $except = []) => array_filter(
        request()->except(array_merge(['page'], $except)),
        fn ($v) => $v !== null && $v !== ''
    );
    $url = fn (array $except, array $add = []) => route('imports.index', array_merge($keep($except), $add)) . '#operaciones';
    $stageUrl = fn ($key) => $url(['stage'], $stage === $key ? [] : ['stage' => $key]);

    // Etapas: rampa de violeta (color de Aduanas); rojo solo para "Retrasado"; ámbar rayado para "Sin actualizar"
    $stageStyle = [
        'docs-pend' => ['bar' => 'bg-violet-200 dark:bg-[#3F3568]', 'step' => 1],
        'docs-ok' => ['bar' => 'bg-violet-300 dark:bg-[#6A58A8]', 'step' => 2],
        'proceso' => ['bar' => 'bg-violet-500 dark:bg-[#8D78D6]', 'step' => 3],
        'concluido' => ['bar' => 'bg-violet-800 dark:bg-[#B9A8F5]', 'step' => 4],
        'retraso' => ['bar' => 'bg-lits-red-500', 'step' => 3],
        'otros' => ['bar' => 'bg-gray-300 dark:bg-gray-600', 'step' => 0],
        'stale' => ['bar' => 'bg-[repeating-linear-gradient(135deg,#F59E0B_0_3px,transparent_3px_6px)] ring-1 ring-inset ring-amber-500', 'step' => 0],
    ];
    $stepColor = [1 => 'bg-violet-200 dark:bg-[#3F3568]', 2 => 'bg-violet-300 dark:bg-[#6A58A8]', 3 => 'bg-violet-500 dark:bg-[#8D78D6]', 4 => 'bg-violet-800 dark:bg-[#B9A8F5]'];
    $stripes = 'bg-[repeating-linear-gradient(135deg,#F59E0B_0_2px,transparent_2px_4px)] ring-1 ring-inset ring-amber-500';

    $stages = $dashboard['stages'];
    $maxMonth = max(1, collect($dashboard['months'])->max('total'));
    $maxCustom = max(1, collect($dashboard['customs'])->max(fn ($c) => $c['active'] + $c['closed']) ?? 1);
    $card = 'rounded-xl border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550';
    $stageKeys = array_filter(
        array_keys($stages),
        fn ($k) => ! ($k === 'otros' && $stages[$k] === 0) && ! ($k === 'retraso' && ! $dashboard['showDelayed'])
    );
@endphp

<div
    class="mt-4 flex flex-col gap-4"
    x-data="{ keepScroll() { try { sessionStorage.setItem('stageScrollY', window.scrollY) } catch (e) {} } }"
    x-init="
        if (! window.__stageScrollBound) {
            window.__stageScrollBound = true;
            document.addEventListener('livewire:navigated', () => {
                try {
                    const y = sessionStorage.getItem('stageScrollY');
                    if (y === null) return;
                    sessionStorage.removeItem('stageScrollY');
                    requestAnimationFrame(() => window.scrollTo(0, Number(y)));
                } catch (e) {}
            });
        }
    "
>
    <div class="flex justify-end">
        <a href="#analisis" class="text-sm font-semibold text-entity-customs dark:text-violet-400 hover:underline">Ver análisis &darr;</a>
    </div>

    {{-- Operaciones --}}
    <section id="operaciones" aria-labelledby="ops-t" class="scroll-mt-4 {{ $card }}">
        <h2 id="ops-t" class="px-4 pt-3.5 text-[17px] font-semibold tracking-tight text-gray-900 dark:text-gray-50">Operaciones</h2>

        {{-- Etapas (filtran el listado) --}}
        <nav aria-label="Filtrar por etapa" class="flex items-center gap-1 overflow-x-auto px-4 pt-1.5 pb-3.5 border-b border-gray-200 dark:border-lits-blue-450">
            @php $allOn = ! $stage; @endphp
            <a wire:navigate @click="keepScroll()" href="{{ $url(['stage']) }}" @if($allOn) aria-current="true" @endif
               class="relative mt-1.5 mb-3.5 flex flex-1 shrink-0 items-center justify-center gap-1.5 whitespace-nowrap rounded-lg px-2.5 py-1.5 text-sm {{ $allOn ? 'font-semibold text-gray-900 dark:text-gray-50 bg-gray-100 dark:bg-lits-blue-600' : 'font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-50' }}">
                Todas <span class="text-xs tabular-nums text-gray-400 dark:text-gray-500">{{ $dashboard['active'] }}</span>
                <span class="absolute inset-x-2.5 -bottom-1.5 h-[5px] rounded-[3px] bg-gray-200 dark:bg-lits-blue-450"></span>
            </a>
            @foreach($stageKeys as $key)
                @php $on = $stage === $key; @endphp
                <a wire:navigate @click="keepScroll()" href="{{ $stageUrl($key) }}" title="{{ $stageLabels[$key] }}" @if($on) aria-current="true" @endif
                   class="relative mt-1.5 mb-3.5 flex flex-1 shrink-0 items-center justify-center gap-1.5 whitespace-nowrap rounded-lg px-2.5 py-1.5 text-sm {{ $on ? 'font-semibold text-gray-900 dark:text-gray-50 bg-gray-100 dark:bg-lits-blue-600' : 'font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-50' }}">
                    {{ ['docs-pend' => 'Pendientes', 'docs-ok' => 'Recopilados', 'proceso' => 'En proceso', 'concluido' => 'Concluidos', 'retraso' => 'Retrasados', 'otros' => 'Otros', 'stale' => 'Sin actualizar'][$key] }}
                    <span class="text-xs tabular-nums text-gray-400 dark:text-gray-500">{{ $stages[$key] }}</span>
                    <span class="absolute inset-x-2.5 -bottom-1.5 h-[5px] rounded-[3px] {{ $stageStyle[$key]['bar'] }}"></span>
                </a>
            @endforeach
        </nav>

        {{-- Búsqueda y filtros --}}
        <form method="GET" action="{{ route('imports.index') }}" class="flex flex-wrap gap-2 p-4">
            @if($stage)<input type="hidden" name="stage" value="{{ $stage }}">@endif
            @if($onlyUrgent)<input type="hidden" name="urgent" value="1">@endif
            <div class="flex min-w-56 flex-1 items-center gap-2 rounded-[10px] border border-gray-200 dark:border-lits-blue-450 bg-gray-50 dark:bg-lits-blue-600/60 px-3 py-2">
                <i class="fa-regular fa-magnifying-glass text-sm text-gray-400 dark:text-gray-500"></i>
                <input type="text" name="code_search" value="{{ request('code_search') }}" autocomplete="off" placeholder="Buscar por tracking, orden o referencia" aria-label="Buscar" class="flex-1 min-w-0 border-0 bg-transparent p-0 text-sm text-gray-900 dark:text-gray-50 placeholder:text-gray-400 dark:placeholder:text-gray-500 outline-none focus:ring-0">
                @if(request('code_search'))
                    <a wire:navigate href="{{ $url(['code_search']) }}" aria-label="Limpiar búsqueda" class="text-gray-400 dark:text-gray-500 hover:text-gray-600"><i class="fa-regular fa-xmark"></i></a>
                @endif
            </div>
            <select name="custom" onchange="this.form.submit()" aria-label="Filtrar por aduana" class="rounded-[10px] border border-gray-200 dark:border-lits-blue-450 bg-gray-50 dark:bg-lits-blue-600/60 px-3 py-2 text-sm text-gray-500 dark:text-gray-300">
                <option value="">Todas las aduanas</option>
                @foreach($dashboard['customOptions'] as $id => $name)
                    <option value="{{ $id }}" @selected($customFilter === $id)>{{ $name }}</option>
                @endforeach
            </select>
            <a wire:navigate @click="keepScroll()" href="{{ $onlyUrgent ? $url(['urgent']) : $url([], ['urgent' => 1]) }}" aria-pressed="{{ $onlyUrgent ? 'true' : 'false' }}"
               class="inline-flex items-center gap-2 rounded-[10px] border px-3.5 py-2 text-sm {{ $onlyUrgent ? 'border-lits-red-500 bg-lits-red-50 dark:bg-lits-red-500/15 font-semibold text-lits-red-500' : 'border-gray-200 dark:border-lits-blue-450 bg-gray-50 dark:bg-lits-blue-600/60 font-medium text-gray-500 dark:text-gray-300' }}">
                <i class="size-[7px] rounded-full bg-lits-red-500"></i>Solo urgentes
            </a>
            <button type="submit" class="sr-only">Buscar</button>
        </form>

        {{-- Tabla --}}
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] border-collapse">
                <thead>
                    <tr class="border-y border-gray-200 dark:border-lits-blue-450">
                        @foreach(['Tracking', 'Cliente', 'Aduana', 'Referencia', 'Solicitud', 'Etapa'] as $th)
                            <th scope="col" class="px-3 py-2.5 first:pl-4 text-left text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">{{ $th }}</th>
                        @endforeach
                        <th scope="col" class="px-3 py-2.5 pr-4 text-center text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($imports as $import)
                    @php
                        $key = $import->latestLocation
                            ? (\App\Http\Controllers\OrderImportController::STAGE_BY_STATUS[strtoupper($import->latestLocation->status->name)] ?? 'otros')
                            : 'stale';
                        $step = $stageStyle[$key]['step'];
                        $label = $key === 'otros' ? $import->latestLocation->status->name : $stageLabels[$key];
                        $days = max(0, (int) $import->created_at->copy()->startOfDay()->diffInDays(now()->startOfDay(), true));
                        $customNames = $import->customsList()->pluck('denomination')->implode(', ');
                        $agentNames = $import->customAgentsList()->map(fn ($a) => $a->company_name ?: trim($a->name . ' ' . $a->last_name))->implode(', ');
                    @endphp
                    <tr class="border-b border-gray-100 dark:border-lits-blue-450/60 {{ $import->urgent ? 'bg-lits-red-50/50 dark:bg-lits-red-500/10 shadow-[inset_3px_0_0_var(--color-lits-red-500)] hover:bg-lits-red-50 dark:hover:bg-lits-red-500/15' : 'hover:bg-gray-50 dark:hover:bg-lits-blue-550/60' }}">
                        <td class="py-3 pl-4 pr-3 whitespace-nowrap">
                            <a href="{{ route('orders.imports.show', ['order' => $import->order->id, 'import' => $import->id]) }}" class="text-sm font-semibold tabular-nums text-entity-customs dark:text-violet-400 hover:underline">{{ $import->tracking_code }}</a>
                            <div class="mt-0.5 text-xs text-gray-400 dark:text-gray-500">{{ $import->order->code }}</div>
                        </td>
                        <td class="px-3 py-3 whitespace-nowrap">
                            <div class="flex items-center gap-2.5">
                                <div class="size-7 shrink-0 flex items-center justify-center rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700">
                                    <img alt="{{ $import->order->client->trade_name }}" src="{{ route('clients.logos', ['filename' => str_replace('.', '_', str_replace('logos/', '', $import->order->client->image))]) }}" />
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900 dark:text-gray-50">{{ $import->order->client->trade_name }}</div>
                                    <div class="text-xs text-gray-400 dark:text-gray-500">{{ $import->order->contact->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-3 py-3 whitespace-nowrap">
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $customNames ?: '—' }}</div>
                            <div class="mt-0.5 text-xs text-gray-400 dark:text-gray-500">{{ $agentNames ?: '—' }}</div>
                        </td>
                        <td class="w-full max-w-0 px-3 py-3">
                            <div class="truncate text-sm text-gray-500 dark:text-gray-400" title="{{ $import->reference }}">{{ $import->reference }}</div>
                        </td>
                        <td class="px-3 py-3 whitespace-nowrap text-sm tabular-nums text-gray-500 dark:text-gray-400">
                            {{ $import->created_at->isoFormat('DD/MM/YYYY') }}
                            <div class="mt-0.5 text-xs {{ $days >= 5 ? 'font-semibold text-amber-700 dark:text-amber-400' : 'text-gray-400 dark:text-gray-500' }}" @if($days >= 5) title="Lleva 5 días o más abierta" @endif>hace {{ $days }} {{ $days === 1 ? 'día' : 'días' }}</div>
                        </td>
                        <td class="px-3 py-3 whitespace-nowrap">
                            <a href="{{ route('orders.imports.show', ['order' => $import->order->id, 'import' => $import->id, 'activeTab' => 2]) }}" data-tippy-content="Ir a estatus" role="button" class="flex flex-col items-start gap-1.5">
                                <span class="text-xs font-medium {{ $key === 'retraso' ? 'font-semibold text-lits-red-500' : ($key === 'stale' ? 'text-amber-700 dark:text-amber-400' : 'text-gray-500 dark:text-gray-400') }}">{{ $label }}</span>
                                <span class="inline-flex gap-0.5" aria-hidden="true">
                                    @foreach([1, 2, 3, 4] as $n)
                                        <i class="h-[5px] w-3.5 rounded-xs {{ $key === 'stale' ? $stripes : ($n <= $step ? ($key === 'retraso' ? 'bg-lits-red-500' : $stepColor[$n]) : 'bg-gray-200 dark:bg-lits-blue-450') }}"></i>
                                    @endforeach
                                </span>
                            </a>
                        </td>
                        <td class="relative py-3 pr-4 pl-3 text-sm font-medium whitespace-nowrap">
                            @include('order-import.partials.row-actions')
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-3 py-10 text-sm text-center text-gray-400 dark:text-gray-500">Ninguna operación coincide con los filtros.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-gray-200 dark:border-lits-blue-450 px-4 py-3 text-xs text-gray-400 dark:text-gray-500">
            <span class="tabular-nums">Mostrando {{ $imports->count() }} de {{ $imports->total() }} operaciones activas</span>
            @if($imports->hasPages())
                <nav>{{ $imports->links() }}</nav>
            @endif
        </div>
    </section>

    {{-- Análisis --}}
    <h2 id="analisis" class="mt-2 scroll-mt-4 text-[17px] font-semibold tracking-tight text-gray-900 dark:text-gray-50">Análisis</h2>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
        <section aria-labelledby="mes-t" class="lg:col-span-7 p-5 {{ $card }}">
            <div class="flex flex-wrap items-baseline justify-between gap-3 mb-3.5">
                <h3 id="mes-t" class="text-[15px] font-semibold text-gray-900 dark:text-gray-50">Operaciones por mes</h3>
                <span class="text-xs text-gray-400 dark:text-gray-500">Últimos 12 meses, por fecha de solicitud</span>
            </div>
            <div class="flex items-end gap-2 h-42 pt-2 border-b border-gray-200 dark:border-lits-blue-450">
                @foreach($dashboard['months'] as $m)
                    <div class="group relative flex-1 h-full flex flex-col items-center justify-end" title="{{ $m['label'] }}: {{ $m['total'] }} {{ $m['total'] === 1 ? 'operación' : 'operaciones' }}">
                        <span class="absolute bottom-full mb-1 text-xs font-semibold tabular-nums text-gray-500 dark:text-gray-400 {{ $m['current'] ? '' : 'opacity-0 group-hover:opacity-100' }} transition-opacity">{{ $m['total'] }}</span>
                        <div class="w-full max-w-7.5 rounded-t-[5px] bg-entity-customs dark:bg-violet-400 {{ $m['current'] ? 'bg-[repeating-linear-gradient(135deg,var(--color-entity-customs)_0_5px,transparent_5px_9px)] dark:bg-[repeating-linear-gradient(135deg,#A78BFA_0_5px,transparent_5px_9px)] ring-[1.5px] ring-inset ring-entity-customs dark:ring-violet-400' : 'opacity-75 group-hover:opacity-100' }}" style="height: {{ $m['total'] > 0 ? max(3, round($m['total'] / $maxMonth * 100)) : 0 }}%"></div>
                    </div>
                @endforeach
            </div>
            <div class="mt-1.5 flex gap-2">
                @foreach($dashboard['months'] as $m)
                    <span class="flex-1 text-center text-xs text-gray-400 dark:text-gray-500">{{ $m['label'] }}</span>
                @endforeach
            </div>
            <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-xs text-gray-500 dark:text-gray-400">
                <span class="inline-flex items-center gap-1.5"><i class="size-2.5 rounded-[3px] bg-entity-customs dark:bg-violet-400"></i>Mes cerrado</span>
                <span class="inline-flex items-center gap-1.5"><i class="size-2.5 rounded-[3px] bg-[repeating-linear-gradient(135deg,var(--color-entity-customs)_0_3px,transparent_3px_6px)] ring-1 ring-inset ring-entity-customs dark:ring-violet-400"></i>Mes en curso, aún incompleto</span>
            </div>
        </section>

        <section aria-labelledby="aduana-t" class="lg:col-span-5 p-5 {{ $card }}">
            <div class="flex flex-wrap items-baseline justify-between gap-3 mb-3.5">
                <h3 id="aduana-t" class="text-[15px] font-semibold text-gray-900 dark:text-gray-50">Carga por aduana</h3>
                <span class="text-xs text-gray-400 dark:text-gray-500">Histórico</span>
            </div>
            @forelse($dashboard['customs'] as $c)
                @php $total = $c['active'] + $c['closed']; @endphp
                <div class="grid grid-cols-[1fr_2.75rem] items-center gap-2.5 py-2 {{ $loop->first ? '' : 'border-t border-gray-100 dark:border-lits-blue-450/60' }}">
                    <div>
                        <div class="mb-1 truncate text-sm font-medium text-gray-900 dark:text-gray-50" title="{{ $c['name'] }}">{{ $c['name'] }}</div>
                        <div class="flex h-2 overflow-hidden rounded bg-gray-100 dark:bg-lits-blue-600" style="width: {{ round($total / $maxCustom * 100) }}%">
                            <span class="bg-entity-customs dark:bg-violet-400" style="flex: {{ $c['active'] }}"></span>
                            <span class="bg-violet-200 dark:bg-[#3F3568]" style="flex: {{ $c['closed'] }}"></span>
                        </div>
                    </div>
                    <div class="text-right text-sm font-semibold tabular-nums text-gray-900 dark:text-gray-50">{{ $total }}</div>
                </div>
            @empty
                <p class="py-6 text-sm text-center text-gray-400 dark:text-gray-500">Sin operaciones todavía.</p>
            @endforelse
            <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-xs text-gray-500 dark:text-gray-400">
                <span class="inline-flex items-center gap-1.5"><i class="size-2.5 rounded-[3px] bg-entity-customs dark:bg-violet-400"></i>Activas</span>
                <span class="inline-flex items-center gap-1.5"><i class="size-2.5 rounded-[3px] bg-violet-200 dark:bg-[#3F3568]"></i>Cerradas</span>
            </div>
        </section>
    </div>
</div>
