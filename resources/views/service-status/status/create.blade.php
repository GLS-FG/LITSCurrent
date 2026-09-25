@section('title', 'Nuevo estatus de servicio')
<x-layout-admin>
    <section>
        <x-navigation.breadcrumbs :links="['Estatus Servicios' => route('service-statuses.index'), $serviceType->code => route('service-statuses.service-type-statuses.index', ['service_status' => $serviceType]), 'Nuevo estatus' => '#']" />
        <x-headings.without-action
            :title="'Nuevo estatus de servicio'"
        />
        @if ($errors->any())
            <x-alerts.error :message="'Para crear el service class soluciona los siguientes errores:'" :errors="$errors" class="my-4" />
        @endif
        <form action="{{ route('service-statuses.service-type-statuses.store', ['service_status' => $serviceType]) }}" method="POST" class="mt-8 grid grid-cols-1 gap-x-8 gap-y-8 lg:grid-cols-1" autocomplete="off">
            @csrf
            <div class="h-fit lg:col-span-2 divide-y divide-gray-200 dark:divide-gray-700 rounded bg-white dark:bg-lits-blue-550 shadow-lits-card">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-50">Nuevo estatus de servicio</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="space-y-4">
                        <div class="pb-4">
                            <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                <div class="sm:col-span-full">
                                    <label for="name" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Nombre</label>
                                    <div class="mt-2">
                                        <input id="name" value="{{old('name')}}" name="name" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="sm:col-span-full">
                                    <fieldset aria-label="Choose a memory option">
                                        <div class="text-sm/6 font-medium text-gray-900 dark:text-gray-50">Elige un color</div>
                                        <div class="mt-2 gap-3 flex flex-wrap">
                                            <label class="group relative flex items-center justify-center rounded-md p-0.5 has-checked:outline-2 has-checked:outline-indigo-600 has-focus-visible:outline-2 has-focus-visible:outline-offset-2 has-focus-visible:outline-indigo-600">
                                                <input type="radio" name="color" value="bg-gray-50 text-gray-600 inset-ring-gray-500/10" @checked(old('color') === "bg-gray-50 text-gray-600 inset-ring-gray-500/10") class="absolute inset-0 appearance-none focus:outline-none disabled:cursor-not-allowed" />
                                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium inset-ring bg-gray-50 dark:bg-gray-800/60 text-gray-600 dark:text-gray-400 inset-ring-gray-500/10">GRIS</span>
                                            </label>
                                            <label class="group relative flex items-center justify-center rounded-md p-0.5 has-checked:outline-2 has-checked:outline-indigo-600 has-focus-visible:outline-2 has-focus-visible:outline-offset-2 has-focus-visible:outline-indigo-600">
                                                <input type="radio" name="color" value="bg-red-50 text-red-700 inset-ring-red-600/10" @checked(old('color') === "bg-red-50 text-red-700 inset-ring-red-600/10") class="absolute inset-0 appearance-none focus:outline-none disabled:cursor-not-allowed" />
                                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium inset-ring bg-red-50 dark:bg-red-500/10 text-red-700 dark:text-red-400 inset-ring-red-600/10">ROJO</span>
                                            </label>
                                            <label class="group relative flex items-center justify-center rounded-md p-0.5 has-checked:outline-2 has-checked:outline-indigo-600 has-focus-visible:outline-2 has-focus-visible:outline-offset-2 has-focus-visible:outline-indigo-600">
                                                <input type="radio" name="color" value="bg-yellow-50 text-yellow-800 inset-ring-yellow-600/20" @checked(old('color') === "bg-yellow-50 text-yellow-800 inset-ring-yellow-600/20") class="absolute inset-0 appearance-none focus:outline-none disabled:cursor-not-allowed" />
                                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium inset-ring bg-yellow-50 dark:bg-yellow-500/10 text-yellow-800 dark:text-yellow-400 inset-ring-yellow-600/20">AMARILLO</span>
                                            </label>
                                            <label class="group relative flex items-center justify-center rounded-md p-0.5 has-checked:outline-2 has-checked:outline-indigo-600 has-focus-visible:outline-2 has-focus-visible:outline-offset-2 has-focus-visible:outline-indigo-600">
                                                <input type="radio" name="color" value="bg-green-50 text-green-700 inset-ring-green-600/20" @checked(old('color') === "bg-green-50 text-green-700 inset-ring-green-600/20") class="absolute inset-0 appearance-none focus:outline-none disabled:cursor-not-allowed" />
                                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium inset-ring bg-green-50 dark:bg-green-500/10 text-green-700 dark:text-green-400 inset-ring-green-600/20">VERDE</span>
                                            </label>
                                            <label class="group relative flex items-center justify-center rounded-md p-0.5 has-checked:outline-2 has-checked:outline-indigo-600 has-focus-visible:outline-2 has-focus-visible:outline-offset-2 has-focus-visible:outline-indigo-600">
                                                <input type="radio" name="color" value="bg-blue-50 text-blue-700 inset-ring-blue-700/10" @checked(old('color') === "bg-blue-50 text-blue-700 inset-ring-blue-700/10") class="absolute inset-0 appearance-none focus:outline-none disabled:cursor-not-allowed" />
                                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium inset-ring bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 inset-ring-blue-700/10">AZUL</span>
                                            </label>
                                            <label class="group relative flex items-center justify-center rounded-md p-0.5 has-checked:outline-2 has-checked:outline-indigo-600 has-focus-visible:outline-2 has-focus-visible:outline-offset-2 has-focus-visible:outline-indigo-600">
                                                <input type="radio" name="color" value="bg-indigo-50 text-indigo-700 inset-ring-indigo-700/10" @checked(old('color') === "bg-indigo-50 text-indigo-700 inset-ring-indigo-700/10") class="absolute inset-0 appearance-none focus:outline-none disabled:cursor-not-allowed" />
                                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium inset-ring bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 inset-ring-indigo-700/10">INDIGO</span>
                                            </label>
                                            <label class="group relative flex items-center justify-center rounded-md p-0.5 has-checked:outline-2 has-checked:outline-indigo-600 has-focus-visible:outline-2 has-focus-visible:outline-offset-2 has-focus-visible:outline-indigo-600">
                                                <input type="radio" name="color" value="bg-purple-50 text-purple-700 inset-ring-purple-700/10" @checked(old('color') === "bg-purple-50 text-purple-700 inset-ring-purple-700/10") class="absolute inset-0 appearance-none focus:outline-none disabled:cursor-not-allowed" />
                                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium inset-ring bg-purple-50 dark:bg-purple-500/10 text-purple-700 dark:text-purple-400 inset-ring-purple-700/10">PURPURA</span>
                                            </label>
                                            <label class="group relative flex items-center justify-center rounded-md p-0.5 has-checked:outline-2 has-checked:outline-indigo-600 has-focus-visible:outline-2 has-focus-visible:outline-offset-2 has-focus-visible:outline-indigo-600">
                                                <input type="radio" name="color" value="bg-pink-50 text-pink-700 inset-ring-pink-700/10" @checked(old('color') === "bg-pink-50 text-pink-700 inset-ring-pink-700/10") class="absolute inset-0 appearance-none focus:outline-none disabled:cursor-not-allowed" />
                                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium inset-ring bg-pink-50 dark:bg-pink-500/10 text-pink-700 dark:text-pink-400 inset-ring-pink-700/10">ROSA</span>
                                            </label>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                        <div class="pb-4">
                            <h2 class="text-base/7 font-semibold text-gray-900 dark:text-gray-50">Service Modes</h2>
                            <p class="mt-1 text-sm/6 text-gray-600 dark:text-gray-400">Selecciona los service modes a los que aplica el estatus.</p>
                            <div class="mt-2 grid grid-cols-1 gap-x-2 gap-y-2 sm:grid-cols-6">
                                <div class="col-span-full">
                                    <table class="relative divide-y border">
                                        <thead>
                                            <tr class="divide-x">
                                                <th scope="col" class="py-3.5 px-3 text-center text-sm font-semibold text-gray-900 dark:text-gray-50 bg-gray-200 dark:bg-gray-700">Service Class</th>
                                                <th scope="col" class="py-3.5 px-3 text-center text-sm font-semibold text-gray-900 dark:text-gray-50 bg-gray-200 dark:bg-gray-700">Service Mode</th>
                                                <th scope="col" class="py-3.5 px-3 bg-gray-200 dark:bg-gray-700">
                                                    <span class="sr-only"></span>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y">
                                            @foreach($serviceType->serviceClasses as $serviceClass)
                                                @foreach($serviceClass->serviceModes as $serviceMode)
                                                <tr class="divide-x">
                                                    @if($loop->first)
                                                        <td rowspan="{{count($serviceClass->serviceModes)}}" class="py-4 px-3 text-sm text-center font-medium whitespace-nowrap text-gray-900 dark:text-gray-50 bg-gray-200 dark:bg-gray-700">{{$serviceClass->code}}</td>
                                                    @endif
                                                    <td class="px-3 py-4 text-sm text-center text-gray-900 dark:text-gray-50 bg-gray-200 dark:bg-gray-700">{{$serviceMode->code}}</td>
                                                    <td class="py-4 px-3 text-sm font-medium text-center">
                                                        <div class="flex h-6 shrink-0 items-center">
                                                            <div class="group grid size-4 grid-cols-1">
                                                                <input id="permissions" type="checkbox" value="{{ $serviceMode->id }}" name="permissions[]" @checked(in_array($serviceMode->id, old('permissions', []))) class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-lits-blue-550 checked:border-indigo-600 checked:bg-indigo-600 indeterminate:border-indigo-600 indeterminate:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 forced-colors:appearance-auto" />
                                                                <i class="fa-solid fa-check text-xs pointer-events-none col-start-1 row-start-1 self-center justify-self-center text-white group-has-disabled:text-gray-950/25 opacity-0 group-has-checked:opacity-100 group-has-indeterminate:opacity-100"></i>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                              @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-4 sm:px-6">
                    <div class="mt-6 flex items-center justify-end gap-x-6">
                        <a href="{{ route('service-statuses.service-type-statuses.index', ['service_status' => $serviceType]) }}" class="text-sm/6 font-semibold text-gray-900 dark:text-gray-50">Cancelar</a>
                        <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Guardar</button>
                    </div>
                </div>
            </div>
        </form>
    </section>
</x-layout-admin>
