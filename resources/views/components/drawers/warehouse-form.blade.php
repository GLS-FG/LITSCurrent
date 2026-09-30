@props(['mode', 'order' => null, 'warehouses', 'serviceClasses', 'defaultServiceClass', 'storage' => null])
{{--
    Create ("create") and edit ("edit") drawer for a warehouse storage service.
    Both share the same fields, so they live in one component; $storage is only
    given in edit mode and provides the saved values.
--}}
@php
    $isEdit = $mode === 'edit';
    $p = 'warehouse_' . $mode . '_';          // id prefix of every field
    $formId = 'warehouse-' . $mode . '-form';
    $drawerKey = 'warehouse-' . $mode;
    $eventName = $isEdit ? 'edit-warehouse-drawer' : 'add-warehouse-drawer';

    // The shipment/customs drawers redirect back to this same page on a failed
    // validation and share field names, so the hidden "_drawer" input tells
    // them apart: only show/open with our own errors and old() input.
    $mine = old('_drawer') === $drawerKey;
    $val = function ($key, $saved = null) use ($mine) {
        return $mine ? old($key) : $saved;
    };
    $date = fn ($value) => is_null($value) ? '' : $value->format('d/m/Y');

    $saved = fn ($field) => ($isEdit && $storage) ? $storage->{$field} : null;

    // On the list page no record is loaded up front ($storage is null): the edit
    // drawer is filled from JSON when a row's edit icon is clicked. After a failed
    // validation the redirect lands back on the list, so the record is recovered
    // from the ids the form sent along (cast to int, never trusted as a URL).
    $recordOrderId = $isEdit ? ($storage ? $order->id : ($mine ? (int) old('_order') : null)) : null;
    $recordId = $isEdit ? ($storage ? $storage->id : ($mine ? (int) old('_record') : null)) : null;
    $formAction = $isEdit
        ? (($recordOrderId && $recordId) ? route('orders.warehouse-storages.update', ['order' => $recordOrderId, 'warehouse_storage' => $recordId]) : '')
        : route('orders.warehouse-storages.store', ['order' => $order]);

    $inputBase = 'block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600';
    $required = 'user-invalid:outline-red-400 dark:user-invalid:outline-red-400 touched-invalid:outline-red-400 dark:touched-invalid:outline-red-400';
    $selectBase = 'col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600';
    $outline = fn ($field) => ($mine && $errors->has($field)) ? 'outline-red-400' : 'outline-gray-300 dark:outline-gray-600';

    $classTarget = $val('service_class_id', $saved('service_class_id')) ?? $defaultServiceClass->id;
@endphp
<div
    class="relative"
    x-data="{ open: {{ $mine && $errors->any() ? 'true' : 'false' }} }"
    x-init="$watch('open', value => { if (!value) window.dispatchEvent(new CustomEvent('{{ $eventName }}-closed')) })"
    x-on:open-{{ $eventName }}.window="open = true"
    x-on:keydown.escape.window="open = false"
    x-trap.inert.noscroll="open"
>
    <form id="{{ $formId }}" action="{{ $formAction }}" method="POST" class="hidden">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif
        <input type="hidden" name="_drawer" value="{{ $drawerKey }}">
        @if($isEdit)
            <input type="hidden" id="{{ $p }}order_id" name="_order" value="{{ $recordOrderId }}">
            <input type="hidden" id="{{ $p }}record_id" name="_record" value="{{ $recordId }}">
        @endif
    </form>

    <div
        x-cloak
        x-show="open"
        class="fixed inset-0 bg-gray-500/75 dark:bg-gray-950/75 transition-opacity z-90"
        x-transition:enter="ease-in-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in-out duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="open = false"
    ></div>

    <div
        x-cloak
        x-show="open"
        x-transition:enter="transition ease-in-out duration-300 sm:duration-500"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in-out duration-300 sm:duration-500"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed inset-y-0 right-0 z-90 flex w-screen sm:max-w-2xl flex-col bg-white dark:bg-lits-blue-550 shadow-xl"
    >
        <div class="flex items-start justify-between gap-3 border-b border-gray-200 dark:border-lits-blue-450 px-6 py-5">
            <div class="flex gap-3">
                <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-entity-warehouses-50 dark:bg-entity-warehouses/15 text-entity-warehouses">
                    <i class="fa-regular {{ $isEdit ? 'fa-pen-to-square' : 'fa-warehouse' }}"></i>
                </div>
                <div>
                    <div class="text-base font-semibold text-gray-900 dark:text-gray-50">{{ $isEdit ? 'Editar almacén' : 'Agregar almacén' }}</div>
                    <div id="{{ $p }}subtitle" class="text-xs text-gray-500 dark:text-gray-400">{{ $isEdit ? ($storage ? $storage->tracking_code . ' · ' . $order->client->trade_name : '') : $order->code . ' · ' . $order->client->trade_name }}</div>
                </div>
            </div>
            <button type="button" @click="open = false" class="rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                <span class="sr-only">Cerrar</span>
                <i class="fa-regular fa-xmark text-lg"></i>
            </button>
        </div>

        <div class="flex-1 min-h-0 overflow-y-auto px-6 py-5 space-y-6">
            @if ($mine && $errors->any())
                <x-alerts.error :message="$isEdit ? 'Para editar el servicio soluciona los siguientes errores:' : 'Para crear el servicio soluciona los siguientes errores:'" :errors="$errors" />
            @endif

            <div>
                <label for="{{ $p }}reference" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Referencia</label>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Recuerda llenar sólo la información necesaria de la referencia.</p>
                <div class="mt-2">
                    <textarea id="{{ $p }}reference" form="{{ $formId }}" name="reference" rows="2" autocomplete="off" required class="{{ $outline('reference') }} {{ $required }} {{ $inputBase }}">{{ $val('reference', $saved('reference')) }}</textarea>
                    @if($mine) @error('reference')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror @endif
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label for="{{ $p }}warehouse_id" class="block text-xs font-medium text-gray-900 dark:text-gray-50">Almacén</label>
                    <div class="mt-1 grid grid-cols-1">
                        <select id="{{ $p }}warehouse_id" form="{{ $formId }}" name="warehouse_id" autocomplete="off" class="{{ $selectBase }}">
                            @foreach ($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" @selected($val('warehouse_id', $saved('warehouse_id')) == $warehouse->id)>{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                        <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 self-center justify-self-end text-sm text-gray-500 dark:text-gray-400"></i>
                    </div>
                </div>

                <div>
                    <label for="{{ $p }}receipt" class="block text-xs font-medium text-gray-900 dark:text-gray-50">Recibido de</label>
                    <div class="mt-1">
                        <input id="{{ $p }}receipt" form="{{ $formId }}" value="{{ $val('receipt', $saved('receipt')) }}" name="receipt" type="text" autocomplete="off" required class="{{ $outline('receipt') }} {{ $required }} {{ $inputBase }}">
                        @if($mine) @error('receipt')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror @endif
                    </div>
                </div>

                <div>
                    <label for="{{ $p }}receipt_date" class="block text-xs font-medium text-gray-900 dark:text-gray-50">Fecha recibo</label>
                    <div class="mt-1">
                        <input id="{{ $p }}receipt_date" form="{{ $formId }}" value="{{ $val('receipt_date', $date($saved('receipt_date'))) }}" name="receipt_date" type="text" autocomplete="off" placeholder="dd/mm/aaaa" required class="{{ $outline('receipt_date') }} {{ $required }} {{ $inputBase }}">
                        @if($mine) @error('receipt_date')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror @endif
                    </div>
                </div>

                <div>
                    <label for="{{ $p }}document" class="block text-xs font-medium text-gray-900 dark:text-gray-50">Documento</label>
                    <div class="mt-1">
                        <input id="{{ $p }}document" form="{{ $formId }}" value="{{ $val('document', $saved('document')) }}" name="document" type="text" autocomplete="off" required class="{{ $outline('document') }} {{ $required }} {{ $inputBase }}">
                        @if($mine) @error('document')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror @endif
                    </div>
                </div>

                <div>
                    <label for="{{ $p }}document_date" class="block text-xs font-medium text-gray-900 dark:text-gray-50">Fecha documento</label>
                    <div class="mt-1">
                        <input id="{{ $p }}document_date" form="{{ $formId }}" value="{{ $val('document_date', $date($saved('document_date'))) }}" name="document_date" type="text" autocomplete="off" placeholder="dd/mm/aaaa" required class="{{ $outline('document_date') }} {{ $required }} {{ $inputBase }}">
                        @if($mine) @error('document_date')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror @endif
                    </div>
                </div>
            </div>

            <div>
                <div class="text-sm font-semibold text-gray-900 dark:text-gray-50">Tipo de servicio</div>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">La selección se acomoda en cascada.</p>
                <div class="mt-3 grid grid-cols-2 gap-3">
                    <div>
                        <label for="{{ $p }}service_class_id" class="block text-xs font-medium text-gray-900 dark:text-gray-50">Service Class</label>
                        <div class="mt-1 grid grid-cols-1">
                            <select id="{{ $p }}service_class_id" form="{{ $formId }}" name="service_class_id" autocomplete="off" class="{{ $selectBase }}">
                                @foreach($serviceClasses as $service)
                                    <option value="{{ $service->id }}" @selected($classTarget == $service->id)>{{ $service->name }}</option>
                                @endforeach
                            </select>
                            <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 self-center justify-self-end text-sm text-gray-500 dark:text-gray-400"></i>
                        </div>
                    </div>
                    <div>
                        <label for="{{ $p }}service_mode_id" class="block text-xs font-medium text-gray-900 dark:text-gray-50">Service Mode</label>
                        <div class="mt-1 grid grid-cols-1">
                            <select id="{{ $p }}service_mode_id" form="{{ $formId }}" name="service_mode_id" autocomplete="off" class="{{ $selectBase }}"></select>
                            <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 self-center justify-self-end text-sm text-gray-500 dark:text-gray-400"></i>
                        </div>
                    </div>
                    <div>
                        <label for="{{ $p }}class_type_id" class="block text-xs font-medium text-gray-900 dark:text-gray-50">Class Type</label>
                        <div class="mt-1 grid grid-cols-1">
                            <select id="{{ $p }}class_type_id" form="{{ $formId }}" name="class_type_id" autocomplete="off" class="{{ $selectBase }}"></select>
                            <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 self-center justify-self-end text-sm text-gray-500 dark:text-gray-400"></i>
                        </div>
                    </div>
                    <div>
                        <label for="{{ $p }}service_level_id" class="block text-xs font-medium text-gray-900 dark:text-gray-50">Service Level</label>
                        <div class="mt-1 grid grid-cols-1">
                            <select id="{{ $p }}service_level_id" form="{{ $formId }}" name="service_level_id" autocomplete="off" class="{{ $selectBase }}"></select>
                            <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 self-center justify-self-end text-sm text-gray-500 dark:text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <label for="{{ $p }}comments" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Comentarios</label>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Información adicional para la orden de almacén.</p>
                <div class="mt-1">
                    <textarea rows="4" id="{{ $p }}comments" form="{{ $formId }}" name="comments" autocomplete="off" class="outline-gray-300 dark:outline-gray-600 {{ $inputBase }}">{{ $val('comments', $saved('comments')) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-lits-blue-450 px-6 py-4">
            <button type="button" @click="open = false" class="text-sm font-semibold text-gray-900 dark:text-gray-50 hover:cursor-pointer">Cancelar</button>
            <button type="submit" form="{{ $formId }}" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">{{ $isEdit ? 'Guardar' : 'Crear almacén' }}</button>
        </div>
    </div>
</div>

<x-drawers.service-type-cascade
    :prefix="$p"
    :classTarget="$classTarget"
    :modeTarget="$val('service_mode_id', $saved('service_mode_id'))"
    :typeTarget="$val('class_type_id', $saved('class_type_id'))"
    :levelTarget="$val('service_level_id', $saved('service_level_id'))"
/>

@push('custom_script')
    <script type="module">
        (function () {
            const p = @js($p);
            const formId = @js($formId);
            const fields = ['reference', 'warehouse_id', 'receipt', 'receipt_date', 'document', 'document_date', 'comments'];
            $('#' + p + 'receipt_date').datepicker({ dateFormat: 'dd/mm/yy' });
            $('#' + p + 'document_date').datepicker({ dateFormat: 'dd/mm/yy' });

            // Marks a required field red as soon as it's left blank, whether the
            // user tabbed past it or clicked into it and out without typing
            // anything (:user-invalid alone doesn't cover that case in Chrome).
            ['reference', 'receipt', 'receipt_date', 'document', 'document_date'].forEach(function (field) {
                const el = document.getElementById(p + field);
                el.addEventListener('blur', function () { el.setAttribute('data-touched', 'true'); });
                el.addEventListener('invalid', function () { el.setAttribute('data-touched', 'true'); });
            });

            $('#' + formId).on('submit', function (e) {
                const $submitButton = $('button[type="submit"][form="' + formId + '"]');
                if ($submitButton.prop('disabled')) {
                    e.preventDefault();
                    return;
                }
                $submitButton.prop('disabled', true);
            });

            // List page (edit mode): fill the drawer with the record behind the
            // clicked row, then open it. The detail page opens it with its own values.
            if (@js($isEdit)) window.addEventListener('request-edit-warehouse', function (event) {
                fetch(event.detail.url, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
                    .then(function (response) {
                        if (!response.ok) { throw new Error('HTTP ' + response.status); }
                        return response.json();
                    })
                    .then(function (data) {
                        document.getElementById(formId).action = data.update_url;
                        $('#' + p + 'order_id').val(data.order_id);
                        $('#' + p + 'record_id').val(data.id);
                        $('#' + p + 'subtitle').text(data.tracking_code + ' · ' + data.client);
                        fields.forEach(function (field) {
                            $('#' + p + field).val(data[field] == null ? '' : data[field]);
                            snapshot[field] = $('#' + p + field).val();
                        });
                        window.dispatchEvent(new CustomEvent(p + 'cascade-set', { detail: {
                            cls: data.service_class_id, mode: data.service_mode_id,
                            type: data.class_type_id, level: data.service_level_id
                        } }));
                        window.dispatchEvent(new CustomEvent('open-edit-warehouse-drawer'));
                    })
                    .catch(function (error) { console.error('No se pudo cargar el almacenamiento', error); });
            });

            // Closing the drawer without saving discards what was typed: restore
            // the values the server rendered (taken once, before any editing).
            const snapshot = {};
            fields.forEach(function (field) { snapshot[field] = $('#' + p + field).val(); });
            window.addEventListener(@js($eventName . '-closed'), function () {
                fields.forEach(function (field) { $('#' + p + field).val(snapshot[field]); });
                document.querySelectorAll('[form="' + formId + '"][data-touched]').forEach(function (el) {
                    el.removeAttribute('data-touched');
                });
                window.dispatchEvent(new CustomEvent(p + 'cascade-reset'));
            });
        })();
    </script>
@endpush
