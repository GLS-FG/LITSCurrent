@section('title', 'Nueva aduana')
@section('custom_script')
    <script type="module">
        $('#cstm_crt').autocomplete({
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
                $('#cstm_crt').val(ui.item.label);
                $('#country_id').val(ui.item.value);
                $('#cstm_stt').val('');
                $('#state_id').val('');
                $('#cstm_cit').val('');
                $('#city_id').val('');
            },
            change: function(event, ui) {
                if($('#cstm_crt').val() === ""){
                    $('#country_id').val("");
                    $('#cstm_stt').val('');
                    $('#state_id').val('');
                    $('#cstm_cit').val('');
                    $('#city_id').val('');
                }
            }
        });
        $('#cstm_stt').autocomplete({
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
                $('#cstm_stt').val(ui.item.label);
                $('#state_id').val(ui.item.value);
                $('#cstm_cit').val('');
                $('#city_id').val('');
            },
            change: function(event, ui) {
                if($('#cstm_stt').val() === ""){
                    $('#state_id').val("");
                    $('#cstm_cit').val('');
                    $('#city_id').val('');
                }
            }
        });
        $('#cstm_cit').autocomplete({
            minLength: 1,
            autoFocus: true,
            source: function( request, response ) {
                $.ajax({
                    url: "{{ route('autocomplete.cities') }}",
                    type: 'GET',
                    dataType: "json",
                    data: {
                        search: request.term,
                        state_id: $('#state_id').val()
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
                $('#cstm_cit').val(ui.item.label);
                $('#city_id').val(ui.item.value);
            },
            change: function(event, ui) {
                if($('#cstm_cit').val() === ""){
                    $('#city_id').val("");
                }
            }
        });
    </script>
@endsection
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
                                    <label for="cstm_crt" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">P<span class="hidden">avoidautocomplete</span>aís</label>
                                    <div class="mt-2">
                                        <input id="cstm_crt" value="{{old('cstm_crt')}}" name="cstm_crt" type="search" autocomplete="off" autofill="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                        <input id="country_id" value="{{old('country_id')}}" name="country_id" type="hidden" />
                                    </div>
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="cstm_stt" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Es<span class="hidden">avoidautocomplete</span>tado</label>
                                    <div class="mt-2">
                                        <input id="cstm_stt" value="{{old('cstm_stt')}}" name="cstm_stt" type="search" autocomplete="off" autofill="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                        <input id="state_id" value="{{old('state_id')}}" name="state_id" type="hidden" />
                                    </div>
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="cstm_cit" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Ci<span class="hidden">avoidautocomplete</span>udad</label>
                                    <div class="mt-2">
                                        <input id="cstm_cit" value="{{old('cstm_cit')}}" name="cstm_cit" type="search" autocomplete="nope" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                        <input id="city_id" value="{{old('city_id')}}" name="city_id" type="hidden" />
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
