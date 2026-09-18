@section('title', 'Dashboard')
@section('custom_script')
    <script type="module">
        const areaData = @json($areaChartData);
        const areaConfig = {
            type: 'line',
            data: areaData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                interaction: {
                    mode: 'index',
                    intersect: false,
                }
            },
        };
        const doughnutDataOrders = @json($doughnutChartDataOrders);
        const doughnutDataShipments = @json($doughnutChartDataShipments);
        const doughnutDataImports = @json($doughnutChartDataImports);
        const doughnutChartDataWarehouse = @json($doughnutChartDataWarehouse);
        const doughnutConfig = {
            type: 'doughnut',
            data: doughnutDataOrders,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                        reverse: true
                    }
                }
            },
        };
        var areaCtx = $('#area-chart')[0].getContext('2d');
        var areaChart = new Chart(areaCtx, areaConfig);
        var doughnutCtx = $('#doughnut-chart')[0].getContext('2d');
        var doughnutChart = new Chart(doughnutCtx, doughnutConfig);
        $('#doughnut-type').on('change', function() {
            var selectedValue = $(this).val();
            if(selectedValue === "Ordenes"){
                doughnutChart.data = doughnutDataOrders;
            } else if(selectedValue === "Aduana"){
                doughnutChart.data = doughnutDataImports;
            } else if(selectedValue === "Almacen"){
                doughnutChart.data = doughnutChartDataWarehouse;
            } else {
                doughnutChart.data = doughnutDataShipments;
            }
            doughnutChart.update();
        });
    </script>
@endsection
<x-layout-app>
    <main>
        <section>
            <div>
                <p class="text-gray-500 hover:text-gray-700">{{__("Hi")}} {{ auth()->user()->name ?? '' }}</p>
                <h2 class="mt-2 text-2xl/7 font-bold text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">{{__("Welcome to")}} LITS</h2>

                <header class="pt-8 pb-4 sm:pb-6">
                    <div class="mx-auto flex flex-wrap items-center gap-6 sm:flex-nowrap">
                        <h1 class="text-base/7 font-semibold text-gray-900">{{__("Orders summary")}}</h1>
                    </div>
                </header>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach($stats as $stat)
                        <div class="relative overflow-hidden px-4 py-5 shadow-xs rounded bg-white sm:px-6 sm:pt-6">
                            <i class="{{$stat->svg}} text-3xl text-lits-red-500 shrink-0"></i>
                            <div class="my-4">
                                <p class="text-sm text-gray-500">{{ $stat->title }}</p>
                                <p class="text-4xl font-semibold text-gray-900">{{ $stat->stat }}</p>
                            </div>
                            <a href="{{ $stat->route }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">{{__("View all")}}<span class="sr-only"> {{ $stat->title }}</span></a>
                        </div>
                    @endforeach
                </div>

                <header class="pt-10 pb-4 sm:pb-6">
                    <div class="mx-auto flex flex-wrap items-center gap-6 sm:flex-nowrap">
                        <h1 class="text-base/7 font-semibold text-gray-900">{{__("Reports")}}</h1>
                        <div class="order-last flex w-full gap-x-8 text-sm/6 font-semibold sm:order-0 sm:w-auto sm:border-l sm:border-gray-200 sm:pl-6 sm:text-sm/7">
                            <a href="{{ route('dashboard', ['reports' => 'month']) }}" class="{{ request('reports') === null || request('reports') === 'month' ? 'text-lits-red-500' : 'text-indigo-600 hover:text-indigo-500' }}">{{__("This month")}}</a>
                            <a href="{{ route('dashboard', ['reports' => 'semester']) }}" class="{{ request('reports') === 'semester' ? 'text-lits-red-500' : 'text-indigo-600 hover:text-indigo-500' }}">{{__("Last 6 months")}}</a>
                            <a href="{{ route('dashboard', ['reports' => 'all-time']) }}" class="{{ request('reports') === 'all-time' ? 'text-lits-red-500' : 'text-indigo-600 hover:text-indigo-500' }}">{{__("All-time")}}</a>
                        </div>
                    </div>
                </header>

                <div class=" grid grid-cols-1 gap-5 md:grid-cols-4 lg:grid-cols-8">
                    <div class="shadow-xs rounded bg-white col-span-1 md:col-span-3 lg:col-span-5">
                        <div class="px-4 pt-4">
                            <h2 class="text-2xl/7 font-bold text-gray-900">{{__("Total orders")}}</h2>
                        </div>
                        <div class="px-2 py-8">
                            <canvas id="area-chart"></canvas>
                        </div>
                    </div>
                    <div class="shadow-xs rounded bg-white col-span-1 md:col-span-1 lg:col-span-3">
                        <div class="px-4 pt-4">
                            <h2 class="text-2xl/7 font-bold text-gray-900">{{__("Closed orders")}}</h2>
                            <div class="sm:col-span-3">
                                <div class="mt-2 grid grid-cols-1">
                                    <select id="doughnut-type" autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white py-1.5 pr-8 pl-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                        <option value="Ordenes">{{__("Orders")}}</option>
                                        <option value="Embarques">{{__("Shipments")}}</option>
                                        <option value="Aduana">{{__("Customs")}}</option>
                                        <option value="Almacen">{{__("Warehouses")}}</option>
                                    </select>
                                    <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 sm:text-sm"></i>
                                </div>
                            </div>
                        </div>
                        <div class="px-2 py-8 max-h-80 flex items-center justify-center">
                            <canvas id="doughnut-chart"></canvas>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </main>
</x-layout-app>
