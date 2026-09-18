@section('title', 'Duplicar embarque')
@section('custom_script')
    <script type="module">
        $('#estimated_time_departure').datepicker({ dateFormat: 'dd/mm/yy' });
        $('#estimated_time_arrival').datepicker({ dateFormat: 'dd/mm/yy' });
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
        @if (old('service_mode_id', $from->service_mode_id))
            hasServiceModeOld = true;
        @endif
        @if (old('class_type_id', $from->class_type_id))
            hasClassTypeOld = true;
        @endif
        @if (old('service_level_id', $from->service_level_id))
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
                        $serviceMode.val('{{old('service_mode_id', $from->service_mode_id)}}').change();
                    } else {
                        const firstId = serviceModes[0].id;
                        $serviceMode.val(firstId).change();
                    }
                }
            });
        }
        fetchServiceTypes("{{old('service_class_id', $from->service_class_id)}}")
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
                $classType.val("{{old('class_type_id', $from->class_type_id)}}").change();
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
                $serviceLevel.val("{{old('service_level_id', $from->service_level_id)}}").change();
            } else {
                const firstId = serviceLevels[0].id;
                $serviceLevel.val(firstId).change();
            }
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
@endsection
<x-layout-app>
    <section>
        <x-navigation.breadcrumbs :links="['Ordenes' => route('orders.index'), $from->order->code => route('orders.show', ['order' => $from->order]), $from->tracking_code => route('orders.shipments.show', ['order' => $from->order, 'shipment' => $from]), 'Duplicar Embarque' => '#']" />
        <x-headings.without-action
            :title="'Duplicar orden de embarque'"
        />
        @if ($errors->any())
            <x-alerts.error :message="'Para duplicar el servicio soluciona los siguientes errores:'" :errors="$errors" class="my-4" />
        @endif
        <form action="{{ route('clone.order.shipments.store', ['shipment' => $from, 'order' => $order]) }}" method="POST">
            @csrf
            <div class="mt-2 divide-y divide-gray-200 rounded bg-white shadow-lits-card">
                <div class="px-4 py-5 sm:p-6">
                    <div class="space-y-2">
                        <div class="border-b border-gray-900/10 pb-4">
                            <h2 class="text-2xl/7 font-bold text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">{{$order->code}}</h2>
                            <div class="pt-2 block md:flex md:justify-between">
                                <div class="flex flex-1 items-center gap-x-6">
                                    <img src="{{ route('clients.logos', [ 'filename' => str_replace(".","_",str_replace("logos/", "", $order->client->image))]) }}" alt="{{$order->client->trade_name}}" class="object-contain size-16 flex-none rounded-full bg-gray-200 outline -outline-offset-1 outline-black/5" />
                                    <div>
                                        <h1 class="mt-1 text-base font-semibold text-gray-900">{{$order->client->trade_name}}</h1>
                                        <p class="text-sm/6 text-gray-700">{{$order->contact->name}}</p>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm/6 text-gray-700 text-left md:text-right">{{$order->reference}}</p>
                                </div>
                            </div>
                        </div>
                        <div class="border-b border-gray-900/10 pb-4">
                            <h2 class="text-base/7 font-semibold text-gray-900">Referencia</h2>
                            <p class="mt-1 text-sm/6 text-gray-600">Recuerda llenar sólo la información necesaria de la referencia.</p>
                            <div class="mt-2 grid grid-cols-1 gap-x-2 gap-y-2 sm:grid-cols-6">
                                <div class="col-span-full">
                                    <label for="reference" class="block text-sm/6 font-medium text-gray-900">Referencia</label>
                                    <div class="mt-1">
                                        <textarea id="reference" name="reference" autocomplete="off" class="@error('reference') outline-red-400 @else outline-gray-300 @enderror block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">{{old('reference', $from->reference)}}</textarea>
                                        @error('reference')
                                        <p class="mt-1 text-sm/6 text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="border-b border-gray-900/10 pb-4">
                            <h2 class="text-base/7 font-semibold text-gray-900">Ori<span class="hidden">avoidautocomplete</span>gen</h2>
                            <p class="mt-1 text-sm/6 text-gray-600">Llena la información del ori<span class="hidden">avoidautocomplete</span>gen y la dire<span class="hidden">avoidautocomplete</span>cción de reco<span class="hidden">avoidautocomplete</span>leccion.</p>
                            <div class="mt-2 grid grid-cols-1 gap-x-2 gap-y-2 sm:grid-cols-6">
                                <div class="sm:col-span-full text-right">
                                    <div class="flex gap-x-4">
                                        <input id="origin_autocomplete" placeholder="Busca una dirección" name="origin_autocomplete" type="search" autocomplete="off" autofill="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                        <livewire:search-addresses type="origen" />
                                    </div>
                                </div>
                                <input id="origin_country_id" value="{{old('origin_country_id', $from->origin_country_id)}}" name="origin_country_id" type="hidden" />
                                <input id="origin_state_id" value="{{old('origin_state_id', $from->origin_state_id)}}" name="origin_state_id" type="hidden" />
                                <input id="origin_city_id" value="{{old('origin_city_id', $from->origin_city_id)}}" name="origin_city_id" type="hidden" />
                                <input id="ship_from_name" value="{{old('ship_from_name', $from->ship_from_name)}}" name="ship_from_name" type="hidden" />
                                <input id="ship_from_id" value="{{old('ship_from_id', $from->ship_from_id)}}" name="ship_from_id" type="hidden" />

                                <div class="col-span-full">
                                    <label for="ship_from" class="block text-sm/6 font-medium text-gray-900">Dir<span class="hidden">avoidautocomplete</span>ección de recolección</label>
                                    <div class="mt-1">
                                        <textarea id="ship_from" rows="5" readonly name="ship_from" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">{{old('ship_from', $from->ship_from)}}</textarea>
                                    </div>
                                </div>

                                <div class="sm:col-span-full">
                                    <label for="ship_from_link" class="block text-sm/6 font-medium text-gray-900">Ubicación maps de recolección</label>
                                    <div class="mt-1">
                                        <input id="ship_from_link" value="{{old('ship_from_link', $from->ship_from_link)}}" name="ship_from_link" type="text" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="estimated_time_departure" class="block text-sm/6 font-medium text-gray-900">ETD</label>
                                    <div class="mt-1">
                                        <input id="estimated_time_departure" value="{{old('estimated_time_departure')}}" name="estimated_time_departure" type="text" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="border-b border-gray-900/10 pb-4">
                            <h2 class="text-base/7 font-semibold text-gray-900">Des<span class="hidden">avoidautocomplete</span>tino</h2>
                            <p class="mt-1 text-sm/6 text-gray-600">Llena la información del des<span class="hidden">avoidautocomplete</span>tino y la dire<span class="hidden">avoidautocomplete</span>cción de ent<span class="hidden">avoidautocomplete</span>rega.</p>
                            <div class="mt-2 grid grid-cols-1 gap-x-2 gap-y-2 sm:grid-cols-6">
                                <div class="sm:col-span-full text-right">
                                    <div class="flex gap-x-4">
                                        <input id="destination_autocomplete" placeholder="Busca una dirección" name="destination_autocomplete" type="search" autocomplete="off" autofill="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                        <livewire:search-addresses type="destino" />
                                    </div>
                                </div>
                                <input id="destination_country_id" value="{{old('destination_country_id', $from->destination_country_id)}}" name="destination_country_id" type="hidden" />
                                <input id="destination_state_id" value="{{old('destination_state_id', $from->destination_state_id)}}" name="destination_state_id" type="hidden" />
                                <input id="destination_city_id" value="{{old('destination_city_id', $from->destination_city_id)}}" name="destination_city_id" type="hidden" />
                                <input id="ship_to_name" value="{{old('ship_to_name', $from->ship_to_name)}}" name="ship_to_name" type="hidden" />
                                <input id="ship_to_id" value="{{old('ship_to_id', $from->ship_to_id)}}" name="ship_to_id" type="hidden" />

                                <div class="col-span-full">
                                    <label for="ship_to" class="block text-sm/6 font-medium text-gray-900">Dir<span class="hidden">avoidautocomplete</span>ección de ent<span class="hidden">avoidautocomplete</span>rega</label>
                                    <div class="mt-1">
                                        <textarea id="ship_to" rows="5" readonly name="ship_to" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">{{old('ship_to', $from->ship_to)}}</textarea>
                                    </div>
                                </div>

                                <div class="sm:col-span-full">
                                    <label for="ship_to_link" class="block text-sm/6 font-medium text-gray-900">Ubicación maps de ent<span class="hidden">avoidautocomplete</span>rega</label>
                                    <div class="mt-1">
                                        <input id="ship_to_link" value="{{old('ship_to_link', $from->ship_to_link)}}" name="ship_to_link" type="text" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="estimated_time_arrival" class="block text-sm/6 font-medium text-gray-900">ETA</label>
                                    <div class="mt-1">
                                        <input id="estimated_time_arrival" value="{{old('estimated_time_arrival')}}" name="estimated_time_arrival" type="text" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="border-b border-gray-900/10 pb-4">
                            <h2 class="text-base/7 font-semibold text-gray-900">Service Type</h2>
                            <p class="mt-1 text-sm/6 text-gray-600">Ingresa la información del tipo de servicio.</p>
                            <div class="mt-2 grid grid-cols-1 gap-x-2 gap-y-2 sm:grid-cols-6">
                                <div class="sm:col-span-3">
                                    <label for="service_class_id" class="block text-sm/6 font-medium text-gray-900">Service Class</label>
                                    <div class="mt-2 grid grid-cols-1">
                                        <select id="service_class_id" name="service_class_id" autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white py-1.5 pr-8 pl-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                            @foreach($serviceClasses as $service)
                                                <option value='{{$service->id}}' @selected(old('service_class_id', $from->service_class_id) == $service->id)>{{$service->name}}</option>
                                            @endforeach
                                        </select>
                                        <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 sm:text-sm"></i>
                                    </div>
                                </div>

                                <div class="sm:col-span-3">
                                    <label for="service_mode_id" class="block text-sm/6 font-medium text-gray-900">Service Mode</label>
                                    <div class="mt-2 grid grid-cols-1">
                                        <select id="service_mode_id" name="service_mode_id" autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white py-1.5 pr-8 pl-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                        </select>
                                        <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 sm:text-sm"></i>
                                    </div>
                                </div>

                                <div class="sm:col-span-3">
                                    <label for="class_type_id" class="block text-sm/6 font-medium text-gray-900">Class Type</label>
                                    <div class="mt-2 grid grid-cols-1">
                                        <select id="class_type_id" name="class_type_id" autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white py-1.5 pr-8 pl-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                        </select>
                                        <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 sm:text-sm"></i>
                                    </div>
                                </div>

                                <div class="sm:col-span-3">
                                    <label for="service_level_id" class="block text-sm/6 font-medium text-gray-900">Service Level</label>
                                    <div class="mt-2 grid grid-cols-1">
                                        <select id="service_level_id" name="service_level_id" autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white py-1.5 pr-8 pl-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                        </select>
                                        <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 sm:text-sm"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="border-b border-gray-900/10 pb-4">
                            <h2 class="text-base/7 font-semibold text-gray-900">Manejo Especial</h2>
                            <p class="mt-1 text-sm/6 text-gray-600">Ingresa la información si el embarque tiene manejo especial.</p>
                            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-10">
                                <div class="sm:col-span-2">
                                    <label for="oversize" class="block text-sm/6 font-medium text-gray-900">Sobre dimensión</label>
                                    <div class="mt-2 grid grid-cols-1">
                                        <select id="oversize" name="oversize" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white py-1.5 pr-8 pl-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                            <option value="No" @selected(old('oversize', $from->oversize) == "No")>No</option>
                                            <option value="Si" @selected(old('oversize', $from->oversize) == "Si")>Si</option>
                                        </select>
                                        <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 sm:text-sm"></i>
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="hazardous_material" class="block text-sm/6 font-medium text-gray-900">Material Peligroso</label>
                                    <div class="mt-2 grid grid-cols-1">
                                        <select id="hazardous_material" name="hazardous_material" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white py-1.5 pr-8 pl-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                            <option value="No" @selected(old('hazardous_material', $from->hazardous_material) == "No")>No</option>
                                            <option value="Si" @selected(old('hazardous_material', $from->hazardous_material) == "Si")>Si</option>
                                        </select>
                                        <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 sm:text-sm"></i>
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="refrigerated" class="block text-sm/6 font-medium text-gray-900">Refrigerado</label>
                                    <div class="mt-2 grid grid-cols-1">
                                        <select id="refrigerated" name="refrigerated" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white py-1.5 pr-8 pl-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                            <option value="No" @selected(old('refrigerated', $from->refrigerated) == "No")>No</option>
                                            <option value="Si" @selected(old('refrigerated', $from->refrigerated) == "Si")>Si</option>
                                        </select>
                                        <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 sm:text-sm"></i>
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="insurance" class="block text-sm/6 font-medium text-gray-900">Seguro</label>
                                    <div class="mt-2 grid grid-cols-1">
                                        <select id="insurance" name="insurance" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white py-1.5 pr-8 pl-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                            <option value="No" @selected(old('insurance', $from->insurance) == "No")>No</option>
                                            <option value="Si" @selected(old('insurance', $from->insurance) == "Si")>Si</option>
                                        </select>
                                        <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 sm:text-sm"></i>
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="tarps" class="block text-sm/6 font-medium text-gray-900">Tarps</label>
                                    <div class="mt-2 grid grid-cols-1">
                                        <select id="tarps" name="tarps" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white py-1.5 pr-8 pl-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                            <option value="No" @selected(old('tarps', $from->tarps) == "No")>No</option>
                                            <option value="Si" @selected(old('tarps', $from->tarps) == "Si")>Si</option>
                                        </select>
                                        <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 sm:text-sm"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="border-b border-gray-900/10 pb-4">
                            <h2 class="text-base/7 font-semibold text-gray-900">Instrucciones</h2>
                            <p class="mt-1 text-sm/6 text-gray-600">Llena las instrucciones de envío y consigna.</p>
                            <div class="mt-2 grid grid-cols-1 gap-x-2 gap-y-2 sm:grid-cols-6">
                                <div class="col-span-full">
                                    <label for="instructions1" class="block text-sm/6 font-medium text-gray-900">Instrucciones de envio</label>
                                    <div class="mt-1">
                                        <textarea rows="4" id="instructions1" name="instructions1" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">{{old('instructions1', $instructionsOne)}}</textarea>
                                    </div>
                                </div>

                                <div class="col-span-full">
                                    <label for="instructions2" class="block text-sm/6 font-medium text-gray-900">Instrucciones de consignia</label>
                                    <div class="mt-1">
                                        <textarea rows="4" id="instructions2" name="instructions2" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">{{old('instructions2', $instructionsTwo)}}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="pb-4">
                            <h2 class="text-base/7 font-semibold text-gray-900">Información adicional</h2>
                            <p class="mt-1 text-sm/6 text-gray-600">Ingresa comentarios adicionales para el embarque.</p>
                            <div class="mt-2 grid grid-cols-1 gap-x-2 gap-y-2 sm:grid-cols-6">
                                <div class="col-span-full">
                                    <label for="comments" class="block text-sm/6 font-medium text-gray-900">Comentarios</label>
                                    <div class="mt-1">
                                        <textarea rows="4" id="comments" name="comments" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">{{old('comments', $from->comments)}}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-4 sm:px-6">
                    <div class="flex items-center justify-end gap-x-6">
                        <a href="{{ route('orders.show', ['order' => $order->id]) }}" class="text-sm/6 font-semibold text-gray-900">Cancelar</a>
                        <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Duplicar orden</button>
                    </div>
                </div>
            </div>
        </form>
    </section>
</x-layout-app>
