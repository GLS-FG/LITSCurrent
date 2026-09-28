@section('title', 'Nueva aduana')
@section('custom_script')
    <script type="module">
        $('#payment_date').datepicker({ dateFormat: 'dd/mm/yy' });
        $('#petition_date').datepicker({ dateFormat: 'dd/mm/yy' });
        $('#entry_date').datepicker({ dateFormat: 'dd/mm/yy' });
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
        $('form').on('submit', function(e) {
            const $submitButton = $(this).find('button[type="submit"]');
            if ($submitButton.prop('disabled')) {
                e.preventDefault();
                return;
            }
            $submitButton.prop('disabled', true);
        });
    </script>
@endsection
<x-layout-app>
    <section>
        <x-navigation.breadcrumbs :links="['Ordenes' => route('orders.index'), $order->code => route('orders.show', ['order' => $order->id]), 'Nueva aduana' => '#']" />
        <x-headings.without-action
            :title="'Nueva orden de aduana'"
        />
        @if ($errors->any())
            <x-alerts.error :message="'Para crear el servicio soluciona los siguientes errores:'" :errors="$errors" class="my-4" />
        @endif
        <form action="{{ route('orders.imports.store', ['order' => $order]) }}" method="POST" class="mt-8">
            @csrf
            <div class="divide-y divide-gray-200 dark:divide-gray-700 rounded bg-white dark:bg-lits-blue-550 shadow-lits-card">
                <div class="px-4 py-5 sm:p-6">
                    <div class="space-y-12">
                        <div class="border-b border-gray-900/10 dark:border-white/10 pb-12">
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
                        <div class="border-b border-gray-900/10 dark:border-white/10 pb-12">
                            <h2 class="text-base/7 font-semibold text-gray-900 dark:text-gray-50">Datos generales</h2>
                            <p class="mt-1 text-sm/6 text-gray-600 dark:text-gray-400">Llena la información necesaria para la órden de almacén.</p>
                            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                <div class="col-span-full">
                                    <label for="reference" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Referencia</label>
                                    <div class="mt-2">
                                        <textarea id="reference" name="reference" autocomplete="off" class="@error('reference') outline-red-400 @else outline-gray-300 dark:outline-gray-600 @enderror block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">{{old('reference')}}</textarea>
                                        @error('reference')
                                        <p class="mt-1 text-sm/6 text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="border-b border-gray-900/10 dark:border-white/10 pb-12">
                            <h2 class="text-base/7 font-semibold text-gray-900 dark:text-gray-50">Service Type</h2>
                            <p class="mt-1 text-sm/6 text-gray-600 dark:text-gray-400">Ingresa la información del tipo de servicio.</p>
                            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                <div class="sm:col-span-3">
                                    <label for="service_class_id" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Service Class</label>
                                    <div class="mt-2 grid grid-cols-1">
                                        <select id="service_class_id" name="service_class_id" autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                            @foreach($serviceClasses as $service)
                                                <option value='{{$service->id}}' @selected(old('service_class_id') == $service->name)>{{$service->name}}</option>
                                            @endforeach
                                        </select>
                                        <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 dark:text-gray-400 sm:text-sm"></i>
                                    </div>
                                </div>

                                <div class="sm:col-span-3">
                                    <label for="service_mode_id" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Service Mode</label>
                                    <div class="mt-2 grid grid-cols-1">
                                        <select id="service_mode_id" name="service_mode_id" autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                        </select>
                                        <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 dark:text-gray-400 sm:text-sm"></i>
                                    </div>
                                </div>

                                <div class="sm:col-span-3">
                                    <label for="class_type_id" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Class Type</label>
                                    <div class="mt-2 grid grid-cols-1">
                                        <select id="class_type_id" name="class_type_id" autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                        </select>
                                        <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 dark:text-gray-400 sm:text-sm"></i>
                                    </div>
                                </div>

                                <div class="sm:col-span-3">
                                    <label for="service_level_id" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Service Level</label>
                                    <div class="mt-2 grid grid-cols-1">
                                        <select id="service_level_id" name="service_level_id" autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                        </select>
                                        <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 dark:text-gray-400 sm:text-sm"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="pb-12">
                            <h2 class="text-base/7 font-semibold text-gray-900 dark:text-gray-50">Información adicional</h2>
                            <p class="mt-1 text-sm/6 text-gray-600 dark:text-gray-400">Ingresa comentarios adicionales para la orden de aduana.</p>
                            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                <div class="col-span-full">
                                    <label for="comments" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Comentarios</label>
                                    <div class="mt-2">
                                        <textarea rows="4" id="comments" name="comments" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">{{old('comments')}}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-4 sm:px-6">
                    <div class="mt-6 flex items-center justify-end gap-x-6">
                        <a href="{{ route('orders.show', ['order' => $order->id]) }}" class="text-sm/6 font-semibold text-gray-900 dark:text-gray-50">Cancelar</a>
                        <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Crear orden</button>
                    </div>
                </div>
            </div>
        </form>
    </section>
</x-layout-app>
