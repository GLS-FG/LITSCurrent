@props(['client', 'countries' => []])
{{--
    Modal para crear una dirección del cliente (se abre desde "Agregar dirección" de clients.show).
    Mismos campos y validaciones que client/address/create.blade.php (que sigue existiendo):
    validación en vivo y las reglas de ClientAddressPostRequest en servidor. País -> Estado ->
    Ciudad son selects en cascada (partials.location-selects) en vez del autocompletado de la
    página vieja; mandan los mismos country_id / state_id / city_id.
    Contacto, email, apodo y teléfono arrancan con los datos del cliente, como la página vieja.
    Si el servidor rechaza el formulario vuelve al detalle del cliente y el modal se reabre solo
    con los errores y el old(). Al cerrarlo sin guardar se vuelve a esos valores iniciales.
    Los ids llevan el prefijo `ca_` para no chocar con los de otros modales de la página.
--}}
@php
    $mine = old('_drawer') === 'client-address-create';
    $defaults = [
        'contact_name' => trim($client->name . ' ' . $client->last_name),
        'email' => $client->email,
        'nickname' => $client->company_name,
        'phone' => $client->phone1,
        'address' => '',
        'neighborhood' => '',
        'postal_code' => '',
    ];
    $val = fn ($field) => $mine ? old($field, $defaults[$field] ?? '') : ($defaults[$field] ?? '');
    $inputClass = 'block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6';
    $selectClass = 'block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6';
    $labelClass = 'block text-sm/6 font-medium text-gray-900 dark:text-gray-50';
    $selectedCountry = $mine ? old('country_id') : null;
@endphp
<div
    class="relative z-90"
    x-data="{ open: {{ $mine && $errors->any() ? 'true' : 'false' }} }"
    x-init="$watch('open', value => { if (!value) window.dispatchEvent(new CustomEvent('add-client-address-modal-closed')) })"
    x-on:open-add-client-address-modal.window="open = true"
    x-on:keydown.escape.window="open = false"
    x-trap.inert.noscroll="open"
    x-cloak
    x-show="open"
    role="dialog"
    aria-modal="true"
    aria-labelledby="add-client-address-title"
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
                class="relative flex max-h-[calc(100vh-3rem)] w-full flex-col overflow-hidden rounded-lg bg-white dark:bg-lits-blue-550 shadow-xl sm:max-w-2xl"
            >
                <form id="client-address-create-form" action="{{ route('clients.addresses.store', ['client' => $client]) }}" method="POST" autocomplete="off" class="flex min-h-0 flex-1 flex-col">
                    @csrf
                    <input type="hidden" name="_drawer" value="client-address-create">

                    <div class="flex items-start justify-between gap-3 border-b border-gray-200 dark:border-lits-blue-450 px-6 py-5">
                        <div>
                            <h3 id="add-client-address-title" class="text-base font-semibold text-gray-900 dark:text-gray-50">Nueva dirección</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Dirección de {{ $client->company_name }}.</p>
                        </div>
                        <button type="button" @click="open = false" class="rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                            <span class="sr-only">Cerrar</span>
                            <i class="fa-regular fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <div class="flex-1 min-h-0 overflow-y-auto px-6 py-5">
                        @if ($mine && $errors->any())
                            <div id="client-address-create-errors">
                                <x-alerts.error :message="'Para crear la dirección soluciona los siguientes errores:'" :errors="$errors" class="mb-6" />
                            </div>
                        @endif

                        <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                            <div class="sm:col-span-6">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-50">Detalles del contacto</h4>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Llena la información del contacto para poder buscar las direcciones con esta información al llenar los embarques.</p>
                            </div>
                            <div class="sm:col-span-3">
                                <label for="ca_contact_name" class="{{ $labelClass }}">Nombre de la persona de contacto</label>
                                <div class="mt-2">
                                    <input id="ca_contact_name" value="{{ $val('contact_name') }}" name="contact_name" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div class="sm:col-span-3">
                                <label for="ca_email" class="{{ $labelClass }}">Email</label>
                                <div class="mt-2">
                                    <input id="ca_email" value="{{ $val('email') }}" name="email" type="email" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div class="sm:col-span-3">
                                <label for="ca_nickname" class="{{ $labelClass }}">Apodo</label>
                                <div class="mt-2">
                                    <input id="ca_nickname" value="{{ $val('nickname') }}" name="nickname" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div class="sm:col-span-3">
                                <label for="ca_phone" class="{{ $labelClass }}">Teléfono</label>
                                <div class="mt-2">
                                    <input id="ca_phone" value="{{ $val('phone') }}" name="phone" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>

                            <div class="sm:col-span-6">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-50">Ubicación</h4>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Llena la dirección del cliente.</p>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="ca_country_id" class="{{ $labelClass }}">País</label>
                                <div class="mt-2">
                                    <select id="ca_country_id" name="country_id" class="{{ $selectClass }}">
                                        <option value="">Selecciona un país</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country->id }}" @selected($selectedCountry == $country->id)>{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="ca_state_id" class="{{ $labelClass }}">Estado</label>
                                <div class="mt-2">
                                    <select id="ca_state_id" name="state_id" class="{{ $selectClass }}">
                                        <option value="">Selecciona un estado</option>
                                    </select>
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="ca_city_id" class="{{ $labelClass }}">Ciudad</label>
                                <div class="mt-2">
                                    <select id="ca_city_id" name="city_id" class="{{ $selectClass }}">
                                        <option value="">Selecciona una ciudad</option>
                                    </select>
                                </div>
                            </div>
                            <div class="sm:col-span-6">
                                <label for="ca_address" class="{{ $labelClass }}">Calle y número</label>
                                <div class="mt-2">
                                    <input id="ca_address" value="{{ $val('address') }}" name="address" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div class="sm:col-span-3">
                                <label for="ca_neighborhood" class="{{ $labelClass }}">Colonia</label>
                                <div class="mt-2">
                                    <input id="ca_neighborhood" value="{{ $val('neighborhood') }}" name="neighborhood" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div class="sm:col-span-3">
                                <label for="ca_postal_code" class="{{ $labelClass }}">Código Postal</label>
                                <div class="mt-2">
                                    <input id="ca_postal_code" value="{{ $val('postal_code') }}" name="postal_code" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-lits-blue-450 px-6 py-4">
                        <button type="button" @click="open = false" class="text-sm font-semibold text-gray-900 dark:text-gray-50 hover:cursor-pointer">Cancelar</button>
                        <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Crear dirección</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('custom_script')
    @include('partials.live-validation')
    @include('partials.location-selects')
    <script type="module">
        (function () {
            const form = document.getElementById('client-address-create-form');
            const defaults = @json($defaults);
            const fields = Object.keys(defaults);

            attachLiveValidation('ca_contact_name', { required: true, min: 3 });
            attachLiveValidation('ca_email', { required: true, email: true });
            attachLiveValidation('ca_nickname', { max: 256 });
            attachLiveValidation('ca_phone', { required: true, numeric: true, min: 10 });
            attachLiveValidation('ca_address', { required: true });
            attachLiveValidation('ca_neighborhood', { required: true, max: 100 });
            attachLiveValidation('ca_postal_code', { required: true, numeric: true, min: 5 });

            const location = initLocationSelects({
                prefix: 'ca_',
                old: {
                    country: @json($mine ? old('country_id') : null),
                    state: @json($mine ? old('state_id') : null),
                    city: @json($mine ? old('city_id') : null),
                },
            });
            attachLiveValidation('ca_country_id', { required: true, requiredMessage: 'Selecciona un país.' });
            attachLiveValidation('ca_state_id', { required: true, requiredMessage: 'Selecciona un estado.' });
            attachLiveValidation('ca_city_id', { required: true, requiredMessage: 'Selecciona una ciudad.' });

            // Avoid creating the address twice on a double click.
            form.addEventListener('submit', function (e) {
                const submit = form.querySelector('button[type="submit"]');
                if (submit.disabled) { e.preventDefault(); return; }
                submit.disabled = true;
            });

            // Closing the modal without saving puts the fields back on the client's data
            // (contact, email, nickname, phone), empties the rest and clears the error marks.
            window.addEventListener('add-client-address-modal-closed', function () {
                fields.forEach(function (field) {
                    document.getElementById('ca_' + field).value = defaults[field] == null ? '' : defaults[field];
                });
                location.clear();
                form.querySelectorAll('[id$="-error"]').forEach(function (el) { el.classList.add('hidden'); });
                form.querySelectorAll('input[type="text"], input[type="email"], select').forEach(function (el) {
                    el.classList.remove('outline-red-500', 'dark:outline-red-500');
                    el.classList.add('outline-gray-300', 'dark:outline-gray-600');
                });
                const serverErrors = document.getElementById('client-address-create-errors');
                if (serverErrors) { serverErrors.remove(); }
            });
        })();
    </script>
@endpush
