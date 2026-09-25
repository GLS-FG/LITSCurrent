@section('title', 'Agregar mercancía a embarque')
@section('custom_script')
    <script type="module">
        var index = 1;
        @if(filled(old('items')))
            index = {{count(old('items'))}};
        @endif
        $("#addRowButton").click(function() {
            var newRow = `
            <tr class="even:bg-gray-50 divide-x divide-gray-200 dark:divide-gray-700">
                <td class="py-4 pr-3 pl-4 text-xs text-gray-500 dark:text-gray-400 sm:pl-3">
                    <input required placeholder="{{__('Product description')}}" type="text" name="items[${index}][product]" class="block min-w-40 w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                </td>
                <td class="px-3 py-4 text-xs text-gray-500 dark:text-gray-400">
                    <input required placeholder="{{__('Product reference')}}" type="text" name="items[${index}][reference]" class="block min-w-40 w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                </td>
                <td class="px-3 py-4 text-xs text-gray-500 dark:text-gray-400">
                    <input placeholder="{{__('length')}}" type="number" name="items[${index}][length]" step="0.0001" class="block w-full min-w-24 rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                </td>
                <td class="px-3 py-4 text-xs text-gray-500 dark:text-gray-400">
                    <input placeholder="{{__('Width')}}" type="number" name="items[${index}][width]" step="0.0001" class="block w-full min-w-24 rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                </td>
                <td class="px-3 py-4 text-xs whitespace-nowrap text-gray-500 dark:text-gray-400">
                    <input placeholder="{{__('Height')}}" type="number" name="items[${index}][height]" step="0.0001" class="block w-full min-w-24 rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                </td>
                <td class="px-3 py-4 text-xs whitespace-nowrap text-gray-500 dark:text-gray-400">
                    <div class="grid grid-cols-1 min-w-24">
                        <select name="items[${index}][unit_measure]" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                            <option value="IN">IN</option>
                            <option value="FT">FT</option>
                            <option value="MM">MM</option>
                            <option value="CM">CM</option>
                            <option value="MTS">MTS</option>
                        </select>
                        <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 dark:text-gray-400 sm:text-sm"></i>
                    </div>
                </td>
                <td class="px-3 py-4 text-xs whitespace-nowrap text-gray-500 dark:text-gray-400">
                    <input placeholder="VARIOS" type="text" name="items[${index}][dimensions]" class="block w-full min-w-24 rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                </td>
                <td class="px-3 py-4 text-xs whitespace-nowrap text-gray-500 dark:text-gray-400">
                    <input placeholder="{{__('Weight')}}" type="text" name="items[${index}][weight]" class="block w-full min-w-24 rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                </td>
                <td class="px-3 py-4 text-xs whitespace-nowrap text-gray-500 dark:text-gray-400">
                    <div class="grid grid-cols-1 min-w-24">
                        <select name="items[${index}][weight_measure]" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                            <option value="KG">KG</option>
                            <option value="LBS">LBS</option>
                        </select>
                        <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 dark:text-gray-400 sm:text-sm"></i>
                    </div>
                </td>
                <td class="px-3 py-4 text-xs whitespace-nowrap text-gray-500 dark:text-gray-400">
                    <div class="grid grid-cols-1 min-w-32">
                        <select name="items[${index}][container]" autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                            <option value="Pallet">Pallet</option>
                            <option value="Crate">Crate</option>
                            <option value="Drum">Drum</option>
                            <option value="Tote">Tote</option>
                            <option value="Bulk">Bulk</option>
                            <option value="Carton">Carton</option>
                        </select>
                        <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 dark:text-gray-400 sm:text-sm"></i>
                    </div>
                </td>
                <td class="px-3 py-4 text-xs whitespace-nowrap text-gray-500 dark:text-gray-400">
                    <input type="number" placeholder="{{__('Quantity')}}" min="0" name="items[${index}][quantity]" class="block w-full min-w-24 rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                </td>
                <td class="px-3 py-4 text-xs whitespace-nowrap text-gray-500 dark:text-gray-400">
                    <input type="number" placeholder="$999.99"  step="0.01" name="items[${index}][value]" class="block w-full min-w-24 rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                </td>
                <td class="px-3 py-4 text-xs whitespace-nowrap text-gray-500 dark:text-gray-400">
                    <div class="grid grid-cols-1 min-w-40">
                        <select name="items[${index}][incoterm_id]" autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                            @foreach ($incoterms as $incoterm)
                            <option value="{{ $incoterm->id }}">{{ $incoterm->code }} ({{ $incoterm->name }})</option>
                            @endforeach
                        </select>
                        <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 dark:text-gray-400 sm:text-sm"></i>
                    </div>
                </td>
                <td class="py-4 pr-4 pl-3 text-right text-xs font-medium whitespace-nowrap sm:pr-3">
                    <div class="flex items-center justify-center gap-x-1">
                        <button
                            type="button"
                            @click="openCancel = true"
                            data-tippy-content="{{__('Delete')}}"
                            class="delete-btn size-7 shrink-0 rounded-md bg-red-100 dark:bg-red-500/15 flex items-center justify-center font-semibold text-red-500 dark:text-red-400 hover:text-red-800 hover:bg-red-200 hover:cursor-pointer"
                        >
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                    </div>
                </td>
            </tr>
            `;
            $("#productsTable tbody").append(newRow);
            index++;
        });
        $(document).on('click', '.delete-btn', function() {
            $(this).closest('tr').remove();
        });
    </script>
@endsection
<x-layout-app>
    <section>
        <x-navigation.breadcrumbs :links="['Ordenes' => route('orders.index'), $order->code => route('orders.show', ['order' => $order->id]), 'Agregar mercancía' => '#']" />
        <x-headings.without-action
            :title="'Agregar mercancía a embarque'"
        />
        @if ($errors->any())
            <x-alerts.error :message="'Para agregar la mercancía soluciona los siguientes errores:'" :errors="$errors" class="my-4" />
        @endif
        <form action="{{ route('orders.shipments.products.store', ['order' => $order, 'shipment' => $shipment]) }}" method="POST">
            @csrf
            <div class="mt-2 divide-y divide-gray-200 dark:divide-gray-700 rounded bg-white dark:bg-lits-blue-550 shadow-lits-card">
                <div class="px-4 py-5 sm:p-6">
                    <div class="space-y-2">
                        <div class="border-b border-gray-900/10 dark:border-white/10 pb-4">
                            <h2 class="text-2xl/7 font-bold text-gray-900 dark:text-gray-50 sm:truncate sm:text-3xl sm:tracking-tight">{{$order->code}}</h2>
                            <div class="pt-2 block md:flex md:justify-between">
                                <div class="flex flex-1 items-center gap-x-6">
                                    <img src="{{ route('clients.logos', [ 'filename' => str_replace(".","_",str_replace("logos/", "", $order->client->image))]) }}" alt="{{$order->client->trade_name}}" class="object-contain size-16 flex-none rounded-full bg-gray-200 dark:bg-gray-700 outline -outline-offset-1 outline-black/5" />
                                    <div>
                                        <h1 class="mt-1 text-base font-semibold text-gray-900 dark:text-gray-50">{{$order->client->trade_name}}</h1>
                                        <p class="text-sm/6 text-gray-700 dark:text-gray-300">{{$order->contact->name}}</p>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm/6 text-gray-700 dark:text-gray-300 text-left md:text-right">{{$order->reference}}</p>
                                </div>
                            </div>
                        </div>
                        <div class="py-4">
                            <div class="overflow-x-auto border border-gray-300 dark:border-gray-600 rounded">
                                <div class="inline-block min-w-full align-middle">
                                    <table id="productsTable" class="relative min-w-full divide-y divide-gray-300 dark:divide-gray-600">
                                        <thead>
                                        <tr class="divide-x divide-gray-200 dark:divide-gray-700">
                                            <th scope="col" class="py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-gray-900 dark:text-gray-50 sm:pl-3">Descripción</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-50">Referencia</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-50">Largo</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-50">Ancho</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-50">Alto</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-50">Unidad de medida</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-50">Otras dimensiones</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-50">Peso</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-50">Unidad de peso</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-50">Tipo de embalaje</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-50">Cantidad</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-50">Valor</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-50">INCOTERM</th>
                                            <th scope="col" class="py-3.5 pr-4 pl-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-50 sm:pr-3">{{__('Actions')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-lits-blue-550 divide-y divide-gray-300 dark:divide-gray-600">
                                            @if(filled(old('items')))
                                                @foreach( old('items') as $i => $item)
                                                    <tr class="even:bg-gray-50 divide-x divide-gray-200 dark:divide-gray-700">
                                                        <td class="py-4 pr-3 pl-4 text-xs text-gray-500 dark:text-gray-400 sm:pl-3">
                                                            <input required placeholder="{{__('Product description')}}" type="text" name="items[{{$i}}][product]" value="{{ old('items.'.$i.'.product', $item['product']) }}" class="block min-w-40 w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                        </td>
                                                        <td class="px-3 py-4 text-xs text-gray-500 dark:text-gray-400">
                                                            <input required placeholder="{{__('Product reference')}}" type="text" name="items[{{$i}}][reference]" value="{{ old('items.'.$i.'.reference', $item['reference']) }}" class="block min-w-40 w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                        </td>
                                                        <td class="px-3 py-4 text-xs text-gray-500 dark:text-gray-400">
                                                            <input placeholder="{{__('length')}}" type="number" name="items[{{$i}}][length]" value="{{ old('items.'.$i.'.length', $item['length']) }}" step="0.0001" class="block w-full min-w-24 rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                        </td>
                                                        <td class="px-3 py-4 text-xs text-gray-500 dark:text-gray-400">
                                                            <input placeholder="{{__('Width')}}" type="number" name="items[{{$i}}][width]" value="{{ old('items.'.$i.'.width', $item['width']) }}" step="0.0001" class="block w-full min-w-24 rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                        </td>
                                                        <td class="px-3 py-4 text-xs whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                            <input placeholder="{{__('Height')}}" type="number" name="items[{{$i}}][height]" value="{{ old('items.'.$i.'.height', $item['height']) }}" step="0.0001" class="block w-full min-w-24 rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                        </td>
                                                        <td class="px-3 py-4 text-xs whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                            <div class="grid grid-cols-1 min-w-24">
                                                                <select name="items[{{$i}}][unit_measure]" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                                    <option value="IN" @selected(old('items.'.$i.'.unit_measure', $item['unit_measure']) == "IN")>IN</option>
                                                                    <option value="FT" @selected(old('items.'.$i.'.unit_measure', $item['unit_measure']) == "FT")>FT</option>
                                                                    <option value="MM" @selected(old('items.'.$i.'.unit_measure', $item['unit_measure']) == "MM")>MM</option>
                                                                    <option value="CM" @selected(old('items.'.$i.'.unit_measure', $item['unit_measure']) == "CM")>CM</option>
                                                                    <option value="MTS" @selected(old('items.'.$i.'.unit_measure', $item['unit_measure']) == "MTS")>MTS</option>
                                                                </select>
                                                                <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 dark:text-gray-400 sm:text-sm"></i>
                                                            </div>
                                                        </td>
                                                        <td class="px-3 py-4 text-xs whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                            <input placeholder="VARIOS" type="text" name="items[{{$i}}][dimensions]" value="{{ old('items.'.$i.'.dimensions', $item['dimensions']) }}" class="block w-full min-w-24 rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                        </td>
                                                        <td class="px-3 py-4 text-xs whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                            <input placeholder="{{__('Weight')}}" type="text" name="items[{{$i}}][weight]" value="{{ old('items.'.$i.'.weight', $item['weight']) }}" class="block w-full min-w-24 rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                        </td>
                                                        <td class="px-3 py-4 text-xs whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                            <div class="grid grid-cols-1 min-w-24">
                                                                <select name="items[{{$i}}][weight_measure]" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                                    <option value="KG" @selected(old('items.'.$i.'.weight_measure', $item['weight_measure']) == "KG")>KG</option>
                                                                    <option value="LBS" @selected(old('items.'.$i.'.weight_measure', $item['weight_measure']) == "LBS")>LBS</option>
                                                                </select>
                                                                <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 dark:text-gray-400 sm:text-sm"></i>
                                                            </div>
                                                        </td>
                                                        <td class="px-3 py-4 text-xs whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                            <div class="grid grid-cols-1 min-w-32">
                                                                <select name="items[{{$i}}][container]" autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                                    <option value="Pallet" @selected(old('items.'.$i.'.container', $item['container']) == "Pallet")>Pallet</option>
                                                                    <option value="Crate" @selected(old('items.'.$i.'.container', $item['container']) == "Crate")>Crate</option>
                                                                    <option value="Drum" @selected(old('items.'.$i.'.container', $item['container']) == "Drum")>Drum</option>
                                                                    <option value="Tote" @selected(old('items.'.$i.'.container', $item['container']) == "Tote")>Tote</option>
                                                                    <option value="Bulk" @selected(old('items.'.$i.'.container', $item['container']) == "Bulk")>Bulk</option>
                                                                    <option value="Carton" @selected(old('items.'.$i.'.container', $item['container']) == "Carton")>Carton</option>
                                                                </select>
                                                                <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 dark:text-gray-400 sm:text-sm"></i>
                                                            </div>
                                                        </td>
                                                        <td class="px-3 py-4 text-xs whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                            <input type="number" placeholder="{{__('Quantity')}}" min="0" name="items[{{$i}}][quantity]" value="{{ old('items.'.$i.'.quantity', $item['quantity']) }}" class="block w-full min-w-24 rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                        </td>
                                                        <td class="px-3 py-4 text-xs whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                            <input type="number" placeholder="$999.99"  step="0.01" name="items[{{$i}}][value]" value="{{ old('items.'.$i.'.value', $item['value']) }}" class="block w-full min-w-24 rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                        </td>
                                                        <td class="px-3 py-4 text-xs whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                            <div class="grid grid-cols-1 min-w-40">
                                                                <select name="items[{{$i}}][incoterm_id]" autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                                    @foreach ($incoterms as $incoterm)
                                                                        <option value="{{ $incoterm->id }}" @selected(old('items.'.$i.'.incoterm_id', $item['incoterm_id']) == $incoterm->id)>{{ $incoterm->code }} ({{ $incoterm->name }})</option>
                                                                    @endforeach
                                                                </select>
                                                                <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 dark:text-gray-400 sm:text-sm"></i>
                                                            </div>
                                                        </td>
                                                        <td class="py-4 pr-4 pl-3 text-right text-xs font-medium whitespace-nowrap sm:pr-3">
                                                            <div class="flex items-center justify-center gap-x-1">
                                                                <button
                                                                    type="button"
                                                                    @click="openCancel = true"
                                                                    data-tippy-content="{{__('Delete')}}"
                                                                    class="delete-btn size-7 shrink-0 rounded-md bg-red-100 dark:bg-red-500/15 flex items-center justify-center font-semibold text-red-500 dark:text-red-400 hover:text-red-800 hover:bg-red-200 hover:cursor-pointer"
                                                                >
                                                                    <i class="fa-regular fa-trash-can"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr class="even:bg-gray-50 divide-x divide-gray-200 dark:divide-gray-700">
                                                    <td class="py-4 pr-3 pl-4 text-xs text-gray-500 dark:text-gray-400 sm:pl-3">
                                                        <input required placeholder="{{__('Product description')}}" type="text" name="items[0][product]" class="block min-w-40 w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                    </td>
                                                    <td class="px-3 py-4 text-xs text-gray-500 dark:text-gray-400">
                                                        <input required placeholder="{{__('Product reference')}}" type="text" name="items[0][reference]" class="block min-w-40 w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                    </td>
                                                    <td class="px-3 py-4 text-xs text-gray-500 dark:text-gray-400">
                                                        <input placeholder="{{__('length')}}" type="number" name="items[0][length]" step="0.0001" class="block w-full min-w-24 rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                    </td>
                                                    <td class="px-3 py-4 text-xs text-gray-500 dark:text-gray-400">
                                                        <input placeholder="{{__('Width')}}" type="number" name="items[0][width]" step="0.0001" class="block w-full min-w-24 rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                    </td>
                                                    <td class="px-3 py-4 text-xs whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                        <input placeholder="{{__('Height')}}" type="number" name="items[0][height]" step="0.0001" class="block w-full min-w-24 rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                    </td>
                                                    <td class="px-3 py-4 text-xs whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                        <div class="grid grid-cols-1 min-w-24">
                                                            <select name="items[0][unit_measure]" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                                <option value="IN">IN</option>
                                                                <option value="FT">FT</option>
                                                                <option value="MM">MM</option>
                                                                <option value="CM">CM</option>
                                                                <option value="MTS">MTS</option>
                                                            </select>
                                                            <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 dark:text-gray-400 sm:text-sm"></i>
                                                        </div>
                                                    </td>
                                                    <td class="px-3 py-4 text-xs whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                        <input placeholder="VARIOS" type="text" name="items[0][dimensions]" class="block w-full min-w-24 rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                    </td>
                                                    <td class="px-3 py-4 text-xs whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                        <input placeholder="{{__('Weight')}}" type="text" name="items[0][weight]" class="block w-full min-w-24 rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                    </td>
                                                    <td class="px-3 py-4 text-xs whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                        <div class="grid grid-cols-1 min-w-24">
                                                            <select name="items[0][weight_measure]" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                                <option value="KG">KG</option>
                                                                <option value="LBS">LBS</option>
                                                            </select>
                                                            <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 dark:text-gray-400 sm:text-sm"></i>
                                                        </div>
                                                    </td>
                                                    <td class="px-3 py-4 text-xs whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                        <div class="grid grid-cols-1 min-w-32">
                                                            <select name="items[0][container]" autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                                <option value="Pallet">Pallet</option>
                                                                <option value="Crate">Crate</option>
                                                                <option value="Drum">Drum</option>
                                                                <option value="Tote">Tote</option>
                                                                <option value="Bulk">Bulk</option>
                                                                <option value="Carton">Carton</option>
                                                            </select>
                                                            <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 dark:text-gray-400 sm:text-sm"></i>
                                                        </div>
                                                    </td>
                                                    <td class="px-3 py-4 text-xs whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                        <input type="number" placeholder="{{__('Quantity')}}" min="0" name="items[0][quantity]" class="block w-full min-w-24 rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                    </td>
                                                    <td class="px-3 py-4 text-xs whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                        <input type="number" placeholder="$999.99"  step="0.01" name="items[0][value]" class="block w-full min-w-24 rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                    </td>
                                                    <td class="px-3 py-4 text-xs whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                        <div class="grid grid-cols-1 min-w-40">
                                                            <select name="items[0][incoterm_id]" autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                                @foreach ($incoterms as $incoterm)
                                                                    <option value="{{ $incoterm->id }}">{{ $incoterm->code }} ({{ $incoterm->name }})</option>
                                                                @endforeach
                                                            </select>
                                                            <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 dark:text-gray-400 sm:text-sm"></i>
                                                        </div>
                                                    </td>
                                                    <td class="py-4 pr-4 pl-3 text-right text-xs font-medium whitespace-nowrap sm:pr-3">
                                                        <div class="flex items-center justify-center gap-x-1">
                                                            <button
                                                                type="button"
                                                                @click="openCancel = true"
                                                                data-tippy-content="{{__('Delete')}}"
                                                                class="delete-btn size-7 shrink-0 rounded-md bg-red-100 dark:bg-red-500/15 flex items-center justify-center font-semibold text-red-500 dark:text-red-400 hover:text-red-800 hover:bg-red-200 hover:cursor-pointer"
                                                            >
                                                                <i class="fa-regular fa-trash-can"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="flex justify-end mt-4">
                                <button
                                    type="button"
                                    id="addRowButton"
                                    data-tippy-content="{{__('Add product')}}"
                                    class="inline-flex items-center gap-x-1.5 rounded-md bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-50 shadow-xs inset-ring inset-ring-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:cursor-pointer"
                                >
                                    <i class="fa-regular fa-plus"></i>
                                    {{__('Add product')}}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-4 sm:px-6">
                    <div class="flex items-center justify-end gap-x-6">
                        <a href="{{ route('orders.shipments.show', ['order' => $order, 'shipment' => $shipment]) }}" class="text-sm/6 font-semibold text-gray-900 dark:text-gray-50">Cancelar</a>
                        <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Guardar</button>
                    </div>
                </div>
            </div>
        </form>
    </section>
</x-layout-app>
