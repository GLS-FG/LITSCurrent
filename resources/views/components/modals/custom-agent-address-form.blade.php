@props(['agent', 'countries' => []])
{{--
    Modal para editar una dirección de un agente aduanal (se abre desde el lápiz de cada
    dirección en custom/agent/show). Mismos campos y validaciones que
    custom/agent/address/edit.blade.php (que sigue existiendo): validación en vivo y las reglas
    de CustomAgentAddressPutRequest en servidor. País -> Estado -> Ciudad son selects en
    cascada (partials.location-selects, prefijo `ea_`).
    Es un solo modal para todas las direcciones: el lápiz de una dirección dispara
    `request-edit-custom-agent-address` con los valores de esa dirección, el modal los pone y se
    abre. Si el servidor rechaza el formulario, Laravel regresa a la página del agente y el
    modal se reabre solo con los errores y el old(); la dirección se recupera de `_record`,
    casteado a entero y buscado entre las direcciones del agente, nunca usado como URL.
    Al cerrarlo sin guardar se descarta lo escrito. Los ids llevan el prefijo `ea_`.
--}}
@php
    $mine = old('_drawer') === 'custom-agent-address-edit';
    $record = $mine && (int) old('_record') ? $agent->addresses->firstWhere('id', (int) old('_record')) : null;
    $textFields = ['street_name', 'street_no', 'neighborhood', 'postal_code'];
    $saved = fn ($field) => $record?->{$field};
    $val = fn ($field) => $mine ? old($field, $saved($field)) : null;
    $formAction = $record ? route('custom-agents.addresses.update', ['custom_agent' => $agent->id, 'address' => $record->id]) : '';
    $inputClass = 'block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6';
    $selectClass = 'block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6';
    $labelClass = 'block text-sm/6 font-medium text-gray-900 dark:text-gray-50';
    $selectedCountry = $val('country_id');
@endphp
<div
    class="relative z-90"
    x-data="{ open: {{ $mine && $errors->any() ? 'true' : 'false' }} }"
    x-init="$watch('open', value => { if (!value) window.dispatchEvent(new CustomEvent('edit-custom-agent-address-modal-closed')) })"
    x-on:open-edit-custom-agent-address-modal.window="open = true"
    x-on:keydown.escape.window="open = false"
    x-trap.inert.noscroll="open"
    x-cloak
    x-show="open"
    role="dialog"
    aria-modal="true"
    aria-labelledby="custom-agent-address-edit-title"
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
                <form id="custom-agent-address-edit-form" action="{{ $formAction }}" method="POST" autocomplete="off" class="flex min-h-0 flex-1 flex-col">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="ea_record_id" name="_record" value="{{ $record?->id }}">
                    <input type="hidden" name="_drawer" value="custom-agent-address-edit">

                    <div class="flex items-start justify-between gap-3 border-b border-gray-200 dark:border-lits-blue-450 px-6 py-5">
                        <div>
                            <h3 id="custom-agent-address-edit-title" class="text-base font-semibold text-gray-900 dark:text-gray-50">Editar dirección</h3>
                            <p id="ea_subtitle" class="text-xs text-gray-500 dark:text-gray-400">{{ $record?->address_type?->label() ?? $agent->company_name }}</p>
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
                                collect($errors->messages())->except(['street_name', 'street_no', 'neighborhood', 'postal_code', 'country_id', 'state_id', 'city_id'])->all()
                            );
                        @endphp
                        @if ($mine && $unmatchedErrors->any())
                            <div id="custom-agent-address-edit-errors">
                                <x-alerts.error :message="'Para editar la dirección soluciona los siguientes errores:'" :errors="$unmatchedErrors" class="mb-6" />
                            </div>
                        @endif

                        <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                            <div class="sm:col-span-2">
                                <label for="ea_country_id" class="{{ $labelClass }}">País</label>
                                <div class="mt-2">
                                    <select id="ea_country_id" name="country_id" class="{{ $selectClass }}">
                                        <option value="">Selecciona un país</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country->id }}" @selected($selectedCountry == $country->id)>{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="ea_state_id" class="{{ $labelClass }}">Estado</label>
                                <div class="mt-2">
                                    <select id="ea_state_id" name="state_id" class="{{ $selectClass }}">
                                        <option value="">Selecciona un estado</option>
                                    </select>
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="ea_city_id" class="{{ $labelClass }}">Ciudad</label>
                                <div class="mt-2">
                                    <select id="ea_city_id" name="city_id" class="{{ $selectClass }}">
                                        <option value="">Selecciona una ciudad</option>
                                    </select>
                                </div>
                            </div>
                            <div class="sm:col-span-6">
                                <label for="ea_street_name" class="{{ $labelClass }}">Calle</label>
                                <div class="mt-2">
                                    <input id="ea_street_name" value="{{ $val('street_name') }}" name="street_name" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="ea_street_no" class="{{ $labelClass }}">Número</label>
                                <div class="mt-2">
                                    <input id="ea_street_no" value="{{ $val('street_no') }}" name="street_no" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="ea_neighborhood" class="{{ $labelClass }}">Colonia</label>
                                <div class="mt-2">
                                    <input id="ea_neighborhood" value="{{ $val('neighborhood') }}" name="neighborhood" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="ea_postal_code" class="{{ $labelClass }}">Código Postal</label>
                                <div class="mt-2">
                                    <input id="ea_postal_code" value="{{ $val('postal_code') }}" name="postal_code" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-lits-blue-450 px-6 py-4">
                        <button type="button" @click="open = false" class="text-sm font-semibold text-gray-900 dark:text-gray-50 hover:cursor-pointer">Cancelar</button>
                        <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Guardar</button>
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
            const p = 'ea_';
            const form = document.getElementById('custom-agent-address-edit-form');
            const el = (name) => document.getElementById(p + name);
            const textFields = @js($textFields);

            const location = initLocationSelects({
                prefix: p,
                old: {
                    country: @json($val('country_id')),
                    state: @json($val('state_id')),
                    city: @json($val('city_id')),
                },
            });
            attachLiveValidation(p + 'country_id', { required: true, requiredMessage: 'Selecciona un país.' });
            attachLiveValidation(p + 'state_id', { required: true, requiredMessage: 'Selecciona un estado.' });
            attachLiveValidation(p + 'city_id', { required: true, requiredMessage: 'Selecciona una ciudad.' });
            attachLiveValidation(p + 'street_name', { required: true });
            attachLiveValidation(p + 'street_no', { required: true, max: 50 });
            attachLiveValidation(p + 'neighborhood', { required: true, max: 100 });
            attachLiveValidation(p + 'postal_code', { required: true, numeric: true, min: 5 });
            // Valida todo al enviar y pinta bajo su campo los errores del servidor (solo los de este modal).
            // Va antes del seguro de doble clic: si cancela el envío, ese seguro no debe bloquear el botón.
            enableFormValidation('custom-agent-address-edit-form', { errors: @json($mine ? $errors->messages() : (object) []) });

            // Avoid saving twice on a double click.
            form.addEventListener('submit', function (e) {
                const submit = form.querySelector('button[type="submit"]');
                if (e.defaultPrevented) return;
                if (submit.disabled) { e.preventDefault(); return; }
                submit.disabled = true;
            });

            // What the server rendered (the saved address when the page came back with errors, or
            // empty): closing the modal without saving restores it. Replaced by the clicked
            // address when the pencil is used.
            const snapshot = {
                @foreach($textFields as $field)
                    {{ $field }}: @json($saved($field) ?? ''),
                @endforeach
                country: @json($record?->country_id),
                state: @json($record?->state_id),
                city: @json($record?->city_id),
            };

            const clearMarks = function () {
                form.querySelectorAll('[id$="-error"]').forEach(function (node) { node.classList.add('hidden'); });
                form.querySelectorAll('input[type="text"], select').forEach(function (node) {
                    node.classList.remove('outline-red-500', 'dark:outline-red-500');
                    node.classList.add('outline-gray-300', 'dark:outline-gray-600');
                });
                const serverErrors = document.getElementById('custom-agent-address-edit-errors');
                if (serverErrors) { serverErrors.remove(); }
            };

            window.addEventListener('edit-custom-agent-address-modal-closed', function () {
                textFields.forEach(function (field) { el(field).value = snapshot[field]; });
                location.set(snapshot.country, snapshot.state, snapshot.city);
                clearMarks();
            });

            // Pencil of an address: fill the modal with that address, then open it.
            window.addEventListener('request-edit-custom-agent-address', function (event) {
                const data = event.detail;
                form.action = data.update_url;
                el('record_id').value = data.id;
                document.getElementById('ea_subtitle').textContent = data.type_label;
                textFields.forEach(function (field) {
                    el(field).value = data[field] == null ? '' : data[field];
                    snapshot[field] = el(field).value;
                });
                location.set(data.country_id, data.state_id, data.city_id);
                snapshot.country = data.country_id;
                snapshot.state = data.state_id;
                snapshot.city = data.city_id;
                clearMarks();
                window.dispatchEvent(new CustomEvent('open-edit-custom-agent-address-modal'));
            });
        })();
    </script>
@endpush
