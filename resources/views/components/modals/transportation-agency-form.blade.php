@props(['mode', 'agency' => null])
{{--
    Modal para crear ("create") y editar ("edit") un transportista. Los dos comparten los mismos
    campos, por eso viven en un solo componente. Mismas validaciones que
    transportation-agency/create y transportation-agency/edit (que siguen existiendo): validación
    en vivo y las reglas de TransportationAgencyPostRequest / TransportationAgencyPutRequest en
    servidor.

    - Crear: lo abre `open-add-transportation-agency-modal` (botón "Nueva transportista" de
      transportation-agencies.index).
    - Editar en transportation-agencies.index: se monta sin transportista ($agency = null); el
      ícono de editar de una fila dispara `request-edit-transportation-agency` con los valores de
      la fila (ya están cargados, no hace falta otra petición) y el modal los pone y se abre.
    - Editar en transportation-agencies.show: se monta con el transportista y el botón "Editar
      transportista" solo dispara `open-edit-transportation-agency-modal`.
    - Si el servidor rechaza el formulario, Laravel regresa a la página de origen y el modal se
      reabre solo con los errores y el old(). El transportista a editar se recupera de `_record`,
      casteado a entero, nunca usado como URL.
    Al cerrarlo sin guardar se descarta lo escrito. Los ids del modal de editar llevan el
    prefijo `edit_` para no chocar con los del de crear en la misma página.
--}}
@php
    $isEdit = $mode === 'edit';
    $p = $isEdit ? 'edit_' : '';
    $formId = 'transportation-agency-' . $mode . '-form';
    $drawerKey = 'transportation-agency-' . $mode;
    $eventName = $isEdit ? 'edit-transportation-agency-modal' : 'add-transportation-agency-modal';
    $mine = old('_drawer') === $drawerKey;
    if ($isEdit && ! $agency && $mine && (int) old('_record')) {
        $agency = \App\Models\TransportationAgency::find((int) old('_record'));
    }
    $saved = fn ($field) => ($isEdit && $agency) ? $agency->{$field} : null;
    $val = fn ($field) => $mine ? old($field, $saved($field)) : $saved($field);
    $formAction = $isEdit
        ? ($agency ? route('transportation-agencies.update', ['transportation_agency' => $agency->id]) : '')
        : route('transportation-agencies.store');
    $inputClass = 'block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6';
    $labelClass = 'block text-sm/6 font-medium text-gray-900 dark:text-gray-50';
    $textFields = ['name', 'company_name', 'rfc', 'caat_code', 'scac_code', 'contact_name', 'phone1', 'phone2'];
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
                        <input type="hidden" id="{{ $p }}record_id" name="_record" value="{{ $agency?->id }}">
                    @endif
                    <input type="hidden" name="_drawer" value="{{ $drawerKey }}">

                    <div class="flex items-start justify-between gap-3 border-b border-gray-200 dark:border-lits-blue-450 px-6 py-5">
                        <div>
                            <h3 id="{{ $formId }}-title" class="text-base font-semibold text-gray-900 dark:text-gray-50">{{ $isEdit ? 'Editar transportista' : 'Nueva transportista' }}</h3>
                            <p id="{{ $p }}subtitle" class="text-xs text-gray-500 dark:text-gray-400">{{ $isEdit ? $agency?->name : 'Ingresa la información necesaria del transportista.' }}</p>
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
                                collect($errors->messages())->except(['name', 'company_name', 'rfc', 'caat_code', 'scac_code', 'contact_name', 'phone1', 'phone2'])->all()
                            );
                        @endphp
                        @if ($mine && $unmatchedErrors->any())
                            <div id="{{ $formId }}-errors">
                                <x-alerts.error :message="$isEdit ? 'Para editar el transportista soluciona los siguientes errores:' : 'Para crear el transportista soluciona los siguientes errores:'" :errors="$unmatchedErrors" class="mb-6" />
                            </div>
                        @endif

                        <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                            <div class="sm:col-span-3">
                                <label for="{{ $p }}name" class="{{ $labelClass }}">Razón social</label>
                                <div class="mt-2">
                                    <input id="{{ $p }}name" value="{{ $val('name') }}" name="name" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div class="sm:col-span-3">
                                <label for="{{ $p }}company_name" class="{{ $labelClass }}">Nombre comercial</label>
                                <div class="mt-2">
                                    <input id="{{ $p }}company_name" value="{{ $val('company_name') }}" name="company_name" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="{{ $p }}rfc" class="{{ $labelClass }}">RFC</label>
                                <div class="mt-2">
                                    <input id="{{ $p }}rfc" value="{{ $val('rfc') }}" name="rfc" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="{{ $p }}caat_code" class="{{ $labelClass }}">CAAT</label>
                                <div class="mt-2">
                                    <input id="{{ $p }}caat_code" value="{{ $val('caat_code') }}" name="caat_code" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="{{ $p }}scac_code" class="{{ $labelClass }}">SCAC</label>
                                <div class="mt-2">
                                    <input id="{{ $p }}scac_code" value="{{ $val('scac_code') }}" name="scac_code" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>

                            <div class="sm:col-span-6">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-50">Contacto</h4>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Llena la información de la persona de contacto y teléfonos.</p>
                            </div>
                            <div class="sm:col-span-6">
                                <label for="{{ $p }}contact_name" class="{{ $labelClass }}">Persona de contacto</label>
                                <div class="mt-2">
                                    <input id="{{ $p }}contact_name" value="{{ $val('contact_name') }}" name="contact_name" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div class="sm:col-span-3">
                                <label for="{{ $p }}phone1" class="{{ $labelClass }}">Teléfono</label>
                                <div class="mt-2">
                                    <input id="{{ $p }}phone1" value="{{ $val('phone1') }}" name="phone1" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div class="sm:col-span-3">
                                <label for="{{ $p }}phone2" class="{{ $labelClass }}">Otro teléfono</label>
                                <div class="mt-2">
                                    <input id="{{ $p }}phone2" value="{{ $val('phone2') }}" name="phone2" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-lits-blue-450 px-6 py-4">
                        <button type="button" @click="open = false" class="text-sm font-semibold text-gray-900 dark:text-gray-50 hover:cursor-pointer">Cancelar</button>
                        <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">{{ $isEdit ? 'Guardar' : 'Crear transportista' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('custom_script')
    @include('partials.live-validation')
    <script type="module">
        (function () {
            const p = @js($p);
            const isEdit = @js($isEdit);
            const form = document.getElementById(@js($formId));
            const el = (name) => document.getElementById(p + name);
            const textFields = @js($textFields);

            attachLiveValidation(p + 'name', { required: true, max: 100 });
            attachLiveValidation(p + 'company_name', { required: true, max: 200 });
            attachLiveValidation(p + 'rfc', { max: 13 });
            attachLiveValidation(p + 'caat_code', { max: 4 });
            attachLiveValidation(p + 'scac_code', { max: 4 });
            attachLiveValidation(p + 'contact_name', { required: true, max: 100 });
            attachLiveValidation(p + 'phone1', { required: true, max: 20 });
            attachLiveValidation(p + 'phone2', { max: 20 });
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
            const snapshot = @json(collect($textFields)->mapWithKeys(fn ($field) => [$field => $saved($field) ?? '']));

            const clearMarks = function () {
                form.querySelectorAll('[id$="-error"]').forEach(function (node) { node.classList.add('hidden'); });
                form.querySelectorAll('input[type="text"]').forEach(function (node) {
                    node.classList.remove('outline-red-500', 'dark:outline-red-500');
                    node.classList.add('outline-gray-300', 'dark:outline-gray-600');
                });
                const serverErrors = document.getElementById(@js($formId . '-errors'));
                if (serverErrors) { serverErrors.remove(); }
            };

            window.addEventListener(@js($eventName . '-closed'), function () {
                textFields.forEach(function (field) { el(field).value = snapshot[field]; });
                clearMarks();
            });

            // List page (edit mode): fill the modal with the clicked row's values, then open it.
            if (isEdit) window.addEventListener('request-edit-transportation-agency', function (event) {
                const data = event.detail;
                form.action = data.update_url;
                el('record_id').value = data.id;
                document.getElementById('edit_subtitle').textContent = data.name;
                textFields.forEach(function (field) {
                    el(field).value = data[field] == null ? '' : data[field];
                    snapshot[field] = el(field).value;
                });
                clearMarks();
                window.dispatchEvent(new CustomEvent('open-edit-transportation-agency-modal'));
            });
        })();
    </script>
@endpush
