@section('title', 'Editar unidad de transporte')
<x-layout-admin>
    <section>
        <x-navigation.breadcrumbs :links="['Transportistas' => route('transportation-agencies.index'), $agency->name => route('transportation-agencies.show', ['transportation_agency' => $agency]), 'Editar unidad' => '#']" />
        <x-headings.without-action
            :title="'Editar unidad de transporte'"
        />
        @if ($errors->any())
            <x-alerts.error :message="'Para editar la unidad de transporte soluciona los siguientes errores:'" :errors="$errors" class="my-4" />
        @endif
        <form action="{{ route('transportation-agencies.vehicles.update', ['transportation_agency' => $agency, 'vehicle' => $vehicle]) }}" method="POST" class="mt-8 grid grid-cols-1 gap-x-8 gap-y-8 lg:grid-cols-1" autocomplete="off">
            @csrf
            @method('PUT')
            <div class="h-fit lg:col-span-2 divide-y divide-gray-200 dark:divide-gray-700 rounded bg-white dark:bg-lits-blue-550 shadow-lits-card">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-50">Editar unidad de transporte</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="pb-12">
                        <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                            <div class="sm:col-span-2">
                                <label for="eco_number" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Número Económico</label>
                                <div class="mt-2">
                                    <input id="eco_number" value="{{old('eco_number', $vehicle->eco_number)}}" name="eco_number" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="plates" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Placas</label>
                                <div class="mt-2">
                                    <input id="plates" value="{{old('plates', $vehicle->plates)}}" name="plates" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="vehicle_type" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Tipo</label>
                                <div class="mt-2">
                                    <input id="vehicle_type" value="{{old('vehicle_type', $vehicle->vehicle_type)}}" name="vehicle_type" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="serial_number" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Número de serie</label>
                                <div class="mt-2">
                                    <input id="serial_number" value="{{old('serial_number', $vehicle->serial_number)}}" name="serial_number" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="vehicle_brand" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Marca y línea</label>
                                <div class="mt-2">
                                    <input id="vehicle_brand" value="{{old('vehicle_brand', $vehicle->vehicle_brand)}}" name="vehicle_brand" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="vehicle_model" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Modelo</label>
                                <div class="mt-2">
                                    <input id="vehicle_model" value="{{old('vehicle_model', $vehicle->vehicle_model)}}" name="vehicle_model" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                </div>
                            </div>
                            <div class="sm:col-span-full">
                                <label for="origin" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Origen</label>
                                <div class="mt-2">
                                    <input id="origin" value="{{old('origin', $vehicle->origin)}}" name="origin" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-4 sm:px-6">
                    <div class="mt-6 flex items-center justify-end gap-x-6">
                        <a href="{{route('transportation-agencies.show', ['transportation_agency' => $agency])}}" class="text-sm/6 font-semibold text-gray-900 dark:text-gray-50">Cancelar</a>
                        <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Guardar</button>
                    </div>
                </div>
            </div>
        </form>
    </section>
</x-layout-admin>
