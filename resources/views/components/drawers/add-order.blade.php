@props(['clients'])
@php
    // "_drawer" tells this drawer's failed validation apart from any other form
    // that redirects back to the same page.
    $mine = old('_drawer') === 'order-create';
    $old = fn ($key) => $mine ? old($key) : null;

    $inputBase = 'block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600';
    $required = 'user-invalid:outline-red-400 dark:user-invalid:outline-red-400 touched-invalid:outline-red-400 dark:touched-invalid:outline-red-400';
    $selectBase = 'col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600';
    $outline = fn ($field) => ($mine && $errors->has($field)) ? 'outline-red-400' : 'outline-gray-300 dark:outline-gray-600';
@endphp
<div
    class="relative"
    x-data="{ open: {{ $mine && $errors->any() ? 'true' : 'false' }} }"
    x-init="$watch('open', value => { if (!value) window.dispatchEvent(new CustomEvent('add-order-drawer-closed')) })"
    x-on:open-add-order-drawer.window="open = true"
    x-on:keydown.escape.window="open = false"
    x-trap.inert.noscroll="open"
>
    <form id="order-create-form" action="{{ route('orders.store') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="_drawer" value="order-create">
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
        class="fixed inset-y-0 right-0 z-90 flex w-screen sm:max-w-xl flex-col bg-white dark:bg-lits-blue-550 shadow-xl"
    >
        <div class="flex items-start justify-between gap-3 border-b border-gray-200 dark:border-lits-blue-450 px-6 py-5">
            <div class="flex gap-3">
                <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-entity-orders-50 dark:bg-entity-orders/15 text-entity-orders">
                    <i class="fa-regular fa-clipboard-list"></i>
                </div>
                <div>
                    <div class="text-base font-semibold text-gray-900 dark:text-gray-50">Nueva orden de servicio</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Después podrás agregarle embarques, aduanas y almacenes.</div>
                </div>
            </div>
            <button type="button" @click="open = false" class="rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                <span class="sr-only">Cerrar</span>
                <i class="fa-regular fa-xmark text-lg"></i>
            </button>
        </div>

        <div class="flex-1 min-h-0 overflow-y-auto px-6 py-5 space-y-6">
            @if ($mine && $errors->any())
                <x-alerts.error :message="'Para crear la órden soluciona los siguientes errores:'" :errors="$errors" />
            @endif

            <div>
                <label for="order_create_client_id" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Cliente</label>
                <div class="mt-1 grid grid-cols-1">
                    <select id="order_create_client_id" form="order-create-form" name="client_id" autocomplete="off" required class="{{ $outline('client_id') }} {{ $required }} {{ $selectBase }}">
                        <option value="">Selecciona un cliente</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" @selected($old('client_id') == $client->id)>{{ $client->company_name }} / {{ $client->trade_name }}</option>
                        @endforeach
                    </select>
                    <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 self-center justify-self-end text-sm text-gray-500 dark:text-gray-400"></i>
                </div>
                @if($mine) @error('client_id')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror @endif
            </div>

            <div>
                <label for="order_create_contact_id" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Usuario</label>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Se cargan los usuarios del cliente elegido.</p>
                <div class="mt-1 grid grid-cols-1">
                    <select id="order_create_contact_id" form="order-create-form" name="contact_id" autocomplete="off" required class="{{ $outline('contact_id') }} {{ $required }} {{ $selectBase }}">
                        <option value="" disabled selected>Selecciona un cliente primero</option>
                    </select>
                    <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 self-center justify-self-end text-sm text-gray-500 dark:text-gray-400"></i>
                </div>
                @if($mine) @error('contact_id')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror @endif
            </div>

            <div>
                <label for="order_create_reference" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Referencia</label>
                <div class="mt-1">
                    <textarea id="order_create_reference" form="order-create-form" name="reference" rows="3" autocomplete="off" required class="{{ $outline('reference') }} {{ $required }} {{ $inputBase }}">{{ $old('reference') }}</textarea>
                    @if($mine) @error('reference')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror @endif
                </div>
            </div>

            <div>
                <label for="order_create_carbon_copy" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Receptores adicionales</label>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Ingresa emails separados por punto y coma ";".</p>
                <div class="mt-1">
                    <input id="order_create_carbon_copy" form="order-create-form" name="carbon_copy" type="text" autocomplete="off" value="{{ $old('carbon_copy') }}" class="{{ $outline('carbon_copy') }} {{ $required }} {{ $inputBase }}">
                    <p id="order_create_carbon_copy_error" class="hidden mt-1 text-sm text-red-600 dark:text-red-400"></p>
                    @if($mine) @error('carbon_copy')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror @endif
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-lits-blue-450 px-6 py-4">
            <button type="button" @click="open = false" class="text-sm font-semibold text-gray-900 dark:text-gray-50 hover:cursor-pointer">Cancelar</button>
            <button type="submit" form="order-create-form" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Crear orden</button>
        </div>
    </div>
</div>

<x-drawers.email-list-validation id="order_create_carbon_copy" :events="['add-order-drawer-closed']" />

@push('custom_script')
    <script type="module">
        (function () {
            const $client = $('#order_create_client_id');
            const $contact = $('#order_create_contact_id');
            const placeholder = 'Selecciona una opción';

            // Contact to pre-select once the client's contacts load: the one typed
            // before a failed validation; afterwards only the auto-pick applies.
            let contactTarget = @js($old('contact_id') === null ? null : (string) $old('contact_id'));
            const initialContact = contactTarget;

            function loadContacts(clientId) {
                $contact.empty();
                if (!clientId) {
                    $contact.append(new Option('Selecciona un cliente primero', '', true, true));
                    $contact.find('option[value=""]').prop('disabled', true);
                    return;
                }
                $.ajax({
                    url: @js(route('autocomplete.contacts')),
                    type: 'GET',
                    dataType: 'json',
                    data: { client_id: clientId },
                    success: function (contacts) {
                        $contact.empty();
                        $contact.append(new Option(placeholder, ''));
                        for (const contact of contacts) {
                            $contact.append(new Option(contact.label, contact.value));
                        }
                        $contact.find('option[value=""]').prop('disabled', true);
                        if (contactTarget) {
                            $contact.val(contactTarget);
                            contactTarget = null;
                        } else if (contacts.length === 1) {
                            $contact.val(contacts[0].value);
                        } else {
                            $contact.val('');
                        }
                    }
                });
            }
            $client.on('change', function () {
                contactTarget = null;
                loadContacts($client.val());
            });

            // Marks a required field red as soon as it's left blank, whether the
            // user tabbed past it or clicked into it and out without typing
            // anything (:user-invalid alone doesn't cover that case in Chrome).
            ['client_id', 'contact_id', 'reference'].forEach(function (field) {
                const el = document.getElementById('order_create_' + field);
                el.addEventListener('blur', function () { el.setAttribute('data-touched', 'true'); });
                el.addEventListener('invalid', function () { el.setAttribute('data-touched', 'true'); });
            });

            $('#order-create-form').on('submit', function (e) {
                const $submitButton = $('button[type="submit"][form="order-create-form"]');
                if ($submitButton.prop('disabled')) {
                    e.preventDefault();
                    return;
                }
                $submitButton.prop('disabled', true);
            });

            // Closing the drawer without saving discards what was typed: restore
            // the values the server rendered (blank, or old() input after a failed
            // validation), taken once before any editing.
            const snapshot = {
                client_id: $client.val(),
                reference: $('#order_create_reference').val(),
                carbon_copy: $('#order_create_carbon_copy').val(),
            };
            window.addEventListener('add-order-drawer-closed', function () {
                $client.val(snapshot.client_id);
                $('#order_create_reference').val(snapshot.reference);
                $('#order_create_carbon_copy').val(snapshot.carbon_copy);
                document.querySelectorAll('[form="order-create-form"][data-touched]').forEach(function (el) {
                    el.removeAttribute('data-touched');
                });
                contactTarget = initialContact;
                loadContacts(snapshot.client_id);
            });

            if ($client.val()) {
                loadContacts($client.val());
            }
        })();
    </script>
@endpush
