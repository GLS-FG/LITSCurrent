@section('title', 'Nueva aduana')
@push('custom_script')
    @include('partials.live-validation')
    @include('partials.location-selects')
    <script type="module">
        initLocationSelects({ old: { country: @json(old('country_id')), state: @json(old('state_id')), city: @json(old('city_id')) } });
        attachLiveValidation('denomination', { required: true, max: 150 });
        attachLiveValidation('code', { required: true, max: 4 });
        attachLiveValidation('country_id', { required: true, requiredMessage: 'Selecciona un país.' });
        attachLiveValidation('state_id', { required: true, requiredMessage: 'Selecciona un estado.' });
        attachLiveValidation('city_id', { required: true, requiredMessage: 'Selecciona una ciudad.' });
    </script>
@endpush
<x-layout-admin>
    <section>
        <x-navigation.breadcrumbs :links="['Aduanas' => route('customs.index'), 'Nueva aduana' => '#']" />
        <x-headings.without-action
            :title="'Nueva aduana'"
        />
        @if ($errors->any())
            <x-alerts.error :message="'Para crear la aduana soluciona los siguientes errores:'" :errors="$errors" class="my-4" />
        @endif
        <form action="{{ route('customs.store') }}" method="POST" class="mt-8 grid grid-cols-1 gap-x-8 gap-y-8 lg:grid-cols-1" autocomplete="off">
            @csrf
            <div class="h-fit lg:col-span-2 divide-y divide-gray-200 dark:divide-gray-700 rounded bg-white dark:bg-lits-blue-550 shadow-lits-card">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-50">Nueva aduana</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="space-y-12">
                        <div class="border-b border-gray-900/10 dark:border-white/10 pb-12">
                            <h2 class="text-base/7 font-semibold text-gray-900 dark:text-gray-50">Aduana</h2>
                            <p class="mt-1 text-sm/6 text-gray-600 dark:text-gray-400">Ingresa la información necesaria de la aduana.</p>
                            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                <div class="sm:col-span-4">
                                    <label for="denomination" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Aduana</label>
                                    <div class="mt-2">
                                        <input id="denomination" value="{{old('denomination')}}" placeholder="Denominación de la aduana" name="denomination" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="code" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Sección</label>
                                    <div class="mt-2">
                                        <input id="code" value="{{old('code')}}" maxlength="4" placeholder="00" name="code" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pb-12">
                            <h2 class="text-base/7 font-semibold text-gray-900 dark:text-gray-50">Ubic<span class="hidden">avoidautocomplete</span>ación</h2>
                            <p class="mt-1 text-sm/6 text-gray-600 dark:text-gray-400">Llena la información de la ubica<span class="hidden">avoidautocomplete</span>ción de la aduana.</p>
                            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                <div class="sm:col-span-2">
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

                                <div class="sm:col-span-2">
                                        <label for="state_id" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Estado</label>
                                        <div class="mt-2">
                                            <select id="state_id" name="state_id" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                <option value="">Selecciona un estado</option>
                                            </select>
                                        </div>
                                    </div>

                                <div class="sm:col-span-2">
                                        <label for="city_id" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Ciudad</label>
                                        <div class="mt-2">
                                            <select id="city_id" name="city_id" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                <option value="">Selecciona una ciudad</option>
                                            </select>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-4 sm:px-6">
                    <div class="mt-6 flex items-center justify-end gap-x-6">
                        <a href="{{ route('customs.index') }}" class="text-sm/6 font-semibold text-gray-900 dark:text-gray-50">Cancelar</a>
                        <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Crear aduana</button>
                    </div>
                </div>
            </div>
        </form>
    </section>
</x-layout-admin>
