@props(['mode', 'countries', 'custom' => null])
{{--
    Modal para crear ("create") y editar ("edit") una aduana del catálogo. Los dos comparten
    los mismos campos, por eso viven en un solo componente. Mismas validaciones que
    custom/create y custom/edit (que siguen existiendo): validación en vivo, selects en
    cascada País -> Estado -> Ciudad (partials.location-selects) y las reglas de
    CustomPostRequest / CustomPutRequest en servidor.

    - Crear: lo abre `open-add-custom-modal` (botón "Nueva aduana" de customs.index).
    - Editar en customs.index: se monta sin aduana ($custom = null); el ícono de editar de una
      fila dispara `request-edit-custom` con los valores de la fila (ya están cargados, no hace
      falta otra petición) y el modal los pone y se abre.
    - Editar en customs.show: se monta con la aduana y el botón "Editar aduana" solo dispara
      `open-edit-custom-modal`.
    - Si el servidor rechaza el formulario, Laravel regresa a la página de origen y el modal se
      reabre solo con los errores y el old(). La aduana a editar se recupera de `_record`,
      casteado a entero, nunca usado como URL.
    Al cerrarlo sin guardar se descarta lo escrito. Los ids del modal de editar llevan el
    prefijo `edit_` para no chocar con los del de crear en la misma página.
--}}
@php
    $isEdit = $mode === 'edit';
    $p = $isEdit ? 'edit_' : '';
    $formId = 'custom-' . $mode . '-form';
    $drawerKey = 'custom-' . $mode;
    $eventName = $isEdit ? 'edit-custom-modal' : 'add-custom-modal';
    $mine = old('_drawer') === $drawerKey;
    if ($isEdit && ! $custom && $mine && (int) old('_record')) {
        $custom = \App\Models\Custom::find((int) old('_record'));
    }
    $saved = fn ($field) => ($isEdit && $custom) ? $custom->{$field} : null;
    $val = fn ($field) => $mine ? old($field, $saved($field)) : $saved($field);
    $formAction = $isEdit
        ? ($custom ? route('customs.update', ['custom' => $custom->id]) : '')
        : route('customs.store');
    $inputClass = 'block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6';
    $selectClass = 'block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6';
    $labelClass = 'block text-sm/6 font-medium text-gray-900 dark:text-gray-50';
    $selectedCountry = $val('country_id');
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
                        <input type="hidden" id="{{ $p }}record_id" name="_record" value="{{ $custom?->id }}">
                    @endif
                    <input type="hidden" name="_drawer" value="{{ $drawerKey }}">

                    <div class="flex items-start justify-between gap-3 border-b border-gray-200 dark:border-lits-blue-450 px-6 py-5">
                        <div>
                            <h3 id="{{ $formId }}-title" class="text-base font-semibold text-gray-900 dark:text-gray-50">{{ $isEdit ? 'Editar aduana' : 'Nueva aduana' }}</h3>
                            <p id="{{ $p }}subtitle" class="text-xs text-gray-500 dark:text-gray-400">{{ $isEdit ? $custom?->denomination : 'Ingresa la información necesaria de la aduana.' }}</p>
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
                                collect($errors->messages())->except(['denomination', 'code', 'country_id', 'state_id', 'city_id'])->all()
                            );
                        @endphp
                        @if ($mine && $unmatchedErrors->any())
                            <div id="{{ $formId }}-errors">
                                <x-alerts.error :message="$isEdit ? 'Para editar la aduana soluciona los siguientes errores:' : 'Para crear la aduana soluciona los siguientes errores:'" :errors="$unmatchedErrors" class="mb-6" />
                            </div>
                        @endif

                        <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                            <div class="sm:col-span-4">
                                <label for="{{ $p }}denomination" class="{{ $labelClass }}">Aduana</label>
                                <div class="mt-2">
                                    <input id="{{ $p }}denomination" value="{{ $val('denomination') }}" placeholder="Denominación de la aduana" name="denomination" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="{{ $p }}code" class="{{ $labelClass }}">Sección</label>
                                <div class="mt-2">
                                    <input id="{{ $p }}code" value="{{ $val('code') }}" maxlength="4" placeholder="00" name="code" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>

                            <div class="sm:col-span-6">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-50">Ubic<span class="hidden">avoidautocomplete</span>ación</h4>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Llena la información de la ubica<span class="hidden">avoidautocomplete</span>ción de la aduana.</p>
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
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-lits-blue-450 px-6 py-4">
                        <button type="button" @click="open = false" class="text-sm font-semibold text-gray-900 dark:text-gray-50 hover:cursor-pointer">Cancelar</button>
                        <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">{{ $isEdit ? 'Guardar' : 'Crear aduana' }}</button>
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
            const p = @js($p);
            const isEdit = @js($isEdit);
            const form = document.getElementById(@js($formId));
            const el = (name) => document.getElementById(p + name);
            const textFields = ['denomination', 'code'];

            const location = initLocationSelects({
                prefix: p,
                old: {
                    country: @json($val('country_id')),
                    state: @json($val('state_id')),
                    city: @json($val('city_id')),
                },
            });
            attachLiveValidation(p + 'denomination', { required: true, max: 150 });
            attachLiveValidation(p + 'code', { required: true, max: 4 });
            attachLiveValidation(p + 'country_id', { required: true, requiredMessage: 'Selecciona un país.' });
            attachLiveValidation(p + 'state_id', { required: true, requiredMessage: 'Selecciona un estado.' });
            attachLiveValidation(p + 'city_id', { required: true, requiredMessage: 'Selecciona una ciudad.' });
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
                denomination: @json($saved('denomination') ?? ''),
                code: @json($saved('code') ?? ''),
                country: @json($saved('country_id') ? (string) $saved('country_id') : ''),
                state: @json($saved('state_id')),
                city: @json($saved('city_id')),
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
                location.set(snapshot.country, snapshot.state, snapshot.city);
                clearMarks();
            });

            // List page (edit mode): fill the modal with the clicked row's values, then open it.
            if (isEdit) window.addEventListener('request-edit-custom', function (event) {
                const data = event.detail;
                form.action = data.update_url;
                el('record_id').value = data.id;
                document.getElementById('edit_subtitle').textContent = data.denomination;
                textFields.forEach(function (field) {
                    el(field).value = data[field] == null ? '' : data[field];
                    snapshot[field] = el(field).value;
                });
                location.set(data.country_id, data.state_id, data.city_id);
                snapshot.country = String(data.country_id);
                snapshot.state = data.state_id;
                snapshot.city = data.city_id;
                clearMarks();
                window.dispatchEvent(new CustomEvent('open-edit-custom-modal'));
            });
        })();
    </script>
@endpush
