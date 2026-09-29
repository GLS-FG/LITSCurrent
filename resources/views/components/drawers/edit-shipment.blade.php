@props(['order', 'shipment', 'serviceClasses', 'defaultServiceClass'])
<div
    class="relative"
    x-data="{ open: {{ $errors->has('ship_from') || $errors->has('service_class_id') ? 'true' : 'false' }} }"
    x-init="$watch('open', value => { if (!value) window.dispatchEvent(new CustomEvent('edit-shipment-drawer-closed')) })"
    x-on:open-edit-shipment-drawer.window="open = true"
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
    <form id="shipment-edit-form" action="{{route('orders.shipments.update', ['order' => $order, 'shipment' => $shipment])}}" method="POST" class="hidden">
        @csrf
        @method('PUT')
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
                    <i class="fa-regular fa-pen-to-square"></i>
                </div>
                <div>
                    <div class="text-base font-semibold text-gray-900 dark:text-gray-50">Editar embarque</div>
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
                <x-alerts.error :message="'Para editar el servicio soluciona los siguientes errores:'" :errors="$errors" />
            @endif

            <div>
                <label for="edit_reference" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Referencia</label>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Recuerda llenar sólo la información necesaria de la referencia.</p>
                <div class="mt-2">
                    <textarea id="edit_reference" form="shipment-edit-form" name="reference" rows="2" autocomplete="off" required class="@error('reference') outline-red-400 @else outline-gray-300 dark:outline-gray-600 @enderror user-invalid:outline-red-400 dark:user-invalid:outline-red-400 touched-invalid:outline-red-400 dark:touched-invalid:outline-red-400 block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">{{old('reference', $shipment->reference)}}</textarea>
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
                            <input id="edit_origin_autocomplete" form="shipment-edit-form" placeholder="Busca una dirección" name="origin_autocomplete" type="search" autocomplete="off" autofill="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">
                            <livewire:search-addresses type="origen" />
                        </div>
                        <input id="edit_origin_country_id" form="shipment-edit-form" value="{{old('origin_country_id', is_null($shipment->origin_country_id) ? '' : $shipment->origin_country_id)}}" name="origin_country_id" type="hidden" />
                        <input id="edit_origin_state_id" form="shipment-edit-form" value="{{old('origin_state_id', is_null($shipment->origin_state_id) ? '' : $shipment->origin_state_id)}}" name="origin_state_id" type="hidden" />
                        <input id="edit_origin_city_id" form="shipment-edit-form" value="{{old('origin_city_id', is_null($shipment->origin_city_id) ? '' : $shipment->origin_city_id)}}" name="origin_city_id" type="hidden" />
                        <input id="edit_ship_from_name" form="shipment-edit-form" value="{{old('ship_from_name', $shipment->ship_from_name)}}" name="ship_from_name" type="hidden" />
                        <input id="edit_ship_from_id" form="shipment-edit-form" value="{{old('ship_from_id', $shipment->ship_from_id)}}" name="ship_from_id" type="hidden" />

                        <div class="mt-3">
                            <label for="edit_ship_from" class="block text-xs font-medium text-gray-900 dark:text-gray-50">Dirección de recolección</label>
                            <div class="mt-1">
                                <textarea id="edit_ship_from" form="shipment-edit-form" rows="3" name="ship_from" autocomplete="off" required class="@error('ship_from') outline-red-400 @else outline-gray-300 dark:outline-gray-600 @enderror user-invalid:outline-red-400 dark:user-invalid:outline-red-400 touched-invalid:outline-red-400 dark:touched-invalid:outline-red-400 block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">{{old('ship_from', $shipment->ship_from)}}</textarea>
                                <input id="edit_ship_from_link" form="shipment-edit-form" value="{{old('ship_from_link', $shipment->ship_from_link)}}" name="ship_from_link" type="hidden" />
                                @error('ship_from')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-3">
                            <label for="edit_estimated_time_departure" class="block text-xs font-medium text-gray-900 dark:text-gray-50">ETD</label>
                            <div class="mt-1">
                                <input id="edit_estimated_time_departure" form="shipment-edit-form" value="{{old('estimated_time_departure', is_null($shipment->estimated_time_departure) ? '' : $shipment->estimated_time_departure->format('d/m/Y'))}}" name="estimated_time_departure" type="text" autocomplete="off" placeholder="dd/mm/aaaa" required class="@error('estimated_time_departure') outline-red-400 @else outline-gray-300 dark:outline-gray-600 @enderror user-invalid:outline-red-400 dark:user-invalid:outline-red-400 touched-invalid:outline-red-400 dark:touched-invalid:outline-red-400 block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">
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
                            <input id="edit_destination_autocomplete" form="shipment-edit-form" placeholder="Busca una dirección" name="destination_autocomplete" type="search" autocomplete="off" autofill="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">
                            <livewire:search-addresses type="destino" />
                        </div>
                        <input id="edit_destination_country_id" form="shipment-edit-form" value="{{old('destination_country_id', is_null($shipment->destination_country_id) ? '' : $shipment->destination_country_id)}}" name="destination_country_id" type="hidden" />
                        <input id="edit_destination_state_id" form="shipment-edit-form" value="{{old('destination_state_id', is_null($shipment->destination_state_id) ? '' : $shipment->destination_state_id)}}" name="destination_state_id" type="hidden" />
                        <input id="edit_destination_city_id" form="shipment-edit-form" value="{{old('destination_city_id', is_null($shipment->destination_city_id) ? '' : $shipment->destination_city_id)}}" name="destination_city_id" type="hidden" />
                        <input id="edit_ship_to_name" form="shipment-edit-form" value="{{old('ship_to_name', $shipment->ship_to_name)}}" name="ship_to_name" type="hidden" />
                        <input id="edit_ship_to_id" form="shipment-edit-form" value="{{old('ship_to_id', $shipment->ship_to_id)}}" name="ship_to_id" type="hidden" />

                        <div class="mt-3">
                            <label for="edit_ship_to" class="block text-xs font-medium text-gray-900 dark:text-gray-50">Dirección de entrega</label>
                            <div class="mt-1">
                                <textarea id="edit_ship_to" form="shipment-edit-form" rows="3" name="ship_to" autocomplete="off" required class="@error('ship_to') outline-red-400 @else outline-gray-300 dark:outline-gray-600 @enderror user-invalid:outline-red-400 dark:user-invalid:outline-red-400 touched-invalid:outline-red-400 dark:touched-invalid:outline-red-400 block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">{{old('ship_to', $shipment->ship_to)}}</textarea>
                                <input id="edit_ship_to_link" form="shipment-edit-form" value="{{old('ship_to_link', $shipment->ship_to_link)}}" name="ship_to_link" type="hidden" />
                                @error('ship_to')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-3">
                            <label for="edit_estimated_time_arrival" class="block text-xs font-medium text-gray-900 dark:text-gray-50">ETA</label>
                            <div class="mt-1">
                                <input id="edit_estimated_time_arrival" form="shipment-edit-form" value="{{old('estimated_time_arrival', is_null($shipment->estimated_time_arrival) ? '' : $shipment->estimated_time_arrival->format('d/m/Y'))}}" name="estimated_time_arrival" type="text" autocomplete="off" placeholder="dd/mm/aaaa" required class="@error('estimated_time_arrival') outline-red-400 @else outline-gray-300 dark:outline-gray-600 @enderror user-invalid:outline-red-400 dark:user-invalid:outline-red-400 touched-invalid:outline-red-400 dark:touched-invalid:outline-red-400 block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">
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
                        <label for="edit_service_class_id" class="block text-xs font-medium text-gray-900 dark:text-gray-50">Service Class</label>
                        <div class="mt-1 grid grid-cols-1">
                            <select id="edit_service_class_id" form="shipment-edit-form" name="service_class_id" autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">
                                @foreach($serviceClasses as $service)
                                    <option value='{{$service->id}}' @selected(old('service_class_id', $shipment->service_class_id) == $service->id)>{{$service->name}}</option>
                                @endforeach
                            </select>
                            <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 self-center justify-self-end text-sm text-gray-500 dark:text-gray-400"></i>
                        </div>
                    </div>
                    <div>
                        <label for="edit_service_mode_id" class="block text-xs font-medium text-gray-900 dark:text-gray-50">Service Mode</label>
                        <div class="mt-1 grid grid-cols-1">
                            <select id="edit_service_mode_id" form="shipment-edit-form" name="service_mode_id" autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">
                            </select>
                            <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 self-center justify-self-end text-sm text-gray-500 dark:text-gray-400"></i>
                        </div>
                    </div>
                    <div>
                        <label for="edit_class_type_id" class="block text-xs font-medium text-gray-900 dark:text-gray-50">Class Type</label>
                        <div class="mt-1 grid grid-cols-1">
                            <select id="edit_class_type_id" form="shipment-edit-form" name="class_type_id" autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">
                            </select>
                            <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 self-center justify-self-end text-sm text-gray-500 dark:text-gray-400"></i>
                        </div>
                    </div>
                    <div>
                        <label for="edit_service_level_id" class="block text-xs font-medium text-gray-900 dark:text-gray-50">Service Level</label>
                        <div class="mt-1 grid grid-cols-1">
                            <select id="edit_service_level_id" form="shipment-edit-form" name="service_level_id" autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">
                            </select>
                            <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 self-center justify-self-end text-sm text-gray-500 dark:text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Manejo especial: no longer editable from this drawer; keeps whatever the shipment already had. --}}
            <input type="hidden" id="edit_oversize" form="shipment-edit-form" name="oversize" value="{{old('oversize', $shipment->oversize)}}" />
            <input type="hidden" id="edit_hazardous_material" form="shipment-edit-form" name="hazardous_material" value="{{old('hazardous_material', $shipment->hazardous_material)}}" />
            <input type="hidden" id="edit_refrigerated" form="shipment-edit-form" name="refrigerated" value="{{old('refrigerated', $shipment->refrigerated)}}" />
            <input type="hidden" id="edit_insurance" form="shipment-edit-form" name="insurance" value="{{old('insurance', $shipment->insurance)}}" />
            <input type="hidden" id="edit_tarps" form="shipment-edit-form" name="tarps" value="{{old('tarps', $shipment->tarps)}}" />

            <div>
                <details class="rounded-lg border border-gray-200 dark:border-lits-blue-450 group">
                    <summary class="flex cursor-pointer items-center justify-between px-4 py-2.5 text-sm font-semibold text-gray-900 dark:text-gray-50">
                        Instrucciones de envío y consigna
                        <i class="fa-regular fa-angle-down text-gray-400 dark:text-gray-500 transition-transform group-open:rotate-180"></i>
                    </summary>
                    <div class="px-4 pb-4 space-y-3">
                        <div>
                            <label for="edit_instructions1" class="block text-xs font-medium text-gray-900 dark:text-gray-50">Instrucciones de envio</label>
                            <div class="mt-1">
                                <textarea rows="3" id="edit_instructions1" form="shipment-edit-form" name="instructions1" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">{{old('instructions1', $shipment->instructions1)}}</textarea>
                            </div>
                        </div>
                        <div>
                            <label for="edit_instructions2" class="block text-xs font-medium text-gray-900 dark:text-gray-50">Instrucciones de consignia</label>
                            <div class="mt-1">
                                <textarea rows="3" id="edit_instructions2" form="shipment-edit-form" name="instructions2" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">{{old('instructions2', $shipment->instructions2)}}</textarea>
                            </div>
                        </div>
                    </div>
                </details>
            </div>

            <div>
                <label for="edit_comments" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Comentarios</label>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Información adicional para el embarque.</p>
                <div class="mt-1">
                    <textarea rows="2" id="edit_comments" form="shipment-edit-form" name="comments" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-sm text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600">{{old('comments', $shipment->comments)}}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-lits-blue-450 px-6 py-4">
            <button type="button" @click="open = false" class="text-sm font-semibold text-gray-900 dark:text-gray-50 hover:cursor-pointer">Cancelar</button>
            <button type="submit" form="shipment-edit-form" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Guardar</button>
        </div>
    </div>
</div>

@push('custom_script')
    <script type="module">
        $('#edit_estimated_time_departure').datepicker({ dateFormat: 'dd/mm/yy' });
        $('#edit_estimated_time_arrival').datepicker({ dateFormat: 'dd/mm/yy' });
        // Marks a required field red as soon as it's left blank, whether the
        // user tabbed past it or clicked into it and out without typing
        // anything (:user-invalid alone doesn't cover that case in Chrome).
        document.querySelectorAll('#edit_reference, #edit_ship_from, #edit_ship_to, #edit_estimated_time_departure, #edit_estimated_time_arrival').forEach(function (el) {
            el.addEventListener('blur', function () { el.setAttribute('data-touched', 'true'); });
            el.addEventListener('invalid', function () { el.setAttribute('data-touched', 'true'); });
        });
        $('#edit_origin_autocomplete').autocomplete({
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
                $('#edit_origin_autocomplete').val('');
                $('#edit_origin_country_id').val(ui.item.country_id);
                $('#edit_origin_state_id').val(ui.item.state_id);
                $('#edit_origin_city_id').val(ui.item.city_id);
                $('#edit_ship_from').val(ui.item.full_address);
                $('#edit_ship_from_name').val(ui.item.label);
                $('#edit_ship_from_link').val(ui.item.link);
                $('#edit_ship_from_id').val(ui.item.value);
            },
            change: function(event, ui) {
                if($('#edit_origin_autocomplete').val() === ""){
                    $('#edit_origin_country_id').val("");
                    $('#edit_origin_state_id').val('');
                    $('#edit_origin_city_id').val('');
                    $('#edit_ship_from').val('');
                    $('#edit_ship_from_name').val('');
                    $('#edit_ship_from_link').val('');
                    $('#edit_ship_from_id').val('');
                }
            }
        });
        $('#edit_destination_autocomplete').autocomplete({
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
                $('#edit_destination_autocomplete').val('');
                $('#edit_destination_country_id').val(ui.item.country_id);
                $('#edit_destination_state_id').val(ui.item.state_id);
                $('#edit_destination_city_id').val(ui.item.city_id);
                $('#edit_ship_to').val(ui.item.full_address);
                $('#edit_ship_to_name').val(ui.item.label);
                $('#edit_ship_to_link').val(ui.item.link);
                $('#edit_ship_to_id').val(ui.item.value);
            },
            change: function(event, ui) {
                if($('#edit_destination_autocomplete').val() === ""){
                    $('#edit_destination_country_id').val("");
                    $('#edit_destination_state_id').val('');
                    $('#edit_destination_city_id').val('');
                    $('#edit_ship_to').val('');
                    $('#edit_ship_to_name').val('');
                    $('#edit_ship_to_link').val('');
                    $('#edit_ship_to_id').val('');
                }
            }
        });
        var editServiceModes;
        var hasEditServiceModeOld = true;
        var hasEditClassTypeOld = true;
        var hasEditServiceLevelOld = true;

        // These represent the shipment's real, saved values (or old() input
        // after a failed validation redirect) — the cascade always falls back
        // to picking the first option once these one-shot flags are spent, so
        // resetEditForm() below re-arms them to restore this exact state.
        var editServiceClassTarget = "{{old('service_class_id', is_null($shipment->service_class_id) ? $defaultServiceClass->id : $shipment->service_class_id)}}";
        var editServiceModeTarget = "{{old('service_mode_id', is_null($shipment->service_mode_id) ? '1' : $shipment->service_mode_id)}}";
        var editClassTypeTarget = "{{old('class_type_id', is_null($shipment->class_type_id) ? '1' : $shipment->class_type_id)}}";
        var editServiceLevelTarget = "{{old('service_level_id', is_null($shipment->service_level_id) ? '1' : $shipment->service_level_id)}}";

        const $editServiceClass = $('#edit_service_class_id');
        const $editServiceMode = $('#edit_service_mode_id');
        const $editClassType = $('#edit_class_type_id');
        const $editServiceLevel = $('#edit_service_level_id');
        function fetchEditServiceTypes (service_type) {
            $.ajax({
                url: "{{ route('autocomplete.serviceTypes') }}",
                type: 'GET',
                dataType: "json",
                data: { service_type },
                success: function(data) {
                    editServiceModes = data;
                    $editServiceMode.empty();
                    for (const serviceMode of editServiceModes) {
                        const serviceModeOption = new Option(serviceMode.name, serviceMode.id);
                        $editServiceMode.append(serviceModeOption);
                    }
                    if(hasEditServiceModeOld === true){
                        hasEditServiceModeOld = false;
                        $editServiceMode.val(editServiceModeTarget).change();
                    } else {
                        const firstId = editServiceModes[0].id;
                        $editServiceMode.val(firstId).change();
                    }
                }
            });
        }
        fetchEditServiceTypes(editServiceClassTarget)
        $editServiceClass.change(function() {
            fetchEditServiceTypes($(this).val());
        });
        $editServiceMode.change(function() {
            $editClassType.empty();
            const classTypes = editServiceModes.find(m => m.id == $editServiceMode.val()).class_types;
            for (const classType of classTypes) {
                const classTypeOption = new Option(classType.name, classType.id);
                $editClassType.append(classTypeOption);
            }
            if(hasEditClassTypeOld === true){
                hasEditClassTypeOld = false;
                $editClassType.val(editClassTypeTarget).change();
            } else {
                const firstId = classTypes[0].id;
                $editClassType.val(firstId).change();
            }
        });
        $editClassType.change(function() {
            $editServiceLevel.empty();
            const serviceLevels = editServiceModes.find(m => m.id == $editServiceMode.val()).class_types.find(m => m.id == $editClassType.val()).service_levels;
            for (const serviceLevel of serviceLevels) {
                const serviceLevelOption = new Option(serviceLevel.name, serviceLevel.id);
                $editServiceLevel.append(serviceLevelOption);
            }
            if(hasEditServiceLevelOld === true){
                hasEditServiceLevelOld = false;
                $editServiceLevel.val(editServiceLevelTarget).change();
            } else {
                const firstId = serviceLevels[0].id;
                $editServiceLevel.val(firstId).change();
            }
        });
        $('#shipment-edit-form').on('submit', function(e) {
            const $submitButton = $(this).find('button[type="submit"]');
            if ($submitButton.prop('disabled')) {
                e.preventDefault();
                return;
            }
            $submitButton.prop('disabled', true);
        });

        // Snapshot of the shipment's real values, taken once while the form
        // still holds exactly what the server rendered (before any editing).
        // Closing the drawer without saving restores this, so reopening it
        // later never shows an abandoned edit.
        var editFormSnapshot = {
            reference: $('#edit_reference').val(),
            origin_country_id: $('#edit_origin_country_id').val(),
            origin_state_id: $('#edit_origin_state_id').val(),
            origin_city_id: $('#edit_origin_city_id').val(),
            ship_from_name: $('#edit_ship_from_name').val(),
            ship_from_id: $('#edit_ship_from_id').val(),
            ship_from: $('#edit_ship_from').val(),
            ship_from_link: $('#edit_ship_from_link').val(),
            estimated_time_departure: $('#edit_estimated_time_departure').val(),
            destination_country_id: $('#edit_destination_country_id').val(),
            destination_state_id: $('#edit_destination_state_id').val(),
            destination_city_id: $('#edit_destination_city_id').val(),
            ship_to_name: $('#edit_ship_to_name').val(),
            ship_to_id: $('#edit_ship_to_id').val(),
            ship_to: $('#edit_ship_to').val(),
            ship_to_link: $('#edit_ship_to_link').val(),
            estimated_time_arrival: $('#edit_estimated_time_arrival').val(),
            instructions1: $('#edit_instructions1').val(),
            instructions2: $('#edit_instructions2').val(),
            comments: $('#edit_comments').val(),
            oversize: $('#edit_oversize').val(),
            hazardous_material: $('#edit_hazardous_material').val(),
            refrigerated: $('#edit_refrigerated').val(),
            insurance: $('#edit_insurance').val(),
            tarps: $('#edit_tarps').val(),
        };

        function resetEditForm() {
            $('#edit_origin_autocomplete').val('');
            $('#edit_destination_autocomplete').val('');
            Object.keys(editFormSnapshot).forEach(function (field) {
                $('#edit_' + field).val(editFormSnapshot[field]);
            });
            document.querySelectorAll('[form="shipment-edit-form"][data-touched]').forEach(function (el) {
                el.removeAttribute('data-touched');
            });
            hasEditServiceModeOld = true;
            hasEditClassTypeOld = true;
            hasEditServiceLevelOld = true;
            $editServiceClass.val(editServiceClassTarget);
            fetchEditServiceTypes(editServiceClassTarget);
        }
        window.addEventListener('edit-shipment-drawer-closed', resetEditForm);
        document.addEventListener('livewire:init', function() {
            Livewire.on('address-selected', (event) => {
                let { address } = event;
                if(address.type === "origen"){
                    $('#edit_origin_country_id').val(address.country_id);
                    $('#edit_origin_state_id').val(address.state_id);
                    $('#edit_origin_city_id').val(address.city_id);
                    $('#edit_ship_from').val(address.full_address);
                    $('#edit_ship_from_name').val(address.address_name);
                    $('#edit_ship_from_link').val(address.link);
                    $('#edit_ship_from_id').val(address.id);
                } else if(address.type === "destino"){
                    $('#edit_destination_country_id').val(address.country_id);
                    $('#edit_destination_state_id').val(address.state_id);
                    $('#edit_destination_city_id').val(address.city_id);
                    $('#edit_ship_to').val(address.full_address);
                    $('#edit_ship_to_name').val(address.address_name);
                    $('#edit_ship_to_link').val(address.link);
                    $('#edit_ship_to_id').val(address.id);
                }
            });
        });
    </script>
@endpush
