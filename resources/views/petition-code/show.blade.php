@section('title', 'Detalles de la clave de pedimento')
<x-layout-admin>
    <section>
        <x-navigation.breadcrumbs :links="['Claves de pedimento' => route('petition-codes.index'), $code->code => '#']" />
        <x-headings.without-action
            :title="'Detalles de la clave de pedimento'"
        />
        @if ($errors->any())
            <x-alerts.error :message="'Ocurrieron los siguientes errores:'" :errors="$errors" class="my-4" />
        @endif
        @if(session()->has('success'))
            <x-alerts.success class="mt-4" :message="session('success')" />
        @endif
        <div class="mt-8">
            <div class="divide-y divide-gray-200 rounded bg-white shadow-lits-card">
                <div class="px-4 py-5 sm:px-6">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Transportista</h3>
                            <p class="mt-1 max-w-2xl text-sm/6 text-gray-500">Información del almacen y su contacto.</p>
                        </div>
                        <div class="flex shrink-0 space-x-5">
                            @can('update', $code)
                                <a href='{{route('petition-codes.edit', [ 'petition_code' => $code->id ])}}' class="inline-flex items-center gap-1 rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">
                                    <i class="fa-regular fa-pen-to-square mr-1.5 -ml-0.5"></i>
                                    Editar clave
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="px-4 py-5 sm:p-6 divide-y divide-gray-200">
                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                        <dt class="text-sm/6 font-medium text-gray-900">Clave</dt>
                        <dd class="mt-1 text-sm/6 text-gray-700 sm:col-span-2 sm:mt-0">
                            {{$code->code}}
                        </dd>
                    </div>
                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                        <dt class="text-sm/6 font-medium text-gray-900">Descripción</dt>
                        <dd class="mt-1 text-sm/6 text-gray-700 sm:col-span-2 sm:mt-0">
                            {{$code->description}}
                        </dd>
                    </div>
                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                        <dt class="text-sm/6 font-medium text-gray-900">Supuesto</dt>
                        <dd class="mt-1 text-sm/6 text-gray-700 sm:col-span-2 sm:mt-0">
                            {{$code->application_assumptions}}
                        </dd>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layout-admin>
