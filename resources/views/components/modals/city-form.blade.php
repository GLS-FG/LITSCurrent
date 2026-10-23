@props(['mode', 'countries', 'city' => null])
{{--
    Modal para crear ("create") y editar ("edit") una ciudad del catálogo. Mismas validaciones que
    city/create y city/edit (que siguen existiendo): validación en vivo, selects en cascada
    País -> Estado (partials.location-selects) y las reglas de CityPostRequest / CityPutRequest en
    servidor. Solo el de crear trae la opción "Quiero crear un nuevo estado" (nombre y abreviación).

    - Crear: lo abre `open-add-city-modal` (botón "Nueva ciudad" de cities.index).
    - Editar en cities.index: se monta sin ciudad ($city = null); el ícono de editar de una fila
      dispara `request-edit-city` con los valores de la fila (ya están cargados, no hace falta otra
      petición) y el modal los pone y se abre.
    - Editar en cities.show: se monta con la ciudad y el botón "Editar ciudad" solo dispara
      `open-edit-city-modal`.
    - Si el servidor rechaza el formulario, Laravel regresa a la página de origen y el modal se
      reabre solo con los errores y el old(). La ciudad a editar se recupera de `_record`,
      casteado a entero, nunca usado como URL.
    Al cerrarlo sin guardar se descarta lo escrito. Los ids del modal de editar llevan el
    prefijo `edit_` para no chocar con los del de crear en la misma página.
--}}
@php
    $isEdit = $mode === 'edit';
    $p = $isEdit ? 'edit_' : '';
    $formId = 'city-' . $mode . '-form';
    $drawerKey = 'city-' . $mode;
    $eventName = $isEdit ? 'edit-city-modal' : 'add-city-modal';
    $mine = old('_drawer') === $drawerKey;
    if ($isEdit && ! $city && $mine && (int) old('_record')) {
        $city = \App\Models\City::with('state')->find((int) old('_record'));
    }
    $saved = fn ($field) => ($isEdit && $city)
        ? ($field === 'country_id' ? $city->state?->country_id : $city->{$field})
        : null;
    $val = fn ($field) => $mine ? old($field, $saved($field)) : $saved($field);
    $formAction = $isEdit
        ? ($city ? route('cities.update', ['city' => $city->id]) : '')
        : route('cities.store');
    $inputClass = 'block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 disabled:bg-gray-100 dark:disabled:bg-gray-800 disabled:cursor-not-allowed sm:text-sm/6';
    $selectClass = 'block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 disabled:bg-gray-100 dark:disabled:bg-gray-800 disabled:cursor-not-allowed sm:text-sm/6';
    $labelClass = 'block text-sm/6 font-medium text-gray-900 dark:text-gray-50';
    $selectedCountry = $val('country_id');
    $startNewState = ! $isEdit && $mine && old('new_state') === 'on';
@endphp
<div
    class="relative z-90"
    x-data="{
        open: {{ $mine && $errors->any() ? 'true' : 'false' }},
        newState: {{ $startNewState ? 'true' : 'false' }},
        syncNewState() {
            if (@js($isEdit)) return;
            const g = (id) => document.getElementById(@js($p) + id);
            if (this.newState) {
                g('state_id').value = '';
            } else {
                g('new_state_name').value = '';
                g('new_state_short_name').value = '';
            }
        },
    }"
    x-init="
        $watch('open', value => { if (!value) { newState = false; window.dispatchEvent(new CustomEvent('{{ $eventName }}-closed')) } });
        $watch('newState', () => syncNewState());
    "
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
                        <input type="hidden" id="{{ $p }}record_id" name="_record" value="{{ $city?->id }}">
                    @endif
                    <input type="hidden" name="_drawer" value="{{ $drawerKey }}">

                    <div class="flex items-start justify-between gap-3 border-b border-gray-200 dark:border-lits-blue-450 px-6 py-5">
                        <div>
                            <h3 id="{{ $formId }}-title" class="text-base font-semibold text-gray-900 dark:text-gray-50">{{ $isEdit ? 'Editar ciudad' : 'Nueva ciudad' }}</h3>
                            <p id="{{ $p }}subtitle" class="text-xs text-gray-500 dark:text-gray-400">{{ $isEdit ? $city?->name : 'Ingresa la información necesaria de la ciudad.' }}</p>
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
                                collect($errors->messages())->except(['name', 'country_id', 'state_id', 'new_state_name', 'new_state_short_name'])->all()
                            );
                        @endphp
                        @if ($mine && $unmatchedErrors->any())
                            <div id="{{ $formId }}-errors">
                                <x-alerts.error :message="$isEdit ? 'Para editar la ciudad soluciona los siguientes errores:' : 'Para crear la ciudad soluciona los siguientes errores:'" :errors="$unmatchedErrors" class="mb-6" />
                            </div>
                        @endif

                        <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                            <div class="sm:col-span-6">
                                <label for="{{ $p }}name" class="{{ $labelClass }}">Nombre</label>
                                <div class="mt-2">
                                    <input id="{{ $p }}name" value="{{ $val('name') }}" maxlength="100" placeholder="Hermosillo" name="name" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div class="sm:col-span-3">
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
                            <div class="sm:col-span-3">
                                <label for="{{ $p }}state_id" class="{{ $labelClass }}">Estado</label>
                                <div class="mt-2">
                                    <select id="{{ $p }}state_id" name="state_id" x-bind:disabled="newState" class="{{ $selectClass }}">
                                        <option value="">Selecciona un estado</option>
                                    </select>
                                </div>
                            </div>

                            @unless($isEdit)
                                <div class="sm:col-span-6 border-b border-gray-200 dark:border-lits-blue-450"></div>
                                <div class="sm:col-span-6">
                                    <div class="flex gap-3">
                                        <div class="flex h-6 shrink-0 items-center">
                                            <div class="group grid size-4 grid-cols-1">
                                                <input id="{{ $p }}new_state" type="checkbox" name="new_state" x-model="newState" aria-describedby="{{ $p }}new_state-description" class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-lits-blue-550 checked:border-indigo-600 checked:bg-indigo-600 indeterminate:border-indigo-600 indeterminate:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 forced-colors:appearance-auto" />
                                                <i class="fa-solid fa-check text-xs pointer-events-none col-start-1 row-start-1 self-center justify-self-center text-white group-has-disabled:text-gray-950/25 opacity-0 group-has-checked:opacity-100 group-has-indeterminate:opacity-100"></i>
                                            </div>
                                        </div>
                                        <div class="text-sm/6">
                                            <label for="{{ $p }}new_state" class="font-medium text-gray-900 dark:text-gray-50">Quiero crear un nuevo estado</label>
                                            <p id="{{ $p }}new_state-description" class="text-gray-500 dark:text-gray-400">Selecciona esta opción cuando quieras crear un nuevo estado.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="sm:col-span-4">
                                    <label for="{{ $p }}new_state_name" class="{{ $labelClass }}">Nuevo Estado Nombre</label>
                                    <div class="mt-2">
                                        <input id="{{ $p }}new_state_name" value="{{ $val('new_state_name') }}" x-bind:disabled="!newState" maxlength="100" x-bind:placeholder="newState ? 'Sonora' : ''" name="new_state_name" type="text" autocomplete="off" class="{{ $inputClass }}">
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="{{ $p }}new_state_short_name" class="{{ $labelClass }}">Nuevo Estado Abreviado</label>
                                    <div class="mt-2">
                                        <input id="{{ $p }}new_state_short_name" value="{{ $val('new_state_short_name') }}" x-bind:disabled="!newState" maxlength="5" x-bind:placeholder="newState ? 'SON' : ''" name="new_state_short_name" type="text" autocomplete="off" class="{{ $inputClass }}">
                                    </div>
                                </div>
                            @endunless
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-lits-blue-450 px-6 py-4">
                        <button type="button" @click="open = false" class="text-sm font-semibold text-gray-900 dark:text-gray-50 hover:cursor-pointer">Cancelar</button>
                        <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">{{ $isEdit ? 'Guardar' : 'Crear ciudad' }}</button>
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
            const textFields = isEdit ? ['name'] : ['name', 'new_state_name', 'new_state_short_name'];
            const newStateChecked = () => !isEdit && el('new_state').checked;

            const location = initLocationSelects({
                prefix: p,
                old: {
                    country: @json($val('country_id')),
                    state: @json($val('state_id')),
                },
            });
            attachLiveValidation(p + 'name', { required: true, max: 100 });
            attachLiveValidation(p + 'country_id', { required: true, requiredMessage: 'Selecciona un país.' });
            attachLiveValidation(p + 'state_id', { required: true, requiredMessage: 'Selecciona un estado.', when: () => !newStateChecked(), watch: [p + 'new_state'] });
            if (!isEdit) {
                attachLiveValidation(p + 'new_state_name', { required: true, max: 100, when: newStateChecked, watch: [p + 'new_state'] });
                attachLiveValidation(p + 'new_state_short_name', { required: true, max: 50, when: newStateChecked, watch: [p + 'new_state'] });
            }
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
                new_state_name: '',
                new_state_short_name: '',
                country: @json($saved('country_id') ? (string) $saved('country_id') : ''),
                state: @json($saved('state_id')),
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
                location.set(snapshot.country, snapshot.state);
                clearMarks();
            });

            // List page (edit mode): fill the modal with the clicked row's values, then open it.
            if (isEdit) window.addEventListener('request-edit-city', function (event) {
                const data = event.detail;
                form.action = data.update_url;
                el('record_id').value = data.id;
                document.getElementById('edit_subtitle').textContent = data.name;
                el('name').value = data.name == null ? '' : data.name;
                snapshot.name = el('name').value;
                location.set(data.country_id, data.state_id);
                snapshot.country = String(data.country_id);
                snapshot.state = data.state_id;
                clearMarks();
                window.dispatchEvent(new CustomEvent('open-edit-city-modal'));
            });
        })();
    </script>
@endpush
