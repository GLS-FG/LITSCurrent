@section('title', 'Editar transportista')
<x-layout-admin>
    <section>
        <x-navigation.breadcrumbs :links="['Transportistas' => route('transportation-agencies.index'), $agency->name => route('transportation-agencies.show', ['transportation_agency' => $agency]), 'Editar transportista' => '#']" />
        <x-headings.without-action
            :title="'Editar transportista'"
        />
        @if ($errors->any())
            <x-alerts.error :message="'Para crear el transportista soluciona los siguientes errores:'" :errors="$errors" class="my-4" />
        @endif
        <form action="{{ route('transportation-agencies.update', ['transportation_agency' => $agency]) }}" method="POST" class="mt-8 grid grid-cols-1 gap-x-8 gap-y-8 lg:grid-cols-1" autocomplete="off">
            @csrf
            @method('PUT')
            <div class="h-fit lg:col-span-2 divide-y divide-gray-200 rounded bg-white shadow-lits-card">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-semibold text-gray-900">Editar transportista</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="space-y-12">
                        <div class="border-b border-gray-900/10 pb-12">
                            <h2 class="text-base/7 font-semibold text-gray-900">Transportista</h2>
                            <p class="mt-1 text-sm/6 text-gray-600">Ingresa la información necesaria de el transportista.</p>
                            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                <div class="sm:col-span-full">
                                    <label for="name" class="block text-sm/6 font-medium text-gray-900">Razon social</label>
                                    <div class="mt-2">
                                        <input id="name" value="{{old('name', $agency->name)}}" name="name" type="text" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="sm:col-span-3">
                                    <label for="company_name" class="block text-sm/6 font-medium text-gray-900">Nombre comercial</label>
                                    <div class="mt-2">
                                        <input id="company_name" value="{{old('company_name', $agency->company_name)}}" name="company_name" type="text" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="rfc" class="block text-sm/6 font-medium text-gray-900">RFC</label>
                                    <div class="mt-2">
                                        <input id="rfc" value="{{old('rfc', $agency->rfc)}}" name="rfc" type="text" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="caat_code" class="block text-sm/6 font-medium text-gray-900">CAAT</label>
                                    <div class="mt-2">
                                        <input id="caat_code" value="{{old('caat_code', $agency->caat_code)}}" name="caat_code" type="text" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="scac_code" class="block text-sm/6 font-medium text-gray-900">SCAC</label>
                                    <div class="mt-2">
                                        <input id="scac_code" value="{{old('scac_code', $agency->scac_code)}}" name="scac_code" type="text" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pb-12">
                            <h2 class="text-base/7 font-semibold text-gray-900">Contacto</h2>
                            <p class="mt-1 text-sm/6 text-gray-600">Llena la información de la persona de contacto y teléfonos.</p>
                            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                <div class="sm:col-span-full">
                                    <label for="contact_name" class="block text-sm/6 font-medium text-gray-900">Persona de contacto</label>
                                    <div class="mt-2">
                                        <input id="contact_name" value="{{old('contact_name', $agency->contact_name)}}" name="contact_name" type="text" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="phone1" class="block text-sm/6 font-medium text-gray-900">Teléfono</label>
                                    <div class="mt-2">
                                        <input id="phone1" value="{{old('phone1', $agency->phone1)}}" name="phone1" type="text" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="phone2" class="block text-sm/6 font-medium text-gray-900">Otro teléfono</label>
                                    <div class="mt-2">
                                        <input id="phone2" value="{{old('phone2', $agency->phone2)}}" name="phone2" type="text" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-4 sm:px-6">
                    <div class="mt-6 flex items-center justify-end gap-x-6">
                        <a href="{{route('transportation-agencies.show', ['transportation_agency' => $agency])}}" class="text-sm/6 font-semibold text-gray-900">Cancelar</a>
                        <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Guardar</button>
                    </div>
                </div>
            </div>
        </form>
    </section>
</x-layout-admin>
