@section('title', 'Editar Tipo de documento')
<x-layout-admin>
    <section>
        <x-navigation.breadcrumbs :links="['Tipos de documentos' => route('document-types.index'), 'Editar Tipo de documento' => '#']" />
        <x-headings.without-action
            :title="'Editar Tipo de documento'"
        />
        @if ($errors->any())
            <x-alerts.error :message="'Para editar el Tipo de documento soluciona los siguientes errores:'" :errors="$errors" class="my-4" />
        @endif
        <form action="{{ route('document-types.update', ['document_type' => $documentType]) }}" method="POST" class="mt-8 grid grid-cols-1 gap-x-8 gap-y-8 lg:grid-cols-1" autocomplete="off">
            @csrf
            @method('PUT')
            <div class="h-fit lg:col-span-2 divide-y divide-gray-200 rounded bg-white shadow-lits-card">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-semibold text-gray-900">Editar Tipo de documento</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="space-y-6">
                        <div>
                            <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                <div class="sm:col-span-full">
                                    <label for="name" class="block text-sm/6 font-medium text-gray-900">Nombre</label>
                                    <div class="mt-2">
                                        <input id="name" value="{{old('name', $documentType->name)}}" name="name" type="text" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="border-b border-gray-900/10 pb-6">
                            <div class="mt-6 space-y-10">
                                <fieldset>
                                    <legend class="text-sm/6 font-semibold text-gray-900">¿Dónde aplica?</legend>
                                    <div class="mt-6 space-y-6">
                                        <div class="flex gap-3">
                                            <div class="flex h-6 shrink-0 items-center">
                                                <div class="group grid size-4 grid-cols-1">
                                                    <input type="hidden" name="shipments" value="0">
                                                    <input id="shipments" type="checkbox" value="1" name="shipments" @checked(old('shipments', $documentType->shipments) == 1) aria-describedby="shipments-description" class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 bg-white checked:border-indigo-600 checked:bg-indigo-600 indeterminate:border-indigo-600 indeterminate:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 forced-colors:appearance-auto" />
                                                    <i class="fa-solid fa-check text-xs pointer-events-none col-start-1 row-start-1 self-center justify-self-center text-white group-has-disabled:text-gray-950/25 opacity-0 group-has-checked:opacity-100 group-has-indeterminate:opacity-100"></i>
                                                </div>
                                            </div>
                                            <div class="text-sm/6">
                                                <label for="shipments" class="font-medium text-gray-900">Embarques</label>
                                            </div>
                                        </div>
                                        <div class="flex gap-3">
                                            <div class="flex h-6 shrink-0 items-center">
                                                <div class="group grid size-4 grid-cols-1">
                                                    <input type="hidden" name="customs" value="0">
                                                    <input id="customs" type="checkbox" value="1" name="customs" @checked(old('customs', $documentType->customs) == 1) aria-describedby="customs-description" class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 bg-white checked:border-indigo-600 checked:bg-indigo-600 indeterminate:border-indigo-600 indeterminate:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 forced-colors:appearance-auto" />
                                                    <i class="fa-solid fa-check text-xs pointer-events-none col-start-1 row-start-1 self-center justify-self-center text-white group-has-disabled:text-gray-950/25 opacity-0 group-has-checked:opacity-100 group-has-indeterminate:opacity-100"></i>
                                                </div>
                                            </div>
                                            <div class="text-sm/6">
                                                <label for="customs" class="font-medium text-gray-900">Aduanas</label>
                                            </div>
                                        </div>
                                        <div class="flex gap-3">
                                            <div class="flex h-6 shrink-0 items-center">
                                                <div class="group grid size-4 grid-cols-1">
                                                    <input type="hidden" name="warehouse" value="0">
                                                    <input id="warehouse" type="checkbox" value="1" name="warehouse" @checked(old('warehouse', $documentType->warehouse) == 1) aria-describedby="warehouse-description" class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 bg-white checked:border-indigo-600 checked:bg-indigo-600 indeterminate:border-indigo-600 indeterminate:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 forced-colors:appearance-auto" />
                                                    <i class="fa-solid fa-check text-xs pointer-events-none col-start-1 row-start-1 self-center justify-self-center text-white group-has-disabled:text-gray-950/25 opacity-0 group-has-checked:opacity-100 group-has-indeterminate:opacity-100"></i>
                                                </div>
                                            </div>
                                            <div class="text-sm/6">
                                                <label for="warehouse" class="font-medium text-gray-900">Almacenes</label>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                        <div class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-end gap-x-6">
                                <a href="{{ route('document-types.index') }}" class="text-sm/6 font-semibold text-gray-900">Cancelar</a>
                                <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Guardar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</x-layout-admin>
