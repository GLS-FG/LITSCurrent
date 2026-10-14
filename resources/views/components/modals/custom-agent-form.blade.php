@props(['mode', 'countries' => [], 'agent' => null])
{{--
    Modal para crear ("create") y editar ("edit") una agencia aduanal. Mismas validaciones que
    custom/agent/create y custom/agent/edit (que siguen existiendo): validación en vivo y las
    reglas de CustomAgentPostRequest / CustomAgentPutRequest en servidor.

    - Crear: pide los datos de contacto y la primera dirección (selects en cascada País ->
      Estado -> Ciudad, partials.location-selects). Lo abre `open-add-custom-agent-modal`.
    - Editar: solo los datos de contacto (las direcciones se editan aparte, como antes).
        - En custom-agents.index se monta sin agente ($agent = null); el ícono de editar de una
          fila dispara `request-edit-custom-agent` con los valores de la fila y el modal los
          pone y se abre.
        - En custom-agents.show se monta con el agente y el botón "Editar agente" solo dispara
          `open-edit-custom-agent-modal`.
    - Si el servidor rechaza el formulario, Laravel regresa a la página de origen y el modal se
      reabre solo con los errores y el old(). El agente a editar se recupera de `_record`,
      casteado a entero, nunca usado como URL.
    Al cerrarlo sin guardar se descarta lo escrito. Los ids del modal de editar llevan el
    prefijo `edit_` para no chocar con los del de crear en la misma página.
--}}
@php
    $isEdit = $mode === 'edit';
    $p = $isEdit ? 'edit_' : '';
    $formId = 'custom-agent-' . $mode . '-form';
    $drawerKey = 'custom-agent-' . $mode;
    $eventName = $isEdit ? 'edit-custom-agent-modal' : 'add-custom-agent-modal';
    $mine = old('_drawer') === $drawerKey;
    if ($isEdit && ! $agent && $mine && (int) old('_record')) {
        $agent = \App\Models\CustomAgent::find((int) old('_record'));
    }
    $saved = fn ($field) => ($isEdit && $agent) ? $agent->{$field} : null;
    $val = fn ($field) => $mine ? old($field, $saved($field)) : $saved($field);
    $formAction = $isEdit
        ? ($agent ? route('custom-agents.update', ['custom_agent' => $agent->id]) : '')
        : route('custom-agents.store');
    $inputClass = 'block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6';
    $selectClass = 'block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6';
    $labelClass = 'block text-sm/6 font-medium text-gray-900 dark:text-gray-50';
    $selectedCountry = $val('country_id');
    $textFields = $isEdit
        ? ['company_name', 'name', 'last_name', 'patent', 'phone1']
        : ['company_name', 'name', 'last_name', 'patent', 'phone1', 'street_name', 'street_no', 'neighborhood', 'postal_code'];
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
                class="relative flex max-h-[calc(100vh-3rem)] w-full flex-col overflow-hidden rounded-lg bg-white dark:bg-lits-blue-550 shadow-xl sm:max-w-2xl"
            >
                <form id="{{ $formId }}" action="{{ $formAction }}" method="POST" autocomplete="off" class="flex min-h-0 flex-1 flex-col">
                    @csrf
                    @if($isEdit)
                        @method('PUT')
                        <input type="hidden" id="{{ $p }}record_id" name="_record" value="{{ $agent?->id }}">
                    @endif
                    <input type="hidden" name="_drawer" value="{{ $drawerKey }}">

                    <div class="flex items-start justify-between gap-3 border-b border-gray-200 dark:border-lits-blue-450 px-6 py-5">
                        <div>
                            <h3 id="{{ $formId }}-title" class="text-base font-semibold text-gray-900 dark:text-gray-50">{{ $isEdit ? 'Editar agente aduanal' : 'Nuevo agente aduanal' }}</h3>
                            <p id="{{ $p }}subtitle" class="text-xs text-gray-500 dark:text-gray-400">{{ $isEdit ? $agent?->company_name : 'Ingresa la información necesaria del agente aduanal.' }}</p>
                        </div>
                        <button type="button" @click="open = false" class="rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                            <span class="sr-only">Cerrar</span>
                            <i class="fa-regular fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <div class="flex-1 min-h-0 overflow-y-auto px-6 py-5">
                        @if ($mine && $errors->any())
                            <div id="{{ $formId }}-errors">
                                <x-alerts.error :message="$isEdit ? 'Para editar el agente aduanal soluciona los siguientes errores:' : 'Para crear el agente aduanal soluciona los siguientes errores:'" :errors="$errors" class="mb-6" />
                            </div>
                        @endif

                        <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                            <div class="sm:col-span-6">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-50">Datos de contacto</h4>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Llena los datos de contacto del agente como su nombre, patente y teléfono.</p>
                            </div>
                            <div class="sm:col-span-6">
                                <label for="{{ $p }}company_name" class="{{ $labelClass }}">Razón social</label>
                                <div class="mt-2">
                                    <input id="{{ $p }}company_name" value="{{ $val('company_name') }}" name="company_name" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div class="sm:col-span-3">
                                <label for="{{ $p }}name" class="{{ $labelClass }}">Nombre</label>
                                <div class="mt-2">
                                    <input id="{{ $p }}name" value="{{ $val('name') }}" name="name" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div class="sm:col-span-3">
                                <label for="{{ $p }}last_name" class="{{ $labelClass }}">Apellido</label>
                                <div class="mt-2">
                                    <input id="{{ $p }}last_name" value="{{ $val('last_name') }}" name="last_name" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div class="sm:col-span-3">
                                <label for="{{ $p }}patent" class="{{ $labelClass }}">Patente</label>
                                <div class="mt-2">
                                    <input id="{{ $p }}patent" value="{{ $val('patent') }}" name="patent" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div class="sm:col-span-3">
                                <label for="{{ $p }}phone1" class="{{ $labelClass }}">Teléfono</label>
                                <div class="mt-2">
                                    <input id="{{ $p }}phone1" value="{{ $val('phone1') }}" name="phone1" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>

                            @unless($isEdit)
                                <div class="sm:col-span-6">
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-50">Dirección</h4>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Llena los datos de la dirección del agente.</p>
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="country_id" class="{{ $labelClass }}">País</label>
                                    <div class="mt-2">
                                        <select id="country_id" name="country_id" class="{{ $selectClass }}">
                                            <option value="">Selecciona un país</option>
                                            @foreach($countries as $country)
                                                <option value="{{ $country->id }}" @selected($selectedCountry == $country->id)>{{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="state_id" class="{{ $labelClass }}">Estado</label>
                                    <div class="mt-2">
                                        <select id="state_id" name="state_id" class="{{ $selectClass }}">
                                            <option value="">Selecciona un estado</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="city_id" class="{{ $labelClass }}">Ciudad</label>
                                    <div class="mt-2">
                                        <select id="city_id" name="city_id" class="{{ $selectClass }}">
                                            <option value="">Selecciona una ciudad</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="sm:col-span-6">
                                    <label for="street_name" class="{{ $labelClass }}">Calle</label>
                                    <div class="mt-2">
                                        <input id="street_name" value="{{ $val('street_name') }}" name="street_name" type="text" autocomplete="off" class="{{ $inputClass }}">
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="street_no" class="{{ $labelClass }}">Número</label>
                                    <div class="mt-2">
                                        <input id="street_no" value="{{ $val('street_no') }}" name="street_no" type="text" autocomplete="off" class="{{ $inputClass }}">
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="neighborhood" class="{{ $labelClass }}">Colonia</label>
                                    <div class="mt-2">
                                        <input id="neighborhood" value="{{ $val('neighborhood') }}" name="neighborhood" type="text" autocomplete="off" class="{{ $inputClass }}">
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="postal_code" class="{{ $labelClass }}">Código Postal</label>
                                    <div class="mt-2">
                                        <input id="postal_code" value="{{ $val('postal_code') }}" name="postal_code" type="text" autocomplete="off" class="{{ $inputClass }}">
                                    </div>
                                </div>
                            @endunless
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-lits-blue-450 px-6 py-4">
                        <button type="button" @click="open = false" class="text-sm font-semibold text-gray-900 dark:text-gray-50 hover:cursor-pointer">Cancelar</button>
                        <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">{{ $isEdit ? 'Guardar' : 'Crear agente' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('custom_script')
    @include('partials.live-validation')
    @unless($isEdit)
        @include('partials.location-selects')
    @endunless
    <script type="module">
        (function () {
            const p = @js($p);
            const isEdit = @js($isEdit);
            const form = document.getElementById(@js($formId));
            const el = (name) => document.getElementById(p + name);
            const textFields = @js($textFields);

            attachLiveValidation(p + 'company_name', { required: true, min: 3, max: 200 });
            attachLiveValidation(p + 'name', { required: true, min: 3 });
            attachLiveValidation(p + 'last_name', { required: true });
            attachLiveValidation(p + 'patent', { required: true });
            attachLiveValidation(p + 'phone1', { required: true, numeric: true, min: 10 });

            let location = null;
            if (!isEdit) {
                location = initLocationSelects({
                    old: {
                        country: @json($val('country_id')),
                        state: @json($val('state_id')),
                        city: @json($val('city_id')),
                    },
                });
                attachLiveValidation('country_id', { required: true, requiredMessage: 'Selecciona un país.' });
                attachLiveValidation('state_id', { required: true, requiredMessage: 'Selecciona un estado.' });
                attachLiveValidation('city_id', { required: true, requiredMessage: 'Selecciona una ciudad.' });
                attachLiveValidation('street_name', { required: true });
                attachLiveValidation('street_no', { required: true, max: 50 });
                attachLiveValidation('neighborhood', { required: true, max: 100 });
                attachLiveValidation('postal_code', { required: true, numeric: true, min: 5 });
            }

            // Avoid submitting twice on a double click.
            form.addEventListener('submit', function (e) {
                const submit = form.querySelector('button[type="submit"]');
                if (submit.disabled) { e.preventDefault(); return; }
                submit.disabled = true;
            });

            // The values to go back to when the modal is closed without saving: the saved
            // record when editing (replaced by the clicked row on the list page), empty when
            // creating. Taken from the saved data, not from the DOM, which may hold old() input.
            const snapshot = {
                @foreach($textFields as $field)
                    {{ $field }}: @json($saved($field) ?? ''),
                @endforeach
            };

            const clearMarks = function () {
                form.querySelectorAll('[id$="-error"]').forEach(function (node) { node.classList.add('hidden'); });
                form.querySelectorAll('input[type="text"], select').forEach(function (node) {
                    node.classList.remove('outline-red-500', 'dark:outline-red-500');
                    node.classList.add('outline-gray-300', 'dark:outline-gray-600');
                });
                const serverErrors = document.getElementById(@js($formId . '-errors'));
                if (serverErrors) { serverErrors.remove(); }
            };

            window.addEventListener(@js($eventName . '-closed'), function () {
                textFields.forEach(function (field) { el(field).value = snapshot[field]; });
                if (location) location.clear();
                clearMarks();
            });

            // List page (edit mode): fill the modal with the clicked row's values, then open it.
            if (isEdit) window.addEventListener('request-edit-custom-agent', function (event) {
                const data = event.detail;
                form.action = data.update_url;
                el('record_id').value = data.id;
                document.getElementById('edit_subtitle').textContent = data.company_name;
                textFields.forEach(function (field) {
                    el(field).value = data[field] == null ? '' : data[field];
                    snapshot[field] = el(field).value;
                });
                clearMarks();
                window.dispatchEvent(new CustomEvent('open-edit-custom-agent-modal'));
            });
        })();
    </script>
@endpush
