@props(['clients', 'order' => null])
@php
    // On the list page no order is loaded up front ($order is null): the drawer is
    // filled from JSON when a row's edit icon is clicked. The detail page passes
    // its order and the drawer is ready with the saved values.
    $isLoaded = ! is_null($order);

    // "_drawer" tells this drawer's failed validation apart from any other form
    // that redirects back to the same page. After a failure on the list page the
    // order is recovered from the id the form sent along (cast to int, never
    // trusted as a URL).
    $mine = old('_drawer') === 'order-edit';
    $recordId = $isLoaded ? $order->id : ($mine ? (int) old('_record') : null);
    $formAction = $recordId ? route('orders.update', ['order' => $recordId]) : '';

    $val = fn ($key, $saved) => $mine ? old($key) : $saved;
    $saved = fn ($field) => $isLoaded ? $order->{$field} : null;

    $inputBase = 'block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600';
    $required = 'user-invalid:outline-red-400 dark:user-invalid:outline-red-400 touched-invalid:outline-red-400 dark:touched-invalid:outline-red-400';
    $selectBase = 'col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600';
    $outline = fn ($field) => ($mine && $errors->has($field)) ? 'outline-red-400' : 'outline-gray-300 dark:outline-gray-600';
@endphp
<div
    class="relative"
    x-data="{ open: {{ $mine && $errors->any() ? 'true' : 'false' }} }"
    x-init="$watch('open', value => { if (!value) window.dispatchEvent(new CustomEvent('edit-order-drawer-closed')) })"
    x-on:open-edit-order-drawer.window="open = true"
    x-on:keydown.escape.window="open = false"
    x-trap.inert.noscroll="open"
>
    <form id="order-edit-form" action="{{ $formAction }}" method="POST" class="hidden">
        @csrf
        @method('PUT')
        <input type="hidden" name="_drawer" value="order-edit">
        <input type="hidden" id="order_edit_record_id" name="_record" value="{{ $recordId }}">
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
                    <i class="fa-regular fa-pen-to-square"></i>
                </div>
                <div>
                    <div class="text-base font-semibold text-gray-900 dark:text-gray-50">Editar orden de servicio</div>
                    <div id="order_edit_subtitle" class="text-xs text-gray-500 dark:text-gray-400">{{ $isLoaded ? $order->code : '' }}</div>
                </div>
            </div>
            <button type="button" @click="open = false" class="rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                <span class="sr-only">Cerrar</span>
                <i class="fa-regular fa-xmark text-lg"></i>
            </button>
        </div>

        <div class="flex-1 min-h-0 overflow-y-auto px-6 py-5 space-y-6">
            @if ($mine && $errors->any())
                <x-alerts.error :message="'Para editar la órden soluciona los siguientes errores:'" :errors="$errors" />
            @endif

            <div>
                <label for="order_edit_client_id" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Cliente</label>
                <div class="mt-1 grid grid-cols-1">
                    <select id="order_edit_client_id" form="order-edit-form" name="client_id" autocomplete="off" required class="{{ $outline('client_id') }} {{ $required }} {{ $selectBase }}">
                        <option value="">Selecciona un cliente</option>
                        @if($isLoaded && ! $clients->contains('id', $order->client_id))
                            {{-- The order's client is no longer in the active list (e.g. deleted): keep it selectable so saving other fields doesn't force a different client. --}}
                            <option value="{{ $order->client_id }}" @selected($val('client_id', $saved('client_id')) == $order->client_id)>{{ $order->client->company_name }} / {{ $order->client->trade_name }}</option>
                        @endif
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" @selected($val('client_id', $saved('client_id')) == $client->id)>{{ $client->company_name }} / {{ $client->trade_name }}</option>
                        @endforeach
                    </select>
                    <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 self-center justify-self-end text-sm text-gray-500 dark:text-gray-400"></i>
                </div>
                @if($mine) @error('client_id')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror @endif
            </div>

            <div>
                <label for="order_edit_contact_id" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Usuario</label>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Se cargan los usuarios del cliente elegido.</p>
                <div class="mt-1 grid grid-cols-1">
                    <select id="order_edit_contact_id" form="order-edit-form" name="contact_id" autocomplete="off" required class="{{ $outline('contact_id') }} {{ $required }} {{ $selectBase }}">
                        <option value="" disabled selected>Selecciona un cliente primero</option>
                    </select>
                    <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 self-center justify-self-end text-sm text-gray-500 dark:text-gray-400"></i>
                </div>
                @if($mine) @error('contact_id')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror @endif
            </div>

            <div>
                <label for="order_edit_reference" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Referencia</label>
                <div class="mt-1">
                    <textarea id="order_edit_reference" form="order-edit-form" name="reference" rows="3" autocomplete="off" required class="{{ $outline('reference') }} {{ $required }} {{ $inputBase }}">{{ $val('reference', $saved('reference')) }}</textarea>
                    @if($mine) @error('reference')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror @endif
                </div>
            </div>

            <div>
                <label for="order_edit_carbon_copy" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Receptores adicionales</label>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Ingresa emails separados por punto y coma ";".</p>
                <div class="mt-1">
                    <input id="order_edit_carbon_copy" form="order-edit-form" name="carbon_copy" type="text" autocomplete="off" value="{{ $val('carbon_copy', $saved('carbon_copy')) }}" class="{{ $outline('carbon_copy') }} {{ $required }} {{ $inputBase }}">
                    <p id="order_edit_carbon_copy_error" class="hidden mt-1 text-sm text-red-600 dark:text-red-400"></p>
                    @if($mine) @error('carbon_copy')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror @endif
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-lits-blue-450 px-6 py-4">
            <button type="button" @click="open = false" class="text-sm font-semibold text-gray-900 dark:text-gray-50 hover:cursor-pointer">Cancelar</button>
            <button type="submit" form="order-edit-form" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Guardar</button>
        </div>
    </div>
</div>

<x-drawers.email-list-validation id="order_edit_carbon_copy" :events="['edit-order-drawer-closed', 'open-edit-order-drawer']" />

@push('custom_script')
    <script type="module">
        (function () {
            const $client = $('#order_edit_client_id');
            const $contact = $('#order_edit_contact_id');
            const $reference = $('#order_edit_reference');
            const $carbonCopy = $('#order_edit_carbon_copy');
            const placeholder = 'Selecciona una opción';

            // Contact to pre-select once the client's contacts load: the saved one
            // (or the one typed before a failed validation). After the user picks
            // another client only the auto-pick applies.
            let contactTarget = @js($val('contact_id', $saved('contact_id')) === null ? null : (string) $val('contact_id', $saved('contact_id')));

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
                const el = document.getElementById('order_edit_' + field);
                el.addEventListener('blur', function () { el.setAttribute('data-touched', 'true'); });
                el.addEventListener('invalid', function () { el.setAttribute('data-touched', 'true'); });
            });

            $('#order-edit-form').on('submit', function (e) {
                const $submitButton = $('button[type="submit"][form="order-edit-form"]');
                if ($submitButton.prop('disabled')) {
                    e.preventDefault();
                    return;
                }
                $submitButton.prop('disabled', true);
            });

            // What the drawer shows by default: the values the server rendered,
            // or (on the list page) those of the last record loaded. Closing the
            // drawer without saving discards what was typed and restores them.
            const snapshot = {
                client_id: $client.val(),
                contact_id: contactTarget,
                reference: $reference.val(),
                carbon_copy: $carbonCopy.val(),
            };

            // List page: fill the drawer with the order behind the clicked row,
            // then open it. The detail page opens it directly with its own values.
            window.addEventListener('request-edit-order', function (event) {
                fetch(event.detail.url, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
                    .then(function (response) {
                        if (!response.ok) { throw new Error('HTTP ' + response.status); }
                        return response.json();
                    })
                    .then(function (data) {
                        document.getElementById('order-edit-form').action = data.update_url;
                        $('#order_edit_record_id').val(data.id);
                        $('#order_edit_subtitle').text(data.code);
                        if (data.client_id != null && $client.find('option[value="' + data.client_id + '"]').length === 0) {
                            $client.append(new Option(data.client_label, data.client_id));
                        }
                        $client.val(data.client_id == null ? '' : data.client_id);
                        $reference.val(data.reference || '');
                        $carbonCopy.val(data.carbon_copy || '');
                        snapshot.client_id = $client.val();
                        snapshot.contact_id = data.contact_id == null ? null : String(data.contact_id);
                        snapshot.reference = $reference.val();
                        snapshot.carbon_copy = $carbonCopy.val();
                        document.querySelectorAll('[form="order-edit-form"][data-touched]').forEach(function (el) {
                            el.removeAttribute('data-touched');
                        });
                        contactTarget = snapshot.contact_id;
                        loadContacts(snapshot.client_id);
                        window.dispatchEvent(new CustomEvent('open-edit-order-drawer'));
                    })
                    .catch(function (error) { console.error('No se pudo cargar la orden', error); });
            });

            window.addEventListener('edit-order-drawer-closed', function () {
                $client.val(snapshot.client_id);
                $reference.val(snapshot.reference);
                $carbonCopy.val(snapshot.carbon_copy);
                document.querySelectorAll('[form="order-edit-form"][data-touched]').forEach(function (el) {
                    el.removeAttribute('data-touched');
                });
                contactTarget = snapshot.contact_id;
                loadContacts(snapshot.client_id);
            });

            if ($client.val()) {
                loadContacts($client.val());
            }
        })();
    </script>
@endpush
