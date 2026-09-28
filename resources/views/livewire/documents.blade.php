<div x-data="{ showError: @entangle('showError') }">
    <div x-cloak x-show="showError" class="mt-2 rounded-md bg-red-50 dark:bg-red-500/10 p-4 border border-red-400">
        <div class="flex">
            <div class="shrink-0">
                <i class="fa-solid fa-circle-x text-red-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800 dark:text-red-400">{{__('Select at least two files to download the ZIP file')}}</h3>
            </div>
        </div>
    </div>
    <div class="flow-root">
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <div class="group/table relative">
                    <div class="absolute top-0 left-14 z-10 hidden h-12 items-center space-x-3 group-has-checked/table:flex sm:left-12">
                        <button
                            type="button"
                            wire:click="download"
                            class="inline-flex items-center rounded-lg border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 disabled:cursor-not-allowed disabled:opacity-30"
                        >
                            {{__('Download ZIP file')}}
                        </button>
                    </div>
                    <table id="users-table" class="relative min-w-full table-fixed">
                        <thead>
                        <tr class="border-b border-gray-200 dark:border-lits-blue-450">
                            <th scope="col" class="relative px-7 sm:w-12 sm:px-6">
                                <div class="group absolute top-1/2 left-4 -mt-2 grid size-4 grid-cols-1">
                                    <input type="checkbox" wire:model.live="selectAll" class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-lits-blue-550 checked:border-lits-red-500 checked:bg-lits-red-500 indeterminate:border-lits-red-500 indeterminate:bg-lits-red-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-500 disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 forced-colors:appearance-auto" />
                                    <i class="fa-solid fa-check text-xs pointer-events-none col-start-1 row-start-1 self-center justify-self-center text-white group-has-disabled:text-gray-950/25 opacity-0 group-has-checked:opacity-100 group-has-indeterminate:opacity-100"></i>
                                </div>
                            </th>
                            <th scope="col" class="min-w-48 py-2.5 pr-3 text-left text-xs font-semibold text-gray-400 dark:text-gray-500">
                                <button type="button" wire:click="changeSort('original_name')" class="rounded hover:bg-gray-100 dark:hover:bg-gray-800 p-1 hover:cursor-pointer">
                                    {{__('File')}}
                                    @if($sortBy == "original_name")
                                        @if($sortByDirection == "asc")
                                            <i class="fa-regular fa-arrow-down-short-wide text-lits-red-500"></i>
                                        @else
                                            <i class="fa-regular fa-arrow-up-short-wide text-lits-red-500"></i>
                                        @endif
                                    @endif
                                </button>
                            </th>
                            <th scope="col" class="px-3 py-2.5 text-left text-xs font-semibold text-gray-400 dark:text-gray-500">
                                <button type="button" wire:click="changeSort('document_type_id')" class="rounded hover:bg-gray-100 dark:hover:bg-gray-800 p-1 hover:cursor-pointer">
                                    {{__('Type')}}
                                    @if($sortBy == "document_type_id")
                                        @if($sortByDirection == "asc")
                                            <i class="fa-regular fa-arrow-down-short-wide text-lits-red-500"></i>
                                        @else
                                            <i class="fa-regular fa-arrow-up-short-wide text-lits-red-500"></i>
                                        @endif
                                    @endif
                                </button>
                            </th>
                            <th scope="col" class="px-3 py-2.5 text-left text-xs font-semibold text-gray-400 dark:text-gray-500">
                                <button type="button" wire:click="changeSort('size_bytes')" class="rounded hover:bg-gray-100 dark:hover:bg-gray-800 p-1 hover:cursor-pointer">
                                    {{__('Size')}}
                                    @if($sortBy == "size_bytes")
                                        @if($sortByDirection == "asc")
                                            <i class="fa-regular fa-arrow-down-short-wide text-lits-red-500"></i>
                                        @else
                                            <i class="fa-regular fa-arrow-up-short-wide text-lits-red-500"></i>
                                        @endif
                                    @endif
                                </button>
                            </th>
                            <th scope="col" class="px-3 py-2.5 text-left text-xs font-semibold text-gray-400 dark:text-gray-500">
                                <button type="button" wire:click="changeSort('created_at')" class="rounded hover:bg-gray-100 dark:hover:bg-gray-800 p-1 hover:cursor-pointer">
                                    {{__('Date')}}
                                    @if($sortBy == "created_at")
                                        @if($sortByDirection == "asc")
                                            <i class="fa-regular fa-arrow-down-short-wide text-lits-red-500"></i>
                                        @else
                                            <i class="fa-regular fa-arrow-up-short-wide text-lits-red-500"></i>
                                        @endif
                                    @endif
                                </button>
                            </th>
                            <th scope="col" class="py-2.5 pr-4 pl-3 sm:pr-3 text-center text-xs font-semibold text-gray-400 dark:text-gray-500">
                                {{__('indexes.actions')}}
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($documents as $file)
                            <tr x-data="{ open: false }" wire:key="{{ $file->id }}" class="group border-b border-gray-100 dark:border-lits-blue-450/60 last:border-b-0 has-checked:bg-gray-50 dark:has-checked:bg-lits-blue-550/60">
                                <td class="relative px-7 sm:w-12 sm:px-6">
                                    <div class="absolute inset-y-0 left-0 hidden w-0.5 bg-lits-red-500 group-has-checked:block"></div>
                                    <div class="group absolute top-1/2 left-4 -mt-2 grid size-4 grid-cols-1">
                                        <input type="checkbox" wire:model.live="files" value="{{$file->id}}" class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-lits-blue-550 checked:border-lits-red-500 checked:bg-lits-red-500 indeterminate:border-lits-red-500 indeterminate:bg-lits-red-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-500 disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 forced-colors:appearance-auto" />
                                        <i class="fa-solid fa-check text-xs pointer-events-none col-start-1 row-start-1 self-center justify-self-center text-white group-has-disabled:text-gray-950/25 opacity-0 group-has-checked:opacity-100 group-has-indeterminate:opacity-100"></i>
                                    </div>
                                </td>
                                <td class="py-3.5 pr-3 text-sm font-medium text-gray-900 dark:text-gray-50 group-has-checked:text-lits-red-600 dark:group-has-checked:text-lits-red-400">{{$file->original_name}}</td>
                                <td class="px-3 py-3.5 text-sm text-gray-500 dark:text-gray-400">{{ $file->documentType->name }}</td>
                                <td class="px-3 py-3.5 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">{{ $file->size_label }}</td>
                                <td class="px-3 py-3.5 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">{{ $file->created_at->isoFormat('DD/MM/YYYY') }}</td>
                                <td class="py-3.5 pr-4 pl-3 text-right text-sm font-medium whitespace-nowrap sm:pr-3">
                                    <div class="relative flex-none flex justify-center gap-x-1">
                                        @if (str_starts_with($file->mime_type, 'image/') || $file->mime_type === 'application/pdf')
                                            <a data-tippy-content="Vista Previa" target="_blank" href="{{route('documents.show', ['document' => $file->id])}}" class="size-7 shrink-0 rounded-md flex items-center justify-center text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-200">
                                                <i class="fa-regular fa-eye"></i>
                                            </a>
                                        @endif
                                        <a data-tippy-content="{{__('Download')}}" href="{{route('documents.download', ['document' => $file->id])}}" class="size-7 shrink-0 rounded-md flex items-center justify-center text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-200">
                                            <i class="fa-regular fa-arrow-down-to-bracket"></i>
                                        </a>
                                        @can('delete', $file)
                                            <button data-tippy-content="{{__('Delete')}}" @click="open = true" type="button" class="size-7 shrink-0 rounded-md flex items-center justify-center text-gray-400 dark:text-gray-500 hover:bg-red-50 dark:hover:bg-red-500/15 hover:text-red-600 dark:hover:text-red-400 hover:cursor-pointer">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                            <div x-cloak x-show="open" class="relative z-100" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                                <div
                                                    class="fixed inset-0 bg-gray-500/75 dark:bg-gray-950/75 transition-opacity"
                                                    aria-hidden="true"
                                                    x-show="open"
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
                                                            x-show="open"
                                                            x-transition:enter="ease-out duration-300"
                                                            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                            x-transition:leave="ease-in duration-200"
                                                            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                                            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                            class="relative transform overflow-hidden rounded-lg bg-white dark:bg-lits-blue-550 px-4 pt-5 pb-4 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6"
                                                        >
                                                            <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
                                                                <button type="button" @click="open = false" class="rounded-md bg-white dark:bg-lits-blue-550 text-gray-400 dark:text-gray-500 hover:text-gray-500 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-hidden">
                                                                    <span class="sr-only">Close</span>
                                                                    <i class="fa-regular fa-xmark"></i>
                                                                </button>
                                                            </div>
                                                            <div class="sm:flex sm:items-start">
                                                                <div class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-500/15 sm:mx-0 sm:size-10">
                                                                    <i class="fa-regular fa-triangle-exclamation text-red-600 dark:text-red-400 text-lg"></i>
                                                                </div>
                                                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left whitespace-normal">
                                                                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-50" id="modal-title">{{__('Delete attachment')}}</h3>
                                                                    <div class="mt-2">
                                                                        <p class="text-sm text-gray-500 dark:text-gray-400 font-normal">{{__('Are you sure you want to delete the following attachment?')}}</p>
                                                                        <p class="text-sm text-red-500 dark:text-red-400 font-medium">{{$file->original_name}}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                                                <form action="{{route('orders.documents.destroy', ['order' => $order->order->id, 'document' => $file->id])}}" method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500 sm:ml-3 sm:w-auto">{{__('Delete')}}</button>
                                                                </form>
                                                                <button type="button" @click="open = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-50 shadow-xs ring-1 ring-gray-300 dark:ring-gray-600 ring-inset hover:bg-gray-50 dark:hover:bg-gray-800 sm:mt-0 sm:w-auto">{{__('Go back')}}</button>
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
                            <tr>
                                <td class="relative px-3 py-4" colspan="6">
                                    <div class="text-center">
                                        <i class="fa-regular fa-folder-open mx-auto text-4xl text-gray-400 dark:text-gray-500"></i>
                                        <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-50">{{__('There are no attachments in the file')}}</h3>
                                        @can('update', $order)
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{__('Attach a new one by clicking on Attach')}}.</p>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
