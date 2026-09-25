@section('title', 'Tipos de documento privado')
<x-layout-admin>
    <section>
        <x-navigation.breadcrumbs :links="['Tipos de documento privado' => '#']" />
        <x-headings.with-one-action
            :title="'Tipos de documento privado'"
            :buttonLabel="'Nuevo Tipo'"
            :buttonAction="route('private-types.create')"
            :objectClass="\App\Models\PrivateDocumentType::class"
        />
        @if(session()->has('success'))
            <x-alerts.success class="mt-4" :message="session('success')" />
        @endif
        <div class="mt-2 flow-root">
            <div class="shadow-lits-card rounded bg-white dark:bg-lits-blue-550">
                <div class="px-4 py-5 sm:p-4 border-b border-gray-200 dark:border-lits-blue-450">
                    <form method="GET" action="{{ route('private-types.index') }}" class="w-full block sm:flex items-center gap-2">
                        <div class="flex-1 flex items-between">
                            <div class="-mr-px flex-1 flex items-center px-0">
                                <div class="grid w-full grid-cols-1 relative">
                                    <input type="text" value="{{ request('search') }}" autocomplete="off" name="search" placeholder="Busca un tipo de documento..." class="col-start-1 row-start-1 block w-full rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-10 pl-10 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    <i class="fa-regular fa-magnifying-glass text-gray-400 dark:text-gray-500 pointer-events-none col-start-1 row-start-1 ml-3 self-center"></i>
                                    @if(request('search'))
                                        <a href="{{ route('private-types.index') }}" class="absolute right-3 size-5 self-center text-gray-400 dark:text-gray-500 hover:text-gray-600" aria-hidden="true" data-slot="icon">
                                            <i class="fa-regular fa-xmark"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="overflow-x-auto">
                    <div class="inline-block min-w-full align-middle">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="">
                            <tr>
                                <th scope="col" class="py-3.5 pr-3 pl-4 text-left text-sm font-semibold whitespace-nowrap text-gray-900 dark:text-gray-50 sm:pl-6">Estatus</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold whitespace-nowrap text-gray-900 dark:text-gray-50">Fecha alta</th>
                                <th scope="col" class="relative py-3.5 pr-4 pl-3 sm:pr-6"><span class="sr-only">Detalles</span></th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 ">
                            @forelse($documentTypes as $docType)
                                <tr>
                                    <td class="py-4 pr-3 pl-4 text-sm text-gray-900 dark:text-gray-50 sm:pl-6 whitespace-nowrap">
                                        {{ $docType->name }}
                                    </td>
                                    <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-900 dark:text-gray-50">
                                        {{ $docType->created_at->isoFormat('D MMM YYYY') }}
                                    </td>
                                    <td class="relative py-4 pr-4 pl-3 text-sm font-medium whitespace-nowrap sm:pr-6 flex items-center justify-end gap-x-1">
                                        @can('update', $docType)
                                            <a
                                                href="{{route('private-types.edit', ['private_type' => $docType->id])}}"
                                                data-tippy-content="Editar"
                                                role="button"
                                                class="size-7 shrink-0 rounded-md bg-green-100 dark:bg-green-500/15 flex items-center justify-center font-semibold text-green-500 dark:text-green-400 hover:text-green-800 hover:bg-green-200"
                                            >
                                                <i class="fa-regular fa-pen-to-square"></i>
                                            </a>
                                        @endcan
                                        @can('delete', $docType)
                                            <div
                                                class="relative inline-block text-left"
                                                x-data="{ openCancel: false }"
                                                @keydown.escape.prevent.stop="close($refs.buttonDropdown)"
                                                @focusin.window="! $refs.panel.contains($event.target) && close()"
                                                x-id="['dropdown-button-{{$docType->id}}']"
                                                @confirm.window="{{$docType->id}} == $event.detail && $refs['delete-row-' + $event.detail].submit()"
                                            >
                                                <button
                                                    type="button"
                                                    @click="openCancel = true"
                                                    data-tippy-content="Eliminar"
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
                                                                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-50" id="modal-title">Eliminar tipo de documento</h3>
                                                                        <div class="mt-2">
                                                                            <p class="text-sm text-gray-500 dark:text-gray-400 font-normal">¿Estás seguro de eliminar el tipo de documento?</p>
                                                                            <p class="text-sm text-red-500 dark:text-red-400 font-medium">{{$docType->name}}</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                                                    <form action="{{route('private-types.destroy', ['private_type' => $docType->id])}}" method="POST">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500 sm:ml-3 sm:w-auto">Eliminar</button>
                                                                    </form>
                                                                    <button type="button" @click="openCancel = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-50 shadow-xs ring-1 ring-gray-300 dark:ring-gray-600 ring-inset hover:bg-gray-50 dark:hover:bg-gray-800 sm:mt-0 sm:w-auto">Regresar</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-3 py-4 text-sm text-center text-gray-500 dark:text-gray-400">
                                        No hay tipos de documentos en la base de datos
                                        @can('create', \App\Models\PrivateDocumentType::class)
                                            <span>, define uno nuevo haciendo <a href="{{route('private-types.create')}}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900">click aquí</a></span>
                                        @endcan
                                        .
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($documentTypes->count())
                    <nav>
                        {{ $documentTypes->links() }}
                    </nav>
                @endif
            </div>
        </div>
    </section>
</x-layout-admin>
