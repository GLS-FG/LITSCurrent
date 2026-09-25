@section('title', 'Editar dirección')
@section('custom_script')
    <script type="module">
        (g=>{var h,a,k,p="The Google Maps JavaScript API",c="google",l="importLibrary",q="__ib__",m=document,b=window;b=b[c]||(b[c]={});var d=b.maps||(b.maps={}),r=new Set,e=new URLSearchParams,u=()=>h||(h=new Promise(async(f,n)=>{await (a=m.createElement("script"));e.set("libraries",[...r]+"");for(k in g)e.set(k.replace(/[A-Z]/g,t=>"_"+t[0].toLowerCase()),g[k]);e.set("callback",c+".maps."+q);a.src=`https://maps.${c}apis.com/maps/api/js?`+e;d[q]=f;a.onerror=()=>h=n(Error(p+" could not load."));a.nonce=m.querySelector("script[nonce]")?.nonce||"";m.head.append(a)}));d[l]?console.warn(p+" only loads once. Ignoring:",g):d[l]=(f,...n)=>r.add(f)&&u().then(()=>d[l](f,...n))})({
            key: "{{config('services.google_maps.key')}}",
            v: "weekly",
        });
        const countrySelect = document.getElementById('country_id');
        const stateSelect = document.getElementById('state_id');
        const citySelect = document.getElementById('city_id');

        function resetSelect(select, placeholder) {
            select.innerHTML = '';
            const option = document.createElement('option');
            option.value = '';
            option.textContent = placeholder;
            select.appendChild(option);
        }

        function populateSelect(select, items, selectedId) {
            items.forEach(item => {
                const option = document.createElement('option');
                option.value = item.value;
                option.textContent = item.label;
                if (selectedId && String(item.value) === String(selectedId)) {
                    option.selected = true;
                }
                select.appendChild(option);
            });
        }

        function loadStates(countryId, selectedStateId = null, selectedCityId = null) {
            resetSelect(stateSelect, 'Selecciona un estado');
            resetSelect(citySelect, 'Selecciona una ciudad');
            if (!countryId) return;
            fetch(`{{ route('autocomplete.states') }}?all=1&country_id=${countryId}`)
                .then(res => res.json())
                .then(data => {
                    populateSelect(stateSelect, data, selectedStateId);
                    if (selectedStateId) {
                        loadCities(selectedStateId, selectedCityId);
                    }
                });
        }

        function loadCities(stateId, selectedCityId = null) {
            resetSelect(citySelect, 'Selecciona una ciudad');
            if (!stateId) return;
            fetch(`{{ route('autocomplete.cities') }}?all=1&state_id=${stateId}`)
                .then(res => res.json())
                .then(data => populateSelect(citySelect, data, selectedCityId));
        }

        countrySelect.addEventListener('change', () => loadStates(countrySelect.value));
        stateSelect.addEventListener('change', () => loadCities(stateSelect.value));

        const oldCountryId = @json(old('country_id'));
        const oldStateId = @json(old('state_id'));
        const oldCityId = @json(old('city_id'));
        const addressCountryId = @json($address->country_id);
        const addressStateId = @json($address->state_id);
        if (oldCountryId && String(oldCountryId) !== String(addressCountryId)) {
            loadStates(oldCountryId, oldStateId, oldCityId);
        } else if (oldStateId && String(oldStateId) !== String(addressStateId)) {
            loadCities(oldStateId, oldCityId);
        }

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

        function attachLiveValidation(id, rules) {
            const input = document.getElementById(id);
            if (!input) return;
            let errorEl = document.getElementById(id + '-error');
            if (!errorEl) {
                errorEl = document.createElement('p');
                errorEl.id = id + '-error';
                errorEl.className = 'mt-1 text-sm text-red-500 dark:text-red-400 hidden';
                input.insertAdjacentElement('afterend', errorEl);
            }
            const check = () => {
                const value = input.value.trim();
                let message = '';
                if (value.length > 0) {
                    if (rules.numeric && !/^\d+$/.test(value)) {
                        message = 'Solo se permiten números.';
                    } else if (rules.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                        message = 'Ingresa un email válido.';
                    } else if (rules.regex && !rules.regex.test(value)) {
                        message = rules.regexMessage || 'Contiene caracteres no permitidos.';
                    } else if (rules.min && value.length < rules.min) {
                        message = `Debe tener al menos ${rules.min} caracteres.`;
                    } else if (rules.max && value.length > rules.max) {
                        message = `Debe tener máximo ${rules.max} caracteres.`;
                    }
                } else if (rules.required) {
                    message = 'Este campo es obligatorio.';
                }
                if (message) {
                    errorEl.textContent = message;
                    errorEl.classList.remove('hidden');
                    input.classList.add('outline-red-500', 'dark:outline-red-500');
                } else {
                    errorEl.classList.add('hidden');
                    input.classList.remove('outline-red-500', 'dark:outline-red-500');
                }
            };
            input.addEventListener('input', check);
        }

        attachLiveValidation('contact_name', { required: true, min: 3 });
        attachLiveValidation('name', { required: true, min: 3, max: 256 });
        attachLiveValidation('trade_name', { required: true, min: 3, max: 256 });
        attachLiveValidation('email', { email: true });
        attachLiveValidation('phone', { numeric: true, min: 10 });
        attachLiveValidation('address', { required: true, regex: /^[\p{L}\p{N}\s.,\/#-]+$/u, regexMessage: 'Solo se permiten letras, números, espacios y los caracteres . , - / #' });
        attachLiveValidation('neighborhood', { min: 3, max: 100 });
        attachLiveValidation('postal_code', { min: 3, max: 15 });
    </script>
@endsection
<x-layout-admin>
    <section>
        <x-navigation.breadcrumbs :links="['Libreta de direcciones' => route('addresses.index'), 'Editar dirección' => '#']" />
        <x-headings.without-action
            :title="'Editar dirección'"
        />
        <form action="{{ route('addresses.update', ['address' => $address]) }}" method="POST" class="mt-8">
            @csrf
            @method('PUT')
            <div class="h-fit lg:col-span-2 divide-y divide-gray-200 dark:divide-gray-700 rounded bg-white dark:bg-lits-blue-550 shadow-lits-card">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-50">Editar dirección</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="space-y-12">
                        <div class="border-b border-gray-900/10 dark:border-white/10 pb-12">
                            <h2 class="text-base/7 font-semibold text-gray-900 dark:text-gray-50">Detalles del contacto</h2>
                            <p class="mt-1 text-sm/6 text-gray-600 dark:text-gray-400">Llena la información del contacto para poder buscar las direcciones con esta información al llenar los embarques.</p>
                            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 lg:grid-cols-12">
                                <div class="sm:col-span-3 lg:col-span-6">
                                    <label for="name" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Nombre de la dirección</label>
                                    <div class="mt-2">
                                        <input id="name" value="{{old('name', $address->name)}}" name="name" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 @error('name') outline-red-500 dark:outline-red-500 @else outline-gray-300 dark:outline-gray-600 @enderror placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                        <p id="name-error" class="mt-1 text-sm text-red-500 dark:text-red-400 @error('name') @else hidden @enderror">@error('name'){{ $message }}@enderror</p>
                                    </div>
                                </div>
                                <div class="sm:col-span-3 lg:col-span-6">
                                    <label for="trade_name" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Nombre comercial</label>
                                    <div class="mt-2">
                                        <input id="trade_name" value="{{old('trade_name', $address->trade_name)}}" name="trade_name" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 @error('trade_name') outline-red-500 dark:outline-red-500 @else outline-gray-300 dark:outline-gray-600 @enderror placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                        <p id="trade_name-error" class="mt-1 text-sm text-red-500 dark:text-red-400 @error('trade_name') @else hidden @enderror">@error('trade_name'){{ $message }}@enderror</p>
                                    </div>
                                </div>
                                <div class="sm:col-span-3 lg:col-span-5">
                                    <label for="contact_name" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Nombre de la persona de contacto</label>
                                    <div class="mt-2">
                                        <input id="contact_name" value="{{old('contact_name', $address->contact_name)}}" name="contact_name" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 @error('contact_name') outline-red-500 dark:outline-red-500 @else outline-gray-300 dark:outline-gray-600 @enderror placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                        <p id="contact_name-error" class="mt-1 text-sm text-red-500 dark:text-red-400 @error('contact_name') @else hidden @enderror">@error('contact_name'){{ $message }}@enderror</p>
                                    </div>
                                </div>
                                <div class="sm:col-span-3 lg:col-span-4">
                                    <label for="email" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Email</label>
                                    <div class="mt-2">
                                        <input id="email" value="{{old('email', $address->email)}}" name="email" type="email" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 @error('email') outline-red-500 dark:outline-red-500 @else outline-gray-300 dark:outline-gray-600 @enderror placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                        <p id="email-error" class="mt-1 text-sm text-red-500 dark:text-red-400 @error('email') @else hidden @enderror">@error('email'){{ $message }}@enderror</p>
                                    </div>
                                </div>
                                <div class="sm:col-span-3 lg:col-span-2">
                                    <label for="phone" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Teléfono</label>
                                    <div class="mt-2">
                                        <input id="phone" value="{{old('phone', $address->phone)}}" name="phone" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 @error('phone') outline-red-500 dark:outline-red-500 @else outline-gray-300 dark:outline-gray-600 @enderror placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                        <p id="phone-error" class="mt-1 text-sm text-red-500 dark:text-red-400 @error('phone') @else hidden @enderror">@error('phone'){{ $message }}@enderror</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="pb-12">
                            <h2 class="text-base/7 font-semibold text-gray-900 dark:text-gray-50">Detalles de la dirección</h2>
                            <p class="mt-1 text-sm/6 text-gray-600 dark:text-gray-400">Llena la información de la dirección, esta se prellenará en los embarques.</p>
                            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 lg:grid-cols-12">
                                <div class="sm:col-span-2 lg:col-span-4">
                                    <label for="country_id" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">País</label>
                                    <div class="mt-2">
                                        <select id="country_id" name="country_id" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 @error('country_id') outline-red-500 dark:outline-red-500 @else outline-gray-300 dark:outline-gray-600 @enderror focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                            <option value="">Selecciona un país</option>
                                            @foreach($countries as $country)
                                                <option value="{{ $country->id }}" @selected(old('country_id', $address->country_id) == $country->id)>{{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                        <p class="mt-1 text-sm text-red-500 dark:text-red-400">@error('country_id'){{ $message }}@enderror</p>
                                    </div>
                                </div>

                                <div class="sm:col-span-2 lg:col-span-4">
                                    <label for="state_id" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Estado</label>
                                    <div class="mt-2">
                                        <select id="state_id" name="state_id" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 @error('state_id') outline-red-500 dark:outline-red-500 @else outline-gray-300 dark:outline-gray-600 @enderror focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                            <option value="">Selecciona un estado</option>
                                            @foreach($states as $state)
                                                <option value="{{ $state->id }}" @selected(old('state_id', $address->state_id) == $state->id)>{{ $state->name }}</option>
                                            @endforeach
                                        </select>
                                        <p class="mt-1 text-sm text-red-500 dark:text-red-400">@error('state_id'){{ $message }}@enderror</p>
                                    </div>
                                </div>

                                <div class="sm:col-span-2 lg:col-span-4">
                                    <label for="city_id" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Ciudad</label>
                                    <div class="mt-2">
                                        <select id="city_id" name="city_id" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 @error('city_id') outline-red-500 dark:outline-red-500 @else outline-gray-300 dark:outline-gray-600 @enderror focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                            <option value="">Selecciona una ciudad</option>
                                            @foreach($cities as $city)
                                                <option value="{{ $city->id }}" @selected(old('city_id', $address->city_id) == $city->id)>{{ $city->name }}</option>
                                            @endforeach
                                        </select>
                                        <p class="mt-1 text-sm text-red-500 dark:text-red-400">@error('city_id'){{ $message }}@enderror</p>
                                    </div>
                                </div>

                                <div class="sm:col-span-full">
                                    <label for="address" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Calle y número</label>
                                    <div class="mt-2">
                                        <input id="address" value="{{old('address', $address->address)}}" name="address" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 @error('address') outline-red-500 dark:outline-red-500 @else outline-gray-300 dark:outline-gray-600 @enderror placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                        <p id="address-error" class="mt-1 text-sm text-red-500 dark:text-red-400 @error('address') @else hidden @enderror">@error('address'){{ $message }}@enderror</p>
                                    </div>
                                </div>

                                <div class="sm:col-span-3 lg:col-span-6">
                                    <label for="neighborhood" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Colonia</label>
                                    <div class="mt-2">
                                        <input id="neighborhood" value="{{old('neighborhood', $address->neighborhood)}}" name="neighborhood" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 @error('neighborhood') outline-red-500 dark:outline-red-500 @else outline-gray-300 dark:outline-gray-600 @enderror placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                        <p id="neighborhood-error" class="mt-1 text-sm text-red-500 dark:text-red-400 @error('neighborhood') @else hidden @enderror">@error('neighborhood'){{ $message }}@enderror</p>
                                    </div>
                                </div>

                                <div class="sm:col-span-3 lg:col-span-4">
                                    <label for="postal_code" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Código Postal</label>
                                    <div class="mt-2">
                                        <input id="postal_code" value="{{old('postal_code', $address->postal_code)}}" name="postal_code" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 @error('postal_code') outline-red-500 dark:outline-red-500 @else outline-gray-300 dark:outline-gray-600 @enderror placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                        <p id="postal_code-error" class="mt-1 text-sm text-red-500 dark:text-red-400 @error('postal_code') @else hidden @enderror">@error('postal_code'){{ $message }}@enderror</p>
                                    </div>
                                </div>

                                <div class="sm:col-span-full">
                                    <label for="name" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Ubicación en maps</label>
                                    <div class="mt-2" id="location-name-div">
                                    </div>
                                </div>

                                <div class="sm:col-span-full lg:col-span-8">
                                    <label for="location_name" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Nombre de Ubicación</label>
                                    <div class="mt-2">
                                        <input id="location_name" value="{{old('location_name', $address->location_name)}}" name="location_name" type="text" readonly autocomplete="off" class="block w-full rounded-md bg-gray-100 dark:bg-gray-800 cursor-not-allowed px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 @error('location_name') outline-red-500 dark:outline-red-500 @else outline-gray-300 dark:outline-gray-600 @enderror sm:text-sm/6">
                                        <p class="mt-1 text-sm text-red-500 dark:text-red-400">@error('location_name'){{ $message }}@enderror</p>
                                    </div>
                                </div>

                                <div class="sm:col-span-3 lg:col-span-2">
                                    <label for="latitude" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Latitud</label>
                                    <div class="mt-2">
                                        <input id="latitude" value="{{old('latitude', $address->latitude)}}" name="latitude" type="text" readonly autocomplete="off" class="block w-full rounded-md bg-gray-100 dark:bg-gray-800 cursor-not-allowed px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 @error('latitude') outline-red-500 dark:outline-red-500 @else outline-gray-300 dark:outline-gray-600 @enderror sm:text-sm/6">
                                        <p class="mt-1 text-sm text-red-500 dark:text-red-400">@error('latitude'){{ $message }}@enderror</p>
                                    </div>
                                </div>

                                <div class="sm:col-span-3 lg:col-span-2">
                                    <label for="longitude" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Latitud</label>
                                    <div class="mt-2">
                                        <input id="longitude" value="{{old('longitude', $address->longitude)}}" name="longitude" type="text" readonly autocomplete="off" class="block w-full rounded-md bg-gray-100 dark:bg-gray-800 cursor-not-allowed px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 @error('longitude') outline-red-500 dark:outline-red-500 @else outline-gray-300 dark:outline-gray-600 @enderror sm:text-sm/6">
                                        <p class="mt-1 text-sm text-red-500 dark:text-red-400">@error('longitude'){{ $message }}@enderror</p>
                                    </div>
                                </div>

                                <div class="sm:col-span-full lg:col-span-8">
                                    <label for="link" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Link de compartir ubicación en maps</label>
                                    <div class="mt-2">
                                        <input id="link" value="{{old('link', $address->link)}}" name="link" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-4 sm:px-6">
                    <div class="mt-6 flex items-center justify-end gap-x-6">
                        <a href="{{ route('addresses.show', ['address' => $address]) }}" class="text-sm/6 font-semibold text-gray-900 dark:text-gray-50">Cancelar</a>
                        <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Guardar</button>
                    </div>
                </div>
            </div>
        </form>
    </section>
</x-layout-admin>
