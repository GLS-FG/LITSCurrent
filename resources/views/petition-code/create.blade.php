@section('title', 'Nueva clave de pedimento')
@push('custom_script')
    @include('partials.live-validation')
    <script type="module">
        attachLiveValidation('code', { required: true, max: 3 });
        attachLiveValidation('description', { required: true, max: 500 });
        attachLiveValidation('application_assumptions', { required: true, max: 500 });
    </script>
@endpush
<x-layout-admin>
    <section>
        <x-navigation.breadcrumbs :links="['Claves de pedimento' => route('petition-codes.index'), 'Nueva clave de pedimento' => '#']" />
        <x-headings.without-action
            :title="'Nueva clave de pedimento'"
        />
        @if ($errors->any())
            <x-alerts.error :message="'Para crear la clave de pedimento soluciona los siguientes errores:'" :errors="$errors" class="my-4" />
        @endif
        <form action="{{ route('petition-codes.store') }}" method="POST" class="mt-8 grid grid-cols-1 gap-x-8 gap-y-8 lg:grid-cols-1" autocomplete="off">
            @csrf
            <div class="h-fit lg:col-span-2 divide-y divide-gray-200 dark:divide-gray-700 rounded bg-white dark:bg-lits-blue-550 shadow-lits-card">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-50">Nueva clave de pedimento</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="space-y-12">
                        <div class="pb-12">
                            <h2 class="text-base/7 font-semibold text-gray-900 dark:text-gray-50">Clave de pedimento</h2>
                            <p class="mt-1 text-sm/6 text-gray-600 dark:text-gray-400">Ingresa la información necesaria para crear la clave de pedimento.</p>
                            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                <div class="sm:col-span-2">
                                    <label for="code" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Clave</label>
                                    <div class="mt-2">
                                        <input id="code" value="{{old('code')}}" name="code" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="sm:col-span-4">
                                    <label for="description" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Descripción</label>
                                    <div class="mt-2">
                                        <input id="description" value="{{old('description')}}" name="description" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="sm:col-span-full">
                                    <label for="application_assumptions" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Supuestos</label>
                                    <div class="mt-2">
                                        <textarea rows="4" id="application_assumptions" name="application_assumptions" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">{{old('application_assumptions')}}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-4 sm:px-6">
                    <div class="mt-6 flex items-center justify-end gap-x-6">
                        <a href="{{ route('petition-codes.index') }}" class="text-sm/6 font-semibold text-gray-900 dark:text-gray-50">Cancelar</a>
                        <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Crear clave</button>
                    </div>
                </div>
            </div>
        </form>
    </section>
</x-layout-admin>
