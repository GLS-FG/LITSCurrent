<div class="contents">
    <div class="col-span-1 flex flex-wrap items-center gap-6 pb-4 sm:pb-6 lg:col-span-8">
        <h2 class="text-base/7 font-semibold text-gray-900 dark:text-gray-50">{{__("Reports")}}</h2>
        @php
            $periods = [
                'month' => __('This month'),
                'semester' => __('Last 6 months'),
                'all-time' => __('All-time'),
            ];
        @endphp
        <div class="inline-flex items-center gap-1 rounded-full bg-gray-100 dark:bg-gray-800 p-1 text-sm/6 font-medium">
            @foreach($periods as $value => $label)
                <button
                    type="button"
                    wire:click="setPeriod('{{ $value }}')"
                    wire:loading.attr="disabled"
                    wire:target="setPeriod('{{ $value }}')"
                    class="rounded-full px-4 py-1.5 transition-colors disabled:cursor-wait disabled:opacity-60 {{ $period === $value ? 'dashboard-pill-active' : 'text-gray-500 hover:text-gray-900' }}"
                >{{ $label }}</button>
            @endforeach
        </div>
    </div>

    <div class="contents">
        <div class="dashboard-chart-card animate-fade-up relative rounded-2xl lg:col-span-5 bg-white dark:bg-lits-blue-550 shadow-lits-card ring-1 ring-gray-100 dark:ring-gray-800" style="animation-delay: 180ms">
            <div class="flex items-center gap-3 px-4 pt-4">
                <div class="flex size-8 items-center justify-center rounded-lg bg-lits-blue-50 text-lits-blue-500">
                    <i class="fa-regular fa-chart-line text-xs"></i>
                </div>
                <h2 class="text-base font-bold text-gray-900 dark:text-gray-50">{{__("Total orders")}}</h2>
            </div>
            <div id="area-chart-legend" class="mt-2 flex flex-wrap gap-x-5 gap-y-1 px-4 text-xs font-medium text-gray-500 dark:text-gray-400" wire:ignore></div>
            <div class="relative">
                <div wire:loading.flex wire:target="setPeriod" class="absolute inset-0 z-10 hidden items-center justify-center rounded-2xl bg-white/60">
                    <i class="fa-regular fa-spinner fa-spin text-2xl text-lits-red-500"></i>
                </div>
                <div id="area-chart-empty" class="hidden flex-col items-center justify-center gap-2 px-5 py-10 text-center">
                    <i class="fa-regular fa-chart-line text-2xl text-gray-300 dark:text-gray-600"></i>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{__("No activity for this period")}}</p>
                </div>
                <div id="area-chart-wrap" class="px-2 py-3" wire:ignore>
                    <canvas id="area-chart" data-chart="{{ json_encode($ordersChartData) }}"></canvas>
                </div>
            </div>
        </div>

        <div class="dashboard-chart-card animate-fade-up relative rounded-2xl bg-white dark:bg-lits-blue-550 shadow-lits-card ring-1 ring-gray-100 dark:ring-gray-800 lg:col-span-3" style="animation-delay: 260ms">
            <div class="flex flex-wrap items-center justify-between gap-3 px-4 pt-4">
                <div class="flex items-center gap-3">
                    <div class="flex size-8 items-center justify-center rounded-lg bg-lits-blue-50 text-lits-blue-500">
                        <i class="fa-regular fa-chart-pie text-xs"></i>
                    </div>
                    <h2 class="text-base font-bold text-gray-900 dark:text-gray-50">{{__("Closed orders")}}</h2>
                </div>
                <div class="flex items-center gap-2">
                    <label for="doughnut-type" class="text-xs font-medium text-gray-400 dark:text-gray-500">{{__("View")}}:</label>
                    <div class="relative">
                        <select id="doughnut-type" autocomplete="off" class="appearance-none rounded-full border-0 bg-gray-100 dark:bg-gray-800 py-1.5 pr-8 pl-3 text-sm font-medium text-gray-700 dark:text-gray-300 outline-none focus:ring-2 focus:ring-lits-red-500">
                            <option value="Ordenes">{{__("Orders")}}</option>
                            <option value="Embarques">{{__("Shipments")}}</option>
                            <option value="Aduana">{{__("Customs")}}</option>
                            <option value="Almacen">{{__("Warehouses")}}</option>
                        </select>
                        <i class="fa-regular fa-angle-down pointer-events-none absolute top-1/2 right-3 -translate-y-1/2 text-xs text-gray-500 dark:text-gray-400"></i>
                    </div>
                </div>
            </div>
            <div class="relative">
                <div wire:loading.flex wire:target="setPeriod" class="absolute inset-0 z-10 hidden items-center justify-center rounded-2xl bg-white/60">
                    <i class="fa-regular fa-spinner fa-spin text-2xl text-lits-red-500"></i>
                </div>
                <div id="doughnut-chart-empty" class="hidden flex-col items-center justify-center gap-2 px-5 py-10 text-center">
                    <i class="fa-regular fa-chart-pie text-2xl text-gray-300 dark:text-gray-600"></i>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{__("No activity for this period")}}</p>
                </div>
                <div id="doughnut-chart-wrap" class="flex flex-wrap items-center gap-6 px-4 py-3" wire:ignore>
                    <div class="relative mx-auto w-full max-w-36 shrink-0">
                        <canvas id="doughnut-chart" data-charts="{{ json_encode($doughnutCharts) }}"></canvas>
                        <div class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center">
                            <span id="doughnut-center-value" class="text-2xl font-bold text-gray-900 dark:text-gray-50">0</span>
                            <span id="doughnut-center-label" class="text-center text-[11px] text-gray-400 dark:text-gray-500"></span>
                        </div>
                    </div>
                    <ul id="doughnut-legend" class="flex min-w-40 flex-1 flex-col gap-2.5 text-sm text-gray-600 dark:text-gray-400"></ul>
                </div>
            </div>
        </div>
    </div>
</div>
