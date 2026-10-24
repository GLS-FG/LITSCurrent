@props(['mode', 'countries', 'address' => null])
{{--
    Modal para crear ("create") y editar ("edit") una dirección de la libreta. Mismas validaciones que
    address/create y address/edit (que siguen existiendo): validación en vivo, selects en cascada
    País -> Estado -> Ciudad (partials.location-selects), autocompletado de Google Maps para la
    ubicación y las reglas de AddressPostRequest / AddressPutRequest en servidor.

    - Crear: lo abre `open-add-address-modal` (botón "Nueva dirección" de addresses.index).
    - Editar en addresses.index: se monta sin dirección ($address = null); el ícono de editar de una
      fila dispara `request-edit-address` con los valores de la fila y el modal los pone y se abre.
    - Editar en addresses.show: se monta con la dirección y el botón "Editar dirección" solo dispara
      `open-edit-address-modal`.
    - Si el servidor rechaza el formulario, Laravel regresa a la página de origen y el modal se
      reabre solo con los errores y el old(). La dirección a editar se recupera de `_record`,
      casteado a entero, nunca usado como URL.
    - Google Maps se carga hasta la primera vez que se abre un modal (partials.google-maps-loader).
    Al cerrarlo sin guardar se descarta lo escrito. Los ids del modal de editar llevan el
    prefijo `edit_` para no chocar con los del de crear en la misma página.
--}}
@php
    $isEdit = $mode === 'edit';
    $p = $isEdit ? 'edit_' : '';
    $formId = 'address-' . $mode . '-form';
    $drawerKey = 'address-' . $mode;
    $eventName = $isEdit ? 'edit-address-modal' : 'add-address-modal';
    $mine = old('_drawer') === $drawerKey;
    if ($isEdit && ! $address && $mine && (int) old('_record')) {
        $address = \App\Models\Address::find((int) old('_record'));
    }
    $saved = fn ($field) => ($isEdit && $address) ? $address->{$field} : null;
    $val = fn ($field) => $mine ? old($field, $saved($field)) : $saved($field);
    $formAction = $isEdit
        ? ($address ? route('addresses.update', ['address' => $address->id]) : '')
        : route('addresses.store');
    $inputClass = 'block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6';
    $readonlyClass = 'block w-full rounded-md bg-gray-100 dark:bg-gray-800 cursor-default px-3 py-1.5 text-base text-gray-700 dark:text-gray-300 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 sm:text-sm/6';
    $selectClass = 'block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6';
    $labelClass = 'block text-sm/6 font-medium text-gray-900 dark:text-gray-50';
    $selectedCountry = $val('country_id');
    $locationText = $val('location_name');
@endphp
<div
    class="relative z-90"
    x-data="{ open: {{ $mine && $errors->any() ? 'true' : 'false' }} }"
    x-init="$watch('open', value => { if (!value) window.dispatchEvent(new CustomEvent('{{ $eventName }}-closed')) })"
    x-on:open-{{ $eventName }}.window="open = true"
    x-on:keydown.escape.window="open = false"
    x-trap.inert.noscroll="open"
    x-cloak
    x-show="open"
    role="dialog"
    aria-modal="true"
    aria-labelledby="{{ $formId }}-title"
>
    <div
        class="fixed inset-0 bg-gray-500/75 dark:bg-gray-950/75 transition-opacity"
        aria-hidden="true"
        x-show="open"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="open = false"
    ></div>

    <div class="fixed inset-0 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 sm:items-center sm:p-6">
            <div
                x-show="open"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative flex max-h-[calc(100vh-3rem)] w-full flex-col overflow-hidden rounded-lg bg-white dark:bg-lits-blue-550 shadow-xl sm:max-w-3xl"
            >
                <form id="{{ $formId }}" action="{{ $formAction }}" method="POST" autocomplete="off" class="flex min-h-0 flex-1 flex-col">
                    @csrf
                    @if($isEdit)
                        @method('PUT')
                        <input type="hidden" id="{{ $p }}record_id" name="_record" value="{{ $address?->id }}">
                    @endif
                    <input type="hidden" name="_drawer" value="{{ $drawerKey }}">

                    <div class="flex items-start justify-between gap-3 border-b border-gray-200 dark:border-lits-blue-450 px-6 py-5">
                        <div>
                            <h3 id="{{ $formId }}-title" class="text-base font-semibold text-gray-900 dark:text-gray-50">{{ $isEdit ? 'Editar dirección' : 'Nueva dirección' }}</h3>
                            <p id="{{ $p }}subtitle" class="text-xs text-gray-500 dark:text-gray-400">{{ $isEdit ? $address?->name : 'Ingresa la información necesaria de la dirección.' }}</p>
                        </div>
                        <button type="button" @click="open = false" class="rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                            <span class="sr-only">Cerrar</span>
                            <i class="fa-regular fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <div class="flex-1 min-h-0 overflow-y-auto px-6 py-5">
                        @php
                            // Los errores de estos campos se muestran bajo su input; el aviso solo conserva los que no tienen campo.
                            $unmatchedErrors = new \Illuminate\Support\MessageBag(
                                collect($errors->messages())->except([
                                    'name', 'trade_name', 'contact_name', 'email', 'phone', 'country_id', 'state_id', 'city_id',
                                    'address', 'neighborhood', 'postal_code', 'location_name', 'latitude', 'longitude', 'link',
                                ])->all()
                            );
                        @endphp
                        @if ($mine && $unmatchedErrors->any())
                            <div id="{{ $formId }}-errors">
                                <x-alerts.error :message="$isEdit ? 'Para editar la dirección soluciona los siguientes errores:' : 'Para crear la dirección soluciona los siguientes errores:'" :errors="$unmatchedErrors" class="mb-6" />
                            </div>
                        @endif

                        <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                            <div class="sm:col-span-6">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-50">Detalles del contacto</h4>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Llena la información del contacto para poder buscar las direcciones con esta información al llenar los embarques.</p>
                            </div>
                            <div class="sm:col-span-3">
                                <label for="{{ $p }}name" class="{{ $labelClass }}">Nombre de la dirección</label>
                                <div class="mt-2">
                                    <input id="{{ $p }}name" value="{{ $val('name') }}" name="name" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div class="sm:col-span-3">
                                <label for="{{ $p }}trade_name" class="{{ $labelClass }}">Nombre comercial</label>
                                <div class="mt-2">
                                    <input id="{{ $p }}trade_name" value="{{ $val('trade_name') }}" name="trade_name" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div class="sm:col-span-6">
                                <label for="{{ $p }}contact_name" class="{{ $labelClass }}">Nombre de la persona de contacto</label>
                                <div class="mt-2">
                                    <input id="{{ $p }}contact_name" value="{{ $val('contact_name') }}" name="contact_name" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div class="sm:col-span-4">
                                <label for="{{ $p }}email" class="{{ $labelClass }}">Email</label>
                                <div class="mt-2">
                                    <input id="{{ $p }}email" value="{{ $val('email') }}" name="email" type="email" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="{{ $p }}phone" class="{{ $labelClass }}">Teléfono</label>
                                <div class="mt-2">
                                    <input id="{{ $p }}phone" value="{{ $val('phone') }}" name="phone" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>

                            <div class="sm:col-span-6">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-50">Detalles de la dirección</h4>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Llena la información de la dirección, esta se prellenará en los embarques.</p>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="{{ $p }}country_id" class="{{ $labelClass }}">País</label>
                                <div class="mt-2">
                                    <select id="{{ $p }}country_id" name="country_id" class="{{ $selectClass }}">
                                        <option value="">Selecciona un país</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country->id }}" @selected($selectedCountry == $country->id)>{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="{{ $p }}state_id" class="{{ $labelClass }}">Estado</label>
                                <div class="mt-2">
                                    <select id="{{ $p }}state_id" name="state_id" class="{{ $selectClass }}">
                                        <option value="">Selecciona un estado</option>
                                    </select>
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="{{ $p }}city_id" class="{{ $labelClass }}">Ciudad</label>
                                <div class="mt-2">
                                    <select id="{{ $p }}city_id" name="city_id" class="{{ $selectClass }}">
                                        <option value="">Selecciona una ciudad</option>
                                    </select>
                                </div>
                            </div>
                            <div class="sm:col-span-6">
                                <label for="{{ $p }}address" class="{{ $labelClass }}">Calle y número</label>
                                <div class="mt-2">
                                    <input id="{{ $p }}address" value="{{ $val('address') }}" name="address" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div class="sm:col-span-4">
                                <label for="{{ $p }}neighborhood" class="{{ $labelClass }}">Colonia</label>
                                <div class="mt-2">
                                    <input id="{{ $p }}neighborhood" value="{{ $val('neighborhood') }}" name="neighborhood" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="{{ $p }}postal_code" class="{{ $labelClass }}">Código postal</label>
                                <div class="mt-2">
                                    <input id="{{ $p }}postal_code" value="{{ $val('postal_code') }}" name="postal_code" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>

                            <div class="sm:col-span-6">
                                <label class="{{ $labelClass }}">Ubicación en maps</label>
                                <div class="mt-2" id="{{ $p }}place-autocomplete"></div>
                                <input id="{{ $p }}location_name_hidden" value="{{ $locationText }}" name="location_name" type="hidden">
                                <input id="{{ $p }}latitude" value="{{ $val('latitude') }}" name="latitude" type="hidden">
                                <input id="{{ $p }}longitude" value="{{ $val('longitude') }}" name="longitude" type="hidden">
                                <div class="mt-2">
                                    <input id="{{ $p }}location_display" value="{{ $locationText }}" type="text" readonly tabindex="-1" placeholder="Busca arriba y selecciona una ubicación" class="{{ $readonlyClass }}">
                                </div>
                            </div>
                            <div class="sm:col-span-6">
                                <label for="{{ $p }}link" class="{{ $labelClass }}">Link de compartir ubicación en maps</label>
                                <div class="mt-2">
                                    <input id="{{ $p }}link" value="{{ $val('link') }}" name="link" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-lits-blue-450 px-6 py-4">
                        <button type="button" @click="open = false" class="text-sm font-semibold text-gray-900 dark:text-gray-50 hover:cursor-pointer">Cancelar</button>
                        <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">{{ $isEdit ? 'Guardar' : 'Crear dirección' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('custom_script')
    @include('partials.live-validation')
    @include('partials.location-selects')
    @include('partials.google-maps-loader')
    <script type="module">
        (function () {
            const p = @js($p);
            const isEdit = @js($isEdit);
            const form = document.getElementById(@js($formId));
            const el = (name) => document.getElementById(p + name);
            const textFields = ['name', 'trade_name', 'contact_name', 'email', 'phone', 'address', 'neighborhood', 'postal_code', 'link'];
            const mapFields = ['latitude', 'longitude'];

            const location = initLocationSelects({
                prefix: p,
                old: {
                    country: @json($val('country_id')),
                    state: @json($val('state_id')),
                    city: @json($val('city_id')),
                },
            });
            attachLiveValidation(p + 'contact_name', { required: true, min: 3 });
            attachLiveValidation(p + 'name', { required: true, min: 3, max: 256 });
            attachLiveValidation(p + 'trade_name', { required: true, min: 3, max: 256 });
            attachLiveValidation(p + 'email', { email: true });
            attachLiveValidation(p + 'phone', { numeric: true, min: 10 });
            attachLiveValidation(p + 'country_id', { required: true, requiredMessage: 'Selecciona un país.' });
            attachLiveValidation(p + 'state_id', { required: true, requiredMessage: 'Selecciona un estado.' });
            attachLiveValidation(p + 'city_id', { required: true, requiredMessage: 'Selecciona una ciudad.' });
            attachLiveValidation(p + 'address', { required: true, regex: /^[\p{L}\p{N}\s.,\/#-]+$/u, regexMessage: 'Solo se permiten letras, números, espacios y los caracteres . , - / #' });
            attachLiveValidation(p + 'neighborhood', { min: 3, max: 100 });
            attachLiveValidation(p + 'postal_code', { min: 3, max: 15 });
            // La ubicación se llena desde el autocompletado de Google: se valida contra la latitud oculta.
            attachLiveValidation(p + 'location_display', { hidden: p + 'latitude', requiredMessage: 'Debes seleccionar una ubicación en el mapa.', serverKey: 'location_name' });
            // Valida todo al enviar y pinta bajo su campo los errores del servidor (solo los de este modal).
            // Va antes del seguro de doble clic: si cancela el envío, ese seguro no debe bloquear el botón.
            enableFormValidation(@js($formId), { errors: @json($mine ? $errors->messages() : (object) []) });

            // Avoid submitting twice on a double click.
            form.addEventListener('submit', function (e) {
                const submit = form.querySelector('button[type="submit"]');
                if (e.defaultPrevented) return;
                if (submit.disabled) { e.preventDefault(); return; }
                submit.disabled = true;
            });

            // The values to go back to when the modal is closed without saving: the saved
            // record when editing (replaced by the clicked row on the list page), empty when
            // creating. Taken from the saved data, not from the DOM, which may hold old() input.
            const snapshot = {
                name: @json($saved('name') ?? ''),
                trade_name: @json($saved('trade_name') ?? ''),
                contact_name: @json($saved('contact_name') ?? ''),
                email: @json($saved('email') ?? ''),
                phone: @json($saved('phone') ?? ''),
                address: @json($saved('address') ?? ''),
                neighborhood: @json($saved('neighborhood') ?? ''),
                postal_code: @json($saved('postal_code') ?? ''),
                link: @json($saved('link') ?? ''),
                latitude: @json($saved('latitude') ?? ''),
                longitude: @json($saved('longitude') ?? ''),
                location_name: @json($saved('location_name') ?? ''),
                country: @json($saved('country_id') ? (string) $saved('country_id') : ''),
                state: @json($saved('state_id')),
                city: @json($saved('city_id')),
            };

            const setLocation = function (name, latitude, longitude) {
                el('location_name_hidden').value = name;
                el('location_display').value = name;
                el('latitude').value = latitude;
                el('longitude').value = longitude;
            };

            // Google Maps se carga la primera vez que se abre el modal, no al cargar la página.
            let autocompleteElement = null;
            let autocompleteStarted = false;
            const startAutocomplete = async function () {
                if (autocompleteStarted) return;
                autocompleteStarted = true;
                try {
                    const { PlaceAutocompleteElement } = await loadPlacesLibrary();
                    autocompleteElement = new PlaceAutocompleteElement();
                    autocompleteElement.style.colorScheme = 'light';
                    el('place-autocomplete').appendChild(autocompleteElement);
                    autocompleteElement.addEventListener('gmp-select', async ({ placePrediction }) => {
                        const place = placePrediction.toPlace();
                        await place.fetchFields({ fields: ['displayName', 'formattedAddress', 'location', 'types'] });
                        const hasTypes = ['political', 'route', 'locality', 'street_address'].some((item) => place.types.includes(item));
                        const locationName = hasTypes ? place.formattedAddress : place.displayName + ', ' + place.formattedAddress;
                        setLocation(locationName, place.location.lat().toFixed(6), place.location.lng().toFixed(6));
                        el('location_display').dispatchEvent(new Event('change'));
                    });
                } catch (error) {
                    autocompleteStarted = false;
                    console.error(error);
                }
            };
            window.addEventListener(@js('open-' . $eventName), startAutocomplete);
            @if($mine && $errors->any())
                startAutocomplete();
            @endif

            const clearMarks = function () {
                form.querySelectorAll('[id$="-error"]').forEach(function (node) { node.classList.add('hidden'); });
                form.querySelectorAll('input[type="text"], input[type="email"], select').forEach(function (node) {
                    node.classList.remove('outline-red-500', 'dark:outline-red-500');
                    node.classList.add('outline-gray-300', 'dark:outline-gray-600');
                });
                const serverErrors = document.getElementById(@js($formId . '-errors'));
                if (serverErrors) { serverErrors.remove(); }
            };

            window.addEventListener(@js($eventName . '-closed'), function () {
                textFields.forEach(function (field) { el(field).value = snapshot[field]; });
                setLocation(snapshot.location_name, snapshot.latitude, snapshot.longitude);
                if (autocompleteElement) autocompleteElement.value = '';
                location.set(snapshot.country, snapshot.state, snapshot.city);
                clearMarks();
            });

            // List page (edit mode): fill the modal with the clicked row's values, then open it.
            if (isEdit) window.addEventListener('request-edit-address', function (event) {
                const data = event.detail;
                form.action = data.update_url;
                el('record_id').value = data.id;
                document.getElementById('edit_subtitle').textContent = data.name;
                textFields.concat(mapFields, ['location_name']).forEach(function (field) {
                    const value = data[field] == null ? '' : data[field];
                    snapshot[field] = String(value);
                    if (field !== 'location_name' && !mapFields.includes(field)) el(field).value = value;
                });
                setLocation(snapshot.location_name, snapshot.latitude, snapshot.longitude);
                location.set(data.country_id, data.state_id, data.city_id);
                snapshot.country = String(data.country_id);
                snapshot.state = data.state_id;
                snapshot.city = data.city_id;
                clearMarks();
                window.dispatchEvent(new CustomEvent('open-edit-address-modal'));
            });
        })();
    </script>
@endpush
