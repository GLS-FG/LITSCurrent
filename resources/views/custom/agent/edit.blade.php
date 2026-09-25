@section('title', 'Editar agente aduanal')
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
<x-layout-admin>
    <section>
        <x-navigation.breadcrumbs :links="['Agentes' => route('custom-agents.index'), 'Editar Agente' => '#']" />
        <x-headings.without-action
            :title="'Editar agente aduanal'"
        />
        @if ($errors->any())
            <x-alerts.error :message="'Para editar el agente aduanal soluciona los siguientes errores:'" :errors="$errors" class="my-4" />
        @endif
        <form action="{{ route('custom-agents.update', [ 'custom_agent' => $agent ]) }}" method="POST" class="mt-8">
            @csrf
            @method('PUT')
            <div class="h-fit lg:col-span-2 divide-y divide-gray-200 dark:divide-gray-700 rounded bg-white dark:bg-lits-blue-550 shadow-lits-card">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-50">Editar agente aduanal</h3>
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
                                        <input id="company_name" value="{{old('company_name', $agent->company_name)}}" name="company_name" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="sm:col-span-6">
                                    <label for="name" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Nombre</label>
                                    <div class="mt-2">
                                        <input id="name" value="{{old('name', $agent->name)}}" name="name" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="sm:col-span-6">
                                    <label for="last_name" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Apellido</label>
                                    <div class="mt-2">
                                        <input id="last_name" value="{{old('last_name', $agent->last_name)}}" name="last_name" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="sm:col-span-3">
                                    <label for="patent" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Patente</label>
                                    <div class="mt-2">
                                        <input id="patent" value="{{old('patent', $agent->patent)}}" name="patent" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="sm:col-span-3">
                                    <label for="phone1" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Teléfono</label>
                                    <div class="mt-2">
                                        <input id="phone1" value="{{old('phone1', $agent->phone1)}}" name="phone1" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-4 sm:px-6">
                    <div class="mt-6 flex items-center justify-end gap-x-6">
                        <a href="{{ route('custom-agents.show', ['custom_agent' => $agent]) }}" class="text-sm/6 font-semibold text-gray-900 dark:text-gray-50">Cancelar</a>
                        <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Guardar</button>
                    </div>
                </div>
            </div>
        </form>
    </section>
</x-layout-admin>
