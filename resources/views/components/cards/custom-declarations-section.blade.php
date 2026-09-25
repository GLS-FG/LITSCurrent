<div class="flow-root sm:col-span-2">
    <div class="flex flex-wrap items-center justify-between sm:flex-nowrap mb-8">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-50">Declaraciones aduanales</h3>
        <div class="flex space-x-2">
            @can('update', $import)
                <a
                    type="button"
                    href="{{route('orders.imports.declarations.create', ['order' => $order->id, 'import' => $import->id])}}"
                    class="inline-flex items-center gap-x-1.5 rounded-md bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-50 shadow-xs inset-ring inset-ring-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800"
                >
                    <i class="fa-regular fa-plus"></i>
                    Agregar declaración
                </a>
            @endcan
        </div>
    </div>
    <div class="overflow-x-auto border border-gray-300 dark:border-gray-600 rounded">
        <div class="inline-block min-w-full align-middle">
            <table class="relative min-w-full divide-y divide-gray-300 dark:divide-gray-600">
                <thead>
                <tr class="divide-x divide-gray-200 dark:divide-gray-700">
                    <th scope="col" class="py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-gray-900 dark:text-gray-50 sm:pl-3">Agencia aduanal</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-50">Aduana</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-50">Declaración de aduana</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-50">Inspección</th>
                    <th scope="col" class="py-3.5 pr-4 pl-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-50 sm:pr-3">Acciones</th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-lits-blue-550 divide-y divide-gray-300 dark:divide-gray-600">
                @forelse($import->declarations as $declaration)
                    <tr class="even:bg-gray-50 divide-x divide-gray-200 dark:divide-gray-700">
                        <td class="py-4 pr-3 pl-4 text-xs text-gray-500 dark:text-gray-400 sm:pl-3">
                            {{$declaration->customAgent->company_name}} / {{$declaration->customAgent->patent}}
                        </td>
                        <td class="px-3 py-4 text-xs text-gray-500 dark:text-gray-400">{{$declaration->custom->code}} / {{$declaration->custom->denomination}}</td>
                        <td class="px-3 py-4 text-xs text-gray-500 dark:text-gray-400">{{$declaration->petition}}</td>
                        <td class="px-3 py-4 text-xs text-gray-500 dark:text-gray-400">
                            @can('update', $import)
                                @if($declaration->inspection)
                                    <form method="POST" action="{{ route('orders.imports.declarations.inspection', ['order' => $order, 'import' => $import, 'declaration' => $declaration]) }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="inspection" value="0">
                                        <span class="isolate inline-flex rounded-md shadow-xs">
                                          <span class="relative inline-flex items-center rounded-l-md bg-red-500 px-3 py-2 text-sm font-semibold text-white inset-ring-1 inset-ring-gray-300 focus:z-10">Si</span>
                                          <button type="submit" class="relative -ml-px inline-flex items-center rounded-r-md bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-50 inset-ring-1 inset-ring-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 focus:z-10">No</button>
                                        </span>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('orders.imports.declarations.inspection', ['order' => $order, 'import' => $import, 'declaration' => $declaration]) }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="inspection" value="1">
                                        <span class="isolate inline-flex rounded-md shadow-xs">
                                          <button type="submit" class="relative inline-flex items-center rounded-l-md bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-50 inset-ring-1 inset-ring-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 focus:z-10">Si</button>
                                          <span class="relative -ml-px inline-flex items-center rounded-r-md bg-green-500 px-3 py-2 text-sm font-semibold text-white inset-ring-1 inset-ring-gray-300 focus:z-10">No</span>
                                        </span>
                                    </form>
                                @endif
                            @else
                                @if($declaration->inspection)
                                    <span class="isolate inline-flex rounded-md shadow-xs">
                                      <span class="relative inline-flex items-center rounded-l-md bg-red-500 px-3 py-2 text-sm font-semibold text-white inset-ring-1 inset-ring-gray-300 focus:z-10">Si</span>
                                      <span class="relative -ml-px inline-flex items-center rounded-r-md bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-50 inset-ring-1 inset-ring-gray-300 focus:z-10">No</span>
                                    </span>
                                @else
                                    <span class="isolate inline-flex rounded-md shadow-xs">
                                      <span class="relative inline-flex items-center rounded-l-md bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-50 inset-ring-1 inset-ring-gray-300 focus:z-10">Si</span>
                                      <span class="relative -ml-px inline-flex items-center rounded-r-md bg-green-500 px-3 py-2 text-sm font-semibold text-white inset-ring-1 inset-ring-gray-300 focus:z-10">No</span>
                                    </span>
                                @endif
                            @endcan
                        </td>
                        <td class="py-4 pr-4 pl-3 text-right text-xs font-medium whitespace-nowrap sm:pr-3">
                            <div class="flex items-center justify-center gap-x-1">
                                <div
                                    class="relative inline-block text-left"
                                    x-data="{ openWatch: false }"
                                >
                                    <button
                                        type="button"
                                        @click="openWatch = true"
                                        data-tippy-content="Ver declaración"
                                        class="size-7 shrink-0 rounded-md bg-blue-100 dark:bg-blue-500/15 flex items-center justify-center font-semibold text-blue-500 dark:text-blue-400 hover:text-blue-800 hover:bg-blue-200"
                                    >
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                    <div x-cloak x-show="openWatch" class="relative z-100" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                        <div
                                            class="fixed inset-0 bg-gray-500/75 dark:bg-gray-950/75 transition-opacity"
                                            aria-hidden="true"
                                            x-show="openWatch"
                                            x-transition:enter="ease-out duration-300"
                                            x-transition:enter-start="opacity-0"
                                            x-transition:enter-end="opacity-100"
                                            x-transition:leave="ease-in duration-200"
                                            x-transition:leave-start="opacity-100"
                                            x-transition:leave-end="opacity-0"
                                        ></div>

                                        <div class="fixed inset-0 z-100 w-screen overflow-y-auto">
                                            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                                                <div
                                                    x-show="openWatch"
                                                    x-transition:enter="ease-out duration-300"
                                                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                    x-transition:leave="ease-in duration-200"
                                                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                    class="relative transform overflow-hidden rounded-lg bg-white dark:bg-lits-blue-550 px-4 pt-5 pb-4 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6"
                                                >
                                                    <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
                                                        <button type="button" @click="openWatch = false" class="rounded-md bg-white dark:bg-lits-blue-550 text-gray-400 dark:text-gray-500 hover:text-gray-500 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-hidden">
                                                            <span class="sr-only">Close</span>
                                                            <i class="fa-regular fa-xmark"></i>
                                                        </button>
                                                    </div>
                                                    <div class="mt-5 flex justify-center">
                                                        <table>
                                                            <thead>
                                                                <tr>
                                                                    <th class="border bg-yellow-100 dark:bg-yellow-500/15 px-3 py-1 text-blue-900 dark:text-blue-300">PEDIMENTO</th>
                                                                    <th class="border bg-yellow-100 dark:bg-yellow-500/15 px-3 py-1 text-blue-900 dark:text-blue-300">{{ $declaration->petition }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td class="border  px-3 py-1 text-blue-900 dark:text-blue-300">ADUANA</td>
                                                                    <td class="border  px-3 py-1 text-blue-900 dark:text-blue-300">{{ $declaration->custom->code }} / {{ $declaration->custom->denomination }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="border  px-3 py-1 text-blue-900 dark:text-blue-300">AGENTE ADUANA</td>
                                                                    <td class="border  px-3 py-1 text-blue-900 dark:text-blue-300">{{ $declaration->customAgent->patent }} / {{ $declaration->customAgent->company_name }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="border  px-3 py-1 text-blue-900 dark:text-blue-300">FECHA DE ENTRADA</td>
                                                                    <td class="border  px-3 py-1 text-blue-900 dark:text-blue-300">{{ $declaration->entry_date->isoFormat('DD/MM/YYYY') }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="border  px-3 py-1 text-blue-900 dark:text-blue-300">FECHA DE PEDIMENTO</td>
                                                                    <td class="border  px-3 py-1 text-blue-900 dark:text-blue-300">{{ $declaration->draft_date->isoFormat('DD/MM/YYYY') }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="border  px-3 py-1 text-blue-900 dark:text-blue-300">FECHA DE PAGO</td>
                                                                    <td class="border  px-3 py-1 text-blue-900 dark:text-blue-300">{{ $declaration->paid_date->isoFormat('DD/MM/YYYY') }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="border  px-3 py-1 text-blue-900 dark:text-blue-300">VALOR COMERCIAL(MXN)</td>
                                                                    <td class="border  px-3 py-1 text-blue-900 dark:text-blue-300">{{ (float)$declaration->commercial_value }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="border  px-3 py-1 text-blue-900 dark:text-blue-300">VALOR EN ADUANA(MXN)</td>
                                                                    <td class="border  px-3 py-1 text-blue-900 dark:text-blue-300">{{ (float)$declaration->customs_value }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="border  px-3 py-1 text-blue-900 dark:text-blue-300">VALOR EN ADUANA(USD)</td>
                                                                    <td class="border  px-3 py-1 text-blue-900 dark:text-blue-300">{{ (float)$declaration->customs_value_foreign }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="border  px-3 py-1 text-blue-900 dark:text-blue-300">TIPO DE CAMBIO</td>
                                                                    <td class="border  px-3 py-1 text-blue-900 dark:text-blue-300">{{ (float)$declaration->exchange_rate }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="border  px-3 py-1 text-blue-900 dark:text-blue-300">PESO BRUTO</td>
                                                                    <td class="border  px-3 py-1 text-blue-900 dark:text-blue-300">{{ (float)$declaration->gross_weight }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="border  px-3 py-1 text-blue-900 dark:text-blue-300">TOTAL DE BULTOS</td>
                                                                    <td class="border  px-3 py-1 text-blue-900 dark:text-blue-300">{{ (float)$declaration->packages }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="border  px-3 py-1 text-blue-900 dark:text-blue-300">INCOTERM</td>
                                                                    <td class="border  px-3 py-1 text-blue-900 dark:text-blue-300">{{ $declaration->incoterm->code }} ({{ $declaration->incoterm->name }})</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                                        <button type="button" @click="openWatch = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-50 shadow-xs ring-1 ring-gray-300 dark:ring-gray-600 ring-inset hover:bg-gray-50 dark:hover:bg-gray-800 sm:mt-0 sm:w-auto">Cerrar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <a
                                    href="https://aplicacionesc.mat.sat.gob.mx/soianet/oia_consultarapd_cep.aspx?&pa={{$declaration->customAgent->patent}}&dn={{$declaration->petition}}&s=0&ap={{$declaration->entry_date->isoFormat('YYYY')}}&pad={{$declaration->custom->code}}&ad={{$declaration->custom->name}}"
                                    target="_blank"
                                    data-tippy-content="Ver enlace"
                                    role="button"
                                    class="size-7 shrink-0 rounded-md bg-indigo-100 dark:bg-indigo-500/15 flex items-center justify-center font-semibold text-indigo-500 dark:text-indigo-400 hover:text-indigo-800 hover:bg-indigo-200"
                                >
                                    <i class="fa-regular fa-link"></i>
                                </a>
                                @can('update', $import)
                                    <a
                                        href='{{route('orders.imports.declarations.edit', [ 'order' => $order->id, 'import' => $import->id, 'declaration' => $declaration->id ])}}'
                                        data-tippy-content="Editar"
                                        role="button"
                                        class="size-7 shrink-0 rounded-md bg-green-100 dark:bg-green-500/15 flex items-center justify-center font-semibold text-green-500 dark:text-green-400 hover:text-green-800 hover:bg-green-200"
                                    >
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </a>
                                @endcan
                                @can('delete', $import)
                                    <div
                                        class="relative inline-block text-left"
                                        x-data="{ openCancel: false }"
                                    >
                                        <button
                                            type="button"
                                            @click="openCancel = true"
                                            data-tippy-content="Cancelar"
                                            class="size-7 shrink-0 rounded-md bg-red-100 dark:bg-red-500/15 flex items-center justify-center font-semibold text-red-500 dark:text-red-400 hover:text-red-800 hover:bg-red-200 hover:cursor-pointer"
                                        >
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                        <div x-cloak x-show="openCancel" class="relative z-100" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                            <div
                                                class="fixed inset-0 bg-gray-500/75 dark:bg-gray-950/75 transition-opacity"
                                                aria-hidden="true"
                                                x-show="openCancel"
                                                x-transition:enter="ease-out duration-300"
                                                x-transition:enter-start="opacity-0"
                                                x-transition:enter-end="opacity-100"
                                                x-transition:leave="ease-in duration-200"
                                                x-transition:leave-start="opacity-100"
                                                x-transition:leave-end="opacity-0"
                                            ></div>

                                            <div class="fixed inset-0 z-100 w-screen overflow-y-auto">
                                                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                                                    <div
                                                        x-show="openCancel"
                                                        x-transition:enter="ease-out duration-300"
                                                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                        x-transition:leave="ease-in duration-200"
                                                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                        class="relative transform overflow-hidden rounded-lg bg-white dark:bg-lits-blue-550 px-4 pt-5 pb-4 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6"
                                                    >
                                                        <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
                                                            <button type="button" @click="openCancel = false" class="rounded-md bg-white dark:bg-lits-blue-550 text-gray-400 dark:text-gray-500 hover:text-gray-500 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-hidden">
                                                                <span class="sr-only">Close</span>
                                                                <i class="fa-regular fa-xmark"></i>
                                                            </button>
                                                        </div>
                                                        <div class="sm:flex sm:items-start">
                                                            <div class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-500/15 sm:mx-0 sm:size-10">
                                                                <i class="fa-regular fa-triangle-exclamation text-red-600 dark:text-red-400 text-lg"></i>
                                                            </div>
                                                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left whitespace-normal">
                                                                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-50" id="modal-title">Eliminar declaración</h3>
                                                                <div class="mt-2">
                                                                    <p class="text-sm text-gray-500 dark:text-gray-400 font-normal">¿Estás seguro de eliminar la declaración de la aduana?</p>
                                                                    <p class="text-sm text-red-500 dark:text-red-400 font-medium">{{$declaration->petition}}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                                            <form action="{{route('orders.imports.declarations.destroy', [ 'order' => $order->id, 'import' => $import->id, 'declaration' => $declaration->id ])}}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500 sm:ml-3 sm:w-auto">Eliminar</button>
                                                            </form>
                                                            <button type="button" @click="openCancel = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-50 shadow-xs ring-1 ring-gray-300 dark:ring-gray-600 ring-inset hover:bg-gray-50 dark:hover:bg-gray-800 sm:mt-0 sm:w-auto">Cancelar</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="text-sm text-center text-gray-400 dark:text-gray-500 py-5 px-4">
                        <td colspan="6" class="py-4 px-4 text-xs text-gray-500 dark:text-gray-400">
                            No hay declaraciones en la Aduana
                            @can('update', $import)
                                <span>, agrega una nueva haciendo click en el botón <span class="font-semibold">Agregar</span></span>
                            @endcan
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
