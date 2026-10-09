@section('title', 'Dashboard')
@section('custom_script')
    <script type="module">
        function styleLineDataset(chartData) {
            chartData.datasets.forEach((dataset) => {
                const color = Array.isArray(dataset.borderColor) ? dataset.borderColor[0] : dataset.borderColor;
                dataset.borderColor = color;
                dataset.backgroundColor = color;
                dataset.fill = false;
                dataset.tension = 0;
                dataset.borderWidth = 2.5;
                dataset.pointRadius = 3.5;
                dataset.pointHoverRadius = 5.5;
                dataset.pointBackgroundColor = color;
                dataset.pointBorderColor = '#fff';
                dataset.pointBorderWidth = 1.5;
                dataset.pointHoverBackgroundColor = color;
                dataset.pointHoverBorderColor = '#fff';
                dataset.pointHoverBorderWidth = 2;
            });
            return chartData;
        }

        function renderLineLegend(chart) {
            const legendEl = document.getElementById('area-chart-legend');
            legendEl.innerHTML = chart.data.datasets.map((dataset, i) => {
                const color = Array.isArray(dataset.borderColor) ? dataset.borderColor[0] : dataset.borderColor;
                const visible = chart.isDatasetVisible(i);
                return `<button type="button" data-index="${i}" class="inline-flex items-center gap-1.5 hover:cursor-pointer ${visible ? '' : 'opacity-40'}">
                    <span class="size-2 shrink-0 rounded-full" style="background-color:${color}"></span>
                    <span class="${visible ? '' : 'line-through'}">${dataset.label}</span>
                </button>`;
            }).join('');
        }

        function roundRect(ctx, x, y, width, height, radius) {
            ctx.beginPath();
            ctx.moveTo(x + radius, y);
            ctx.arcTo(x + width, y, x + width, y + height, radius);
            ctx.arcTo(x + width, y + height, x, y + height, radius);
            ctx.arcTo(x, y + height, x, y, radius);
            ctx.arcTo(x, y, x + width, y, radius);
            ctx.closePath();
        }

        const endValueLabelsPlugin = {
            id: 'endValueLabels',
            afterDatasetsDraw(chart) {
                const { ctx, chartArea } = chart;
                chart.data.datasets.forEach((dataset, i) => {
                    const meta = chart.getDatasetMeta(i);
                    if (meta.hidden || !dataset.data.length) return;
                    const lastPoint = meta.data[meta.data.length - 1];
                    if (!lastPoint) return;
                    const value = dataset.data[dataset.data.length - 1];
                    const color = Array.isArray(dataset.borderColor) ? dataset.borderColor[0] : dataset.borderColor;
                    const label = String(value);

                    ctx.save();
                    ctx.font = "600 11px 'Instrument Sans', sans-serif";
                    const textWidth = ctx.measureText(label).width;
                    const boxWidth = Math.max(textWidth + 14, 24);
                    const boxHeight = 20;
                    const x = Math.min(lastPoint.x + 8, chartArea.right + 34 - boxWidth);
                    const y = lastPoint.y - boxHeight / 2;

                    ctx.fillStyle = color;
                    roundRect(ctx, x, y, boxWidth, boxHeight, 5);
                    ctx.fill();

                    ctx.fillStyle = '#fff';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(label, x + boxWidth / 2, y + boxHeight / 2 + 0.5);
                    ctx.restore();
                });
            },
        };

        function chartHasData(chartData) {
            return chartData.datasets.some((dataset) => dataset.data.some((value) => Number(value) > 0));
        }

        function toggleEmptyState(wrapId, emptyId, hasData) {
            document.getElementById(wrapId).classList.toggle('hidden', !hasData);
            const emptyEl = document.getElementById(emptyId);
            emptyEl.classList.toggle('hidden', hasData);
            emptyEl.classList.toggle('flex', !hasData);
        }

        function renderDoughnutLegend(chartData) {
            const legendEl = document.getElementById('doughnut-legend');
            const dataset = chartData.datasets[0];
            const total = dataset.data.reduce((sum, value) => sum + Number(value), 0);
            legendEl.innerHTML = chartData.labels.map((label, i) => {
                const value = Number(dataset.data[i]) || 0;
                const pct = total > 0 ? Math.round((value / total) * 100) : 0;
                const color = Array.isArray(dataset.backgroundColor) ? dataset.backgroundColor[i] : dataset.backgroundColor;
                return `<li class="flex items-center gap-2">
                    <span class="size-2.5 shrink-0 rounded-full" style="background-color:${color}"></span>
                    <span class="flex-1">${label}</span>
                    <span class="font-semibold text-gray-900 dark:text-gray-50">${value} (${pct}%)</span>
                </li>`;
            }).join('');
        }

        function updateDoughnutCenter(chartData) {
            const dataset = chartData.datasets[0];
            const activeValue = Number(dataset.data[0]) || 0;
            document.getElementById('doughnut-center-value').textContent = activeValue.toLocaleString();
            document.getElementById('doughnut-center-label').textContent = dataset.label;
        }

        var areaEl = document.getElementById('area-chart');
        var areaCtx = areaEl.getContext('2d');
        const areaConfig = {
            type: 'line',
            data: styleLineDataset(JSON.parse(areaEl.dataset.chart)),
            plugins: [endValueLabelsPlugin],
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 3.4,
                layout: {
                    padding: { right: 38 },
                },
                animation: {
                    duration: 900,
                    easing: 'easeOutQuart',
                },
                plugins: {
                    legend: { display: false },
                },
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    x: {
                        grid: { display: false },
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(15, 23, 42, 0.06)' },
                        ticks: { precision: 0 },
                    },
                },
            },
        };
        var areaChart = new Chart(areaCtx, areaConfig);
        renderLineLegend(areaChart);
        toggleEmptyState('area-chart-wrap', 'area-chart-empty', chartHasData(areaConfig.data));
        document.getElementById('area-chart-legend').addEventListener('click', function(e) {
            const btn = e.target.closest('[data-index]');
            if (!btn) return;
            const index = Number(btn.dataset.index);
            if (areaChart.isDatasetVisible(index)) {
                areaChart.hide(index);
            } else {
                areaChart.show(index);
            }
            renderLineLegend(areaChart);
        });

        var doughnutEl = document.getElementById('doughnut-chart');
        var doughnutCtx = doughnutEl.getContext('2d');
        var doughnutDatasets = JSON.parse(doughnutEl.dataset.charts);
        const doughnutConfig = {
            type: 'doughnut',
            data: doughnutDatasets['Ordenes'],
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 1,
                cutout: '72%',
                animation: {
                    duration: 900,
                    easing: 'easeOutQuart',
                },
                elements: {
                    arc: {
                        borderWidth: 3,
                        borderColor: '#fff',
                        borderRadius: 6,
                    },
                },
                plugins: {
                    legend: {
                        display: false,
                    },
                },
            },
        };
        var doughnutChart = new Chart(doughnutCtx, doughnutConfig);
        renderDoughnutLegend(doughnutConfig.data);
        updateDoughnutCenter(doughnutConfig.data);
        toggleEmptyState('doughnut-chart-wrap', 'doughnut-chart-empty', chartHasData(doughnutConfig.data));
        $('#doughnut-type').on('change', function() {
            doughnutChart.data = doughnutDatasets[$(this).val()];
            doughnutChart.update();
            renderDoughnutLegend(doughnutChart.data);
            updateDoughnutCenter(doughnutChart.data);
            toggleEmptyState('doughnut-chart-wrap', 'doughnut-chart-empty', chartHasData(doughnutChart.data));
        });

        Livewire.on('reports-charts-updated', (event) => {
            areaChart.data = styleLineDataset(event.orders);
            areaChart.update();
            renderLineLegend(areaChart);
            toggleEmptyState('area-chart-wrap', 'area-chart-empty', chartHasData(areaChart.data));

            doughnutDatasets = event.doughnuts;
            doughnutChart.data = doughnutDatasets[$('#doughnut-type').val()];
            doughnutChart.update();
            renderDoughnutLegend(doughnutChart.data);
            updateDoughnutCenter(doughnutChart.data);
            toggleEmptyState('doughnut-chart-wrap', 'doughnut-chart-empty', chartHasData(doughnutChart.data));
        });

        function animateCount(el) {
            const target = parseInt(el.dataset.countTarget, 10) || 0;
            const duration = 900;
            const start = performance.now();
            function tick(now) {
                const progress = Math.min((now - start) / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3);
                el.textContent = Math.round(eased * target).toLocaleString();
                if (progress < 1) {
                    requestAnimationFrame(tick);
                }
            }
            requestAnimationFrame(tick);
        }
        document.querySelectorAll('[data-count-target]').forEach(animateCount);
    </script>
@endsection
<x-layout-app>
    <div>
        <h1 class="flex flex-wrap items-baseline gap-x-2 gap-y-1 text-2xl/7 font-bold text-gray-900 dark:text-gray-50 sm:text-3xl sm:tracking-tight">
            {{__("Hi")}} {{ auth()->user()->name ?? '' }}
            <span class="text-sm font-normal text-gray-500 dark:text-gray-400">{{__("Welcome to")}} LITS</span>
        </h1>

        @php
            $statIconColors = [
                'order' => 'bg-entity-orders-50 text-entity-orders group-hover:bg-entity-orders group-hover:text-white',
                'shipment' => 'bg-entity-shipments-50 text-entity-shipments group-hover:bg-entity-shipments group-hover:text-white',
                'import' => 'bg-entity-customs-50 text-entity-customs group-hover:bg-entity-customs group-hover:text-white',
                'warehouse_storage' => 'bg-entity-warehouses-50 text-entity-warehouses group-hover:bg-entity-warehouses group-hover:text-white',
            ];
        @endphp
        <section class="animate-fade-up mt-8 overflow-hidden rounded-2xl bg-white dark:bg-lits-blue-550 shadow-lits-card ring-1 ring-gray-100 dark:ring-gray-800">
            <div class="flex items-center gap-3 px-5 pt-4 pb-3">
                <div class="flex size-8 items-center justify-center rounded-lg bg-lits-red-50 text-lits-red-500">
                    <i class="fa-regular fa-triangle-exclamation text-xs"></i>
                </div>
                <h2 class="text-base font-bold text-gray-900 dark:text-gray-50">{{__("Needs your attention")}}</h2>
            </div>

            @if($pending)
                <div class="grid grid-cols-1 border-t border-gray-100 dark:border-lits-blue-450/60 sm:grid-cols-3">
                    @foreach([
                        ['n' => $pending['mine'], 'dot' => null, 'label' => 'Órdenes activas mías', 'link' => 'Ver mis órdenes', 'href' => route('orders.index', ['mine' => 1])],
                        ['n' => $pending['overdue'], 'dot' => 'bg-lits-red-500', 'label' => 'Embarques con ETA vencido', 'link' => 'Revisar', 'href' => route('shipments.index', ['overdue' => 1, 'mine' => 1])],
                        ['n' => $pending['stale'], 'dot' => 'bg-amber-500', 'label' => 'Sin movimiento más de ' . $pending['staleDays'] . ' días', 'link' => 'Revisar', 'href' => route('orders.index', ['mine' => 1, 'stale' => 1])],
                    ] as $cell)
                        <div class="flex flex-col gap-0.5 border-t border-gray-100 px-5 py-4 first:border-t-0 dark:border-lits-blue-450/60 sm:border-t-0 sm:border-l sm:first:border-l-0">
                            <p class="flex items-center gap-2 text-3xl font-semibold tabular-nums text-gray-900 dark:text-gray-50">
                                @if($cell['dot'])<span class="size-2 rounded-full {{ $cell['dot'] }}"></span>@endif
                                {{ $cell['n'] }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $cell['label'] }}</p>
                            <a href="{{ $cell['href'] }}" class="mt-1 text-sm font-semibold text-lits-red-500 hover:text-lits-red-600">{{ $cell['link'] }} <span aria-hidden="true">→</span></a>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="border-t border-gray-100 px-5 py-3 dark:border-lits-blue-450/60">
                <p class="pb-1 text-xs font-semibold text-gray-400 dark:text-gray-500">{{ $pending ? 'Urgentes de todo el equipo' : 'Órdenes urgentes' }}</p>
                @if(count($attentionItems) > 0)
                    <ul class="grid grid-cols-1 gap-x-8 lg:grid-cols-2">
                        @foreach($attentionItems as $item)
                            <li class="border-b border-gray-100 last:border-b-0 dark:border-lits-blue-450/60 {{ $loop->iteration === count($attentionItems) - 1 && $loop->iteration % 2 === 1 ? 'lg:border-b-0' : '' }}">
                                <a href="{{ $item['route'] }}" class="group -mx-2 flex items-center justify-between gap-4 rounded-lg px-2 py-3 transition-colors hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <div class="flex min-w-0 items-center gap-3">
                                        <span class="flex size-8 shrink-0 items-center justify-center rounded-lg {{ $statIconColors[$item['slug']] ?? $statIconColors['order'] }}">
                                            <i class="{{ $item['icon'] }} text-sm"></i>
                                        </span>
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-medium text-gray-900 dark:text-gray-50">{{ $item['type'] }} · {{ $item['reference'] }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $item['created_at']->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    <span class="inline-flex shrink-0 items-center rounded-full bg-red-100 dark:bg-red-500/15 px-2.5 py-1 text-xs font-bold text-red-700 dark:text-red-400">
                                        {{__("Urgent")}}
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="flex items-center gap-2 py-2 text-sm text-gray-500 dark:text-gray-400">
                        <i class="fa-regular fa-circle-check text-base text-gray-300 dark:text-gray-600"></i>
                        {{__("No urgent items right now")}}
                    </div>
                @endif
            </div>
        </section>

        <header class="pt-8 pb-4 sm:pb-6">
            <div class="mx-auto flex flex-wrap items-center gap-6 sm:flex-nowrap">
                <h2 class="text-base/7 font-semibold text-gray-900 dark:text-gray-50">{{__("Orders summary")}}</h2>
            </div>
        </header>
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($stats as $stat)
                <div
                    class="dashboard-stat-card group animate-fade-up relative overflow-hidden rounded-2xl bg-white dark:bg-lits-blue-550 px-5 py-6 shadow-lits-card ring-1 ring-gray-100 dark:ring-gray-800"
                    style="animation-delay: {{ $loop->index * 90 }}ms"
                >
                    <div class="dashboard-stat-icon flex size-10 items-center justify-center rounded-xl {{ $statIconColors[$stat->slug] ?? $statIconColors['order'] }}">
                        <i class="{{$stat->svg}} text-lg"></i>
                    </div>
                    <div class="my-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $stat->title }}</p>
                        <p class="text-4xl font-semibold text-gray-900 dark:text-gray-50" data-count-target="{{ $stat->stat }}">0</p>
                    </div>
                    <a href="{{ $stat->route }}" class="dashboard-stat-link inline-flex items-center gap-1 text-sm font-medium text-lits-red-500 hover:text-lits-red-600">
                        {{__("View all")}}
                        <i class="fa-regular fa-arrow-right text-xs"></i>
                        <span class="sr-only"> {{ $stat->title }}</span>
                    </a>
                </div>
            @endforeach
        </div>

        <div class="mt-10 grid grid-cols-1 gap-5 lg:grid-cols-8">
            <livewire:dashboard-reports-charts />
        </div>
    </div>
</x-layout-app>
