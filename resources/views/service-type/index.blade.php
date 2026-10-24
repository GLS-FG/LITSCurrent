@section('title', 'Service Types')
<x-layout-admin>
    <section>
        <x-navigation.breadcrumbs :links="['Service Types' => '#']" />
        <x-headings.without-action :title="'Service Types'" />
        @if(session()->has('success'))
            <x-alerts.success class="mt-4" :message="session('success')" />
        @endif
        <div class="mt-5 overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <table class="min-w-full">
                    <thead>
                    <tr class="border-b border-gray-200 dark:border-lits-blue-450">
                        <th scope="col" class="py-2.5 pl-2 text-left text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">Código</th>
                        <th scope="col" class="px-3 py-2.5 text-left text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">Nombre</th>
                        <th scope="col" class="px-3 py-2.5 text-left text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">Fecha alta</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($services as $service)
                        <tr class="border-b border-gray-100 dark:border-lits-blue-450/60 hover:bg-gray-50 dark:hover:bg-lits-blue-550/60">
                            <td class="py-3.5 pl-2 whitespace-nowrap">
                                <a href="{{route('service-types.service-classes.index', ['service_type' => $service->id])}}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 text-sm font-medium">{{ $service->code }}</a>
                            </td>
                            <td class="px-3 py-3.5 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                {{ $service->name }}
                            </td>
                            <td class="px-3 py-3.5 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                {{ $service->created_at->isoFormat('D MMM YYYY') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-3 py-4 text-sm text-center text-gray-500 dark:text-gray-400">
                                No hay Service Types en la base de datos.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</x-layout-admin>
