@section('title', 'Nueva ciudad')
@push('custom_script')
    @include('partials.live-validation')
    @include('partials.location-selects')
    <script type="module">
        initLocationSelects({ old: { country: @json(old('country_id')), state: @json(old('state_id')) } });
        attachLiveValidation('name', { required: true, max: 100 });
        attachLiveValidation('country_id', { required: true, requiredMessage: 'Selecciona un país.' });
        const newStateChecked = () => document.getElementById('new_state').checked;
        attachLiveValidation('state_id', { required: true, requiredMessage: 'Selecciona un estado.', when: () => !newStateChecked(), watch: ['new_state'] });
        attachLiveValidation('new_state_name', { required: true, max: 100, when: newStateChecked, watch: ['new_state'] });
        attachLiveValidation('new_state_short_name', { required: true, max: 50, when: newStateChecked, watch: ['new_state'] });
    </script>
@endpush
<x-layout-admin>
    <section>
        <x-navigation.breadcrumbs :links="['Ciudades' => route('cities.index'), 'Nueva ciudad' => '#']" />
        <x-headings.without-action
            :title="'Nueva ciudad'"
        />
        @if ($errors->any())
            <x-alerts.error :message="'Para crear la ciudad soluciona los siguientes errores:'" :errors="$errors" class="my-4" />
        @endif
        <form action="{{ route('cities.store') }}" method="POST" class="mt-8 grid grid-cols-1 gap-x-8 gap-y-8 lg:grid-cols-1" autocomplete="off">
            @csrf
            <div
                x-data="{
                    newState: {{old('new_state') == 'on' ? 'true' : 'false'}},
                    toggleNewState() {
                        this.newState = !this.newState;
                        if(this.newState == true){
                            $refs.stateId.value = '';
                        } else {
                            $refs.newStateName.value = '';
                            $refs.newStateShortName.value = '';
                        }
                    }
                }"
                class="h-fit lg:col-span-2 divide-y divide-gray-200 dark:divide-gray-700 rounded bg-white dark:bg-lits-blue-550 shadow-lits-card"
            >
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-50">Nueva ciudad</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                        <div class="sm:col-span-full">
                            <label for="name" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Nombre</label>
                            <div class="mt-2">
                                <input id="name" value="{{old('name')}}" maxlength="100" placeholder="Hermosillo" name="name" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                            </div>
                        </div>
                        <div class="sm:col-span-3">
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
                        <div class="sm:col-span-3">
                                <label for="state_id" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Estado</label>
                                <div class="mt-2">
                                    <select id="state_id" name="state_id" x-ref="stateId" x-bind:disabled="newState" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                        <option value="">Selecciona un estado</option>
                                    </select>
                                </div>
                            </div>
                        <div class="sm:col-span-full border-b border-gray-200 dark:border-lits-blue-450"></div>
                        <div class="sm:col-span-full">
                            <div class="flex gap-3">
                                <div class="flex h-6 shrink-0 items-center">
                                    <div class="group grid size-4 grid-cols-1">
                                        <input id="new_state" type="checkbox" name="new_state" @checked(old('new_state', false)) @change="toggleNewState" aria-describedby="new_state-description" class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-lits-blue-550 checked:border-indigo-600 checked:bg-indigo-600 indeterminate:border-indigo-600 indeterminate:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 forced-colors:appearance-auto" />
                                        <i class="fa-solid fa-check text-xs pointer-events-none col-start-1 row-start-1 self-center justify-self-center text-white group-has-disabled:text-gray-950/25 opacity-0 group-has-checked:opacity-100 group-has-indeterminate:opacity-100"></i>
                                    </div>
                                </div>
                                <div class="text-sm/6">
                                    <label for="new_state" class="font-medium text-gray-900 dark:text-gray-50">Quiero crear un nuevo estado</label>
                                    <p id="new_state-description" class="text-gray-500 dark:text-gray-400">Selecciona esta opción cuando quieras crear un nuevo estado.</p>
                                </div>
                            </div>
                        </div>
                        <div class="sm:col-span-4">
                            <label for="new_state_name" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Nuevo Estado Nombre</label>
                            <div class="mt-2">
                                <input id="new_state_name" x-ref="newStateName" value="{{old('new_state_name')}}" x-bind:disabled="!newState" maxlength="100" x-bind:placeholder="newState ? 'Sonora' : ''" name="new_state_name" type="text" autocomplete="off" class="block w-full rounded-md  px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" :class="{ 'bg-white': newState, 'bg-gray-100 cursor-not-allowed': !newState }">
                            </div>
                        </div>
                        <div class="sm:col-span-2">
                            <label for="new_state_short_name" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Nuevo Estado Abreviado</label>
                            <div class="mt-2">
                                <input id="new_state_short_name" x-ref="newStateShortName" value="{{old('new_state_short_name')}}" x-bind:disabled="!newState" maxlength="5" x-bind:placeholder="newState ? 'SON' : ''" name="new_state_short_name" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" :class="{ 'bg-white': newState, 'bg-gray-100 cursor-not-allowed': !newState }">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-4 sm:px-6">
                    <div class="mt-6 flex items-center justify-end gap-x-6">
                        <a href="{{ route('cities.index') }}" class="text-sm/6 font-semibold text-gray-900 dark:text-gray-50">Cancelar</a>
                        <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Crear ciudad</button>
                    </div>
                </div>
            </div>
        </form>
    </section>
</x-layout-admin>
