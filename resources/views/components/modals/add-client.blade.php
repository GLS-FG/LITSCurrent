{{--
    Modal para crear un cliente (se abre desde el botón "Nuevo cliente" de clients.index).
    Mismos campos y validaciones que client/create.blade.php (que sigue existiendo):
    validación en vivo (partials.live-validation), selects País -> Estado -> Ciudad
    (partials.location-selects) y las reglas de ClientPostRequest en servidor.
    Si el servidor rechaza el formulario vuelve a la lista y el modal se reabre solo
    con los errores y el old() capturado. Al cerrarlo se descarta lo escrito.
--}}
@props(['countries'])
@php
    $mine = old('_drawer') === 'client-create';
@endphp
<div
    class="relative z-90"
    x-data="{ open: {{ $mine && $errors->any() ? 'true' : 'false' }} }"
    x-init="$watch('open', value => { if (!value) window.dispatchEvent(new CustomEvent('add-client-modal-closed')) })"
    x-on:open-add-client-modal.window="open = true"
    x-on:keydown.escape.window="open = false"
    x-trap.inert.noscroll="open"
    x-cloak
    x-show="open"
    role="dialog"
    aria-modal="true"
    aria-labelledby="add-client-title"
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
                class="relative flex max-h-[calc(100vh-3rem)] w-full flex-col overflow-hidden rounded-lg bg-white dark:bg-lits-blue-550 shadow-xl sm:max-w-3xl"
            >
                <form id="client-create-form" action="{{ route('clients.store') }}" method="POST" class="flex min-h-0 flex-1 flex-col">
                    @csrf
                    <input type="hidden" name="_drawer" value="client-create">

                    <div class="flex items-start justify-between gap-3 border-b border-gray-200 dark:border-lits-blue-450 px-6 py-5">
                        <div>
                            <h3 id="add-client-title" class="text-base font-semibold text-gray-900 dark:text-gray-50">Nuevo cliente</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Llena los datos del cliente y su dirección.</p>
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
                                collect($errors->messages())->except(['company_name', 'trade_name', 'federal_tax_id', 'national_id', 'email', 'phone1', 'country_id', 'state_id', 'city_id', 'street_name', 'street_no', 'neighborhood', 'postal_code'])->all()
                            );
                        @endphp
                        @if ($mine && $unmatchedErrors->any())
                            <div id="client-create-errors">
                                <x-alerts.error :message="'Para crear el cliente soluciona los siguientes errores:'" :errors="$unmatchedErrors" class="mb-6" />
                            </div>
                        @endif
                        <div class="space-y-12">
                                <div class="border-b border-gray-900/10 dark:border-white/10 pb-12">
                                    <h2 class="text-base/7 font-semibold text-gray-900 dark:text-gray-50">Datos del cliente</h2>
                                    <p class="mt-1 text-sm/6 text-gray-600 dark:text-gray-400">Llena los datos relacionados con el cliente.</p>
                                    <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 lg:grid-cols-12">
                                        <div class="sm:col-span-6">
                                            <label for="company_name" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Razón social</label>
                                            <div class="mt-2">
                                                <input id="company_name" value="{{old('company_name')}}" name="company_name" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                            </div>
                                        </div>
                                        <div class="sm:col-span-6">
                                            <label for="trade_name" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Nombre comercial</label>
                                            <div class="mt-2">
                                                <input id="trade_name" value="{{old('trade_name')}}" name="trade_name" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                            </div>
                                        </div>
                                        <div class="sm:col-span-3">
                                            <label for="federal_tax_id" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">RFC</label>
                                            <div class="mt-2">
                                                <input id="federal_tax_id" value="{{old('federal_tax_id')}}" name="federal_tax_id" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                            </div>
                                        </div>
                                        <div class="sm:col-span-3">
                                            <label for="national_id" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">CURP</label>
                                            <div class="mt-2">
                                                <input id="national_id" value="{{old('national_id')}}" name="national_id" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                            </div>
                                        </div>
                                        <div class="sm:col-span-3">
                                            <label for="phone1" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Teléfono</label>
                                            <div class="mt-2">
                                                <input id="phone1" value="{{old('phone1')}}" name="phone1" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                            </div>
                                        </div>
                                        <div class="sm:col-span-3">
                                            <label for="email" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Email</label>
                                            <div class="mt-2">
                                                <input id="email" value="{{old('email')}}" name="email" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="pb-12">
                                    <h2 class="text-base/7 font-semibold text-gray-900 dark:text-gray-50">Dirección</h2>
                                    <p class="mt-1 text-sm/6 text-gray-600 dark:text-gray-400">Llena los datos de la dirección del cliente.</p>
                                    <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 lg:grid-cols-12">
                                        <div class="sm:col-span-2 lg:col-span-4">
                                                <label for="country_id" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">País</label>
                                                <div class="mt-2">
                                                    <select id="country_id" name="country_id" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                        <option value="">Selecciona un país</option>
                                                        @foreach($countries as $country)
                                                            <option value="{{ $country->id }}" @selected(old('country_id') == $country->id)>{{ $country->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                        <div class="sm:col-span-2 lg:col-span-4">
                                                <label for="state_id" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Estado</label>
                                                <div class="mt-2">
                                                    <select id="state_id" name="state_id" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                        <option value="">Selecciona un estado</option>
                                                    </select>
                                                </div>
                                            </div>

                                        <div class="sm:col-span-2 lg:col-span-4">
                                                <label for="city_id" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Ciudad</label>
                                                <div class="mt-2">
                                                    <select id="city_id" name="city_id" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                        <option value="">Selecciona una ciudad</option>
                                                    </select>
                                                </div>
                                            </div>

                                        <div class="sm:col-span-full">
                                            <label for="street_name" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Calle</label>
                                            <div class="mt-2">
                                                <input id="street_name" value="{{old('street_name')}}" name="street_name" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                            </div>
                                        </div>

                                        <div class="sm:col-span-2 lg:col-span-4">
                                            <label for="street_no" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Número</label>
                                            <div class="mt-2">
                                                <input id="street_no" value="{{old('street_no')}}" name="street_no" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                            </div>
                                        </div>

                                        <div class="sm:col-span-2 lg:col-span-4">
                                            <label for="neighborhood" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Colonia</label>
                                            <div class="mt-2">
                                                <input id="neighborhood" value="{{old('neighborhood')}}" name="neighborhood" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                            </div>
                                        </div>

                                        <div class="sm:col-span-2 lg:col-span-4">
                                            <label for="postal_code" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Código Postal</label>
                                            <div class="mt-2">
                                                <input id="postal_code" value="{{old('postal_code')}}" name="postal_code" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-lits-blue-450 px-6 py-4">
                        <button type="button" @click="open = false" class="text-sm font-semibold text-gray-900 dark:text-gray-50 hover:cursor-pointer">Cancelar</button>
                        <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Crear cliente</button>
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
            const mine = @json($mine);
            initLocationSelects({ old: { country: mine ? @json(old('country_id')) : null, state: mine ? @json(old('state_id')) : null, city: mine ? @json(old('city_id')) : null } });
            attachLiveValidation('company_name', { required: true });
            attachLiveValidation('trade_name', { required: true });
            attachLiveValidation('email', { required: true, email: true });
            attachLiveValidation('phone1', { required: true, numeric: true, min: 10 });
            attachLiveValidation('country_id', { required: true, requiredMessage: 'Selecciona un país.' });
            attachLiveValidation('state_id', { required: true, requiredMessage: 'Selecciona un estado.' });
            attachLiveValidation('city_id', { required: true, requiredMessage: 'Selecciona una ciudad.' });
            attachLiveValidation('street_name', { required: true });
            attachLiveValidation('street_no', { required: true, max: 50 });
            attachLiveValidation('neighborhood', { required: true, max: 100 });
            attachLiveValidation('postal_code', { required: true, numeric: true, min: 5 });
            // Sin reglas en el navegador: solo para pintar bajo su campo el error que devuelva el servidor.
            attachLiveValidation('federal_tax_id');
            attachLiveValidation('national_id');

            const form = document.getElementById('client-create-form');
            // Valida todo al enviar y pinta bajo su campo los errores del servidor (solo los de este modal).
            // Va antes del seguro de doble clic: si cancela el envío, ese seguro no debe bloquear el botón.
            enableFormValidation('client-create-form', { errors: @json($mine ? $errors->messages() : (object) []) });

            // Avoid creating the client twice on a double click.
            form.addEventListener('submit', function (e) {
                const submit = form.querySelector('button[type="submit"]');
                if (e.defaultPrevented) return;
                if (submit.disabled) { e.preventDefault(); return; }
                submit.disabled = true;
            });

            // Closing the modal without saving discards what was typed: empty every
            // field, collapse the State/City selects and clear the live error marks.
            window.addEventListener('add-client-modal-closed', function () {
                form.querySelectorAll('input[type="text"], select').forEach(function (el) {
                    el.value = '';
                    el.classList.remove('outline-red-500', 'dark:outline-red-500');
                    el.classList.add('outline-gray-300', 'dark:outline-gray-600');
                });
                [['state_id', 'Selecciona un estado'], ['city_id', 'Selecciona una ciudad']].forEach(function (pair) {
                    document.getElementById(pair[0]).innerHTML = '<option value="">' + pair[1] + '</option>';
                });
                form.querySelectorAll('[id$="-error"]').forEach(function (el) { el.classList.add('hidden'); });
                const serverErrors = document.getElementById('client-create-errors');
                if (serverErrors) { serverErrors.remove(); }
            });
        })();
    </script>
@endpush
