@props(['agent', 'countries' => []])
{{--
    Modal para agregar una dirección a un agente aduanal (se abre desde "Agregar dirección" en
    custom/agent/show). Mismos campos y validaciones que custom/agent/address/create.blade.php
    (que sigue existiendo): validación en vivo y las reglas de CustomAgentAddressPostRequest en
    servidor. País -> Estado -> Ciudad son selects en cascada (partials.location-selects,
    prefijo `aa_`).
    Si el servidor rechaza el formulario, Laravel regresa a la página del agente y el modal se
    reabre solo con los errores y el old(). Al cerrarlo sin guardar se descarta lo escrito.
    Los ids llevan el prefijo `aa_` para no chocar con los del modal de editar (`ea_`).
--}}
@php
    $mine = old('_drawer') === 'custom-agent-address-create';
    $textFields = ['street_name', 'street_no', 'neighborhood', 'postal_code'];
    $val = fn ($field) => $mine ? old($field) : null;
    $inputClass = 'block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6';
    $selectClass = 'block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6';
    $labelClass = 'block text-sm/6 font-medium text-gray-900 dark:text-gray-50';
    $selectedCountry = $val('country_id');
@endphp
<div
    class="relative z-90"
    x-data="{ open: {{ $mine && $errors->any() ? 'true' : 'false' }} }"
    x-init="$watch('open', value => { if (!value) window.dispatchEvent(new CustomEvent('add-custom-agent-address-modal-closed')) })"
    x-on:open-add-custom-agent-address-modal.window="open = true"
    x-on:keydown.escape.window="open = false"
    x-trap.inert.noscroll="open"
    x-cloak
    x-show="open"
    role="dialog"
    aria-modal="true"
    aria-labelledby="custom-agent-address-create-title"
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
                <form id="custom-agent-address-create-form" action="{{ route('custom-agents.addresses.store', ['custom_agent' => $agent->id]) }}" method="POST" autocomplete="off" class="flex min-h-0 flex-1 flex-col">
                    @csrf
                    <input type="hidden" name="_drawer" value="custom-agent-address-create">

                    <div class="flex items-start justify-between gap-3 border-b border-gray-200 dark:border-lits-blue-450 px-6 py-5">
                        <div>
                            <h3 id="custom-agent-address-create-title" class="text-base font-semibold text-gray-900 dark:text-gray-50">Nueva dirección</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $agent->company_name }}</p>
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
                            <div id="custom-agent-address-create-errors">
                                <x-alerts.error :message="'Para agregar la dirección soluciona los siguientes errores:'" :errors="$unmatchedErrors" class="mb-6" />
                            </div>
                        @endif

                        <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                            <div class="sm:col-span-2">
                                <label for="aa_country_id" class="{{ $labelClass }}">País</label>
                                <div class="mt-2">
                                    <select id="aa_country_id" name="country_id" class="{{ $selectClass }}">
                                        <option value="">Selecciona un país</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country->id }}" @selected($selectedCountry == $country->id)>{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="aa_state_id" class="{{ $labelClass }}">Estado</label>
                                <div class="mt-2">
                                    <select id="aa_state_id" name="state_id" class="{{ $selectClass }}">
                                        <option value="">Selecciona un estado</option>
                                    </select>
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="aa_city_id" class="{{ $labelClass }}">Ciudad</label>
                                <div class="mt-2">
                                    <select id="aa_city_id" name="city_id" class="{{ $selectClass }}">
                                        <option value="">Selecciona una ciudad</option>
                                    </select>
                                </div>
                            </div>
                            <div class="sm:col-span-6">
                                <label for="aa_street_name" class="{{ $labelClass }}">Calle</label>
                                <div class="mt-2">
                                    <input id="aa_street_name" value="{{ $val('street_name') }}" name="street_name" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="aa_street_no" class="{{ $labelClass }}">Número</label>
                                <div class="mt-2">
                                    <input id="aa_street_no" value="{{ $val('street_no') }}" name="street_no" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="aa_neighborhood" class="{{ $labelClass }}">Colonia</label>
                                <div class="mt-2">
                                    <input id="aa_neighborhood" value="{{ $val('neighborhood') }}" name="neighborhood" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="aa_postal_code" class="{{ $labelClass }}">Código Postal</label>
                                <div class="mt-2">
                                    <input id="aa_postal_code" value="{{ $val('postal_code') }}" name="postal_code" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-lits-blue-450 px-6 py-4">
                        <button type="button" @click="open = false" class="text-sm font-semibold text-gray-900 dark:text-gray-50 hover:cursor-pointer">Cancelar</button>
                        <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Agregar dirección</button>
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
            const p = 'aa_';
            const form = document.getElementById('custom-agent-address-create-form');
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
            enableFormValidation('custom-agent-address-create-form', { errors: @json($mine ? $errors->messages() : (object) []) });

            // Avoid saving twice on a double click.
            form.addEventListener('submit', function (e) {
                const submit = form.querySelector('button[type="submit"]');
                if (e.defaultPrevented) return;
                if (submit.disabled) { e.preventDefault(); return; }
                submit.disabled = true;
            });

            // Closing the modal without saving leaves it empty again.
            window.addEventListener('add-custom-agent-address-modal-closed', function () {
                textFields.forEach(function (field) { el(field).value = ''; });
                location.clear();
                form.querySelectorAll('[id$="-error"]').forEach(function (node) { node.classList.add('hidden'); });
                form.querySelectorAll('input[type="text"], select').forEach(function (node) {
                    node.classList.remove('outline-red-500', 'dark:outline-red-500');
                    node.classList.add('outline-gray-300', 'dark:outline-gray-600');
                });
                const serverErrors = document.getElementById('custom-agent-address-create-errors');
                if (serverErrors) { serverErrors.remove(); }
            });
        })();
    </script>
@endpush
