@props(['order' => null, 'import' => null, 'serviceClasses', 'defaultServiceClass'])
@php
    // See add-import: "_drawer" tells this drawer's failed validation apart
    // from the other forms that redirect back to the same page.
    $mine = old('_drawer') === 'import-edit';
    $val = fn ($key, $saved) => $mine ? old($key) : $saved;

    // On the list page no record is loaded up front ($import is null): the drawer
    // is filled from JSON when a row's edit icon is clicked. After a failed
    // validation the redirect lands back on the list, so the record is recovered
    // from the ids the form sent along (cast to int, never trusted as a URL).
    $recordOrderId = $import ? $order->id : ($mine ? (int) old('_order') : null);
    $recordId = $import ? $import->id : ($mine ? (int) old('_record') : null);
    $formAction = ($recordOrderId && $recordId)
        ? route('orders.imports.update', ['order' => $recordOrderId, 'import' => $recordId])
        : '';
@endphp
<div
    class="relative"
    x-data="{ open: {{ $mine && $errors->any() ? 'true' : 'false' }} }"
    x-init="$watch('open', value => { if (!value) window.dispatchEvent(new CustomEvent('edit-import-drawer-closed')) })"
    x-on:open-edit-import-drawer.window="open = true"
    x-on:keydown.escape.window="open = false"
    x-trap.inert.noscroll="open"
>
    <form id="import-edit-form" action="{{ $formAction }}" method="POST" class="hidden">
        @csrf
        @method('PUT')
        <input type="hidden" name="_drawer" value="import-edit">
        <input type="hidden" id="import_edit_order_id" name="_order" value="{{ $recordOrderId }}">
        <input type="hidden" id="import_edit_record_id" name="_record" value="{{ $recordId }}">
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
                <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-entity-customs-50 dark:bg-entity-customs/15 text-entity-customs">
                    <i class="fa-regular fa-pen-to-square"></i>
                </div>
                <div>
                    <div class="text-base font-semibold text-gray-900 dark:text-gray-50">Editar aduana</div>
                    <div id="import_edit_subtitle" class="text-xs text-gray-500 dark:text-gray-400">{{ $import ? $import->tracking_code . ' · ' . $order->client->trade_name : '' }}</div>
                </div>
            </div>
            <button type="button" @click="open = false" class="rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                <span class="sr-only">Cerrar</span>
                <i class="fa-regular fa-xmark text-lg"></i>
            </button>
        </div>

        <div class="flex-1 min-h-0 overflow-y-auto px-6 py-5 space-y-6">
            @if ($mine && $errors->any())
                <x-alerts.error :message="'Para editar el servicio soluciona los siguientes errores:'" :errors="$errors" />
            @endif

            <div>
                <label for="import_edit_reference" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Referencia</label>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Recuerda llenar sólo la información necesaria de la referencia.</p>
                <div class="mt-2">
                    <textarea id="import_edit_reference" form="import-edit-form" name="reference" rows="2" autocomplete="off" required class="@if($mine) @error('reference') outline-red-400 @else outline-gray-300 dark:outline-gray-600 @enderror @else outline-gray-300 dark:outline-gray-600 @endif user-invalid:outline-red-400 dark:user-invalid:outline-red-400 touched-invalid:outline-red-400 dark:touched-invalid:outline-red-400 block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">{{$val('reference', $import?->reference)}}</textarea>
                    @if($mine)
                        @error('reference')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    @endif
                </div>
            </div>

            <div>
                <div class="text-sm font-semibold text-gray-900 dark:text-gray-50">Tipo de servicio</div>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">La selección se acomoda en cascada.</p>
                <div class="mt-3 grid grid-cols-2 gap-3">
                    <div>
                        <label for="import_edit_service_class_id" class="block text-xs font-medium text-gray-900 dark:text-gray-50">Service Class</label>
                        <div class="mt-1 grid grid-cols-1">
                            <select id="import_edit_service_class_id" form="import-edit-form" name="service_class_id" autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">
                                @foreach($serviceClasses as $service)
                                    <option value='{{$service->id}}' @selected(($val('service_class_id', $import?->service_class_id) ?? $defaultServiceClass->id) == $service->id)>{{$service->name}}</option>
                                @endforeach
                            </select>
                            <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 self-center justify-self-end text-sm text-gray-500 dark:text-gray-400"></i>
                        </div>
                    </div>
                    <div>
                        <label for="import_edit_service_mode_id" class="block text-xs font-medium text-gray-900 dark:text-gray-50">Service Mode</label>
                        <div class="mt-1 grid grid-cols-1">
                            <select id="import_edit_service_mode_id" form="import-edit-form" name="service_mode_id" autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">
                            </select>
                            <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 self-center justify-self-end text-sm text-gray-500 dark:text-gray-400"></i>
                        </div>
                    </div>
                    <div>
                        <label for="import_edit_class_type_id" class="block text-xs font-medium text-gray-900 dark:text-gray-50">Class Type</label>
                        <div class="mt-1 grid grid-cols-1">
                            <select id="import_edit_class_type_id" form="import-edit-form" name="class_type_id" autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">
                            </select>
                            <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 self-center justify-self-end text-sm text-gray-500 dark:text-gray-400"></i>
                        </div>
                    </div>
                    <div>
                        <label for="import_edit_service_level_id" class="block text-xs font-medium text-gray-900 dark:text-gray-50">Service Level</label>
                        <div class="mt-1 grid grid-cols-1">
                            <select id="import_edit_service_level_id" form="import-edit-form" name="service_level_id" autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">
                            </select>
                            <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 self-center justify-self-end text-sm text-gray-500 dark:text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <label for="import_edit_comments" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Comentarios</label>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Información adicional para la orden de aduana.</p>
                <div class="mt-1">
                    <textarea rows="4" id="import_edit_comments" form="import-edit-form" name="comments" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">{{$val('comments', $import?->comments)}}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-lits-blue-450 px-6 py-4">
            <button type="button" @click="open = false" class="text-sm font-semibold text-gray-900 dark:text-gray-50 hover:cursor-pointer">Cancelar</button>
            <button type="submit" form="import-edit-form" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Guardar</button>
        </div>
    </div>
</div>

<x-drawers.service-type-cascade
    prefix="import_edit_"
    :classTarget="$val('service_class_id', $import?->service_class_id) ?? $defaultServiceClass->id"
    :modeTarget="$val('service_mode_id', $import?->service_mode_id)"
    :typeTarget="$val('class_type_id', $import?->class_type_id)"
    :levelTarget="$val('service_level_id', $import?->service_level_id)"
/>

@push('custom_script')
    <script type="module">
        // Marks a required field red as soon as it's left blank, whether the
        // user tabbed past it or clicked into it and out without typing
        // anything (:user-invalid alone doesn't cover that case in Chrome).
        const importEditReference = document.getElementById('import_edit_reference');
        importEditReference.addEventListener('blur', function () { importEditReference.setAttribute('data-touched', 'true'); });
        importEditReference.addEventListener('invalid', function () { importEditReference.setAttribute('data-touched', 'true'); });
        $('#import-edit-form').on('submit', function(e) {
            const $submitButton = $('button[type="submit"][form="import-edit-form"]');
            if ($submitButton.prop('disabled')) {
                e.preventDefault();
                return;
            }
            $submitButton.prop('disabled', true);
        });

        // List page: fill the drawer with the record behind the clicked row,
        // then open it. The detail page opens it directly with its own values.
        window.addEventListener('request-edit-import', function (event) {
            fetch(event.detail.url, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
                .then(function (response) {
                    if (!response.ok) { throw new Error('HTTP ' + response.status); }
                    return response.json();
                })
                .then(function (data) {
                    document.getElementById('import-edit-form').action = data.update_url;
                    $('#import_edit_order_id').val(data.order_id);
                    $('#import_edit_record_id').val(data.id);
                    $('#import_edit_subtitle').text(data.tracking_code + ' · ' + data.client);
                    $('#import_edit_reference').val(data.reference || '');
                    $('#import_edit_comments').val(data.comments || '');
                    importEditSnapshot.reference = $('#import_edit_reference').val();
                    importEditSnapshot.comments = $('#import_edit_comments').val();
                    window.dispatchEvent(new CustomEvent('import_edit_cascade-set', { detail: {
                        cls: data.service_class_id, mode: data.service_mode_id,
                        type: data.class_type_id, level: data.service_level_id
                    } }));
                    window.dispatchEvent(new CustomEvent('open-edit-import-drawer'));
                })
                .catch(function (error) { console.error('No se pudo cargar la aduana', error); });
        });

        // Closing the drawer without saving discards what was typed: restore
        // the values the server rendered (taken once, before any editing).
        const importEditSnapshot = {
            reference: $('#import_edit_reference').val(),
            comments: $('#import_edit_comments').val(),
        };
        window.addEventListener('edit-import-drawer-closed', function () {
            Object.keys(importEditSnapshot).forEach(function (field) {
                $('#import_edit_' + field).val(importEditSnapshot[field]);
            });
            importEditReference.removeAttribute('data-touched');
            window.dispatchEvent(new CustomEvent('import_edit_cascade-reset'));
        });
    </script>
@endpush
