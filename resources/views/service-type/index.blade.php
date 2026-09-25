@section('title', 'Service Types')
<x-layout-admin>
    <section>
        <x-navigation.breadcrumbs :links="['Service Types' => '#']" />
        <x-headings.without-action :title="'Service Types'" />
        @if(session()->has('success'))
            <x-alerts.success class="mt-4" :message="session('success')" />
        @endif
        <div class="mt-2 flow-root">
            <div class="shadow-lits-card rounded bg-white dark:bg-lits-blue-550">
                <div class="overflow-x-auto">
                    <div class="inline-block min-w-full align-middle">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="">
                            <tr>
                                <th scope="col" class="py-3.5 pr-3 pl-4 text-left text-sm font-semibold whitespace-nowrap text-gray-900 dark:text-gray-50 sm:pl-6">Código</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold whitespace-nowrap text-gray-900 dark:text-gray-50">Nombre</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold whitespace-nowrap text-gray-900 dark:text-gray-50">Fecha alta</th>
                                <th scope="col" class="relative py-3.5 pr-4 pl-3 sm:pr-6"><span class="sr-only">Detalles</span></th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 ">
                            @forelse($services as $service)
                                <tr>
                                    <td class="py-4 pr-3 pl-4 text-gray-900 dark:text-gray-50 sm:pl-6 whitespace-nowrap">
                                        <a href="{{route('service-types.service-classes.index', ['service_type' => $service->id])}}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 text-sm font-medium">{{ $service->code }}</a>
                                    </td>
                                    <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-900 dark:text-gray-50">
                                        {{ $service->name }}
                                    </td>
                                    <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-900 dark:text-gray-50">
                                        {{ $service->created_at->isoFormat('D MMM YYYY') }}
                                    </td>
                                    <td class="relative py-4 pr-4 pl-3 text-sm font-medium whitespace-nowrap sm:pr-6 flex items-center justify-end gap-x-1">
                                        <a
                                            href="{{route('service-types.service-classes.index', ['service_type' => $service->id])}}"
                                            data-tippy-content="Ver"
                                            role="button"
                                            class="size-7 shrink-0 rounded-md bg-blue-100 dark:bg-blue-500/15 flex items-center justify-center font-semibold text-blue-500 dark:text-blue-400 hover:text-blue-800 hover:bg-blue-200"
                                        >
                                            <i class="fa-regular fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-3 py-4 text-sm text-center text-gray-500 dark:text-gray-400">
                                        No hay Service Types en la base de datos.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layout-admin>
