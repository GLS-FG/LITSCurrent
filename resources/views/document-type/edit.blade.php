@section('title', 'Editar Tipo de documento')
@push('custom_script')
    @include('partials.live-validation')
    <script type="module">
        attachLiveValidation('name', { required: true });
    </script>
@endpush
<x-layout-admin>
    <section>
        <x-navigation.breadcrumbs :links="['Tipos de documentos' => route('document-types.index'), 'Editar Tipo de documento' => '#']" />
        <x-headings.without-action
            :title="'Editar Tipo de documento'"
        />
        @if ($errors->any())
            <x-alerts.error :message="'Para editar el Tipo de documento soluciona los siguientes errores:'" :errors="$errors" class="my-4" />
        @endif
        <form action="{{ route('document-types.update', ['document_type' => $documentType]) }}" method="POST" class="mt-8" autocomplete="off">
            @csrf
            @method('PUT')
            <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550">
                <div class="space-y-8 p-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-900 dark:text-gray-50">Nombre</label>
                        <input id="name" value="{{ old('name', $documentType->name) }}" name="name" type="text" autocomplete="off" class="mt-2 block w-full rounded-lg border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-600 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-50 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-1 focus:outline-indigo-600">
                    </div>
                    <fieldset>
                        <legend class="text-sm font-medium text-gray-900 dark:text-gray-50">¿Dónde aplica?</legend>
                        <div class="mt-3 grid grid-cols-1 gap-3 md:grid-cols-3">
                            <label for="shipments" class="flex cursor-pointer items-center gap-3 rounded-xl border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 p-3 hover:bg-gray-50 dark:hover:bg-white/[0.03] has-checked:border-indigo-600">
                                <span class="group grid size-4 flex-none grid-cols-1">
                                    <input type="hidden" name="shipments" value="0">
                                    <input id="shipments" type="checkbox" value="1" name="shipments" @checked(old('shipments', $documentType->shipments) == 1) class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-lits-blue-550 checked:border-indigo-600 checked:bg-indigo-600 indeterminate:border-indigo-600 indeterminate:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 forced-colors:appearance-auto" />
                                    <i class="fa-solid fa-check text-xs pointer-events-none col-start-1 row-start-1 self-center justify-self-center text-white group-has-disabled:text-gray-950/25 opacity-0 group-has-checked:opacity-100 group-has-indeterminate:opacity-100"></i>
                                </span>
                                <span class="flex size-9 flex-none items-center justify-center rounded-lg text-sm bg-entity-shipments-50 dark:bg-entity-shipments/15 text-entity-shipments dark:text-cyan-400" aria-hidden="true"><i class="fa-regular fa-route"></i></span>
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-50">Embarques</span>
                            </label>
                            <label for="customs" class="flex cursor-pointer items-center gap-3 rounded-xl border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 p-3 hover:bg-gray-50 dark:hover:bg-white/[0.03] has-checked:border-indigo-600">
                                <span class="group grid size-4 flex-none grid-cols-1">
                                    <input type="hidden" name="customs" value="0">
                                    <input id="customs" type="checkbox" value="1" name="customs" @checked(old('customs', $documentType->customs) == 1) class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-lits-blue-550 checked:border-indigo-600 checked:bg-indigo-600 indeterminate:border-indigo-600 indeterminate:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 forced-colors:appearance-auto" />
                                    <i class="fa-solid fa-check text-xs pointer-events-none col-start-1 row-start-1 self-center justify-self-center text-white group-has-disabled:text-gray-950/25 opacity-0 group-has-checked:opacity-100 group-has-indeterminate:opacity-100"></i>
                                </span>
                                <span class="flex size-9 flex-none items-center justify-center rounded-lg text-sm bg-entity-customs-50 dark:bg-entity-customs/15 text-entity-customs dark:text-violet-400" aria-hidden="true"><i class="fa-regular fa-person-military-pointing"></i></span>
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-50">Aduanas</span>
                            </label>
                            <label for="warehouse" class="flex cursor-pointer items-center gap-3 rounded-xl border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 p-3 hover:bg-gray-50 dark:hover:bg-white/[0.03] has-checked:border-indigo-600">
                                <span class="group grid size-4 flex-none grid-cols-1">
                                    <input type="hidden" name="warehouse" value="0">
                                    <input id="warehouse" type="checkbox" value="1" name="warehouse" @checked(old('warehouse', $documentType->warehouse) == 1) class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-lits-blue-550 checked:border-indigo-600 checked:bg-indigo-600 indeterminate:border-indigo-600 indeterminate:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 forced-colors:appearance-auto" />
                                    <i class="fa-solid fa-check text-xs pointer-events-none col-start-1 row-start-1 self-center justify-self-center text-white group-has-disabled:text-gray-950/25 opacity-0 group-has-checked:opacity-100 group-has-indeterminate:opacity-100"></i>
                                </span>
                                <span class="flex size-9 flex-none items-center justify-center rounded-lg text-sm bg-entity-warehouses-50 dark:bg-entity-warehouses/15 text-entity-warehouses dark:text-amber-400" aria-hidden="true"><i class="fa-regular fa-warehouse"></i></span>
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-50">Almacenes</span>
                            </label>
                        </div>
                    </fieldset>
                </div>
                <div class="flex items-center justify-end gap-x-6 border-t border-gray-200 dark:border-lits-blue-450 bg-gray-50 dark:bg-white/[0.02] px-6 py-4">
                    <a href="{{ route('document-types.index') }}" class="text-sm font-semibold text-gray-900 dark:text-gray-50 hover:underline">Cancelar</a>
                    <button type="submit" class="rounded-lg bg-lits-red-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Guardar</button>
                </div>
            </div>
        </form>
    </section>
</x-layout-admin>
