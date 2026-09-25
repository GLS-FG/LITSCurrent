@section('title', 'Nuevo almacen')
<x-layout-admin>
    <section>
        <x-navigation.breadcrumbs :links="['Almacenes' => route('warehouses.index'), 'Nuevo almacen' => '#']" />
        <x-headings.without-action
            :title="'Nuevo almacen'"
        />
        @if ($errors->any())
            <x-alerts.error :message="'Para crear el almacen de transporte soluciona los siguientes errores:'" :errors="$errors" class="my-4" />
        @endif
        <form action="{{ route('warehouses.store') }}" method="POST" class="mt-8 grid grid-cols-1 gap-x-8 gap-y-8 lg:grid-cols-1" autocomplete="off">
            @csrf
            <div class="h-fit lg:col-span-2 divide-y divide-gray-200 dark:divide-gray-700 rounded bg-white dark:bg-lits-blue-550 shadow-lits-card">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-50">Nuevo almacen</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="space-y-12">
                        <div class="border-b border-gray-900/10 dark:border-white/10 pb-12">
                            <h2 class="text-base/7 font-semibold text-gray-900 dark:text-gray-50">Almacen</h2>
                            <p class="mt-1 text-sm/6 text-gray-600 dark:text-gray-400">Ingresa la información necesaria el almacen.</p>
                            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                <div class="sm:col-span-full">
                                    <label for="name" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Nombre</label>
                                    <div class="mt-2">
                                        <input id="name" value="{{old('name')}}" name="name" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pb-12">
                            <h2 class="text-base/7 font-semibold text-gray-900 dark:text-gray-50">Contacto</h2>
                            <p class="mt-1 text-sm/6 text-gray-600 dark:text-gray-400">Llena la información de la persona de contacto y teléfonos.</p>
                            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                <div class="sm:col-span-full">
                                    <label for="contact_name" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Persona de contacto</label>
                                    <div class="mt-2">
                                        <input id="contact_name" value="{{old('contact_name')}}" name="contact_name" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="phone1" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Teléfono</label>
                                    <div class="mt-2">
                                        <input id="phone1" value="{{old('phone1')}}" name="phone1" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="phone2" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Otro teléfono</label>
                                    <div class="mt-2">
                                        <input id="phone2" value="{{old('phone2')}}" name="phone2" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-4 sm:px-6">
                    <div class="mt-6 flex items-center justify-end gap-x-6">
                        <a href="{{ route('warehouses.index') }}" class="text-sm/6 font-semibold text-gray-900 dark:text-gray-50">Cancelar</a>
                        <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Crear almacen</button>
                    </div>
                </div>
            </div>
        </form>
    </section>
</x-layout-admin>
