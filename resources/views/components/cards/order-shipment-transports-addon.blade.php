<div class="flow-root">
    <div class="border border-gray-300 dark:border-gray-600 rounded">
        <div class="inline-block min-w-full align-middle">
            <table class="relative min-w-full divide-y divide-gray-300 dark:divide-gray-600">
                <tbody class="">
                @forelse($shipment->transportations as $transportation)
                    <tr class="">
                        <td class="py-2 pr-3 pl-4 text-xs text-gray-900 dark:text-gray-50 font-medium sm:pl-3">
                            {{$transportation->agency->company_name}}
                        </td>
                        <td class="py-2 pr-4 pl-3 text-right text-xs font-medium whitespace-nowrap sm:pr-3">
                            <div class="flex items-center justify-center gap-x-1">
                                <div
                                    class="relative inline-block text-left"
                                    x-data="{ openWatch: false }"
                                >
                                    <button
                                        type="button"
                                        @click="openWatch = true"
                                        data-tippy-content="{{__('indexes.view')}}"
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
                                                                <th class="border bg-yellow-100 dark:bg-yellow-500/15 px-3 py-1 text-blue-900 dark:text-blue-300">{{__('ECO NUMBER')}}</th>
                                                                <th class="border bg-yellow-100 dark:bg-yellow-500/15 px-3 py-1 text-blue-900 dark:text-blue-300">{{ $transportation->unit_eco_number }}</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <tr>
                                                                <td class="border  px-3 py-1 text-blue-900 dark:text-blue-300">{{__('PLATES')}}</td>
                                                                <td class="border  px-3 py-1 text-blue-900 dark:text-blue-300">{{ $transportation->plates }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="border  px-3 py-1 text-blue-900 dark:text-blue-300">CAAT</td>
                                                                <td class="border  px-3 py-1 text-blue-900 dark:text-blue-300">{{ $transportation->agency->caat_code }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="border  px-3 py-1 text-blue-900 dark:text-blue-300">SCAC</td>
                                                                <td class="border  px-3 py-1 text-blue-900 dark:text-blue-300">{{ $transportation->agency->scac_code }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="border  px-3 py-1 text-blue-900 dark:text-blue-300">{{__('SERIES')}}</td>
                                                                <td class="border  px-3 py-1 text-blue-900 dark:text-blue-300">{{ $transportation->vehicle?->serial_number }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="border  px-3 py-1 text-blue-900 dark:text-blue-300">{{__('UNIT')}}</td>
                                                                <td class="border  px-3 py-1 text-blue-900 dark:text-blue-300">{{ $transportation->vehicle?->vehicle_type }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="border  px-3 py-1 text-blue-900 dark:text-blue-300">{{__('BRAND AND LINE')}}</td>
                                                                <td class="border  px-3 py-1 text-blue-900 dark:text-blue-300">{{ $transportation->vehicle?->vehicle_brand }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="border  px-3 py-1 text-blue-900 dark:text-blue-300">{{__('MODEL')}}</td>
                                                                <td class="border  px-3 py-1 text-blue-900 dark:text-blue-300">{{ $transportation->vehicle?->vehicle_model }}</td>
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
                                @if($transportation->tracking_link != null)
                                    <a
                                        href='{{$transportation->tracking_link}}'
                                        target="_blank"
                                        data-tippy-content="Ver enlace"
                                        role="button"
                                        class="size-7 shrink-0 rounded-md bg-indigo-100 dark:bg-indigo-500/15 flex items-center justify-center font-semibold text-indigo-500 dark:text-indigo-400 hover:text-indigo-800 hover:bg-indigo-200"
                                    >
                                        <i class="fa-regular fa-link"></i>
                                    </a>
                                @endif
                                @can('update', $shipment)
                                    <a
                                        href='{{route('orders.shipments.transportations.edit', [ 'order' => $order->id, 'shipment' => $shipment->id, 'transportation' => $transportation->id ])}}'
                                        data-tippy-content="{{__('indexes.edit')}}"
                                        role="button"
                                        class="size-7 shrink-0 rounded-md bg-green-100 dark:bg-green-500/15 flex items-center justify-center font-semibold text-green-500 dark:text-green-400 hover:text-green-800 hover:bg-green-200"
                                    >
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </a>
                                @endcan
                                @can('delete', $shipment)
                                    <div
                                        class="relative inline-block text-left"
                                        x-data="{ openCancel: false }"
                                    >
                                        <button
                                            type="button"
                                            @click="openCancel = true"
                                            data-tippy-content="{{__('indexes.cancel')}}"
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
                                                                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-50" id="modal-title">{{__('Delete transport')}}</h3>
                                                                <div class="mt-2">
                                                                    <p class="text-sm text-gray-500 dark:text-gray-400 font-normal">{{__('Are you sure to remove the following transport from the shipping order?')}}</p>
                                                                    <p class="text-sm text-red-500 dark:text-red-400 font-medium">{{$transportation->agency->name}} - {{$transportation->transportation_type}}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                                            <form action="{{route('orders.shipments.transportations.destroy', [ 'order' => $order->id, 'shipment' => $shipment->id, 'transportation' => $transportation->id ])}}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500 sm:ml-3 sm:w-auto">{{__('Delete')}}</button>
                                                            </form>
                                                            <button type="button" @click="openCancel = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-50 shadow-xs ring-1 ring-gray-300 dark:ring-gray-600 ring-inset hover:bg-gray-50 dark:hover:bg-gray-800 sm:mt-0 sm:w-auto">{{__('Go back')}}</button>
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
                        <td colspan="6" class="py-2 px-4 text-xs text-gray-500 dark:text-gray-400">
                            {{__('There is no transportation in the shipment')}}
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
