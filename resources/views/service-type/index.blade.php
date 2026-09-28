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
                        <th scope="col" class="py-2.5 pr-2 pl-3 text-center text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">Acciones</th>
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
                            <td class="py-3.5 pr-2 pl-3 text-sm font-medium whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1">
                                <a
                                    href="{{route('service-types.service-classes.index', ['service_type' => $service->id])}}"
                                    data-tippy-content="Ver"
                                    role="button"
                                    class="size-7 shrink-0 rounded-md flex items-center justify-center text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-200"
                                >
                                    <i class="fa-regular fa-eye"></i>
                                </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-3 py-4 text-sm text-center text-gray-500 dark:text-gray-400">
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
