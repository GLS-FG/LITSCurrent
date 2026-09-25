@section('title', 'Nueva declaración aduanal')
@section('custom_script')
    <script type="module">
        $('#entry_date').datepicker({ dateFormat: 'dd/mm/yy' });
        $('#draft_date').datepicker({ dateFormat: 'dd/mm/yy' });
        $('#paid_date').datepicker({ dateFormat: 'dd/mm/yy' });

        const customs = {{ Js::from($customs) }};
        $('#custom').autocomplete({
            minLength: 1,
            source: function (request, response) {
                response($.map(customs, function (obj, key) {
                    const name = obj.denomination.toUpperCase();
                    const code = obj.code?.toUpperCase();
                    if (name.indexOf(request.term.toUpperCase()) !== -1 || code?.indexOf(request.term.toUpperCase()) !== -1) {
                        return {
                            label: code + " / " + name,
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
                $('#custom').val(ui.item.label);
                $('#custom_id').val(ui.item.value);
            },
            change: function(event, ui) {
                if($('#custom').val() === ""){
                    $('#custom_id').val("");
                }
            }
        });

        const agents = {{ Js::from($agents) }};
        $('#custom_agent').autocomplete({
            minLength: 1,
            source: function (request, response) {
                response($.map(agents, function (obj, key) {
                    const patent = obj.patent.toUpperCase();
                    const name = obj.name?.toUpperCase();
                    const last_name = obj.last_name?.toUpperCase();
                    const company_name = obj.company_name?.toUpperCase() || '';
                    if (patent.indexOf(request.term.toUpperCase()) !== -1 || company_name?.indexOf(request.term.toUpperCase()) !== -1 || name?.indexOf(request.term.toUpperCase()) !== -1 || last_name?.indexOf(request.term.toUpperCase()) !== -1) {
                        return {
                            label: company_name + " / " + patent,
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
                $('#custom_agent').val(ui.item.label);
                $('#custom_agent_id').val(ui.item.value);
            },
            change: function(event, ui) {
                if($('#custom_agent').val() === ""){
                    $('#custom_agent_id').val("");
                }
            }
        });

        @if($import->class_type_id == 16)
        $('#port_departure').autocomplete({
            minLength: 1,
            source: function (request, response) {
                response($.map(customs, function (obj, key) {
                    const name = obj.denomination.toUpperCase();
                    const code = obj.code?.toUpperCase();
                    if (name.indexOf(request.term.toUpperCase()) !== -1 || code?.indexOf(request.term.toUpperCase()) !== -1) {
                        return {
                            label: code + " / " + name,
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
                $('#port_departure').val(ui.item.label);
                $('#port_departure_id').val(ui.item.value);
            },
            change: function(event, ui) {
                if($('#port_departure').val() === ""){
                    $('#port_departure_id').val("");
                }
            }
        });
        @endif
        $('#customs_value').blur(function () {
            let exchangeRate = $('#exchange_rate').val();
            if(exchangeRate !== ""){
                let revertedExchangeRate = 1 / parseFloat(exchangeRate);
                let amount = parseFloat($(this).val()) * parseFloat(revertedExchangeRate);
                amount = parseFloat(amount.toFixed(2));
                $('#customs_value_foreign').val(amount);
            }
        });

        $('#customs_value_foreign').blur(function () {
            let exchangeRate = $('#exchange_rate').val();
            if(exchangeRate !== ""){
                let amount = parseFloat($(this).val()) * parseFloat(exchangeRate);
                amount = parseFloat(amount.toFixed(2));
                $('#customs_value').val(amount);
            }
        })
    </script>
@endsection
<x-layout-app>
    <section>
        <x-navigation.breadcrumbs :links="['Ordenes' => route('orders.index'), $order->code => route('orders.show', ['order' => $order->id]), $import->tracking_code => route('orders.imports.show', ['order' => $order->id, 'import' => $import->id]), 'Nueva declaración aduanal' => '#']" />
        <x-headings.without-action
            :title="'Nueva declaración aduanal'"
        />
        @if ($errors->any())
            <x-alerts.error :message="'Para crear la declaración soluciona los siguientes errores:'" :errors="$errors" class="my-4" />
        @endif
        <form action="{{ route('orders.imports.declarations.store', ['order' => $order, 'import' => $import->id]) }}" method="POST" class="mt-8">
            @csrf
            <div class="divide-y divide-gray-200 dark:divide-gray-700 rounded bg-white dark:bg-lits-blue-550 shadow-lits-card">
                <div class="px-4 py-5 sm:p-6">
                    <div class="space-y-12">
                        <div class="pb-12">
                            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                <div class="sm:col-span-2">
                                    <label for="custom" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Aduana</label>
                                    <div class="mt-2">
                                        <input id="custom" value="{{old('custom')}}" placeholder="Busca una aduana" name="custom" type="search" autocomplete="off" autofill="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                        <input id="custom_id" value="{{old('custom_id')}}" name="custom_id" type="hidden">
                                    </div>
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="custom_agent_id" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Agente aduanal</label>
                                    <div class="mt-2">
                                        <input id="custom_agent" value="{{old('custom_agent')}}" placeholder="Busca un agente" name="custom_agent" type="search" autocomplete="off" autofill="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                        <input id="custom_agent_id" value="{{old('custom_agent_id')}}" name="custom_agent_id" type="hidden">
                                    </div>
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="petition" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Pedimento</label>
                                    <div class="mt-2">
                                        <input id="petition" value="{{old('petition')}}" name="petition" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="entry_date" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Fecha de entrada</label>
                                    <div class="mt-2">
                                        <input id="entry_date" value="{{old('entry_date')}}" name="entry_date" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="draft_date" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Fecha de pedimento (Proforma)</label>
                                    <div class="mt-2">
                                        <input id="draft_date" value="{{old('draft_date')}}" name="draft_date" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="paid_date" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Fecha de pago</label>
                                    <div class="mt-2">
                                        <input id="paid_date" value="{{old('paid_date')}}" name="paid_date" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="commercial_value" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Valor comercial(MXN)</label>
                                    <div class="mt-2">
                                        <input id="commercial_value" value="{{old('commercial_value')}}" name="commercial_value" type="number" step=".01" min="0" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="exchange_rate" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Tipo de cambio</label>
                                    <div class="mt-2">
                                        <input id="exchange_rate" value="{{old('exchange_rate')}}" name="exchange_rate" type="number" step=".0001" min="0" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="customs_value" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Valor en aduana(MXN)</label>
                                    <div class="mt-2">
                                        <input id="customs_value" value="{{old('customs_value')}}" name="customs_value" type="number" step=".01" min="0" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="customs_value_foreign" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Valor en aduana(USD)</label>
                                    <div class="mt-2">
                                        <input id="customs_value_foreign" value="{{old('customs_value_foreign')}}" name="customs_value_foreign" type="number" step=".01" min="0" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="gross_weight" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Peso bruto</label>
                                    <div class="mt-2">
                                        <input id="gross_weight" value="{{old('gross_weight')}}" name="gross_weight" type="number" step=".01" min="0" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="packages" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Total de bultos</label>
                                    <div class="mt-2">
                                        <input id="packages" value="{{old('packages')}}" name="packages" type="number" step=".01" min="0" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>

                                <div class="sm:col-span-3">
                                    <label for="incoterm_id" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">INCOTERM</label>
                                    <div class="mt-2 grid grid-cols-1">
                                        <select id="incoterm_id" name="incoterm_id" autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                            @foreach ($incoterms as $incoterm)
                                                <option value="{{ $incoterm->id }}" @selected(old('incoterm_id') == $incoterm->id)>{{ $incoterm->code }} ({{ $incoterm->name }})</option>
                                            @endforeach
                                        </select>
                                        <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 dark:text-gray-400 sm:text-sm"></i>
                                    </div>
                                </div>

                                @if($import->class_type_id == 16)
                                    <div class="sm:col-span-3">
                                        <label for="port_departure" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Aduana de salida</label>
                                        <div class="mt-2">
                                            <input id="port_departure" value="{{old('port_departure')}}" placeholder="Busca una aduana de salida" name="port_departure" type="search" autocomplete="off" autofill="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                            <input id="port_departure_id" value="{{old('port_departure_id')}}" name="port_departure_id" type="hidden">
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-4 sm:px-6">
                    <div class="mt-6 flex items-center justify-end gap-x-6">
                        <a href="{{ route('orders.imports.show', ['order' => $order->id, 'import' => $import->id]) }}" class="text-sm/6 font-semibold text-gray-900 dark:text-gray-50">Cancelar</a>
                        <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Crear declaración</button>
                    </div>
                </div>
            </div>
        </form>
    </section>
</x-layout-app>
