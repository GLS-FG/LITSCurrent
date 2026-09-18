@section('title', 'Nuevo service mode')
<x-layout-admin>
    <section>
        <x-navigation.breadcrumbs :links="['Service Types' => route('service-types.index'), $serviceType->code => route('service-types.service-classes.index', ['service_type' => $serviceType]), $serviceClass->code => route('service-types.service-classes.service-modes.index', ['service_type' => $serviceType, 'service_class' => $serviceClass]), 'Nuevo service mode' => '#']" />
        <x-headings.without-action
            :title="'Nuevo service mode'"
        />
        @if ($errors->any())
            <x-alerts.error :message="'Para crear el service mode soluciona los siguientes errores:'" :errors="$errors" class="my-4" />
        @endif
        <form action="{{ route('service-types.service-classes.service-modes.store', ['service_type' => $serviceType, 'service_class' => $serviceClass]) }}" method="POST" class="mt-8 grid grid-cols-1 gap-x-8 gap-y-8 lg:grid-cols-1" autocomplete="off">
            @csrf
            <div class="h-fit lg:col-span-2 divide-y divide-gray-200 rounded bg-white shadow-lits-card">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-semibold text-gray-900">Nuevo service mode</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="space-y-12">
                        <div class="pb-12">
                            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                <div class="sm:col-span-2">
                                    <label for="code" class="block text-sm/6 font-medium text-gray-900">Código</label>
                                    <div class="mt-2">
                                        <input id="code" value="{{old('code')}}" name="code" type="text" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="sm:col-span-4">
                                    <label for="name" class="block text-sm/6 font-medium text-gray-900">Nombre</label>
                                    <div class="mt-2">
                                        <input id="name" value="{{old('name')}}" name="name" type="text" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="icon" class="block text-sm/6 font-medium text-gray-900">Icono</label>
                                    <div class="mt-2">
                                        <input id="icon" value="{{old('icon')}}" name="icon" type="text" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-4 sm:px-6">
                    <div class="mt-6 flex items-center justify-end gap-x-6">
                        <a href="{{ route('service-types.service-classes.service-modes.index', ['service_type' => $serviceType, 'service_class' => $serviceClass]) }}" class="text-sm/6 font-semibold text-gray-900">Cancelar</a>
                        <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Guardar</button>
                    </div>
                </div>
            </div>
        </form>
    </section>
</x-layout-admin>
