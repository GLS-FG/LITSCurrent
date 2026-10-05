@section('title', 'Nuevo agente aduanal')
@push('custom_script')
    @include('partials.live-validation')
    @include('partials.location-selects')
    <script type="module">
        initLocationSelects({ old: { country: @json(old('country_id')), state: @json(old('state_id')), city: @json(old('city_id')) } });
        attachLiveValidation('company_name', { required: true, min: 3, max: 200 });
        attachLiveValidation('name', { required: true, min: 3 });
        attachLiveValidation('last_name', { required: true });
        attachLiveValidation('patent', { required: true });
        attachLiveValidation('phone1', { required: true, numeric: true, min: 10 });
        attachLiveValidation('country_id', { required: true, requiredMessage: 'Selecciona un país.' });
        attachLiveValidation('state_id', { required: true, requiredMessage: 'Selecciona un estado.' });
        attachLiveValidation('city_id', { required: true, requiredMessage: 'Selecciona una ciudad.' });
        attachLiveValidation('street_name', { required: true });
        attachLiveValidation('street_no', { required: true, max: 50 });
        attachLiveValidation('neighborhood', { required: true, max: 100 });
        attachLiveValidation('postal_code', { required: true, numeric: true, min: 5 });
    </script>
@endpush
<x-layout-admin>
    <section>
        <x-navigation.breadcrumbs :links="['Agentes' => route('custom-agents.index'), 'Nuevo Agente' => '#']" />
        <x-headings.without-action
            :title="'Nuevo agente aduanal'"
        />
        @if ($errors->any())
            <x-alerts.error :message="'Para crear el agente aduanal soluciona los siguientes errores:'" :errors="$errors" class="my-4" />
        @endif
        <form action="{{ route('custom-agents.store') }}" method="POST" class="mt-8">
            @csrf
            <div class="h-fit lg:col-span-2 divide-y divide-gray-200 dark:divide-gray-700 rounded bg-white dark:bg-lits-blue-550 shadow-lits-card">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-50">Nuevo agente aduanal</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="space-y-12">
                        <div class="border-b border-gray-900/10 dark:border-white/10 pb-12">
                            <h2 class="text-base/7 font-semibold text-gray-900 dark:text-gray-50">Datos de contacto</h2>
                            <p class="mt-1 text-sm/6 text-gray-600 dark:text-gray-400">Llena los datos de contacto del agente como su nombre, patente y teléfono.</p>
                            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 lg:grid-cols-12">
                                <div class="sm:col-span-full">
                                    <label for="company_name" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Razón social</label>
                                    <div class="mt-2">
                                        <input id="company_name" value="{{old('company_name')}}" name="company_name" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="sm:col-span-6">
                                    <label for="name" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Nombre</label>
                                    <div class="mt-2">
                                        <input id="name" value="{{old('name')}}" name="name" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="sm:col-span-6">
                                    <label for="last_name" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Apellido</label>
                                    <div class="mt-2">
                                        <input id="last_name" value="{{old('last_name')}}" name="last_name" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="sm:col-span-3">
                                    <label for="patent" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Patente</label>
                                    <div class="mt-2">
                                        <input id="patent" value="{{old('patent')}}" name="patent" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="sm:col-span-3">
                                    <label for="phone1" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Teléfono</label>
                                    <div class="mt-2">
                                        <input id="phone1" value="{{old('phone1')}}" name="phone1" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="border-b border-gray-900/10 dark:border-white/10 pb-12">
                            <h2 class="text-base/7 font-semibold text-gray-900 dark:text-gray-50">Dirección</h2>
                            <p class="mt-1 text-sm/6 text-gray-600 dark:text-gray-400">Llena los datos de la dirección del agente.</p>
                            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 lg:grid-cols-12">
                                <div class="sm:col-span-2 lg:col-span-4">
                                        <label for="country_id" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">País</label>
                                        <div class="mt-2">
                                            <select id="country_id" name="country_id" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                <option value="">Selecciona un país</option>
                                                @foreach($countries as $country)
                                                    <option value="{{ $country->id }}" @selected(old('country_id') == $country->id)>{{ $country->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                <div class="sm:col-span-2 lg:col-span-4">
                                        <label for="state_id" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Estado</label>
                                        <div class="mt-2">
                                            <select id="state_id" name="state_id" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                <option value="">Selecciona un estado</option>
                                            </select>
                                        </div>
                                    </div>

                                <div class="sm:col-span-2 lg:col-span-4">
                                        <label for="city_id" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Ciudad</label>
                                        <div class="mt-2">
                                            <select id="city_id" name="city_id" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                <option value="">Selecciona una ciudad</option>
                                            </select>
                                        </div>
                                    </div>

                                <div class="sm:col-span-full">
                                    <label for="street_name" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Calle</label>
                                    <div class="mt-2">
                                        <input id="street_name" value="{{old('street_name')}}" name="street_name" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>

                                <div class="sm:col-span-2 lg:col-span-4">
                                    <label for="street_no" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Número</label>
                                    <div class="mt-2">
                                        <input id="street_no" value="{{old('street_no')}}" name="street_no" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>

                                <div class="sm:col-span-2 lg:col-span-4">
                                    <label for="neighborhood" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Colonia</label>
                                    <div class="mt-2">
                                        <input id="neighborhood" value="{{old('neighborhood')}}" name="neighborhood" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>

                                <div class="sm:col-span-2 lg:col-span-4">
                                    <label for="postal_code" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Código Postal</label>
                                    <div class="mt-2">
                                        <input id="postal_code" value="{{old('postal_code')}}" name="postal_code" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-4 sm:px-6">
                    <div class="mt-6 flex items-center justify-end gap-x-6">
                        <a href="{{ route('custom-agents.index') }}" class="text-sm/6 font-semibold text-gray-900 dark:text-gray-50">Cancelar</a>
                        <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Crear agente</button>
                    </div>
                </div>
            </div>
        </form>
    </section>
</x-layout-admin>
