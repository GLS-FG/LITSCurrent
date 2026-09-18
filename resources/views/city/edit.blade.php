@section('title', 'Editar ciudad')
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
        <x-navigation.breadcrumbs :links="['Ciudades' => route('cities.index'), $city->name => route('cities.show', ['city' => $city]), 'Editar ciudad' => '#']" />
        <x-headings.without-action
            :title="'Editar ciudad'"
        />
        @if ($errors->any())
            <x-alerts.error :message="'Para editar la ciudad soluciona los siguientes errores:'" :errors="$errors" class="my-4" />
        @endif
        <form action="{{ route('cities.update', ['city' => $city]) }}" method="POST" class="mt-8 grid grid-cols-1 gap-x-8 gap-y-8 lg:grid-cols-1" autocomplete="off">
            @csrf
            @method('PUT')
            <div class="h-fit lg:col-span-2 divide-y divide-gray-200 rounded bg-white shadow-lits-card">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-semibold text-gray-900">Editar ciudad</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                        <div class="sm:col-span-full">
                            <label for="name" class="block text-sm/6 font-medium text-gray-900">Nombre</label>
                            <div class="mt-2">
                                <input id="name" value="{{old('name', $city->name)}}" maxlength="100" placeholder="Sonora" name="name" type="text" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                            </div>
                        </div>
                        <div class="sm:col-span-3">
                            <label for="stte_ctr" class="block text-sm/6 font-medium text-gray-900">P<span class="hidden">avoidautocomplete</span>aís</label>
                            <div class="mt-2">
                                <input id="stte_ctr" value="{{old('stte_ctr', $city->state->country->name)}}" name="stte_ctr" placeholder="México" type="search" autocomplete="off" autofill="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                <input id="country_id" value="{{old('country_id', $city->state->country_id)}}" name="country_id" type="hidden" />
                            </div>
                        </div>
                        <div class="sm:col-span-3">
                            <label for="cty_stte" class="block text-sm/6 font-medium text-gray-900">Es<span class="hidden">avoidautocomplete</span>tado</label>
                            <div class="mt-2">
                                <input id="cty_stte" value="{{old('cty_stte', $city->state->name)}}" name="cty_stte" placeholder="Sonora" type="search" autocomplete="off" autofill="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                <input id="state_id" value="{{old('state_id', $city->state_id)}}" name="state_id" type="hidden" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-4 sm:px-6">
                    <div class="mt-6 flex items-center justify-end gap-x-6">
                        <a href="{{route('cities.show', ['city' => $city])}}" class="text-sm/6 font-semibold text-gray-900">Cancelar</a>
                        <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Guardar</button>
                    </div>
                </div>
            </div>
        </form>
    </section>
</x-layout-admin>
