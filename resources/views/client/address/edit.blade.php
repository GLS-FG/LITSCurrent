@section('title', 'Editar dirección del cliente')
@section('custom_script')
    <script type="module">
        $('#clt_crt').autocomplete({
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
            focus: function(event) {
                event.preventDefault();
            },
            select: function(event, ui) {
                event.preventDefault();
                $('#clt_crt').val(ui.item.label);
                $('#country_id').val(ui.item.value);
                $('#clt_stt').val('');
                $('#state_id').val('');
                $('#clt_cit').val('');
                $('#city_id').val('');
            },
            change: function() {
                if($('#clt_crt').val() === ""){
                    $('#country_id').val("");
                    $('#clt_stt').val('');
                    $('#state_id').val('');
                    $('#clt_cit').val('');
                    $('#city_id').val('');
                }
            }
        });
        $('#clt_stt').autocomplete({
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
            focus: function(event) {
                event.preventDefault();
            },
            select: function(event, ui) {
                event.preventDefault();
                $('#clt_stt').val(ui.item.label);
                $('#state_id').val(ui.item.value);
                $('#clt_cit').val('');
                $('#city_id').val('');
            },
            change: function() {
                if($('#clt_stt').val() === ""){
                    $('#state_id').val("");
                    $('#clt_cit').val('');
                    $('#city_id').val('');
                }
            }
        });
        $('#clt_cit').autocomplete({
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
            focus: function(event) {
                event.preventDefault();
            },
            select: function(event, ui) {
                event.preventDefault();
                $('#clt_cit').val(ui.item.label);
                $('#city_id').val(ui.item.value);
            },
            change: function() {
                if($('#clt_cit').val() === ""){
                    $('#city_id').val("");
                }
            }
        });
    </script>
@endsection
<x-layout-app>
    <section>
        <x-navigation.breadcrumbs :links="['Clientes' => route('clients.index'), $client->company_name => route('clients.show', [ 'client' => $client ]), 'Editar dirección' => '#']" />
        <x-headings.without-action
            :title="'Editar dirección del cliente'"
        />
        @if ($errors->any())
            <x-alerts.error :message="'Para crear el cliente los siguientes errores:'" :errors="$errors" class="my-4" />
        @endif
        <form action="{{ route('clients.addresses.update', [ 'client' => $client, 'address' => $address ]) }}" method="POST" class="mt-8">
            @csrf
            @method('PUT')
            <div class="h-fit lg:col-span-2 divide-y divide-gray-200 dark:divide-gray-700 rounded bg-white dark:bg-lits-blue-550 shadow-lits-card">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-50">Editar dirección del cliente</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="space-y-12">
                        <div class="pb-12">
                            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 lg:grid-cols-12">
                                <div class="sm:col-span-2 lg:col-span-4">
                                    <label for="clt_crt" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">P<span class="hidden">avoidautocomplete</span>aís</label>
                                    <div class="mt-2">
                                        <input id="clt_crt" value="{{old('clt_crt', $address->country->name)}}" name="clt_crt" type="search" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                        <input id="country_id" value="{{old('country_id', $address->country_id)}}" name="country_id" type="hidden" />
                                    </div>
                                </div>

                                <div class="sm:col-span-2 lg:col-span-4">
                                    <label for="clt_stt" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Es<span class="hidden">avoidautocomplete</span>tado</label>
                                    <div class="mt-2">
                                        <input id="clt_stt" value="{{old('clt_stt', $address->state->name)}}" name="clt_stt" type="search" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                        <input id="state_id" value="{{old('state_id', $address->state_id)}}" name="state_id" type="hidden" />
                                    </div>
                                </div>

                                <div class="sm:col-span-2 lg:col-span-4">
                                    <label for="clt_cit" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Ci<span class="hidden">avoidautocomplete</span>udad</label>
                                    <div class="mt-2">
                                        <input id="clt_cit" value="{{old('clt_cit', $address->city->name)}}" name="clt_cit" type="search" autocomplete="nope" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                        <input id="city_id" value="{{old('city_id', $address->city_id)}}" name="city_id" type="hidden" />
                                    </div>
                                </div>

                                <div class="sm:col-span-full">
                                    <label for="street_name" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Calle</label>
                                    <div class="mt-2">
                                        <input id="street_name" value="{{old('street_name', $address->street_name)}}" name="street_name" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>

                                <div class="sm:col-span-2 lg:col-span-4">
                                    <label for="street_no" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Número</label>
                                    <div class="mt-2">
                                        <input id="street_no" value="{{old('street_no', $address->street_no)}}" name="street_no" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>

                                <div class="sm:col-span-2 lg:col-span-4">
                                    <label for="neighborhood" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Colonia</label>
                                    <div class="mt-2">
                                        <input id="neighborhood" value="{{old('neighborhood', $address->neighborhood)}}" name="neighborhood" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>

                                <div class="sm:col-span-2 lg:col-span-4">
                                    <label for="postal_code" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Código Postal</label>
                                    <div class="mt-2">
                                        <input id="postal_code" value="{{old('postal_code', $address->postal_code)}}" name="postal_code" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-4 sm:px-6">
                    <div class="mt-6 flex items-center justify-end gap-x-6">
                        <a href="{{ route('clients.show', [ 'client' => $client ]) }}" class="text-sm/6 font-semibold text-gray-900 dark:text-gray-50">Cancelar</a>
                        <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Guardar</button>
                    </div>
                </div>
            </div>
        </form>
    </section>
</x-layout-app>
