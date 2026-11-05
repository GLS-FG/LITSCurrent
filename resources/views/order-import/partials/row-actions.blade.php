                                <div class="flex items-center justify-center gap-1">
                                    @can('update', $import)
                                        <button
                                            type="button"
                                            @click="window.dispatchEvent(new CustomEvent('request-edit-import', { detail: { url: '{{ route('orders.imports.edit-data', ['order' => $import->order->id, 'import' => $import->id]) }}' } }))"
                                            data-tippy-content="Editar"
                                            class="size-7 shrink-0 rounded-md flex items-center justify-center text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-200 hover:cursor-pointer"
                                        >
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </button>
                                    @endcan
                                    @can('delete', $import)
                                        <div
                                            class="relative inline-block text-left"
                                            x-data="{ openCancel: false }"
                                            @keydown.escape.prevent.stop="close($refs.buttonDropdown)"
                                            @focusin.window="! $refs.panel.contains($event.target) && close()"
                                            x-id="['dropdown-button-{{$import->id}}']"
                                            @confirm.window="{{$import->id}} == $event.detail && $refs['delete-row-' + $event.detail].submit()"
                                        >
                                            <button
                                                type="button"
                                                @click="openCancel = true"
                                                data-tippy-content="Cancelar"
                                                class="size-7 shrink-0 rounded-md flex items-center justify-center text-gray-400 dark:text-gray-500 hover:bg-red-50 dark:hover:bg-red-500/15 hover:text-red-600 dark:hover:text-red-400 hover:cursor-pointer"
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
                                                                    <i class="fa-regular fa-triangle-exclamation text-lg text-red-600 dark:text-red-400"></i>
                                                                </div>
                                                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left whitespace-normal">
                                                                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-50" id="modal-title">Cancelar importación</h3>
                                                                    <div class="mt-2">
                                                                        <p class="text-sm text-gray-500 dark:text-gray-400 font-normal">¿Estás seguro de cancelar la siguiente importación?</p>
                                                                        <p class="text-sm text-red-500 dark:text-red-400 font-medium">{{$import->reference}}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                                                <form action="{{route('orders.imports.destroy', ['order' => $import->order->id, 'import' => $import->id])}}" method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500 sm:ml-3 sm:w-auto">Cancelar</button>
                                                                </form>
                                                                <button type="button" @click="openCancel = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-50 shadow-xs ring-1 ring-gray-300 dark:ring-gray-600 ring-inset hover:bg-gray-50 dark:hover:bg-gray-800 sm:mt-0 sm:w-auto">Regresar</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endcan
                                </div>
