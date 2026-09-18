@section('title', 'Detalles del cliente')
<x-layout-admin>
    <section>
        <x-navigation.breadcrumbs :links="['Clientes' => route('clients.index'), $client->company_name => '#']" />
        <x-headings.without-action
            :title="'Detalles del cliente'"
        />
        @if ($errors->any())
            <x-alerts.error :message="'Ocurrieron los siguientes errores:'" :errors="$errors" class="my-4" />
        @endif
        @if(session()->has('success'))
            <x-alerts.success class="mt-4" :message="session('success')" />
        @endif
        <div class="mt-2 space-y-8">
            <div class="divide-y divide-gray-200 rounded bg-white shadow-lits-card">
                <div class="px-4 py-5 sm:px-6">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $client->company_name }}</h3>
                            <p class="mt-1 max-w-2xl text-sm/6 text-gray-500">{{ str_pad($client->id, 3, '0', STR_PAD_LEFT) }}</p>
                        </div>
                        <div class="flex shrink-0 space-x-5">
                            @can('update', $client)
                                <a href='{{route('clients.edit', [ 'client' => $client->id ])}}'
                                   class="inline-flex items-center gap-1 rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer"
                                >
                                    <i class="fa-regular fa-pen-to-square mr-1.5 -ml-0.5"></i>
                                    Editar cliente
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="px-4 py-5 sm:p-6 divide-y divide-gray-200">
                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                        <dt class="text-sm/6 font-medium text-gray-900">Imágen</dt>
                        <dd class="mt-1 text-sm/6 text-gray-700 sm:col-span-2 sm:mt-0">
                            <img src="{{ route('clients.logos', [ 'filename' => str_replace(".","_",str_replace("logos/", "", $client->image))]) }}" alt="" class="size-16 flex-none rounded-lg bg-gray-100 object-contain outline -outline-offset-1 outline-black/5" />
                        </dd>
                    </div>
                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                        <dt class="text-sm/6 font-medium text-gray-900">RFC</dt>
                        <dd class="mt-1 text-sm/6 text-gray-700 sm:col-span-2 sm:mt-0">
                            {{$client->federal_tax_id}}
                        </dd>
                    </div>
                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                        <dt class="text-sm/6 font-medium text-gray-900">CURP</dt>
                        <dd class="mt-1 text-sm/6 text-gray-700 sm:col-span-2 sm:mt-0">
                            {{$client->national_id}}
                        </dd>
                    </div>
                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                        <dt class="text-sm/6 font-medium text-gray-900">Teléfonos</dt>
                        <dd class="mt-1 text-sm/6 text-gray-700 sm:col-span-2 sm:mt-0">
                            <a href="tel:{{$client->phone1}}" class="text-indigo-600 hover:text-indigo-900 text-sm">{{$client->phone1}}</a>

                        </dd>
                    </div>
                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                        <dt class="text-sm/6 font-medium text-gray-900">Email</dt>
                        <dd class="mt-1 text-sm/6 text-gray-700 sm:col-span-2 sm:mt-0">
                            <a href="mailto:{{$client->email}}" class="text-indigo-600 hover:text-indigo-900 text-sm">{{$client->email}}</a>
                        </dd>
                    </div>
                </div>
            </div>
            <div class="divide-y divide-gray-200 rounded bg-white shadow-lits-card">
                <div class="px-4 py-5 sm:px-6">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Contactos del cliente</h3>
                            <p class="mt-1 max-w-2xl text-sm/6 text-gray-500">Personas de contacto del cliente</p>
                        </div>
                        <div class="flex shrink-0 space-x-5">
                            @can('create', \App\Models\User::class)
                                <a href='{{route('clients.users.create', [ 'client' => $client->id ])}}'
                                   class="inline-flex items-center gap-1 rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer"
                                >
                                    <i class="fa-regular fa-plus mr-1.5 -ml-0.5"></i>
                                    Crear contacto
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="overflow-x-auto">
                        <div class="inline-block min-w-full align-middle">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="">
                                <tr>
                                    <th scope="col" class="py-3.5 pr-3 pl-4 text-left text-sm font-semibold whitespace-nowrap text-gray-900 sm:pl-6">Nombre</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold whitespace-nowrap text-gray-900">Email</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold whitespace-nowrap text-gray-900">Fecha alta</th>
                                    <th scope="col" class="relative py-3.5 pr-4 pl-3 sm:pr-6"><span class="sr-only">Detalles</span></th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 ">
                                @forelse($client->contacts as $user)
                                    <tr>
                                        <td class="py-4 pr-3 pl-4 text-sm text-gray-900 sm:pl-6">
                                            {{ $user->name }}
                                        </td>
                                        <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-900">
                                            {{ $user->email }}
                                        </td>
                                        <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-900">
                                            {{ $user->created_at->isoFormat('D MMM YYYY') }}
                                        </td>
                                        <td class="relative py-4 pr-4 pl-3 text-sm font-medium whitespace-nowrap sm:pr-6 flex items-center justify-end gap-x-1">
                                            @can('update', $user)
                                                <a
                                                    href="{{route('clients.users.show', ['client' => $client, 'user' => $user])}}"
                                                    data-tippy-content="Ver usuario"
                                                    role="button"
                                                    class="size-7 shrink-0 rounded-md bg-blue-100 flex items-center justify-center font-semibold text-blue-500 hover:text-blue-800 hover:bg-blue-200"
                                                >
                                                    <i class="fa-regular fa-eye"></i>
                                                </a>
                                                <a
                                                    href="{{route('clients.users.edit', ['client' => $client, 'user' => $user])}}"
                                                    data-tippy-content="Editar"
                                                    role="button"
                                                    class="size-7 shrink-0 rounded-md bg-green-100 flex items-center justify-center font-semibold text-green-500 hover:text-green-800 hover:bg-green-200"
                                                >
                                                    <i class="fa-regular fa-pen-to-square"></i>
                                                </a>
                                            @endcan
                                            @can('delete', $user)
                                                <div
                                                    class="relative inline-block text-left"
                                                    x-data="{ openCancel: false }"
                                                >
                                                    <button
                                                        type="button"
                                                        @click="openCancel = true"
                                                        data-tippy-content="Eliminar"
                                                        class="size-7 shrink-0 rounded-md bg-red-100 flex items-center justify-center font-semibold text-red-500 hover:text-red-800 hover:bg-red-200 hover:cursor-pointer"
                                                    >
                                                        <i class="fa-regular fa-trash-can"></i>
                                                    </button>
                                                    <div x-cloak x-show="openCancel" class="relative z-100" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                                        <div
                                                            class="fixed inset-0 bg-gray-500/75 transition-opacity"
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
                                                                    class="relative transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6"
                                                                >
                                                                    <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
                                                                        <button type="button" @click="openCancel = false" class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-hidden">
                                                                            <span class="sr-only">Close</span>
                                                                            <i class="fa-regular fa-xmark"></i>
                                                                        </button>
                                                                    </div>
                                                                    <div class="sm:flex sm:items-start">
                                                                        <div class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:size-10">
                                                                            <i class="fa-regular fa-triangle-exclamation text-lg text-red-600"></i>
                                                                        </div>
                                                                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left whitespace-normal">
                                                                            <h3 class="text-base font-semibold text-gray-900" id="modal-title">Eliminar contacto</h3>
                                                                            <div class="mt-2">
                                                                                <p class="text-sm text-gray-500 font-normal">¿Estás seguro de eliminar el siguiente contacto?</p>
                                                                                <p class="text-sm text-red-500 font-medium">{{$user->name}}</p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                                                        <form action="{{route('users.destroy', ['user' => $user])}}" method="POST">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500 sm:ml-3 sm:w-auto">Eliminar</button>
                                                                        </form>
                                                                        <button type="button" @click="openCancel = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs ring-1 ring-gray-300 ring-inset hover:bg-gray-50 sm:mt-0 sm:w-auto">Regresar</button>
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
                                        <td colspan="6" class="px-3 py-4 text-sm text-center text-gray-500">
                                            No hay contactos del cliente.
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="divide-y divide-gray-200 rounded bg-white shadow-lits-card">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-semibold text-gray-900">Direcciones del cliente</h3>
                    <p class="mt-1 max-w-2xl text-sm/6 text-gray-500">Crea o edita nuevas direcciones del cliente</p>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <ul role="list" class="divide-y divide-gray-200 text-sm/6">
                        @forelse($client->addresses as $address)
                            <li class="flex justify-between items-center gap-x-6 py-6">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $address->nickname }}</p>
                                    <p class="text-gray-500">{{ $address->address }}, {{ $address->neighborhood }}, C.P. {{ $address->postal_code }}, {{ $address->city->name }}, {{ $address->state->name }}, {{ $address->country->name }}</p>
                                    <p class="text-gray-500">{{ $address->contact_name }}</p>
                                    <p class="text-gray-500">{{ $address->email }}</p>
                                    <p class="text-gray-500">{{ $address->phone }}</p>
                                </div>
                                <div class="flex gap-x-2">
                                    @can('update', $client)
                                        <a
                                            href="{{route('clients.addresses.edit', [ 'client' => $client->id, 'address' => $address->id ])}}"
                                            data-tippy-content="Editar"
                                            role="button"
                                            class="size-7 shrink-0 rounded-md bg-green-100 flex items-center justify-center font-semibold text-green-500 hover:text-green-800 hover:bg-green-200"
                                        >
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </a>
                                    @endcan
                                    @can('delete', $client)
                                        <div
                                            class="relative inline-block text-left"
                                            x-data="{ openCancel: false }"
                                            @keydown.escape.prevent.stop="close($refs.buttonDropdown)"
                                            @focusin.window="! $refs.panel.contains($event.target) && close()"
                                            x-id="['dropdown-button-{{$client->id}}']"
                                            @confirm.window="{{$client->id}} == $event.detail && $refs['delete-row-' + $event.detail].submit()"
                                        >
                                            <button
                                                type="button"
                                                @click="openCancel = true"
                                                data-tippy-content="Desactivar"
                                                class="size-7 shrink-0 rounded-md bg-red-100 flex items-center justify-center font-semibold text-red-500 hover:text-red-800 hover:bg-red-200 hover:cursor-pointer"
                                            >
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                            <div x-cloak x-show="openCancel" class="relative z-100" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                                <div
                                                    class="fixed inset-0 bg-gray-500/75 transition-opacity"
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
                                                            class="relative transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6"
                                                        >
                                                            <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
                                                                <button type="button" @click="openCancel = false" class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-hidden">
                                                                    <span class="sr-only">Close</span>
                                                                    <i class="fa-regular fa-xmark"></i>
                                                                </button>
                                                            </div>
                                                            <div class="sm:flex sm:items-start">
                                                                <div class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:size-10">
                                                                    <i class="fa-regular fa-triangle-exclamation text-red-600 text-lg"></i>
                                                                </div>
                                                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left whitespace-normal">
                                                                    <h3 class="text-base font-semibold text-gray-900" id="modal-title">Eliminar dirección</h3>
                                                                    <div class="mt-2">
                                                                        <p class="text-sm text-gray-500 font-normal">¿Estás seguro de eliminar la dirección del cliente?</p>
                                                                        <p class="text-sm text-red-500 font-medium">{{ $address->address }}, {{ $address->neighborhood }}, C.P. {{ $address->postal_code }}, {{ $address->city->name }}, {{ $address->state->name }}, {{ $address->country->name }}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                                                <form action="{{route('clients.addresses.destroy', [ 'client' => $client->id, 'address' => $address->id ])}}" method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500 sm:ml-3 sm:w-auto">Eliminar</button>
                                                                </form>
                                                                <button type="button" @click="openCancel = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs ring-1 ring-gray-300 ring-inset hover:bg-gray-50 sm:mt-0 sm:w-auto">Regresar</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endcan
                                </div>
                            </li>
                        @empty
                            <li>
                                No hay direcciones del cliente en la base de datos
                                @can('update', $client)
                                    <span>, define una nueva haciendo <a href="{{route('clients.addresses.create', [ 'client' => $client->id ])}}" class="text-indigo-600 hover:text-indigo-900">click aquí</a></span>
                                @endcan
                            </li>
                        @endforelse
                    </ul>
                    <div class="flex border-t border-gray-200 pt-6">
                        <a href='{{route('clients.addresses.create', [ 'client' => $client->id ])}}' class="text-sm/6 font-semibold text-indigo-600 hover:text-indigo-500"><span aria-hidden="true">+</span> Agregar dirección</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layout-admin>
