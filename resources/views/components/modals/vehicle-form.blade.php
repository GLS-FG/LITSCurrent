@props(['mode', 'agency'])
{{--
    Modal para agregar ("create") y editar ("edit") una unidad de transporte de un transportista.
    Los dos comparten los mismos campos, por eso viven en un solo componente. Mismas validaciones
    que transportation-agency/vehicle/create y vehicle/edit (que siguen existiendo): validación en
    vivo y las reglas de VehiclePostRequest / VehiclePutRequest en servidor.

    - Crear: lo abre `open-add-vehicle-modal` (botón "Agregar unidad" de transportation-agencies.show).
    - Editar: se monta sin unidad; el lápiz de cada fila dispara `request-edit-vehicle` con los
      valores de la fila (ya están cargados, no hace falta otra petición) y el modal los pone y
      se abre.
    - Si el servidor rechaza el formulario, Laravel regresa a la página de origen y el modal se
      reabre solo con los errores y el old(). La unidad a editar se recupera de `_record`,
      casteado a entero y buscada solo entre las unidades de este transportista, nunca usado como URL.
    Al cerrarlo sin guardar se descarta lo escrito. Los ids del modal de editar llevan el
    prefijo `edit_` para no chocar con los del de crear en la misma página.
--}}
@php
    $isEdit = $mode === 'edit';
    $p = $isEdit ? 'edit_vehicle_' : 'vehicle_';
    $formId = 'vehicle-' . $mode . '-form';
    $drawerKey = 'vehicle-' . $mode;
    $eventName = $isEdit ? 'edit-vehicle-modal' : 'add-vehicle-modal';
    $mine = old('_drawer') === $drawerKey;
    $vehicle = null;
    if ($isEdit && $mine && (int) old('_record')) {
        $vehicle = $agency->vehicles()->find((int) old('_record'));
    }
    $saved = fn ($field) => $vehicle?->{$field};
    $val = fn ($field) => $mine ? old($field, $saved($field)) : $saved($field);
    $formAction = $isEdit
        ? ($vehicle ? route('transportation-agencies.vehicles.update', ['transportation_agency' => $agency->id, 'vehicle' => $vehicle->id]) : '')
        : route('transportation-agencies.vehicles.store', ['transportation_agency' => $agency->id]);
    $inputClass = 'block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6';
    $labelClass = 'block text-sm/6 font-medium text-gray-900 dark:text-gray-50';
    $fields = [
        'eco_number' => ['label' => 'Número económico', 'span' => 2],
        'plates' => ['label' => 'Placas', 'span' => 2],
        'vehicle_type' => ['label' => 'Tipo', 'span' => 2],
        'serial_number' => ['label' => 'Número de serie', 'span' => 2],
        'vehicle_brand' => ['label' => 'Marca y línea', 'span' => 2],
        'vehicle_model' => ['label' => 'Modelo', 'span' => 2],
        'origin' => ['label' => 'Origen', 'span' => 6],
    ];
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
                        <input type="hidden" id="{{ $p }}record_id" name="_record" value="{{ $vehicle?->id }}">
                    @endif
                    <input type="hidden" name="_drawer" value="{{ $drawerKey }}">

                    <div class="flex items-start justify-between gap-3 border-b border-gray-200 dark:border-lits-blue-450 px-6 py-5">
                        <div>
                            <h3 id="{{ $formId }}-title" class="text-base font-semibold text-gray-900 dark:text-gray-50">{{ $isEdit ? 'Editar unidad de transporte' : 'Agregar unidad de transporte' }}</h3>
                            <p id="{{ $p }}subtitle" class="text-xs text-gray-500 dark:text-gray-400">{{ $isEdit ? ($vehicle?->eco_number ?? $agency->name) : 'Unidad de ' . $agency->name }}</p>
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
                                collect($errors->messages())->except(array_keys($fields))->all()
                            );
                        @endphp
                        @if ($mine && $unmatchedErrors->any())
                            <div id="{{ $formId }}-errors">
                                <x-alerts.error :message="$isEdit ? 'Para editar la unidad de transporte soluciona los siguientes errores:' : 'Para agregar la unidad de transporte soluciona los siguientes errores:'" :errors="$unmatchedErrors" class="mb-6" />
                            </div>
                        @endif

                        <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                            @foreach($fields as $name => $field)
                                <div class="{{ $field['span'] === 6 ? 'sm:col-span-6' : 'sm:col-span-2' }}">
                                    <label for="{{ $p }}{{ $name }}" class="{{ $labelClass }}">{{ $field['label'] }}</label>
                                    <div class="mt-2">
                                        <input id="{{ $p }}{{ $name }}" value="{{ $val($name) }}" name="{{ $name }}" type="text" autocomplete="off" class="{{ $inputClass }}">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-lits-blue-450 px-6 py-4">
                        <button type="button" @click="open = false" class="text-sm font-semibold text-gray-900 dark:text-gray-50 hover:cursor-pointer">Cancelar</button>
                        <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">{{ $isEdit ? 'Guardar' : 'Agregar unidad' }}</button>
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
            const textFields = @js(array_keys($fields));

            attachLiveValidation(p + 'eco_number', { required: true, max: 100 });
            attachLiveValidation(p + 'plates', { max: 100 });
            attachLiveValidation(p + 'vehicle_type', { max: 100 });
            attachLiveValidation(p + 'serial_number', { max: 100 });
            attachLiveValidation(p + 'vehicle_brand', { max: 100 });
            attachLiveValidation(p + 'vehicle_model', { max: 10 });
            attachLiveValidation(p + 'origin', { max: 100 });
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
            // record when editing (replaced by the clicked row), empty when creating. Taken from
            // the saved data, not from the DOM, which may hold old() input.
            const snapshot = @json(collect(array_keys($fields))->mapWithKeys(fn ($field) => [$field => $saved($field) ?? '']));

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

            // Edit mode: fill the modal with the clicked row's values, then open it.
            if (isEdit) window.addEventListener('request-edit-vehicle', function (event) {
                const data = event.detail;
                form.action = data.update_url;
                el('record_id').value = data.id;
                document.getElementById(p + 'subtitle').textContent = data.eco_number;
                textFields.forEach(function (field) {
                    el(field).value = data[field] == null ? '' : data[field];
                    snapshot[field] = el(field).value;
                });
                clearMarks();
                window.dispatchEvent(new CustomEvent('open-edit-vehicle-modal'));
            });
        })();
    </script>
@endpush
