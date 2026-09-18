@section('title', 'Detalles del estatus del servicio')
<x-layout-admin>
    <section>
        <x-navigation.breadcrumbs :links="['Estatus Servicios' => route('service-statuses.index'), $serviceType->code => route('service-statuses.service-type-statuses.index', ['service_status' => $serviceType]), $serviceTypeStatus->name => '#']" />
        <x-headings.without-action
            :title="'Detalles del estatus'"
        />
        @if ($errors->any())
            <x-alerts.error :message="'Ocurrieron los siguientes errores:'" :errors="$errors" class="my-4" />
        @endif
        @if(session()->has('success'))
            <x-alerts.success class="mt-4" :message="session('success')" />
        @endif
        <div class="mt-8">
            <div class="divide-y divide-gray-200 rounded bg-white shadow-lits-card">
                <div class="px-4 py-5 sm:px-6">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                        </div>
                        <div class="flex shrink-0 space-x-5">
                            @can('update', $serviceType)
                                <a href='{{route('service-statuses.service-type-statuses.edit', [ 'service_status' => $serviceType, 'service_type_status' => $serviceTypeStatus ])}}' class="inline-flex items-center gap-1 rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">
                                    <i class="fa-regular fa-pen-to-square mr-1.5 -ml-0.5"></i>
                                    Editar
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="px-4 py-5 sm:p-6 divide-y divide-gray-200">
                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                        <dt class="text-sm/6 font-medium text-gray-900">Nombre</dt>
                        <dd class="mt-1 text-sm/6 text-gray-700 sm:col-span-2 sm:mt-0">
                            {{$serviceTypeStatus->name}}
                        </dd>
                    </div>
                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                        <dt class="text-sm/6 font-medium text-gray-900">Color</dt>
                        <dd class="mt-1 text-sm/6 text-gray-700 sm:col-span-2 sm:mt-0">
                            <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium inset-ring {{$serviceTypeStatus->color}}">{{$serviceTypeStatus->name}}</span>
                        </dd>
                    </div>
                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                        <dt class="text-sm/6 font-medium text-gray-900">Service Modes</dt>
                        <dd class="mt-1 text-sm/6 text-gray-700 sm:col-span-2 sm:mt-0">
                            <table class="relative divide-y border">
                                <thead>
                                <tr class="divide-x">
                                    <th scope="col" class="py-3.5 px-3 text-center text-sm font-semibold text-gray-900 bg-gray-200">Service Class</th>
                                    <th scope="col" class="py-3.5 px-3 text-center text-sm font-semibold text-gray-900 bg-gray-200">Service Mode</th>
                                    <th scope="col" class="py-3.5 px-3 bg-gray-200">
                                        <span class="sr-only"></span>
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="divide-y">
                                @foreach($serviceType->serviceClasses as $serviceClass)
                                    @foreach($serviceClass->serviceModes as $serviceMode)
                                        <tr class="divide-x">
                                            @if($loop->first)
                                                <td rowspan="{{count($serviceClass->serviceModes)}}" class="py-4 px-3 text-sm text-center font-medium whitespace-nowrap text-gray-900 bg-gray-200">{{$serviceClass->code}}</td>
                                            @endif
                                            <td class="px-3 py-4 text-sm text-center text-gray-900 bg-gray-200">{{$serviceMode->code}}</td>
                                            <td class="py-4 px-3 text-lg font-medium text-center">
                                                @if(in_array($serviceMode->id, $modes))
                                                    <i class="fa-solid fa-square-check text-green-500"></i>
                                                @else
                                                    <i class="fa-solid fa-square-xmark text-red-500"></i>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                                </tbody>
                            </table>
                        </dd>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layout-admin>
