@props(['kind', 'mode', 'serviceType', 'serviceClass' => null, 'serviceMode' => null, 'classType' => null])
{{--
    Modal para crear ("create") y editar ("edit") cada nivel del árbol de Service Types: service class,
    service mode, class type y service level. Los cuatro comparten Código y Nombre (el service mode
    también lleva Icono), por eso viven en un solo componente; `kind` elige el nivel. Mismas validaciones
    que las páginas viejas create/edit (que siguen existiendo): validación en vivo y las reglas de
    ServiceTypeRequest / ServiceModeRequest en servidor.

    - Crear: lo abre `open-add-{kind}-modal` (botón "Nuevo ..." de la lista del nivel).
    - Editar: se monta sin registro; el ícono de editar de una fila dispara `request-edit-{kind}` con los
      valores de la fila (ya están cargados, no hace falta otra petición) y el modal los pone y se abre.
    - Si el servidor rechaza el formulario, Laravel regresa a la lista y el modal se reabre solo con los
      errores y el old(). El registro a editar se recupera de `_record`, casteado a entero, nunca usado
      como URL.
    Al cerrarlo sin guardar se descarta lo escrito. Los ids del modal de editar llevan el prefijo `edit_`
    para no chocar con los del de crear en la misma página.
--}}
@php
    $cfg = match ($kind) {
        'service-class' => [
            'label' => 'service class', 'model' => \App\Models\ServiceClass::class, 'icon' => false,
            'routeBase' => 'service-types.service-classes', 'key' => 'service_class',
            'params' => ['service_type' => $serviceType],
        ],
        'service-mode' => [
            'label' => 'service mode', 'model' => \App\Models\ServiceMode::class, 'icon' => true,
            'routeBase' => 'service-types.service-classes.service-modes', 'key' => 'service_mode',
            'params' => ['service_type' => $serviceType, 'service_class' => $serviceClass],
        ],
        'class-type' => [
            'label' => 'class type', 'model' => \App\Models\ClassType::class, 'icon' => false,
            'routeBase' => 'service-types.service-classes.service-modes.class-types', 'key' => 'class_type',
            'params' => ['service_type' => $serviceType, 'service_class' => $serviceClass, 'service_mode' => $serviceMode],
        ],
        'service-level' => [
            'label' => 'service level', 'model' => \App\Models\ServiceLevel::class, 'icon' => false,
            'routeBase' => 'service-types.service-classes.service-modes.class-types.service-levels', 'key' => 'service_level',
            'params' => ['service_type' => $serviceType, 'service_class' => $serviceClass, 'service_mode' => $serviceMode, 'class_type' => $classType],
        ],
    };
    $isEdit = $mode === 'edit';
    $p = $isEdit ? 'edit_' : '';
    $formId = $kind . '-' . $mode . '-form';
    $drawerKey = $kind . '-' . $mode;
    $eventName = ($isEdit ? 'edit-' : 'add-') . $kind . '-modal';
    $mine = old('_drawer') === $drawerKey;
    $record = ($isEdit && $mine && (int) old('_record')) ? $cfg['model']::find((int) old('_record')) : null;
    $saved = fn ($field) => $record?->{$field};
    $val = fn ($field) => $mine ? old($field, $saved($field)) : null;
    $formAction = $isEdit
        ? ($record ? route($cfg['routeBase'] . '.update', $cfg['params'] + [$cfg['key'] => $record->id]) : '')
        : route($cfg['routeBase'] . '.store', $cfg['params']);
    $inputClass = 'block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6';
    $labelClass = 'block text-sm/6 font-medium text-gray-900 dark:text-gray-50';
    $fields = $cfg['icon'] ? ['code', 'name', 'icon'] : ['code', 'name'];
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
                class="relative flex max-h-[calc(100vh-3rem)] w-full flex-col overflow-hidden rounded-lg bg-white dark:bg-lits-blue-550 shadow-xl sm:max-w-lg"
            >
                <form id="{{ $formId }}" action="{{ $formAction }}" method="POST" autocomplete="off" class="flex min-h-0 flex-1 flex-col">
                    @csrf
                    @if($isEdit)
                        @method('PUT')
                        <input type="hidden" id="{{ $p }}record_id" name="_record" value="{{ $record?->id }}">
                    @endif
                    <input type="hidden" name="_drawer" value="{{ $drawerKey }}">

                    <div class="flex items-start justify-between gap-3 border-b border-gray-200 dark:border-lits-blue-450 px-6 py-5">
                        <div>
                            <h3 id="{{ $formId }}-title" class="text-base font-semibold text-gray-900 dark:text-gray-50">{{ ($isEdit ? 'Editar ' : 'Nuevo ') . $cfg['label'] }}</h3>
                            <p id="{{ $p }}subtitle" class="text-xs text-gray-500 dark:text-gray-400">{{ $isEdit ? $record?->code : 'Ingresa la información necesaria del ' . $cfg['label'] . '.' }}</p>
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
                                collect($errors->messages())->except($fields)->all()
                            );
                        @endphp
                        @if ($mine && $unmatchedErrors->any())
                            <div id="{{ $formId }}-errors">
                                <x-alerts.error :message="'Para ' . ($isEdit ? 'editar' : 'crear') . ' el ' . $cfg['label'] . ' soluciona los siguientes errores:'" :errors="$unmatchedErrors" class="mb-6" />
                            </div>
                        @endif

                        <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                            <div class="sm:col-span-2">
                                <label for="{{ $p }}code" class="{{ $labelClass }}">Código</label>
                                <div class="mt-2">
                                    <input id="{{ $p }}code" value="{{ $val('code') }}" maxlength="10" name="code" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div class="sm:col-span-4">
                                <label for="{{ $p }}name" class="{{ $labelClass }}">Nombre</label>
                                <div class="mt-2">
                                    <input id="{{ $p }}name" value="{{ $val('name') }}" name="name" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            @if($cfg['icon'])
                                <div class="sm:col-span-6">
                                    <label for="{{ $p }}icon" class="{{ $labelClass }}">Icono</label>
                                    <div class="mt-2">
                                        <input id="{{ $p }}icon" value="{{ $val('icon') }}" placeholder="fa-regular fa-plane" name="icon" type="text" autocomplete="off" class="{{ $inputClass }}">
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-lits-blue-450 px-6 py-4">
                        <button type="button" @click="open = false" class="text-sm font-semibold text-gray-900 dark:text-gray-50 hover:cursor-pointer">Cancelar</button>
                        <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">{{ $isEdit ? 'Guardar' : 'Crear ' . $cfg['label'] }}</button>
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
            const textFields = @js($fields);

            attachLiveValidation(p + 'code', { required: true, max: 10 });
            attachLiveValidation(p + 'name', { required: true, max: 255 });
            @if($cfg['icon'])
                attachLiveValidation(p + 'icon', { required: true, max: 255 });
            @endif
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

            // The values to go back to when the modal is closed without saving: the saved record when
            // editing (replaced by the clicked row), empty when creating. Taken from the saved data,
            // not from the DOM, which may hold old() input.
            const snapshot = {};
            textFields.forEach(function (field) { snapshot[field] = ''; });

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
            if (isEdit) window.addEventListener(@js('request-edit-' . $kind), function (event) {
                const data = event.detail;
                form.action = data.update_url;
                el('record_id').value = data.id;
                document.getElementById('edit_subtitle').textContent = data.code;
                textFields.forEach(function (field) {
                    el(field).value = data[field] == null ? '' : data[field];
                    snapshot[field] = el(field).value;
                });
                clearMarks();
                window.dispatchEvent(new CustomEvent(@js('open-edit-' . $kind . '-modal')));
            });
        })();
    </script>
@endpush
