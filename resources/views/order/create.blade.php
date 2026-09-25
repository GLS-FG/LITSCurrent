@section('title', 'Nuevo Servicio')
@section('custom_script')
    <script type="module">
        const $contactSelect = $('#contact_id');
        $('#client_id').on('change', function() {
            const clientId = $(this).val();
            if (clientId) {
                fetchClientContacts(clientId);
            } else {
                $contactSelect.empty();
            }
        });
        var hasContactOld = false;
        function fetchClientContacts (client_id) {
            $.ajax({
                url: "{{ route('autocomplete.contacts') }}",
                type: 'GET',
                dataType: "json",
                data: { client_id },
                success: function(data) {
                    const contacts = data;
                    $contactSelect.empty();
                    const emptyOption = new Option('Selecciona una opción', '');
                    $contactSelect.append(emptyOption);
                    for (const contact of contacts) {
                        const option = new Option(contact.label, contact.value);
                        $contactSelect.append(option);
                    }
                    $("#contact_id option[value='']").prop("disabled", true);
                    if(hasContactOld === true){
                        hasContactOld = false;
                        $contactSelect.val('{{old('contact_id')}}');
                    } else {
                        if(contacts.length < 2){
                            $contactSelect.val(contacts[0].value);
                        }
                    }
                }
            });
        }
        @if (old('client_id'))
        fetchClientContacts({{old('client_id')}});
        @endif
        @if (old('contact_id'))
        hasContactOld = true;
        @endif
    </script>
@endsection
<x-layout-app>
    <section>
        <x-navigation.breadcrumbs :links="['Ordenes' => route('orders.index'), 'Nuevo servicio' => '#']" />
        <x-headings.without-action :title="'Nueva orden de servicio'" />
        @if ($errors->any())
            <x-alerts.error :message="'Para crear la órden soluciona los siguientes errores:'" :errors="$errors" class="my-4" />
        @endif
        <form action="{{ route('orders.store') }}" method="POST" class="mt-2 flow-root">
            @csrf
            <div class="divide-y divide-gray-200 dark:divide-gray-700 overflow-hidden rounded bg-white dark:bg-lits-blue-550 shadow-lits-card">
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                        <div class="sm:col-span-4">
                            <label for="client_id" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Cliente</label>
                            <div class="mt-2">
                                <select
                                    id="client_id"
                                    name="client_id"
                                    autocomplete="off"
                                    class="@error('client_id') outline-red-400 @else outline-gray-300 dark:outline-gray-600 @enderror block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    <option value="">Selecciona un cliente</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" @selected(old('client_id') == $client->id)>{{ $client->company_name }} / {{ $client->trade_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('client_id')
                                <p class="mt-1 text-sm/6 text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label for="contact_id" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Usuario</label>
                            <div class="mt-2 grid grid-cols-1">
                                <select id="contact_id" name="contact_id" autocomplete="off" class="@error('contact_id') outline-red-400 @else outline-gray-300 dark:outline-gray-600 @enderror col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1  focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
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
                                    value="{{old('carbon_copy')}}"
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
                                >{{old('reference')}}</textarea>
                            </div>
                            @error('reference')
                            <p class="mt-1 text-sm/6 text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="px-4 py-4 sm:px-6">
                    <div class="mt-6 flex items-center justify-end gap-x-6">
                        <a href="{{route('orders.index')}}" class="text-sm/6 font-semibold text-gray-900 dark:text-gray-50">Cancelar</a>
                        <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Crear orden</button>
                    </div>
                </div>
            </div>
        </form>
    </section>
</x-layout-app>
