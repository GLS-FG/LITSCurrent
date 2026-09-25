@section('title', 'Editar cliente')
<x-layout-admin>
    <section>
        <x-navigation.breadcrumbs :links="['Clientes' => route('clients.index'), $client->company_name => route('clients.show', [ 'client' => $client]), 'Editar cliente' => '#']" />
        <x-headings.without-action
            :title="'Editar cliente'"
        />
        @if ($errors->any())
            <x-alerts.error :message="'Para editar el cliente soluciona los siguientes errores:'" :errors="$errors" class="my-4" />
        @endif
        <form action="{{ route('clients.update', [ 'client' => $client]) }}" method="POST" enctype="multipart/form-data" class="mt-8">
            @csrf
            @method('PUT')
            <div class="h-fit lg:col-span-2 divide-y divide-gray-200 dark:divide-gray-700 rounded bg-white dark:bg-lits-blue-550 shadow-lits-card">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-50">Guardar</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="space-y-12">
                        <div class="pb-12">
                            <h2 class="text-base/7 font-semibold text-gray-900 dark:text-gray-50">Datos del cliente</h2>
                            <p class="mt-1 text-sm/6 text-gray-600 dark:text-gray-400">Llena los datos relacionados con el cliente.</p>
                            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 lg:grid-cols-12">
                                <div class="col-span-full flex items-center gap-x-8"
                                     x-data="{ imagePreview: '{{ route('clients.logos', [ 'filename' => str_replace(".","_",str_replace("logos/", "", $client->image))]) }}',
                                        handleFileChange(event) {
                                            const file = event.target.files[0];
                                            if (file) {
                                                this.imagePreview = URL.createObjectURL(file);
                                            } else {
                                                this.imagePreview = '{{ route('clients.logos', [ 'filename' => str_replace(".","_",str_replace("logos/", "", $client->image))]) }}';
                                            }
                                        }
                                     }"
                                >
                                    <img :src="imagePreview" alt="{{$client->company_name}}" class="size-24 flex-none rounded-lg bg-gray-100 dark:bg-gray-800 object-contain outline -outline-offset-1 outline-black/5" />
                                    <div>
                                        <label for="image" class="rounded-md bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-50 shadow-xs inset-ring-1 inset-ring-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Cambiar foto</label>
                                        <input accept="image/*" type="file" id="image" name="image" title="" class="hidden" @change="handleFileChange" />
                                        <p class="mt-2 text-xs/5 text-gray-500 dark:text-gray-400">JPG, GIF o PNG. 1MB max.</p>
                                    </div>
                                </div>

                                <div class="sm:col-span-6">
                                    <label for="company_name" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Razón social</label>
                                    <div class="mt-2">
                                        <input id="company_name" value="{{old('company_name', $client->company_name)}}" name="company_name" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="sm:col-span-6">
                                    <label for="trade_name" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Nombre comercial</label>
                                    <div class="mt-2">
                                        <input id="trade_name" value="{{old('trade_name', $client->trade_name)}}" name="trade_name" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="sm:col-span-3">
                                    <label for="federal_tax_id" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">RFC</label>
                                    <div class="mt-2">
                                        <input id="federal_tax_id" value="{{old('federal_tax_id', $client->federal_tax_id)}}" name="federal_tax_id" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="sm:col-span-3">
                                    <label for="national_id" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">CURP</label>
                                    <div class="mt-2">
                                        <input id="national_id" value="{{old('national_id', $client->national_id)}}" name="national_id" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="sm:col-span-3">
                                    <label for="phone1" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Teléfono</label>
                                    <div class="mt-2">
                                        <input id="phone1" value="{{old('phone1', $client->phone1)}}" name="phone1" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="sm:col-span-3">
                                    <label for="email" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Email</label>
                                    <div class="mt-2">
                                        <input id="email" value="{{old('email', $client->email)}}" name="email" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-4 sm:px-6">
                    <div class="mt-6 flex items-center justify-end gap-x-6">
                        <a href="{{ route('clients.show', [ 'client' => $client]) }}" class="text-sm/6 font-semibold text-gray-900 dark:text-gray-50">Cancelar</a>
                        <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Guardar</button>
                    </div>
                </div>
            </div>
        </form>
    </section>
</x-layout-admin>
