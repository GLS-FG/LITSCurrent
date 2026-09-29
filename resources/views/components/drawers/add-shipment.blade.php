@props(['order', 'serviceClasses', 'defaultServiceClass', 'instructionsOne', 'instructionsTwo'])
<div
    class="relative"
    x-data="{ open: {{ $errors->has('ship_from') || $errors->has('service_class_id') ? 'true' : 'false' }} }"
    x-on:open-add-shipment-drawer.window="open = true"
    x-on:keydown.escape.window="open = false"
    x-trap.inert.noscroll="open"
>
    {{--
        The real <form> lives here, outside the visual panel below, because the
        "Libreta de Direcciones" button rendered by <livewire:search-addresses>
        further down carries its own inner <form>. Nesting a <form> inside a
        <form> corrupts the parsed DOM (confirmed by feeding the rendered HTML
        through a real HTML5 parser): everything after the first nested form
        gets detached from this form's tree, including the footer buttons.
        Every field below is associated to this form via the `form` attribute
        instead of DOM nesting, which HTML5 supports natively.
    --}}
    <form id="shipment-create-form" action="{{route('orders.shipments.store', ['order' => $order])}}" method="POST" class="hidden">
        @csrf
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
        class="fixed inset-y-0 right-0 z-90 flex w-screen sm:max-w-4xl flex-col bg-white dark:bg-lits-blue-550 shadow-xl"
    >
                        <div class="flex items-start justify-between gap-3 border-b border-gray-200 dark:border-lits-blue-450 px-6 py-5">
                            <div class="flex gap-3">
                                <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-entity-shipments-50 dark:bg-entity-shipments/15 text-entity-shipments">
                                    <i class="fa-regular fa-route"></i>
                                </div>
                                <div>
                                    <div class="text-base font-semibold text-gray-900 dark:text-gray-50">Agregar embarque</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{$order->code}} &middot; {{$order->client->trade_name}}</div>
                                </div>
                            </div>
                            <button type="button" @click="open = false" class="rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                <span class="sr-only">Cerrar</span>
                                <i class="fa-regular fa-xmark text-lg"></i>
                            </button>
                        </div>

                        <div class="flex-1 min-h-0 overflow-y-auto px-6 py-5 space-y-6">
                            @if ($errors->any())
                                <x-alerts.error :message="'Para crear el servicio soluciona los siguientes errores:'" :errors="$errors" />
                            @endif

                            <div>
                                <label for="reference" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Referencia</label>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Recuerda llenar sólo la información necesaria de la referencia.</p>
                                <div class="mt-2">
                                    <textarea id="reference" form="shipment-create-form" name="reference" rows="2" autocomplete="off" required class="@error('reference') outline-red-400 @else outline-gray-300 dark:outline-gray-600 @enderror user-invalid:outline-red-400 dark:user-invalid:outline-red-400 touched-invalid:outline-red-400 dark:touched-invalid:outline-red-400 block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">{{old('reference')}}</textarea>
                                    @error('reference')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <div class="text-sm font-semibold text-gray-900 dark:text-gray-50">Ruta</div>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Busca una dirección o elige una de la libreta de direcciones.</p>
                                <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4">

                                    <div class="rounded-lg border border-gray-200 dark:border-lits-blue-450 p-4">
                                        <div class="flex items-center gap-1.5 text-xs font-bold tracking-wide text-entity-shipments mb-3">
                                            <i class="fa-regular fa-circle-dot"></i> ORIGEN
                                        </div>
                                        <div class="flex gap-2">
                                            <input id="origin_autocomplete" form="shipment-create-form" placeholder="Busca una dirección" name="origin_autocomplete" type="search" autocomplete="off" autofill="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">
                                            <livewire:search-addresses type="origen" />
                                        </div>
                                        <input id="origin_country_id" form="shipment-create-form" value="{{old('origin_country_id')}}" name="origin_country_id" type="hidden" />
                                        <input id="origin_state_id" form="shipment-create-form" value="{{old('origin_state_id')}}" name="origin_state_id" type="hidden" />
                                        <input id="origin_city_id" form="shipment-create-form" value="{{old('origin_city_id')}}" name="origin_city_id" type="hidden" />
                                        <input id="ship_from_name" form="shipment-create-form" value="{{old('ship_from_name')}}" name="ship_from_name" type="hidden" />
                                        <input id="ship_from_id" form="shipment-create-form" value="{{old('ship_from_id')}}" name="ship_from_id" type="hidden" />

                                        <div class="mt-3">
                                            <label for="ship_from" class="block text-xs font-medium text-gray-900 dark:text-gray-50">Dirección de recolección</label>
                                            <div class="mt-1">
                                                <textarea id="ship_from" form="shipment-create-form" rows="3" name="ship_from" autocomplete="off" required class="@error('ship_from') outline-red-400 @else outline-gray-300 dark:outline-gray-600 @enderror user-invalid:outline-red-400 dark:user-invalid:outline-red-400 touched-invalid:outline-red-400 dark:touched-invalid:outline-red-400 block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">{{old('ship_from')}}</textarea>
                                                <input id="ship_from_link" form="shipment-create-form" value="{{old('ship_from_link')}}" name="ship_from_link" type="hidden" />
                                                @error('ship_from')
                                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="mt-3">
                                            <label for="estimated_time_departure" class="block text-xs font-medium text-gray-900 dark:text-gray-50">ETD</label>
                                            <div class="mt-1">
                                                <input id="estimated_time_departure" form="shipment-create-form" value="{{old('estimated_time_departure')}}" name="estimated_time_departure" type="text" autocomplete="off" placeholder="dd/mm/aaaa" required class="@error('estimated_time_departure') outline-red-400 @else outline-gray-300 dark:outline-gray-600 @enderror user-invalid:outline-red-400 dark:user-invalid:outline-red-400 touched-invalid:outline-red-400 dark:touched-invalid:outline-red-400 block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">
                                                @error('estimated_time_departure')
                                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="rounded-lg border border-gray-200 dark:border-lits-blue-450 p-4">
                                        <div class="flex items-center gap-1.5 text-xs font-bold tracking-wide text-entity-shipments mb-3">
                                            <i class="fa-regular fa-location-dot"></i> DESTINO
                                        </div>
                                        <div class="flex gap-2">
                                            <input id="destination_autocomplete" form="shipment-create-form" placeholder="Busca una dirección" name="destination_autocomplete" type="search" autocomplete="off" autofill="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">
                                            <livewire:search-addresses type="destino" />
                                        </div>
                                        <input id="destination_country_id" form="shipment-create-form" value="{{old('destination_country_id')}}" name="destination_country_id" type="hidden" />
                                        <input id="destination_state_id" form="shipment-create-form" value="{{old('destination_state_id')}}" name="destination_state_id" type="hidden" />
                                        <input id="destination_city_id" form="shipment-create-form" value="{{old('destination_city_id')}}" name="destination_city_id" type="hidden" />
                                        <input id="ship_to_name" form="shipment-create-form" value="{{old('ship_to_name')}}" name="ship_to_name" type="hidden" />
                                        <input id="ship_to_id" form="shipment-create-form" value="{{old('ship_to_id')}}" name="ship_to_id" type="hidden" />

                                        <div class="mt-3">
                                            <label for="ship_to" class="block text-xs font-medium text-gray-900 dark:text-gray-50">Dirección de entrega</label>
                                            <div class="mt-1">
                                                <textarea id="ship_to" form="shipment-create-form" rows="3" name="ship_to" autocomplete="off" required class="@error('ship_to') outline-red-400 @else outline-gray-300 dark:outline-gray-600 @enderror user-invalid:outline-red-400 dark:user-invalid:outline-red-400 touched-invalid:outline-red-400 dark:touched-invalid:outline-red-400 block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">{{old('ship_to')}}</textarea>
                                                <input id="ship_to_link" form="shipment-create-form" value="{{old('ship_to_link')}}" name="ship_to_link" type="hidden" />
                                                @error('ship_to')
                                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="mt-3">
                                            <label for="estimated_time_arrival" class="block text-xs font-medium text-gray-900 dark:text-gray-50">ETA</label>
                                            <div class="mt-1">
                                                <input id="estimated_time_arrival" form="shipment-create-form" value="{{old('estimated_time_arrival')}}" name="estimated_time_arrival" type="text" autocomplete="off" placeholder="dd/mm/aaaa" required class="@error('estimated_time_arrival') outline-red-400 @else outline-gray-300 dark:outline-gray-600 @enderror user-invalid:outline-red-400 dark:user-invalid:outline-red-400 touched-invalid:outline-red-400 dark:touched-invalid:outline-red-400 block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">
                                                @error('estimated_time_arrival')
                                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div>
                                <div class="text-sm font-semibold text-gray-900 dark:text-gray-50">Tipo de servicio</div>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">La selección se acomoda en cascada.</p>
                                <div class="mt-3 grid grid-cols-2 lg:grid-cols-4 gap-3">
                                    <div>
                                        <label for="service_class_id" class="block text-xs font-medium text-gray-900 dark:text-gray-50">Service Class</label>
                                        <div class="mt-1 grid grid-cols-1">
                                            <select id="service_class_id" form="shipment-create-form" name="service_class_id" autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">
                                                @foreach($serviceClasses as $service)
                                                    <option value='{{$service->id}}' @selected(old('service_class_id') == $service->id)>{{$service->name}}</option>
                                                @endforeach
                                            </select>
                                            <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 self-center justify-self-end text-sm text-gray-500 dark:text-gray-400"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <label for="service_mode_id" class="block text-xs font-medium text-gray-900 dark:text-gray-50">Service Mode</label>
                                        <div class="mt-1 grid grid-cols-1">
                                            <select id="service_mode_id" form="shipment-create-form" name="service_mode_id" autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">
                                            </select>
                                            <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 self-center justify-self-end text-sm text-gray-500 dark:text-gray-400"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <label for="class_type_id" class="block text-xs font-medium text-gray-900 dark:text-gray-50">Class Type</label>
                                        <div class="mt-1 grid grid-cols-1">
                                            <select id="class_type_id" form="shipment-create-form" name="class_type_id" autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">
                                            </select>
                                            <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 self-center justify-self-end text-sm text-gray-500 dark:text-gray-400"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <label for="service_level_id" class="block text-xs font-medium text-gray-900 dark:text-gray-50">Service Level</label>
                                        <div class="mt-1 grid grid-cols-1">
                                            <select id="service_level_id" form="shipment-create-form" name="service_level_id" autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">
                                            </select>
                                            <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 self-center justify-self-end text-sm text-gray-500 dark:text-gray-400"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Manejo especial: no longer editable from this drawer, always defaults to "No". --}}
                            <input type="hidden" form="shipment-create-form" name="oversize" value="No" />
                            <input type="hidden" form="shipment-create-form" name="hazardous_material" value="No" />
                            <input type="hidden" form="shipment-create-form" name="refrigerated" value="No" />
                            <input type="hidden" form="shipment-create-form" name="insurance" value="No" />
                            <input type="hidden" form="shipment-create-form" name="tarps" value="No" />

                            <div>
                                <details class="rounded-lg border border-gray-200 dark:border-lits-blue-450 group">
                                    <summary class="flex cursor-pointer items-center justify-between px-4 py-2.5 text-sm font-semibold text-gray-900 dark:text-gray-50">
                                        Instrucciones de envío y consigna
                                        <i class="fa-regular fa-angle-down text-gray-400 dark:text-gray-500 transition-transform group-open:rotate-180"></i>
                                    </summary>
                                    <div class="px-4 pb-4 space-y-3">
                                        <div>
                                            <label for="instructions1" class="block text-xs font-medium text-gray-900 dark:text-gray-50">Instrucciones de envio</label>
                                            <div class="mt-1">
                                                <textarea rows="3" id="instructions1" form="shipment-create-form" name="instructions1" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">{{old('instructions1', $instructionsOne)}}</textarea>
                                            </div>
                                        </div>
                                        <div>
                                            <label for="instructions2" class="block text-xs font-medium text-gray-900 dark:text-gray-50">Instrucciones de consignia</label>
                                            <div class="mt-1">
                                                <textarea rows="3" id="instructions2" form="shipment-create-form" name="instructions2" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">{{old('instructions2', $instructionsTwo)}}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </details>
                            </div>

                            <div>
                                <label for="comments" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Comentarios</label>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Información adicional para el embarque.</p>
                                <div class="mt-1">
                                    <textarea rows="2" id="comments" form="shipment-create-form" name="comments" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">{{old('comments')}}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-lits-blue-450 px-6 py-4">
                            <button type="button" @click="open = false" class="text-sm font-semibold text-gray-900 dark:text-gray-50 hover:cursor-pointer">Cancelar</button>
                            <button type="submit" form="shipment-create-form" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Crear embarque</button>
                        </div>
    </div>
</div>

@push('custom_script')
    <script type="module">
        $('#estimated_time_departure').datepicker({ dateFormat: 'dd/mm/yy' });
        $('#estimated_time_arrival').datepicker({ dateFormat: 'dd/mm/yy' });
        // Marks a required field red as soon as it's left blank, whether the
        // user tabbed past it or clicked into it and out without typing
        // anything (:user-invalid alone doesn't cover that case in Chrome).
        document.querySelectorAll('#reference, #ship_from, #ship_to, #estimated_time_departure, #estimated_time_arrival').forEach(function (el) {
            el.addEventListener('blur', function () { el.setAttribute('data-touched', 'true'); });
            el.addEventListener('invalid', function () { el.setAttribute('data-touched', 'true'); });
        });
        $('#origin_autocomplete').autocomplete({
            minLength: 1,
            autoFocus: true,
            source: function( request, response ) {
                $.ajax({
                    url: "{{ route('autocomplete.addresses') }}",
                    type: 'GET',
                    dataType: "json",
                    data: {
                        search: request.term
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            focus: function(event, ui) {
                event.preventDefault();
            },
            select: function(event, ui) {
                event.preventDefault();
                $('#origin_autocomplete').val('');
                $('#origin_country_id').val(ui.item.country_id);
                $('#origin_state_id').val(ui.item.state_id);
                $('#origin_city_id').val(ui.item.city_id);
                $('#ship_from').val(ui.item.full_address);
                $('#ship_from_name').val(ui.item.label);
                $('#ship_from_link').val(ui.item.link);
                $('#ship_from_id').val(ui.item.value);
            },
            change: function(event, ui) {
                if($('#origin_autocomplete').val() === ""){
                    $('#origin_country_id').val("");
                    $('#origin_state_id').val('');
                    $('#origin_city_id').val('');
                    $('#ship_from').val('');
                    $('#ship_from_name').val('');
                    $('#ship_from_link').val('');
                    $('#ship_from_id').val('');
                }
            }
        });
        $('#destination_autocomplete').autocomplete({
            minLength: 1,
            autoFocus: true,
            source: function( request, response ) {
                $.ajax({
                    url: "{{ route('autocomplete.addresses') }}",
                    type: 'GET',
                    dataType: "json",
                    data: {
                        search: request.term
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            focus: function(event, ui) {
                event.preventDefault();
            },
            select: function(event, ui) {
                event.preventDefault();
                $('#destination_autocomplete').val('');
                $('#destination_country_id').val(ui.item.country_id);
                $('#destination_state_id').val(ui.item.state_id);
                $('#destination_city_id').val(ui.item.city_id);
                $('#ship_to').val(ui.item.full_address);
                $('#ship_to_name').val(ui.item.label);
                $('#ship_to_link').val(ui.item.link);
                $('#ship_to_id').val(ui.item.value);
            },
            change: function(event, ui) {
                if($('#destination_autocomplete').val() === ""){
                    $('#destination_country_id').val("");
                    $('#destination_state_id').val('');
                    $('#destination_city_id').val('');
                    $('#ship_to').val('');
                    $('#ship_to_name').val('');
                    $('#ship_to_link').val('');
                    $('#ship_to_id').val('');
                }
            }
        });
        var serviceModes;
        var hasServiceModeOld = false;
        var hasClassTypeOld = false;
        var hasServiceLevelOld = false;
        @if (old('service_mode_id'))
            hasServiceModeOld = true;
        @endif
        @if (old('class_type_id'))
            hasClassTypeOld = true;
        @endif
        @if (old('service_level_id'))
            hasServiceLevelOld = true;
        @endif

        const $serviceClass = $('#service_class_id');
        const $serviceMode = $('#service_mode_id');
        const $classType = $('#class_type_id');
        const $serviceLevel = $('#service_level_id');
        function fetchServiceTypes (service_type) {
            $.ajax({
                url: "{{ route('autocomplete.serviceTypes') }}",
                type: 'GET',
                dataType: "json",
                data: { service_type },
                success: function(data) {
                    serviceModes = data;
                    $serviceMode.empty();
                    for (const serviceMode of serviceModes) {
                        const serviceModeOption = new Option(serviceMode.name, serviceMode.id);
                        $serviceMode.append(serviceModeOption);
                    }
                    if(hasServiceModeOld === true){
                        hasServiceModeOld = false;
                        $serviceMode.val('{{old('service_mode_id')}}').change();
                    } else {
                        const firstId = serviceModes[0].id;
                        $serviceMode.val(firstId).change();
                    }
                }
            });
        }
        fetchServiceTypes("{{old('service_class_id', $defaultServiceClass->id)}}")
        $serviceClass.change(function() {
            fetchServiceTypes($(this).val());
        });
        $serviceMode.change(function() {
            $classType.empty();
            const classTypes = serviceModes.find(m => m.id == $serviceMode.val()).class_types;
            for (const classType of classTypes) {
                const classTypeOption = new Option(classType.name, classType.id);
                $classType.append(classTypeOption);
            }
            if(hasClassTypeOld === true){
                hasClassTypeOld = false;
                $classType.val("{{old('class_type_id')}}").change();
            } else {
                const firstId = classTypes[0].id;
                $classType.val(firstId).change();
            }

        });
        $classType.change(function() {
            $serviceLevel.empty();
            const serviceLevels = serviceModes.find(m => m.id == $serviceMode.val()).class_types.find(m => m.id == $classType.val()).service_levels;
            for (const serviceLevel of serviceLevels) {
                const serviceLevelOption = new Option(serviceLevel.name, serviceLevel.id);
                $serviceLevel.append(serviceLevelOption);
            }
            if(hasServiceLevelOld === true){
                hasServiceLevelOld = false;
                $serviceLevel.val("{{old('service_level_id')}}").change();
            } else {
                const firstId = serviceLevels[0].id;
                $serviceLevel.val(firstId).change();
            }
        });
        $('#shipment-create-form').on('submit', function(e) {
            const $submitButton = $(this).find('button[type="submit"]');
            if ($submitButton.prop('disabled')) {
                e.preventDefault();
                return;
            }
            $submitButton.prop('disabled', true);
        });
        document.addEventListener('livewire:init', function() {
            Livewire.on('address-selected', (event) => {
                let { address } = event;
                if(address.type === "origen"){
                    $('#origin_country_id').val(address.country_id);
                    $('#origin_state_id').val(address.state_id);
                    $('#origin_city_id').val(address.city_id);
                    $('#ship_from').val(address.full_address);
                    $('#ship_from_name').val(address.address_name);
                    $('#ship_from_link').val(address.link);
                    $('#ship_from_id').val(address.id);
                } else if(address.type === "destino"){
                    $('#destination_country_id').val(address.country_id);
                    $('#destination_state_id').val(address.state_id);
                    $('#destination_city_id').val(address.city_id);
                    $('#ship_to').val(address.full_address);
                    $('#ship_to_name').val(address.address_name);
                    $('#ship_to_link').val(address.link);
                    $('#ship_to_id').val(address.id);
                }
            });
        });
    </script>
@endpush
