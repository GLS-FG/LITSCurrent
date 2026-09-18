@section('title', 'Nueva unidad de transporte')
<x-layout-admin>
    <section>
        <x-navigation.breadcrumbs :links="['Transportistas' => route('transportation-agencies.index'), $agency->name => route('transportation-agencies.show', ['transportation_agency' => $agency]), 'Nueva unidad' => '#']" />
        <x-headings.without-action
            :title="'Nueva unidad de transporte'"
        />
        @if ($errors->any())
            <x-alerts.error :message="'Para crear el transportista soluciona los siguientes errores:'" :errors="$errors" class="my-4" />
        @endif
        <form action="{{ route('transportation-agencies.vehicles.store', ['transportation_agency' => $agency]) }}" method="POST" class="mt-8 grid grid-cols-1 gap-x-8 gap-y-8 lg:grid-cols-1" autocomplete="off">
            @csrf
            <div class="h-fit lg:col-span-2 divide-y divide-gray-200 rounded bg-white shadow-lits-card">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-semibold text-gray-900">Alta de unidad de transporte</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="pb-12">
                        <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                            <div class="sm:col-span-2">
                                <label for="eco_number" class="block text-sm/6 font-medium text-gray-900">Número Económico</label>
                                <div class="mt-2">
                                    <input id="eco_number" value="{{old('eco_number')}}" name="eco_number" type="text" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="plates" class="block text-sm/6 font-medium text-gray-900">Placas</label>
                                <div class="mt-2">
                                    <input id="plates" value="{{old('plates')}}" name="plates" type="text" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="vehicle_type" class="block text-sm/6 font-medium text-gray-900">Tipo</label>
                                <div class="mt-2">
                                    <input id="vehicle_type" value="{{old('vehicle_type')}}" name="vehicle_type" type="text" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="serial_number" class="block text-sm/6 font-medium text-gray-900">Número de serie</label>
                                <div class="mt-2">
                                    <input id="serial_number" value="{{old('serial_number')}}" name="serial_number" type="text" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="vehicle_brand" class="block text-sm/6 font-medium text-gray-900">Marca y línea</label>
                                <div class="mt-2">
                                    <input id="vehicle_brand" value="{{old('vehicle_brand')}}" name="vehicle_brand" type="text" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="vehicle_model" class="block text-sm/6 font-medium text-gray-900">Modelo</label>
                                <div class="mt-2">
                                    <input id="vehicle_model" value="{{old('vehicle_model')}}" name="vehicle_model" type="text" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                </div>
                            </div>
                            <div class="sm:col-span-full">
                                <label for="origin" class="block text-sm/6 font-medium text-gray-900">Origen</label>
                                <div class="mt-2">
                                    <input id="origin" value="{{old('origin')}}" name="origin" type="text" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-4 sm:px-6">
                    <div class="mt-6 flex items-center justify-end gap-x-6">
                        <a href="{{ route('transportation-agencies.index') }}" class="text-sm/6 font-semibold text-gray-900">Cancelar</a>
                        <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Crear unidad</button>
                    </div>
                </div>
            </div>
        </form>
    </section>
</x-layout-admin>
