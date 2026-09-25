@section('title', 'Nueva ciudad')
@section('custom_script')
    <script type="module">
        $('#stte_ctr').autocomplete({
            minLength: 1,
            autoFocus: true,
            source: function( request, response ) {
                $.ajax({
                    url: "{{ route('autocomplete.countries') }}",
                    type: 'GET',
                    dataType: "json",
                    data: {
                        search: request.term
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            focus: function(event, ui) {
                event.preventDefault();
            },
            select: function(event, ui) {
                event.preventDefault();
                $('#stte_ctr').val(ui.item.label);
                $('#country_id').val(ui.item.value);
            },
            change: function(event, ui) {
                if($('#stte_ctr').val() === ""){
                    $('#country_id').val("");
                }
            }
        });
        $('#cty_stte').autocomplete({
            minLength: 1,
            autoFocus: true,
            source: function( request, response ) {
                $.ajax({
                    url: "{{ route('autocomplete.states') }}",
                    type: 'GET',
                    dataType: "json",
                    data: {
                        search: request.term,
                        country_id: $('#country_id').val()
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            focus: function(event, ui) {
                event.preventDefault();
            },
            select: function(event, ui) {
                event.preventDefault();
                $('#cty_stte').val(ui.item.label);
                $('#state_id').val(ui.item.value);
            },
            change: function(event, ui) {
                if($('#cty_stte').val() === ""){
                    $('#state_id').val("");
                }
            }
        });
    </script>
@endsection
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
                            $refs.ctyStte.value = '';
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
                            <label for="stte_ctr" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">P<span class="hidden">avoidautocomplete</span>aís</label>
                            <div class="mt-2">
                                <input id="stte_ctr" value="{{old('stte_ctr')}}" name="stte_ctr" placeholder="México" type="search" autocomplete="off" autofill="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                <input id="country_id" value="{{old('country_id')}}" name="country_id" type="hidden" />
                            </div>
                        </div>
                        <div class="sm:col-span-3">
                            <label for="cty_stte" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Es<span class="hidden">avoidautocomplete</span>tado</label>
                            <div class="mt-2">
                                <input id="cty_stte" x-ref="ctyStte" value="{{old('cty_stte')}}" x-bind:disabled="newState" name="cty_stte" x-bind:placeholder="newState ? 'Nuevo Estado' : 'Sonora'" type="search" autocomplete="off" autofill="off" class="block w-full rounded-md  px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" :class="{ 'bg-white': !newState, 'bg-gray-100 cursor-not-allowed': newState }">
                                <input id="state_id" x-ref="stateId" value="{{old('state_id')}}" x-bind:disabled="newState" name="state_id" type="hidden" />
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
