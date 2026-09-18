@section('title', 'Editar dirección')
@section('custom_script')
    <script type="module">
        (g=>{var h,a,k,p="The Google Maps JavaScript API",c="google",l="importLibrary",q="__ib__",m=document,b=window;b=b[c]||(b[c]={});var d=b.maps||(b.maps={}),r=new Set,e=new URLSearchParams,u=()=>h||(h=new Promise(async(f,n)=>{await (a=m.createElement("script"));e.set("libraries",[...r]+"");for(k in g)e.set(k.replace(/[A-Z]/g,t=>"_"+t[0].toLowerCase()),g[k]);e.set("callback",c+".maps."+q);a.src=`https://maps.${c}apis.com/maps/api/js?`+e;d[q]=f;a.onerror=()=>h=n(Error(p+" could not load."));a.nonce=m.querySelector("script[nonce]")?.nonce||"";m.head.append(a)}));d[l]?console.warn(p+" only loads once. Ignoring:",g):d[l]=(f,...n)=>r.add(f)&&u().then(()=>d[l](f,...n))})({
            key: "{{config('services.google_maps.key')}}",
            v: "weekly",
        });
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
        async function initAutocomplete() {
            const { PlaceAutocompleteElement } = await google.maps.importLibrary("places");
            const autocompleteElement = new PlaceAutocompleteElement();
            autocompleteElement.style.colorScheme = 'light';
            document.getElementById("location-name-div").appendChild(autocompleteElement);
            autocompleteElement.addEventListener('gmp-select', async ({ placePrediction }) => {
                const place = placePrediction.toPlace();
                await place.fetchFields({ fields: ['displayName', 'formattedAddress', 'location', 'types'] });
                const hasTypes = ['political', 'route', 'locality', 'street_address'].some(item => place.types.includes(item));
                let locationName = place.displayName + ', ' + place.formattedAddress;
                if (hasTypes) {
                    locationName = place.formattedAddress;
                }
                $("#location_name").val(locationName);
                $("#latitude").val(place.location.lat().toFixed(6));
                $("#longitude").val(place.location.lng().toFixed(6));
            });
        }
        initAutocomplete();
    </script>
@endsection
<x-layout-admin>
    <section>
        <x-navigation.breadcrumbs :links="['Libreta de direcciones' => route('addresses.index'), 'Editar dirección' => '#']" />
        <x-headings.without-action
            :title="'Editar dirección'"
        />
        @if ($errors->any())
            <x-alerts.error :message="'Para editar la dirección los siguientes errores:'" :errors="$errors" class="my-4" />
        @endif
        <form action="{{ route('addresses.update', ['address' => $address]) }}" method="POST" class="mt-8">
            @csrf
            @method('PUT')
            <div class="h-fit lg:col-span-2 divide-y divide-gray-200 rounded bg-white shadow-lits-card">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-semibold text-gray-900">Editar dirección</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="space-y-12">
                        <div class="border-b border-gray-900/10 pb-12">
                            <h2 class="text-base/7 font-semibold text-gray-900">Detalles del contacto</h2>
                            <p class="mt-1 text-sm/6 text-gray-600">Llena la información del contacto para poder buscar las direcciones con esta información al llenar los embarques.</p>
                            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 lg:grid-cols-12">
                                <div class="sm:col-span-3 lg:col-span-6">
                                    <label for="name" class="block text-sm/6 font-medium text-gray-900">Nombre de la dirección</label>
                                    <div class="mt-2">
                                        <input id="name" value="{{old('name', $address->name)}}" name="name" type="text" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="sm:col-span-3 lg:col-span-6">
                                    <label for="trade_name" class="block text-sm/6 font-medium text-gray-900">Nombre comercial</label>
                                    <div class="mt-2">
                                        <input id="trade_name" value="{{old('trade_name', $address->trade_name)}}" name="trade_name" type="text" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="sm:col-span-3 lg:col-span-5">
                                    <label for="contact_name" class="block text-sm/6 font-medium text-gray-900">Nombre de la persona de contacto</label>
                                    <div class="mt-2">
                                        <input id="contact_name" value="{{old('contact_name', $address->contact_name)}}" name="contact_name" type="text" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="sm:col-span-3 lg:col-span-4">
                                    <label for="email" class="block text-sm/6 font-medium text-gray-900">Email</label>
                                    <div class="mt-2">
                                        <input id="email" value="{{old('email', $address->email)}}" name="email" type="email" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="sm:col-span-3 lg:col-span-2">
                                    <label for="phone" class="block text-sm/6 font-medium text-gray-900">Teléfono</label>
                                    <div class="mt-2">
                                        <input id="phone" value="{{old('phone', $address->phone)}}" name="phone" type="text" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="pb-12">
                            <h2 class="text-base/7 font-semibold text-gray-900">Detalles de la dirección</h2>
                            <p class="mt-1 text-sm/6 text-gray-600">Llena la información de la dirección, esta se prellenará en los embarques.</p>
                            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 lg:grid-cols-12">
                                <div class="sm:col-span-2 lg:col-span-4">
                                    <label for="clt_crt" class="block text-sm/6 font-medium text-gray-900">P<span class="hidden">avoidautocomplete</span>aís</label>
                                    <div class="mt-2">
                                        <input id="clt_crt" value="{{old('clt_crt', $address->country->name)}}" name="clt_crt" type="search" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                        <input id="country_id" value="{{old('country_id', $address->country_id)}}" name="country_id" type="hidden" />
                                    </div>
                                </div>

                                <div class="sm:col-span-2 lg:col-span-4">
                                    <label for="clt_stt" class="block text-sm/6 font-medium text-gray-900">Es<span class="hidden">avoidautocomplete</span>tado</label>
                                    <div class="mt-2">
                                        <input id="clt_stt" value="{{old('clt_stt', $address->state->name)}}" name="clt_stt" type="search" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                        <input id="state_id" value="{{old('state_id', $address->state_id)}}" name="state_id" type="hidden" />
                                    </div>
                                </div>

                                <div class="sm:col-span-2 lg:col-span-4">
                                    <label for="clt_cit" class="block text-sm/6 font-medium text-gray-900">Ci<span class="hidden">avoidautocomplete</span>udad</label>
                                    <div class="mt-2">
                                        <input id="clt_cit" value="{{old('clt_cit', $address->city->name)}}" name="clt_cit" type="search" autocomplete="nope" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                        <input id="city_id" value="{{old('city_id', $address->city_id)}}" name="city_id" type="hidden" />
                                    </div>
                                </div>

                                <div class="sm:col-span-full">
                                    <label for="address" class="block text-sm/6 font-medium text-gray-900">Calle y número</label>
                                    <div class="mt-2">
                                        <input id="address" value="{{old('address', $address->address)}}" name="address" type="text" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>

                                <div class="sm:col-span-3 lg:col-span-6">
                                    <label for="neighborhood" class="block text-sm/6 font-medium text-gray-900">Colonia</label>
                                    <div class="mt-2">
                                        <input id="neighborhood" value="{{old('neighborhood', $address->neighborhood)}}" name="neighborhood" type="text" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>

                                <div class="sm:col-span-3 lg:col-span-4">
                                    <label for="postal_code" class="block text-sm/6 font-medium text-gray-900">Código Postal</label>
                                    <div class="mt-2">
                                        <input id="postal_code" value="{{old('postal_code', $address->postal_code)}}" name="postal_code" type="text" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>

                                <div class="sm:col-span-full">
                                    <label for="name" class="block text-sm/6 font-medium text-gray-900">Ubicación en maps</label>
                                    <div class="mt-2" id="location-name-div">
                                    </div>
                                </div>

                                <div class="sm:col-span-full lg:col-span-8">
                                    <label for="location_name" class="block text-sm/6 font-medium text-gray-900">Nombre de Ubicación</label>
                                    <div class="mt-2">
                                        <input id="location_name" value="{{old('location_name', $address->location_name)}}" name="location_name" type="text" readonly autocomplete="off" class="block w-full rounded-md bg-gray-100 cursor-not-allowed px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 sm:text-sm/6">
                                    </div>
                                </div>

                                <div class="sm:col-span-3 lg:col-span-2">
                                    <label for="latitude" class="block text-sm/6 font-medium text-gray-900">Latitud</label>
                                    <div class="mt-2">
                                        <input id="latitude" value="{{old('latitude', $address->latitude)}}" name="latitude" type="text" readonly autocomplete="off" class="block w-full rounded-md bg-gray-100 cursor-not-allowed px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 sm:text-sm/6">
                                    </div>
                                </div>

                                <div class="sm:col-span-3 lg:col-span-2">
                                    <label for="longitude" class="block text-sm/6 font-medium text-gray-900">Latitud</label>
                                    <div class="mt-2">
                                        <input id="longitude" value="{{old('longitude', $address->longitude)}}" name="longitude" type="text" readonly autocomplete="off" class="block w-full rounded-md bg-gray-100 cursor-not-allowed px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 sm:text-sm/6">
                                    </div>
                                </div>

                                <div class="sm:col-span-full lg:col-span-8">
                                    <label for="link" class="block text-sm/6 font-medium text-gray-900">Link de compartir ubicación en maps</label>
                                    <div class="mt-2">
                                        <input id="link" value="{{old('link', $address->link)}}" name="link" type="text" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-4 sm:px-6">
                    <div class="mt-6 flex items-center justify-end gap-x-6">
                        <a href="{{ route('addresses.show', ['address' => $address]) }}" class="text-sm/6 font-semibold text-gray-900">Cancelar</a>
                        <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Guardar</button>
                    </div>
                </div>
            </div>
        </form>
    </section>
</x-layout-admin>
