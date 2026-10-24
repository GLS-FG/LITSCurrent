@props(['mode', 'order', 'shipment', 'agencies'])
{{--
    Modal para agregar ("create") y editar ("edit") un transporte de un embarque. Mismas validaciones que
    order-shipment/transportation/create y edit (que siguen existiendo): validación en vivo, selects
    de Proveedor (select con las agencias) y de Unidad (select que se llena con las unidades del proveedor elegido, petición a autocomplete.vehicles) y las reglas
    de TransportationPostRequest / TransportationPutRequest en servidor.

    - Crear: lo abre `open-add-transportation-modal` (botón "Agregar transporte" de transports-section).
    - Editar: se monta sin transporte; el ícono de editar de una fila dispara `request-edit-transportation`
      con los valores de la fila (ya están cargados, no hace falta otra petición) y el modal los pone y se
      abre. En el embarque puede haber varios transportes, por eso hay un solo modal de editar.
    - Si el servidor rechaza el formulario, Laravel regresa al embarque y el modal se reabre solo con los
      errores y el old(). El transporte a editar se recupera de `_record`, casteado a entero, nunca usado
      como URL.
    Al cerrarlo sin guardar se descarta lo escrito. Los ids del modal de editar llevan el prefijo `edit_`
    para no chocar con los del de crear en la misma página.
--}}
@php
    $isEdit = $mode === 'edit';
    $p = $isEdit ? 'edit_' : '';
    $formId = 'transportation-' . $mode . '-form';
    $drawerKey = 'transportation-' . $mode;
    $eventName = $isEdit ? 'edit-transportation-modal' : 'add-transportation-modal';
    $mine = old('_drawer') === $drawerKey;
    $record = ($isEdit && $mine && (int) old('_record'))
        ? \App\Models\Transportation::with('agency')->where('order_shipment_id', $shipment->id)->find((int) old('_record'))
        : null;
    $saved = fn ($field) => $record?->{$field};
    $val = fn ($field) => $mine ? old($field, $saved($field)) : null;
    $selectClass = 'block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6';
    $agencyList = collect($agencies)->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)->values();
    $agencyLabel = fn ($agency) => $agency->name . ($agency->company_name ? ' / ' . $agency->company_name : '');
    $savedAgencyLabel = $record ? $record->agency->name . ' / ' . $record->agency->company_name : '';
    $formAction = $isEdit
        ? ($record ? route('orders.shipments.transportations.update', ['order' => $order->id, 'shipment' => $shipment->id, 'transportation' => $record->id]) : '')
        : route('orders.shipments.transportations.store', ['order' => $order->id, 'shipment' => $shipment->id]);
    $savedSnapshot = $record
        ? array_merge(
            ['transportation_agency' => $savedAgencyLabel],
            $record->only(['transportation_agency_id', 'unit_eco_number', 'vehicle_id', 'transportation_type', 'plates', 'driver', 'tracking_link'])
        )
        : (object) [];
    $inputClass = 'block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6';
    $labelClass = 'block text-sm/6 font-medium text-gray-900 dark:text-gray-50';
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
                        <input type="hidden" id="{{ $p }}record_id" name="_record" value="{{ $record?->id }}">
                    @endif
                    <input type="hidden" name="_drawer" value="{{ $drawerKey }}">

                    <div class="flex items-start justify-between gap-3 border-b border-gray-200 dark:border-lits-blue-450 px-6 py-5">
                        <div>
                            <h3 id="{{ $formId }}-title" class="text-base font-semibold text-gray-900 dark:text-gray-50">{{ $isEdit ? 'Editar transporte' : 'Agregar transporte' }}</h3>
                            <p id="{{ $p }}subtitle" class="text-xs text-gray-500 dark:text-gray-400">{{ $isEdit ? $savedAgencyLabel : 'Ingresa la información general del transporte.' }}</p>
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
                                collect($errors->messages())->except(['transportation_agency_id', 'vehicle_id', 'unit_eco_number', 'transportation_type', 'plates', 'driver', 'tracking_link'])->all()
                            );
                        @endphp
                        @if ($mine && $unmatchedErrors->any())
                            <div id="{{ $formId }}-errors">
                                <x-alerts.error :message="$isEdit ? 'Para editar el transporte soluciona los siguientes errores:' : 'Para crear el transporte soluciona los siguientes errores:'" :errors="$unmatchedErrors" class="mb-6" />
                            </div>
                        @endif

                        <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                            <div class="sm:col-span-3">
                                <label for="{{ $p }}transportation_agency_id" class="{{ $labelClass }}">Proveedor</label>
                                <div class="mt-2">
                                    <select id="{{ $p }}transportation_agency_id" name="transportation_agency_id" class="{{ $selectClass }}">
                                        <option value="">Selecciona un proveedor</option>
                                        @foreach($agencyList as $agency)
                                            <option value="{{ $agency->id }}" @selected((string) $val('transportation_agency_id') === (string) $agency->id)>{{ $agencyLabel($agency) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="sm:col-span-3">
                                <label for="{{ $p }}vehicle_id" class="{{ $labelClass }}">Unit Eco. Number</label>
                                <div class="mt-2">
                                    <select id="{{ $p }}vehicle_id" name="vehicle_id" class="{{ $selectClass }}">
                                        <option value="">Sin unidad</option>
                                    </select>
                                    <input id="{{ $p }}unit_eco_number" value="{{ $val('unit_eco_number') }}" name="unit_eco_number" type="hidden">
                                </div>
                            </div>
                            <div class="sm:col-span-3">
                                <label for="{{ $p }}transportation_type" class="{{ $labelClass }}">Tipo de Transporte</label>
                                <div class="mt-2">
                                    <input id="{{ $p }}transportation_type" value="{{ $val('transportation_type') }}" name="transportation_type" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div class="sm:col-span-3">
                                <label for="{{ $p }}plates" class="{{ $labelClass }}">Placas</label>
                                <div class="mt-2">
                                    <input id="{{ $p }}plates" value="{{ $val('plates') }}" name="plates" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div class="sm:col-span-6">
                                <label for="{{ $p }}driver" class="{{ $labelClass }}">Chofer</label>
                                <div class="mt-2">
                                    <input id="{{ $p }}driver" value="{{ $val('driver') }}" name="driver" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div class="sm:col-span-6">
                                <label for="{{ $p }}tracking_link" class="{{ $labelClass }}">Enlace de rastreo</label>
                                <div class="mt-2">
                                    <input id="{{ $p }}tracking_link" value="{{ $val('tracking_link') }}" name="tracking_link" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-lits-blue-450 px-6 py-4">
                        <button type="button" @click="open = false" class="text-sm font-semibold text-gray-900 dark:text-gray-50 hover:cursor-pointer">Cancelar</button>
                        <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">{{ $isEdit ? 'Guardar' : 'Crear transporte' }}</button>
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
            const textFields = ['transportation_type', 'plates', 'driver', 'tracking_link'];

            attachLiveValidation(p + 'transportation_agency_id', { required: true, requiredMessage: 'Selecciona un proveedor.' });
            attachLiveValidation(p + 'transportation_type', { required: true, max: 255 });
            attachLiveValidation(p + 'plates', { required: true, max: 255 });
            attachLiveValidation(p + 'driver', { max: 255 });
            attachLiveValidation(p + 'tracking_link', { max: 500 });
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

            // Las unidades del proveedor elegido (Unit Eco. Number). `loadVehicles` vuelve a llenar el select
            // y, si se indica, deja seleccionada una unidad (al editar o al restaurar).
            const agencySelect = el('transportation_agency_id');
            const vehicleSelect = el('vehicle_id');
            let vehicles = [];
            let loadToken = 0;

            const resetVehicles = function () {
                vehicleSelect.innerHTML = '';
                const option = document.createElement('option');
                option.value = '';
                option.textContent = 'Sin unidad';
                vehicleSelect.appendChild(option);
                vehicles = [];
            };

            const loadVehicles = function (agencyId, selectedId = null) {
                const token = ++loadToken;
                resetVehicles();
                if (!agencyId) return Promise.resolve();
                return fetch(@js(route('autocomplete.vehicles')) + '?all=1&transportation_agency_id=' + encodeURIComponent(agencyId))
                    .then((res) => res.json())
                    .then((data) => {
                        if (token !== loadToken) return;
                        vehicles = data;
                        data.forEach(function (item) {
                            const option = document.createElement('option');
                            option.value = item.value;
                            option.textContent = item.label;
                            if (selectedId && String(item.value) === String(selectedId)) option.selected = true;
                            vehicleSelect.appendChild(option);
                        });
                    });
            };

            // Al elegir proveedor se cargan sus unidades y se limpia lo que dependía de la unidad anterior.
            agencySelect.addEventListener('change', function () {
                el('unit_eco_number').value = '';
                el('transportation_type').value = '';
                el('plates').value = '';
                loadVehicles(agencySelect.value);
            });
            // Al elegir unidad se llenan tipo de transporte y placas; sin unidad se limpian.
            vehicleSelect.addEventListener('change', function () {
                const vehicle = vehicles.find((item) => String(item.value) === vehicleSelect.value);
                el('unit_eco_number').value = vehicle ? vehicle.label : '';
                el('transportation_type').value = vehicle ? vehicle.vehicle_type : '';
                el('plates').value = vehicle ? vehicle.plates : '';
                ['transportation_type', 'plates'].forEach(function (field) { el(field).dispatchEvent(new Event('change')); });
            });

            // Valores iniciales (editar con errores del servidor o crear con old()).
            loadVehicles(agencySelect.value, @json($val('vehicle_id')));

            // The values to go back to when the modal is closed without saving: the saved record when
            // editing (replaced by the clicked row), empty when creating. Taken from the saved data,
            // not from the DOM, which may hold old() input.
            const snapshot = {
                transportation_agency_id: '', vehicle_id: '', unit_eco_number: '',
                transportation_type: '', plates: '', driver: '', tracking_link: '',
            };
            Object.assign(snapshot, @json($savedSnapshot));

            const fillFromSnapshot = function () {
                agencySelect.value = snapshot.transportation_agency_id == null ? '' : String(snapshot.transportation_agency_id);
                el('unit_eco_number').value = snapshot.unit_eco_number == null ? '' : snapshot.unit_eco_number;
                textFields.forEach(function (field) { el(field).value = snapshot[field] == null ? '' : snapshot[field]; });
                return loadVehicles(agencySelect.value, snapshot.vehicle_id);
            };

            const clearMarks = function () {
                form.querySelectorAll('[id$="-error"]').forEach(function (node) { node.classList.add('hidden'); });
                form.querySelectorAll('input[type="text"], select').forEach(function (node) {
                    node.classList.remove('outline-red-500', 'dark:outline-red-500');
                    node.classList.add('outline-gray-300', 'dark:outline-gray-600');
                });
                const serverErrors = document.getElementById(@js($formId . '-errors'));
                if (serverErrors) { serverErrors.remove(); }
            };

            window.addEventListener(@js($eventName . '-closed'), function () {
                fillFromSnapshot();
                clearMarks();
            });

            // Shipment page (edit mode): fill the modal with the clicked row's values, then open it.
            if (isEdit) window.addEventListener('request-edit-transportation', function (event) {
                const data = event.detail;
                form.action = data.update_url;
                el('record_id').value = data.id;
                document.getElementById('edit_subtitle').textContent = data.transportation_agency;
                Object.keys(snapshot).forEach(function (field) {
                    snapshot[field] = data[field] == null ? '' : data[field];
                });
                fillFromSnapshot();
                clearMarks();
                window.dispatchEvent(new CustomEvent('open-edit-transportation-modal'));
            });
        })();
    </script>
@endpush
