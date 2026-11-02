{{--
    Tablero de indicadores de Aduanas — solo rol Cliente.
    Recibe: $dashboard (OrderImportController::clientDashboard), $stage (filtro activo), $stageLabels.
--}}
@php
    $stageUrl = fn ($key) => route('imports.index', array_merge(
        request()->except(['stage', 'page']),
        $stage === $key ? [] : ['stage' => $key]
    )) . '#operaciones';
    // Rampa de violeta (color de Aduanas); rojo solo para "Retrasado"; ámbar rayado para "Sin actualizar"
    $stageStyle = [
        'docs-pend' => ['bg' => 'bg-violet-200 dark:bg-[#3F3568]', 'text' => 'text-violet-950 dark:text-violet-100', 'dot' => 'bg-violet-200 dark:bg-[#3F3568]'],
        'docs-ok' => ['bg' => 'bg-violet-300 dark:bg-[#6A58A8]', 'text' => 'text-violet-950 dark:text-violet-100', 'dot' => 'bg-violet-300 dark:bg-[#6A58A8]'],
        'proceso' => ['bg' => 'bg-violet-500 dark:bg-[#8D78D6]', 'text' => 'text-white', 'dot' => 'bg-violet-500 dark:bg-[#8D78D6]'],
        'concluido' => ['bg' => 'bg-violet-800 dark:bg-[#B9A8F5]', 'text' => 'text-white dark:text-violet-950', 'dot' => 'bg-violet-800 dark:bg-[#B9A8F5]'],
        'retraso' => ['bg' => 'bg-lits-red-500', 'text' => 'text-white', 'dot' => 'bg-lits-red-500'],
        'otros' => ['bg' => 'bg-gray-300 dark:bg-gray-600', 'text' => 'text-gray-800 dark:text-gray-100', 'dot' => 'bg-gray-300 dark:bg-gray-600'],
        'stale' => [
            'bg' => 'bg-[repeating-linear-gradient(135deg,#FBEEE1_0_6px,transparent_6px_12px)] dark:bg-[repeating-linear-gradient(135deg,rgba(251,191,36,0.14)_0_6px,transparent_6px_12px)] ring-[1.5px] ring-inset ring-amber-500',
            'text' => 'text-amber-700 dark:text-amber-400',
            'dot' => 'bg-amber-500',
        ],
    ];
    $stages = $dashboard['stages'];
    $maxMonth = max(1, collect($dashboard['months'])->max('total'));
    $maxCustom = max(1, collect($dashboard['customs'])->max(fn ($c) => $c['active'] + $c['closed']) ?? 1);
@endphp

<div
    class="mt-5 flex flex-col gap-5"
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
    {{-- Indicadores --}}
    <section aria-label="Indicadores" class="grid grid-cols-1 sm:grid-cols-3 rounded-xl border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550">
        <div class="p-5">
            <div class="flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400"><span class="size-1.5 rounded-full bg-green-600 dark:bg-green-400"></span>Activas</div>
            <div class="mt-2 text-3xl font-bold tabular-nums text-gray-900 dark:text-gray-50">{{ $dashboard['active'] }}</div>
            <div class="mt-1.5 text-xs text-gray-400 dark:text-gray-500">operaciones sin cerrar</div>
        </div>
        <div class="p-5 border-t sm:border-t-0 sm:border-l border-gray-200 dark:border-lits-blue-450">
            <div class="flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400"><span class="size-1.5 rounded-full bg-lits-red-500"></span>Urgentes</div>
            <div class="mt-2 text-3xl font-bold tabular-nums {{ $dashboard['urgent'] ? 'text-lits-red-500' : 'text-gray-900 dark:text-gray-50' }}">{{ $dashboard['urgent'] }}</div>
            <div class="mt-1.5 text-xs text-gray-400 dark:text-gray-500">marcadas como urgentes</div>
        </div>
        <div class="p-5 border-t sm:border-t-0 sm:border-l border-gray-200 dark:border-lits-blue-450">
            <div class="flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400"><span class="size-1.5 rounded-full bg-entity-customs"></span>Días abiertas</div>
            <div class="mt-2 flex items-baseline gap-1.5 text-3xl font-bold tabular-nums text-gray-900 dark:text-gray-50">
                @if($dashboard['avgOpen'] !== null)
                    {{ rtrim(rtrim(number_format($dashboard['avgOpen'], 1), '0'), '.') }}<small class="text-sm font-medium text-gray-400 dark:text-gray-500">días</small>
                @else
                    —
                @endif
            </div>
            <div class="mt-1.5 text-xs text-gray-400 dark:text-gray-500">
                @if($dashboard['oldest'])
                    promedio de las activas · la más antigua lleva {{ $dashboard['oldest']['days'] }} {{ $dashboard['oldest']['days'] === 1 ? 'día' : 'días' }}
                @else
                    promedio de las operaciones activas
                @endif
            </div>
        </div>
    </section>

    {{-- Etapas --}}
    <section aria-labelledby="etapas-t" class="rounded-xl border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 p-5">
        <div class="flex flex-wrap items-baseline justify-between gap-3 mb-3.5">
            <h2 id="etapas-t" class="text-base font-semibold text-gray-900 dark:text-gray-50">En qué etapa está cada operación activa</h2>
            @if($stage && isset($stageLabels[$stage]))
                <a wire:navigate @click="keepScroll()" href="{{ route('imports.index', request()->except(['stage', 'page'])) }}#operaciones" class="inline-flex items-center gap-1.5 rounded-full bg-entity-customs-50 dark:bg-entity-customs/15 px-2.5 py-0.5 text-xs font-semibold text-entity-customs dark:text-violet-400">{{ $stageLabels[$stage] }} <i class="fa-regular fa-xmark"></i></a>
            @else
                <span class="text-xs text-gray-400 dark:text-gray-500">Toca una etapa para filtrar el listado</span>
            @endif
        </div>
        @if($dashboard['active'] > 0)
            <div class="flex gap-[3px] h-11">
                @foreach($stages as $key => $count)
                    @if($count > 0)
                        <a wire:navigate @click="keepScroll()" href="{{ $stageUrl($key) }}" style="flex: {{ $count }}" aria-label="{{ $stageLabels[$key] }}, {{ $count }}"
                           class="min-w-9 rounded-md px-3 flex items-center text-sm font-bold transition {{ $stageStyle[$key]['bg'] }} {{ $stageStyle[$key]['text'] }} {{ $stage && $stage !== $key ? 'opacity-35' : '' }} {{ $stage === $key ? '-translate-y-0.5' : '' }}">{{ $count }}</a>
                    @endif
                @endforeach
            </div>
        @else
            <div class="h-11 rounded-md border border-dashed border-gray-200 dark:border-lits-blue-450 flex items-center px-3 text-sm text-gray-400 dark:text-gray-500">No tienes operaciones activas</div>
        @endif
        <div class="mt-2.5 grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-1">
            @foreach($stages as $key => $count)
                @continue($key === 'otros' && $count === 0)
                @continue($key === 'retraso' && ! $dashboard['showDelayed'])
                <a wire:navigate @click="keepScroll()" href="{{ $stageUrl($key) }}" class="rounded-lg px-1.5 py-1.5 hover:bg-gray-50 dark:hover:bg-lits-blue-600/60">
                    <div class="flex items-center gap-1.5 text-xs leading-snug text-gray-500 dark:text-gray-400"><span class="size-2 shrink-0 rounded-[3px] {{ $stageStyle[$key]['dot'] }}"></span>{{ $stageLabels[$key] }}</div>
                    <div class="ml-3.5 mt-0.5 text-xs tabular-nums text-gray-400 dark:text-gray-500">{{ $count }} {{ $count === 1 ? 'operación' : 'operaciones' }}</div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Gráficas --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
        <section aria-labelledby="mes-t" class="lg:col-span-7 rounded-xl border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 p-5">
            <div class="flex flex-wrap items-baseline justify-between gap-3 mb-3.5">
                <h2 id="mes-t" class="text-base font-semibold text-gray-900 dark:text-gray-50">Operaciones por mes</h2>
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

        <section aria-labelledby="aduana-t" class="lg:col-span-5 rounded-xl border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 p-5">
            <div class="flex flex-wrap items-baseline justify-between gap-3 mb-3.5">
                <h2 id="aduana-t" class="text-base font-semibold text-gray-900 dark:text-gray-50">Carga por aduana</h2>
                <span class="text-xs text-gray-400 dark:text-gray-500">Operaciones del periodo</span>
            </div>
            @forelse($dashboard['customs'] as $c)
                @php $total = $c['active'] + $c['closed']; @endphp
                <div class="grid grid-cols-[1fr_2.5rem] items-center gap-2.5 py-2 {{ $loop->first ? '' : 'border-t border-gray-100 dark:border-lits-blue-450/60' }}">
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
                <p class="py-6 text-sm text-center text-gray-400 dark:text-gray-500">Sin operaciones en este periodo.</p>
            @endforelse
            <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-xs text-gray-500 dark:text-gray-400">
                <span class="inline-flex items-center gap-1.5"><i class="size-2.5 rounded-[3px] bg-entity-customs dark:bg-violet-400"></i>Activas</span>
                <span class="inline-flex items-center gap-1.5"><i class="size-2.5 rounded-[3px] bg-violet-200 dark:bg-[#3F3568]"></i>Cerradas</span>
            </div>
        </section>
    </div>
</div>
