@props(['mode', 'documentType' => null])
{{--
    Modal para crear ("create") y editar ("edit") un tipo de documento. Mismas reglas que
    document-type/create y edit (que siguen existiendo): validación en vivo de `name` y las reglas de
    DocumentTypeController (`name` required|string; `shipments`, `customs` y `warehouse` boolean).

    - Crear: lo abre `open-add-document-type-modal` (botón "Nuevo Tipo" de document-types.index).
    - Editar: se monta sin registro; el ícono de editar de una fila dispara `request-edit-document-type`
      con los valores de la fila y el modal los pone y se abre.
    - Si el servidor rechaza el formulario, Laravel regresa a la lista y el modal se reabre solo con
      el error y el old(). El registro a editar se recupera de `_record`, casteado a entero, nunca
      usado como URL.
    Al cerrarlo sin guardar se descarta lo escrito. Los ids del modal de editar llevan el prefijo
    `edit_` para no chocar con los del de crear en la misma página.
--}}
@php
    $isEdit = $mode === 'edit';
    $p = $isEdit ? 'edit_' : '';
    $formId = 'document-type-' . $mode . '-form';
    $drawerKey = 'document-type-' . $mode;
    $eventName = $isEdit ? 'edit-document-type-modal' : 'add-document-type-modal';
    $mine = old('_drawer') === $drawerKey;
    if ($isEdit && ! $documentType && $mine && (int) old('_record')) {
        $documentType = \App\Models\DocumentType::find((int) old('_record'));
    }
    $saved = fn ($field) => ($isEdit && $documentType) ? $documentType->{$field} : null;
    $val = fn ($field) => $mine ? old($field, $saved($field)) : $saved($field);
    $formAction = $isEdit
        ? ($documentType ? route('document-types.update', ['document_type' => $documentType->id]) : '')
        : route('document-types.store');
    $inputClass = 'block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6';
    $labelClass = 'block text-sm/6 font-medium text-gray-900 dark:text-gray-50';
    $tiles = [
        'shipments' => ['Embarques', 'fa-route', 'bg-entity-shipments-50 dark:bg-entity-shipments/15 text-entity-shipments dark:text-cyan-400'],
        'customs'   => ['Aduanas', 'fa-person-military-pointing', 'bg-entity-customs-50 dark:bg-entity-customs/15 text-entity-customs dark:text-violet-400'],
        'warehouse' => ['Almacenes', 'fa-warehouse', 'bg-entity-warehouses-50 dark:bg-entity-warehouses/15 text-entity-warehouses dark:text-amber-400'],
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
                class="relative flex max-h-[calc(100vh-3rem)] w-full flex-col overflow-hidden rounded-lg bg-white dark:bg-lits-blue-550 shadow-xl sm:max-w-lg"
            >
                <form id="{{ $formId }}" action="{{ $formAction }}" method="POST" autocomplete="off" class="flex min-h-0 flex-1 flex-col">
                    @csrf
                    @if($isEdit)
                        @method('PUT')
                        <input type="hidden" id="{{ $p }}record_id" name="_record" value="{{ $documentType?->id }}">
                    @endif
                    <input type="hidden" name="_drawer" value="{{ $drawerKey }}">

                    <div class="flex items-start justify-between gap-3 border-b border-gray-200 dark:border-lits-blue-450 px-6 py-5">
                        <div>
                            <h3 id="{{ $formId }}-title" class="text-base font-semibold text-gray-900 dark:text-gray-50">{{ $isEdit ? 'Editar tipo de documento' : 'Nuevo tipo de documento' }}</h3>
                            <p id="{{ $p }}subtitle" class="text-xs text-gray-500 dark:text-gray-400">{{ $isEdit ? $documentType?->name : 'Ingresa la información necesaria del tipo de documento.' }}</p>
                        </div>
                        <button type="button" @click="open = false" class="rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                            <span class="sr-only">Cerrar</span>
                            <i class="fa-regular fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <div class="flex-1 min-h-0 overflow-y-auto px-6 py-5">
                        @php
                            // El error de `name` se muestra bajo su input; el aviso solo conserva los que no tienen campo.
                            $unmatchedErrors = new \Illuminate\Support\MessageBag(
                                collect($errors->messages())->except(['name'])->all()
                            );
                        @endphp
                        @if ($mine && $unmatchedErrors->any())
                            <div id="{{ $formId }}-errors">
                                <x-alerts.error :message="$isEdit ? 'Para editar el tipo de documento soluciona los siguientes errores:' : 'Para crear el tipo de documento soluciona los siguientes errores:'" :errors="$unmatchedErrors" class="mb-6" />
                            </div>
                        @endif

                        <div class="space-y-6">
                            <div>
                                <label for="{{ $p }}name" class="{{ $labelClass }}">Nombre</label>
                                <div class="mt-2">
                                    <input id="{{ $p }}name" value="{{ $val('name') }}" name="name" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>

                            <fieldset>
                                <legend class="{{ $labelClass }}">¿Dónde aplica?</legend>
                                <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-3">
                                    @foreach($tiles as $field => [$label, $icon, $iconClasses])
                                        <label for="{{ $p . $field }}" class="flex cursor-pointer flex-col items-center gap-2 rounded-xl border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 p-3 hover:bg-gray-50 dark:hover:bg-white/[0.03] has-checked:border-indigo-600">
                                            <span class="group grid size-4 flex-none grid-cols-1">
                                                <input type="hidden" name="{{ $field }}" value="0">
                                                <input id="{{ $p . $field }}" type="checkbox" value="1" name="{{ $field }}" @checked($val($field) == 1) class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-lits-blue-550 checked:border-indigo-600 checked:bg-indigo-600 indeterminate:border-indigo-600 indeterminate:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 forced-colors:appearance-auto" />
                                                <i class="fa-solid fa-check text-xs pointer-events-none col-start-1 row-start-1 self-center justify-self-center text-white group-has-disabled:text-gray-950/25 opacity-0 group-has-checked:opacity-100 group-has-indeterminate:opacity-100"></i>
                                            </span>
                                            <span class="flex size-9 flex-none items-center justify-center rounded-lg text-sm {{ $iconClasses }}" aria-hidden="true"><i class="fa-regular {{ $icon }}"></i></span>
                                            <span class="text-sm font-medium text-gray-900 dark:text-gray-50">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </fieldset>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-lits-blue-450 px-6 py-4">
                        <button type="button" @click="open = false" class="text-sm font-semibold text-gray-900 dark:text-gray-50 hover:cursor-pointer">Cancelar</button>
                        <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">{{ $isEdit ? 'Guardar' : 'Crear tipo' }}</button>
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
            const flags = ['shipments', 'customs', 'warehouse'];
            const form = document.getElementById(@js($formId));
            const el = (name) => document.getElementById(p + name);

            attachLiveValidation(p + 'name', { required: true });
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

            // Valores a los que vuelve el formulario al cerrar sin guardar: el registro guardado al editar
            // (se reemplaza con la fila elegida en la lista), vacío al crear. Se toman de los datos
            // guardados, no del DOM, que puede traer el old() de un envío rechazado.
            let snapshot = {
                name: @json($saved('name') ?? ''),
                shipments: {{ (int) $saved('shipments') }},
                customs: {{ (int) $saved('customs') }},
                warehouse: {{ (int) $saved('warehouse') }},
            };

            const apply = function (values) {
                el('name').value = values.name == null ? '' : values.name;
                flags.forEach(function (flag) { el(flag).checked = Number(values[flag]) === 1; });
            };

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
                apply(snapshot);
                clearMarks();
            });

            // Lista (modo editar): llena el modal con la fila elegida y lo abre.
            if (isEdit) window.addEventListener('request-edit-document-type', function (event) {
                const data = event.detail;
                form.action = data.update_url;
                el('record_id').value = data.id;
                document.getElementById('edit_subtitle').textContent = data.name;
                apply(data);
                snapshot = { name: el('name').value, shipments: data.shipments, customs: data.customs, warehouse: data.warehouse };
                clearMarks();
                window.dispatchEvent(new CustomEvent('open-edit-document-type-modal'));
            });
        })();
    </script>
@endpush
