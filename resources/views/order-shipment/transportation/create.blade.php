@section('title', 'Nuevo transporte')
@section('custom_script')
    <script type="module">
        const agencies = {{ Js::from($agencies) }};
        $('#transportation_agency').autocomplete({
            minLength: 2,
            source: function (request, response) {
                response($.map(agencies, function (obj, key) {
                    const name = obj.name.toUpperCase();
                    const company_name = obj.company_name?.toUpperCase();
                    if (name.indexOf(request.term.toUpperCase()) !== -1 || company_name?.indexOf(request.term.toUpperCase()) !== -1) {
                        return {
                            label: name + " / " + company_name,
                            value: obj.id
                        }
                    } else {
                        return null;
                    }
                }));
            },
            focus: function(event, ui) {
                event.preventDefault();
            },
            select: function(event, ui) {
                event.preventDefault();
                $('#transportation_agency').val(ui.item.label);
                $('#transportation_agency_id').val(ui.item.value);
                $('#unit_eco_number').val("");
                $('#vehicle_id').val("");
                $('#transportation_type').val("");
                $('#plates').val("");
            },
            change: function(event, ui) {
                if($('#transportation_agency').val() === ""){
                    $('#transportation_agency_id').val("");
                    $('#unit_eco_number').val("");
                    $('#vehicle_id').val("");
                    $('#transportation_type').val("");
                    $('#plates').val("");
                }
            }
        });
        $('#unit_eco_number').autocomplete({
            minLength: 1,
            autoFocus: true,
            source: function( request, response ) {
                $.ajax({
                    url: "{{ route('autocomplete.vehicles') }}",
                    type: 'GET',
                    dataType: "json",
                    data: {
                        search: request.term,
                        transportation_agency_id: $('#transportation_agency_id').val()
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
                $('#unit_eco_number').val(ui.item.label);
                $('#vehicle_id').val(ui.item.value);
                $('#transportation_type').val(ui.item.vehicle_type);
                $('#plates').val(ui.item.plates);
            },
            change: function(event, ui) {
                if($('#unit_eco_number').val() === ""){
                    $('#unit_eco_number').val("");
                    $('#vehicle_id').val('');
                    $('#transportation_type').val('');
                    $('#plates').val('');
                }
            }
        });
    </script>
@endsection
<x-layout-app>
    <section>
        <x-navigation.breadcrumbs :links="['Ordenes' => route('orders.index'), $order->code => route('orders.show', ['order' => $order->id]), 'Embarque' => route('orders.shipments.show', ['order' => $order->id, 'shipment' => $shipment->id]), 'Nuevo transporte' => '#']" />
        <x-headings.without-action
            :title="'Nuevo transporte'"
        />
        @if ($errors->any())
            <x-alerts.error :message="'Para crear el transporte soluciona los siguientes errores:'" :errors="$errors" class="my-4" />
        @endif
        <form action="{{ route('orders.shipments.transportations.store', ['order' => $order, 'shipment' => $shipment]) }}" method="POST" class="mt-8" autocomplete="off">
            @csrf
            <div class="divide-y divide-gray-200 rounded bg-white shadow-lits-card">
                <div class="px-4 py-5 sm:p-6">
                    <div class="space-y-12">
                        <div class="border-b border-gray-900/10 pb-12">
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
                        <div class="pb-12">
                            <h2 class="text-base/7 font-semibold text-gray-900">Información general</h2>
                            <p class="mt-1 text-sm/6 text-gray-600">Ingresa la información general del transporte.</p>
                            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                <div class="sm:col-span-3">
                                    <label for="transportation_agency" class="block text-sm/6 font-medium text-gray-900">Proveedor</label>
                                    <div class="mt-2">
                                        <input id="transportation_agency" value="{{old('transportation_agency')}}" placeholder="Busca una agencia" name="transportation_agency" type="search" autocomplete="off" autofill="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                        <input id="transportation_agency_id" value="{{old('transportation_agency_id')}}" name="transportation_agency_id" type="hidden">
                                    </div>
                                </div>

                                <div class="sm:col-span-3">
                                    <label for="unit_eco_number" class="block text-sm/6 font-medium text-gray-900">Unit Eco. Number</label>
                                    <div class="mt-2">
                                        <input id="unit_eco_number" value="{{old('unit_eco_number')}}" placeholder="Busca una unidad" name="unit_eco_number" type="search" autocomplete="off" autofill="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                        <input id="vehicle_id" value="{{old('vehicle_id')}}" name="vehicle_id" type="hidden">
                                    </div>
                                </div>

                                <div class="sm:col-span-3">
                                    <label for="transportation_type" class="block text-sm/6 font-medium text-gray-900">Tipo de Transporte</label>
                                    <div class="mt-2">
                                        <input id="transportation_type" value="{{old('transportation_type')}}" name="transportation_type" type="text" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>

                                <div class="sm:col-span-3">
                                    <label for="plates" class="block text-sm/6 font-medium text-gray-900">Placas</label>
                                    <div class="mt-2">
                                        <input id="plates" value="{{old('plates')}}" name="plates" type="text" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>

                                <div class="sm:col-span-3">
                                    <label for="driver" class="block text-sm/6 font-medium text-gray-900">Chofer</label>
                                    <div class="mt-2">
                                        <input id="driver" value="{{old('driver')}}" name="driver" type="text" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>

                                <div class="sm:col-span-full">
                                    <label for="tracking_link" class="block text-sm/6 font-medium text-gray-900">Enlace de rastreo</label>
                                    <div class="mt-2">
                                        <input id="tracking_link" value="{{old('tracking_link')}}" name="tracking_link" type="text" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-4 sm:px-6">
                    <div class="mt-6 flex items-center justify-end gap-x-6">
                        <a href="{{ route('orders.shipments.show', ['order' => $order->id, 'shipment' => $shipment->id]) }}" class="text-sm/6 font-semibold text-gray-900">Cancelar</a>
                        <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Crear transporte</button>
                    </div>
                </div>
            </div>
        </form>
    </section>
</x-layout-app>
