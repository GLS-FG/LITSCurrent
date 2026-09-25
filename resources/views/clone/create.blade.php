@section('title', 'Duplicar Embarque')
@section('custom_script')
    <script type="module">
        const clients = {{ Js::from($clients) }};
        const $contactSelect = $('#contact_id');
        $('#client').autocomplete({
            minLength: 2,
            source: function (request, response) {
                response($.map(clients, function (obj, key) {
                    const company_name = obj.company_name.toUpperCase();
                    const trade_name = obj.trade_name?.toUpperCase();
                    if (company_name.indexOf(request.term.toUpperCase()) !== -1 || trade_name?.indexOf(request.term.toUpperCase()) !== -1) {
                        return {
                            label: obj.company_name + " / " + trade_name,
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
                $('#client').val(ui.item.label);
                $('#client_id').val(ui.item.value);
                fetchClientContacts(ui.item.value)
            },
            change: function(event, ui) {
                if($('#client').val() === ""){
                    $('#client_id').val("");
                    $contactSelect.empty();
                }
            }
        });
        function fetchClientContacts (client_id) {
            $.ajax({
                url: "{{ route('autocomplete.contacts') }}",
                type: 'GET',
                dataType: "json",
                data: { client_id },
                success: function(data) {
                    const contacts = data;
                    $contactSelect.empty();
                    for (const contact of contacts) {
                        const option = new Option(contact.label, contact.value);
                        $contactSelect.append(option);
                    }
                    @if(old('contact_id', $from->order->contact_id))
                    $contactSelect.val("{{old('contact_id', $from->order->contact_id)}}");
                    @endif
                }
            });
        }
        @if(old('client_id', $from->order->client_id))
        fetchClientContacts({{old('client_id', $from->order->client_id)}});
        @endif
    </script>
@endsection
<x-layout-app>
    <section>
        <x-navigation.breadcrumbs :links="['Ordenes' => route('orders.index'), $from->order->code => route('orders.show', ['order' => $from->order]), $from->tracking_code => route('orders.shipments.show', ['order' => $from->order, 'shipment' => $from]), 'Duplicar Embarque' => '#']" />
        <x-headings.without-action :title="'Duplicar Embarque'" />
        @if ($errors->any())
            <x-alerts.error :message="'Para duplicar el embarque soluciona los siguientes errores:'" :errors="$errors" class="my-4" />
        @endif
        <form action="{{ route('clone.order.store', ['shipment' => $from]) }}" method="POST" class="mt-2 flow-root">
            @csrf
            <div class="divide-y divide-gray-200 dark:divide-gray-700 overflow-hidden rounded bg-white dark:bg-lits-blue-550 shadow-lits-card">
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                        <div class="sm:col-span-4">
                            <label for="client" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Cliente</label>
                            <div class="mt-2">
                                <input
                                    id="client"
                                    value="{{old('client', $from->order->client->company_name . ' / ' . $from->order->client->trade_name)}}"
                                    name="client"
                                    type="text"
                                    autocomplete="off"
                                    class="@error('client_id') outline-red-400 @else outline-gray-300 dark:outline-gray-600 @enderror block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1  placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                <input id="client_id" value="{{old('client_id', $from->order->client_id)}}" name="client_id" type="hidden" />
                            </div>
                            @error('client_id')
                                <p class="mt-1 text-sm/6 text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label for="contact_id" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Usuario</label>
                            <div class="mt-2 grid grid-cols-1">
                                <select id="contact_id" name="contact_id" value="{{old('contact_id', $from->order->contact_id)}}" autocomplete="off" class="@error('contact_id') outline-red-400 @else outline-gray-300 dark:outline-gray-600 @enderror col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1  focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                </select>
                                <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 dark:text-gray-400 sm:text-sm"></i>
                            </div>
                            @error('contact_id')
                            <p class="mt-1 text-sm/6 text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-span-full">
                            <label for="carbon_copy" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">
                                Receptores Adicionales <span class="font-normal text-gray-500 dark:text-gray-400">(Ingresa emails separados por punto y coma ";")</span>
                            </label>
                            <div class="mt-2">
                                <input
                                    id="carbon_copy"
                                    name="carbon_copy"
                                    autocomplete="off"
                                    type="text"
                                    value="{{old('carbon_copy', $from->order->carbon_copy)}}"
                                    class="@error('reference') outline-red-400 @else outline-gray-300 dark:outline-gray-600 @enderror block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                                />
                            </div>
                            @error('carbon_copy')
                            <p class="mt-1 text-sm/6 text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-span-full">
                            <label for="reference" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Referencia</label>
                            <div class="mt-2">
                                <textarea
                                    id="reference"
                                    name="reference"
                                    autocomplete="off"
                                    class="@error('reference') outline-red-400 @else outline-gray-300 dark:outline-gray-600 @enderror block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                                >{{old('reference', $from->order->reference)}}</textarea>
                            </div>
                            @error('reference')
                            <p class="mt-1 text-sm/6 text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="px-4 py-4 sm:px-6">
                    <div class="mt-6 flex items-center justify-end gap-x-6">
                        <a href="{{route('orders.shipments.show', ['order' => $from->order->id, 'shipment' => $from->id])}}" class="text-sm/6 font-semibold text-gray-900 dark:text-gray-50">Cancelar</a>
                        <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Duplicar servicio</button>
                    </div>
                </div>
            </div>
        </form>
    </section>
</x-layout-app>
