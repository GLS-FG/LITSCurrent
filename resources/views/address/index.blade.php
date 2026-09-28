@section('title', 'Direcciones')
<x-layout-admin>
    <section>
        <x-navigation.breadcrumbs :links="['Direcciones' => '#']" />
        <x-headings.with-one-action
            :title="'Libreta de Direcciones'"
            :buttonLabel="'Nueva dirección'"
            :buttonAction="route('addresses.create')"
            :objectClass="\App\Models\Address::class"
        />
        @if(session()->has('success'))
            <x-alerts.success class="mt-4" :message="session('success')" />
        @endif
        <div class="mt-5">
            <form method="GET" action="{{ route('addresses.index') }}" class="flex flex-col md:flex-row gap-2">
                <div class="flex-1 flex items-center gap-2 rounded-lg bg-white dark:bg-lits-blue-550 border border-gray-200 dark:border-lits-blue-450 px-3.5 py-2.5">
                    <i class="fa-regular fa-magnifying-glass text-gray-400 dark:text-gray-500"></i>
                    <input type="text" value="{{ request('search') }}" autocomplete="off" name="search" placeholder="Busca una dirección..." class="flex-1 border-0 bg-transparent p-0 text-sm text-gray-900 dark:text-gray-50 placeholder:text-gray-400 dark:placeholder:text-gray-500 outline-none">
                    @if(request('search'))
                        <a href="{{ route('addresses.index') }}" class="text-gray-400 dark:text-gray-500 hover:text-gray-600" aria-hidden="true"><i class="fa-regular fa-xmark"></i></a>
                    @endif
                </div>
                <div
                    x-data="{
                        open: false,
                        toggle() {
                            if (this.open) {
                                return this.close()
                            }

                            this.$refs.buttonDropdown.focus()

                            this.open = true
                        },
                        close(focusAfter) {
                            if (! this.open) return

                            this.open = false

                            focusAfter && focusAfter.focus()
                        }
                    }"
                    @keydown.escape.prevent.stop="close($refs.buttonDropdown)"
                    @focusin.window="! $refs.panel.contains($event.target) && close()"
                    x-id="['sort-dropdown-button']"
                    class="relative inline-block text-left">
                    <div>
                        <button
                            type="button"
                            class="inline-flex items-center justify-center gap-x-1.5 rounded-lg border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 px-3.5 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:cursor-pointer"
                            aria-haspopup="true"
                            x-ref="buttonDropdown"
                            @click="toggle()"
                            :aria-expanded="open"
                            :aria-controls="$id('sort-dropdown-button')"
                            id="sort-menu-button"
                        >
                            Ordenar
                            <i class="fa-regular fa-arrow-down-wide-short"></i>
                        </button>
                    </div>
                    <div
                        class="absolute left-0 sm:left-auto sm:right-0 z-10 mt-2 w-56 origin-top-right rounded-md bg-white dark:bg-lits-blue-550 shadow-lg ring-1 ring-black/5 dark:ring-white/10 focus:outline-hidden"
                        x-ref="panel"
                        x-show="open"
                        @click.outside="close($refs.button)"
                        :id="$id('sort-dropdown-button')"
                        x-cloak
                        role="menu"
                        aria-orientation="vertical"
                        aria-labelledby="user-menu-button"
                        tabindex="-1"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                    >
                        <div class="py-1" role="none">
                            <a href="{{ route('addresses.index', ['search' => request('search'), 'status' => request('status'), 'ordering_by' => 'created_at', 'ordering_rule' => 'desc']) }}" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 hover:outline-hidden {{request('ordering_by') == null || (request('ordering_by') == 'created_at' && request('ordering_rule') == 'desc') ? "font-medium text-gray-900 dark:text-gray-50" : "text-gray-500 dark:text-gray-400"}}" role="menuitem" tabindex="-1" id="menu-item-0">Más reciente</a>
                            <a href="{{ route('addresses.index', ['search' => request('search'), 'status' => request('status'), 'ordering_by' => 'created_at', 'ordering_rule' => 'asc']) }}" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 hover:outline-hidden {{request('ordering_by') == 'created_at' && request('ordering_rule') == 'asc' ? "font-medium text-gray-900 dark:text-gray-50" : "text-gray-500 dark:text-gray-400"}}" role="menuitem" tabindex="-1" id="menu-item-1">Más antigüo</a>
                            <a href="{{ route('addresses.index', ['search' => request('search'), 'status' => request('status'), 'ordering_by' => 'name', 'ordering_rule' => 'asc']) }}" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 hover:outline-hidden {{request('ordering_by') == 'name' && request('ordering_rule') == 'asc' ? "font-medium text-gray-900 dark:text-gray-50" : "text-gray-500 dark:text-gray-400"}}" role="menuitem" tabindex="-1" id="menu-item-1">Alfabéticamente: A - Z</a>
                            <a href="{{ route('addresses.index', ['search' => request('search'), 'status' => request('status'), 'ordering_by' => 'name', 'ordering_rule' => 'desc']) }}" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 hover:outline-hidden {{(request('ordering_by') == 'name' && request('ordering_rule') == 'desc') ? "font-medium text-gray-900 dark:text-gray-50" : "text-gray-500 dark:text-gray-400"}}" role="menuitem" tabindex="-1" id="menu-item-0">Alfabéticamente: Z - A</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="mt-4 overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <table class="min-w-full">
                    <thead>
                    <tr class="border-b border-gray-200 dark:border-lits-blue-450">
                        <th scope="col" class="py-2.5 pl-2 text-left text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">Nombre</th>
                        <th scope="col" class="px-3 py-2.5 text-left text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">Nombre comercial</th>
                        <th scope="col" class="px-3 py-2.5 text-left text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">Contacto</th>
                        <th scope="col" class="px-3 py-2.5 text-left text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">Dirección</th>
                        <th scope="col" class="px-3 py-2.5 text-left text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">Ciudad</th>
                        <th scope="col" class="px-3 py-2.5 text-left text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">Fecha alta</th>
                        <th scope="col" class="py-2.5 pr-2 pl-3 text-center text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($addresses as $address)
                        <tr class="border-b border-gray-100 dark:border-lits-blue-450/60 hover:bg-gray-50 dark:hover:bg-lits-blue-550/60">
                            <td class="py-3.5 pl-2">
                                <a href="{{route('addresses.show', ['address' => $address->id])}}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 text-sm font-medium">{{ $address->name }}</a>
                            </td>
                            <td class="px-3 py-3.5 text-sm text-gray-500 dark:text-gray-400">
                                {{ $address->trade_name }}
                            </td>
                            <td class="px-3 py-3.5 text-sm text-gray-500 dark:text-gray-400">
                                {{ $address->contact_name }}
                            </td>
                            <td class="px-3 py-3.5 text-sm text-gray-500 dark:text-gray-400">
                                {{ $address->address }}
                            </td>
                            <td class="px-3 py-3.5 text-sm text-gray-500 dark:text-gray-400">
                                {{ $address->city->name }}, {{ $address->state->name }}, {{ $address->country->name }}
                            </td>
                            <td class="px-3 py-3.5 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                {{ $address->created_at->isoFormat('D MMM YYYY') }}
                            </td>
                            <td class="py-3.5 pr-2 pl-3 text-sm font-medium whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1">
                                <a
                                    href="{{route('addresses.show', ['address' => $address->id])}}"
                                    data-tippy-content="Ver"
                                    role="button"
                                    class="size-7 shrink-0 rounded-md flex items-center justify-center text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-200"
                                >
                                    <i class="fa-regular fa-eye"></i>
                                </a>
                                @can('update', $address)
                                    <a
                                        href="{{route('addresses.edit', ['address' => $address->id])}}"
                                        data-tippy-content="Editar"
                                        role="button"
                                        class="size-7 shrink-0 rounded-md flex items-center justify-center text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-200"
                                    >
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </a>
                                @endcan
                                @can('delete', $address)
                                    <div
                                        class="relative inline-block text-left"
                                        x-data="{ openCancel: false }"
                                        @keydown.escape.prevent.stop="close($refs.buttonDropdown)"
                                        @focusin.window="! $refs.panel.contains($event.target) && close()"
                                        x-id="['dropdown-button-{{$address->id}}']"
                                        @confirm.window="{{$address->id}} == $event.detail && $refs['delete-row-' + $event.detail].submit()"
                                    >
                                        <button
                                            type="button"
                                            @click="openCancel = true"
                                            data-tippy-content="Eliminar"
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
                                                                        <i class="fa-regular fa-triangle-exclamation text-red-600 dark:text-red-400 text-lg"></i>
                                                                    </div>
                                                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left whitespace-normal">
                                                                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-50" id="modal-title">Eliminar dirección</h3>
                                                                        <div class="mt-2">
                                                                            <p class="text-sm text-gray-500 dark:text-gray-400 font-normal">¿Estás seguro de eliminar la dirección?</p>
                                                                            <p class="text-sm text-red-500 dark:text-red-400 font-medium">{{$address->name}}</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                                                    <form action="{{route('addresses.destroy', ['address' => $address->id])}}" method="POST">
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
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-3 py-4 text-sm text-center text-gray-500 dark:text-gray-400">
                                        No hay direcciones en la base de datos
                                        @can('create', \App\Models\Address::class)
                                            <span>, define una nueva haciendo <a href="{{route('addresses.create')}}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900">click aquí</a></span>
                                        @endcan
                                        .
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($addresses->count())
                    <nav class="mt-4">
                        {{ $addresses->links() }}
                    </nav>
                @endif
    </section>
</x-layout-admin>
