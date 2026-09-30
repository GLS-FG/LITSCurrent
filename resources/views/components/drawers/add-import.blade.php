@props(['order', 'serviceClasses', 'defaultServiceClass'])
@php
    // Both this drawer and the shipment ones redirect back to the same page on
    // a failed validation and share field names (reference, service_class_id...),
    // so the hidden "_drawer" input tells them apart.
    $mine = old('_drawer') === 'import-create';
    $old = fn ($key) => $mine ? old($key) : null;
@endphp
<div
    class="relative"
    x-data="{ open: {{ $mine && $errors->any() ? 'true' : 'false' }} }"
    x-init="$watch('open', value => { if (!value) window.dispatchEvent(new CustomEvent('add-import-drawer-closed')) })"
    x-on:open-add-import-drawer.window="open = true"
    x-on:keydown.escape.window="open = false"
    x-trap.inert.noscroll="open"
>
    <form id="import-create-form" action="{{route('orders.imports.store', ['order' => $order])}}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="_drawer" value="import-create">
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
                    <i class="fa-regular fa-person-military-pointing"></i>
                </div>
                <div>
                    <div class="text-base font-semibold text-gray-900 dark:text-gray-50">Agregar aduana</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{$order->code}} &middot; {{$order->client->trade_name}}</div>
                </div>
            </div>
            <button type="button" @click="open = false" class="rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                <span class="sr-only">Cerrar</span>
                <i class="fa-regular fa-xmark text-lg"></i>
            </button>
        </div>

        <div class="flex-1 min-h-0 overflow-y-auto px-6 py-5 space-y-6">
            @if ($mine && $errors->any())
                <x-alerts.error :message="'Para crear el servicio soluciona los siguientes errores:'" :errors="$errors" />
            @endif

            <div>
                <label for="import_create_reference" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Referencia</label>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Recuerda llenar sólo la información necesaria de la referencia.</p>
                <div class="mt-2">
                    <textarea id="import_create_reference" form="import-create-form" name="reference" rows="2" autocomplete="off" required class="@if($mine) @error('reference') outline-red-400 @else outline-gray-300 dark:outline-gray-600 @enderror @else outline-gray-300 dark:outline-gray-600 @endif user-invalid:outline-red-400 dark:user-invalid:outline-red-400 touched-invalid:outline-red-400 dark:touched-invalid:outline-red-400 block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">{{$old('reference')}}</textarea>
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
                        <label for="import_create_service_class_id" class="block text-xs font-medium text-gray-900 dark:text-gray-50">Service Class</label>
                        <div class="mt-1 grid grid-cols-1">
                            <select id="import_create_service_class_id" form="import-create-form" name="service_class_id" autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">
                                @foreach($serviceClasses as $service)
                                    <option value='{{$service->id}}' @selected(($old('service_class_id') ?? $defaultServiceClass->id) == $service->id)>{{$service->name}}</option>
                                @endforeach
                            </select>
                            <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 self-center justify-self-end text-sm text-gray-500 dark:text-gray-400"></i>
                        </div>
                    </div>
                    <div>
                        <label for="import_create_service_mode_id" class="block text-xs font-medium text-gray-900 dark:text-gray-50">Service Mode</label>
                        <div class="mt-1 grid grid-cols-1">
                            <select id="import_create_service_mode_id" form="import-create-form" name="service_mode_id" autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">
                            </select>
                            <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 self-center justify-self-end text-sm text-gray-500 dark:text-gray-400"></i>
                        </div>
                    </div>
                    <div>
                        <label for="import_create_class_type_id" class="block text-xs font-medium text-gray-900 dark:text-gray-50">Class Type</label>
                        <div class="mt-1 grid grid-cols-1">
                            <select id="import_create_class_type_id" form="import-create-form" name="class_type_id" autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">
                            </select>
                            <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 self-center justify-self-end text-sm text-gray-500 dark:text-gray-400"></i>
                        </div>
                    </div>
                    <div>
                        <label for="import_create_service_level_id" class="block text-xs font-medium text-gray-900 dark:text-gray-50">Service Level</label>
                        <div class="mt-1 grid grid-cols-1">
                            <select id="import_create_service_level_id" form="import-create-form" name="service_level_id" autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">
                            </select>
                            <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 self-center justify-self-end text-sm text-gray-500 dark:text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <label for="import_create_comments" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Comentarios</label>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Información adicional para la orden de aduana.</p>
                <div class="mt-1">
                    <textarea rows="4" id="import_create_comments" form="import-create-form" name="comments" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">{{$old('comments')}}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-lits-blue-450 px-6 py-4">
            <button type="button" @click="open = false" class="text-sm font-semibold text-gray-900 dark:text-gray-50 hover:cursor-pointer">Cancelar</button>
            <button type="submit" form="import-create-form" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Crear aduana</button>
        </div>
    </div>
</div>

<x-drawers.service-type-cascade
    prefix="import_create_"
    :classTarget="$old('service_class_id') ?? $defaultServiceClass->id"
    :modeTarget="$old('service_mode_id')"
    :typeTarget="$old('class_type_id')"
    :levelTarget="$old('service_level_id')"
/>

@push('custom_script')
    <script type="module">
        // Marks a required field red as soon as it's left blank, whether the
        // user tabbed past it or clicked into it and out without typing
        // anything (:user-invalid alone doesn't cover that case in Chrome).
        const importCreateReference = document.getElementById('import_create_reference');
        importCreateReference.addEventListener('blur', function () { importCreateReference.setAttribute('data-touched', 'true'); });
        importCreateReference.addEventListener('invalid', function () { importCreateReference.setAttribute('data-touched', 'true'); });
        $('#import-create-form').on('submit', function(e) {
            const $submitButton = $('button[type="submit"][form="import-create-form"]');
            if ($submitButton.prop('disabled')) {
                e.preventDefault();
                return;
            }
            $submitButton.prop('disabled', true);
        });

        // Closing the drawer without saving discards what was typed: restore
        // the values the server rendered (blank, or old() input after a failed
        // validation), taken once before any editing.
        const importCreateSnapshot = {
            reference: $('#import_create_reference').val(),
            comments: $('#import_create_comments').val(),
        };
        window.addEventListener('add-import-drawer-closed', function () {
            Object.keys(importCreateSnapshot).forEach(function (field) {
                $('#import_create_' + field).val(importCreateSnapshot[field]);
            });
            importCreateReference.removeAttribute('data-touched');
            window.dispatchEvent(new CustomEvent('import_create_cascade-reset'));
        });
    </script>
@endpush
