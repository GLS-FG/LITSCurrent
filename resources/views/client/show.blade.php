@section('title', 'Detalles del cliente')
@php
    $iconBtn = 'size-7 shrink-0 rounded-md flex items-center justify-center text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-200';
    $iconBtnDanger = 'size-7 shrink-0 rounded-md flex items-center justify-center text-gray-400 dark:text-gray-500 hover:bg-red-50 dark:hover:bg-red-500/15 hover:text-red-500 dark:hover:text-red-400 hover:cursor-pointer';
@endphp
<x-layout-admin>
    <section>
        <x-navigation.breadcrumbs :links="['Clientes' => route('clients.index'), $client->company_name => '#']" />
        @if ($errors->any() && ! old('_drawer'))
            <x-alerts.error :message="'Ocurrieron los siguientes errores:'" :errors="$errors" class="my-4" />
        @endif
        @if(session()->has('success'))
            <x-alerts.success class="mt-4" :message="session('success')" />
        @endif

        <div class="mt-4 space-y-8">
            {{-- Cabecera compacta --}}
            <header class="flex flex-wrap items-center gap-x-4 gap-y-3">
                <img src="{{ route('clients.logos', [ 'filename' => str_replace(".","_",str_replace("logos/", "", $client->image))]) }}" alt="" class="size-12 flex-none rounded-lg bg-white dark:bg-lits-blue-550 border border-gray-200 dark:border-lits-blue-450 object-contain" />
                <div class="min-w-0">
                    <h1 class="text-xl font-semibold tracking-tight text-gray-900 dark:text-gray-50 break-words">{{ $client->company_name }}</h1>
                    <p class="mt-0.5 flex flex-wrap items-center gap-x-2 text-xs text-gray-500 dark:text-gray-400">
                        <span>Cliente {{ str_pad($client->id, 3, '0', STR_PAD_LEFT) }}</span>
                        <span aria-hidden="true">·</span>
                        <span>Alta {{ $client->created_at->isoFormat('D MMM YYYY') }}</span>
                    </p>
                </div>
                <div class="flex shrink-0 gap-2 sm:ml-auto">
                    @can('update', $client)
                        <button type="button" @click="window.dispatchEvent(new CustomEvent('open-edit-client-modal'))"
                           class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:cursor-pointer"
                        >
                            <i class="fa-regular fa-pen-to-square"></i>
                            Editar cliente
                        </button>
                    @endcan
                </div>
            </header>

            {{-- Franja de datos --}}
            <dl class="grid grid-cols-2 gap-x-7 gap-y-5 border-y border-gray-200 dark:border-lits-blue-450 py-5 md:grid-cols-4">
                <div class="min-w-0">
                    <dt class="mb-1 text-xs font-semibold text-gray-400 dark:text-gray-500">RFC</dt>
                    <dd class="text-sm text-gray-900 dark:text-gray-50 break-words">{{ $client->federal_tax_id ?: '—' }}</dd>
                </div>
                <div class="min-w-0">
                    <dt class="mb-1 text-xs font-semibold text-gray-400 dark:text-gray-500">CURP</dt>
                    <dd class="text-sm text-gray-900 dark:text-gray-50 break-words">{{ $client->national_id ?: '—' }}</dd>
                </div>
                <div class="min-w-0">
                    <dt class="mb-1 text-xs font-semibold text-gray-400 dark:text-gray-500">Teléfono</dt>
                    <dd class="text-sm break-words">
                        @if($client->phone1)
                            <a href="tel:{{$client->phone1}}" class="text-lits-red-500 hover:underline">{{$client->phone1}}</a>
                        @else
                            <span class="text-gray-900 dark:text-gray-50">—</span>
                        @endif
                    </dd>
                </div>
                <div class="min-w-0">
                    <dt class="mb-1 text-xs font-semibold text-gray-400 dark:text-gray-500">Correo</dt>
                    <dd class="text-sm break-words">
                        @if($client->email)
                            <a href="mailto:{{$client->email}}" class="text-lits-red-500 hover:underline">{{$client->email}}</a>
                        @else
                            <span class="text-gray-900 dark:text-gray-50">—</span>
                        @endif
                    </dd>
                </div>
            </dl>

            {{-- Contactos --}}
            <section>
                <div class="mb-2 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-[15px] font-semibold text-gray-900 dark:text-gray-50">Contactos<span class="ml-1.5 font-medium text-gray-400 dark:text-gray-500">{{ $client->contacts->count() }}</span></h2>
                        <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">Personas de contacto del cliente.</p>
                    </div>
                    @can('create', \App\Models\User::class)
                        <button type="button" @click="window.dispatchEvent(new CustomEvent('open-add-client-contact-modal'))"
                           class="inline-flex items-center gap-1.5 rounded-md bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer"
                        >
                            <i class="fa-regular fa-plus"></i>
                            Crear contacto
                        </button>
                    @endcan
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                        <tr class="border-b border-gray-200 dark:border-lits-blue-450">
                            <th scope="col" class="py-2.5 pr-3 text-left text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">Nombre</th>
                            <th scope="col" class="px-3 py-2.5 text-left text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">Correo</th>
                            <th scope="col" class="px-3 py-2.5 text-left text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">Fecha alta</th>
                            <th scope="col" class="px-3 py-2.5 text-center text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($client->contacts as $user)
                            <tr class="border-b border-gray-100 dark:border-lits-blue-450/60 hover:bg-gray-50 dark:hover:bg-lits-blue-550/60">
                                <td class="py-3.5 pr-3 text-sm whitespace-nowrap">
                                    <div class="flex items-center gap-2.5 font-semibold text-gray-900 dark:text-gray-50">
                                        <span class="flex size-7 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 text-[11px] font-bold text-gray-500 dark:text-gray-400">{{ \Illuminate\Support\Str::of($user->name)->explode(' ')->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('') }}</span>
                                        {{ $user->name }}
                                    </div>
                                </td>
                                <td class="px-3 py-3.5 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                    {{ $user->email }}
                                </td>
                                <td class="px-3 py-3.5 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                    {{ $user->created_at->isoFormat('D MMM YYYY') }}
                                </td>
                                <td class="px-3 py-3.5 text-sm font-medium whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-0.5">
                                        @can('update', $user)
                                            <a
                                                href="{{route('clients.users.show', ['client' => $client, 'user' => $user])}}"
                                                data-tippy-content="Ver usuario"
                                                role="button"
                                                class="{{ $iconBtn }}"
                                            >
                                                <i class="fa-regular fa-eye"></i>
                                            </a>
                                            <a
                                                href="{{route('clients.users.edit', ['client' => $client, 'user' => $user])}}"
                                                data-tippy-content="Editar"
                                                role="button"
                                                class="{{ $iconBtn }}"
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
                                                    data-tippy-content="Desactivar"
                                                    class="{{ $iconBtnDanger }}"
                                                >
                                                    <i class="fa-regular fa-trash-can"></i>
                                                </button>
                                                <div x-cloak x-show="openCancel" @keydown.escape.window="openCancel = false" class="relative z-100" aria-labelledby="modal-title-user-{{ $user->id }}" role="alertdialog" aria-modal="true">
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
                                                        @click="openCancel = false"
                                                    ></div>

                                                    <div class="fixed inset-0 z-100 w-screen overflow-y-auto">
                                                        <div class="flex min-h-full items-end justify-center p-4 sm:items-center sm:p-6">
                                                            <div
                                                                x-show="openCancel"
                                                                x-transition:enter="ease-out duration-300"
                                                                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                                x-transition:leave="ease-in duration-200"
                                                                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                                                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                                class="relative w-full overflow-hidden rounded-xl border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 text-left whitespace-normal sm:max-w-md"
                                                            >
                                                                <div class="flex items-center gap-3 border-b border-gray-200 dark:border-lits-blue-450 px-6 py-4">
                                                                    <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-lits-red-50 dark:bg-lits-red-500/15 text-lits-red-500">
                                                                        <i class="fa-regular fa-triangle-exclamation"></i>
                                                                    </div>
                                                                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-50" id="modal-title-user-{{ $user->id }}">Desactivar contacto</h3>
                                                                </div>
                                                                <div class="px-6 pt-4 pb-2 text-sm/6 font-normal text-gray-500 dark:text-gray-400">
                                                                    ¿Quieres desactivar a <b class="font-semibold text-gray-900 dark:text-gray-50">{{ $user->name }}</b>? Dejará de aparecer en los contactos de este cliente.
                                                                </div>
                                                                <div class="flex items-center justify-end gap-2 border-t border-gray-200 dark:border-lits-blue-450 px-6 py-3.5 mt-4">
                                                                    <button type="button" @click="openCancel = false" class="rounded-md px-3 py-2 text-sm font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-50 hover:cursor-pointer">Cancelar</button>
                                                                    <form action="{{route('users.destroy', ['user' => $user])}}" method="POST">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="rounded-md bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Desactivar</button>
                                                                    </form>
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
                                <td colspan="4" class="px-3 py-6 text-sm text-center text-gray-500 dark:text-gray-400">
                                    No hay contactos del cliente.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- Direcciones --}}
            <section>
                <div class="mb-2">
                    <h2 class="text-[15px] font-semibold text-gray-900 dark:text-gray-50">Direcciones<span class="ml-1.5 font-medium text-gray-400 dark:text-gray-500">{{ $client->addresses->count() }}</span></h2>
                    <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">Crea o edita las direcciones del cliente.</p>
                </div>
                @if($client->addresses->isNotEmpty())
                    <div class="grid grid-cols-1 gap-x-7 md:grid-cols-2">
                        @foreach($client->addresses as $address)
                            <article class="flex min-w-0 justify-between gap-3 border-b border-gray-100 dark:border-lits-blue-450/60 py-4">
                                <div class="min-w-0 text-[13px]/6 text-gray-500 dark:text-gray-400 break-words">
                                    <p class="mb-0.5 text-sm font-semibold text-gray-900 dark:text-gray-50">{{ $address->nickname }}</p>
                                    <p>{{ $address->address }}, {{ $address->neighborhood }}, C.P. {{ $address->postal_code }}, {{ $address->city->name }}, {{ $address->state->name }}, {{ $address->country->name }}</p>
                                    @if($address->contact_name || $address->phone)
                                        <p>{{ $address->contact_name }}@if($address->contact_name && $address->phone) · @endif{{ $address->phone }}</p>
                                    @endif
                                    @if($address->email)
                                        <p>{{ $address->email }}</p>
                                    @endif
                                </div>
                                <div class="flex items-start gap-0.5">
                                    @can('update', $client)
                                        <a
                                            href="{{route('clients.addresses.edit', [ 'client' => $client->id, 'address' => $address->id ])}}"
                                            data-tippy-content="Editar"
                                            role="button"
                                            class="{{ $iconBtn }}"
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
                                                class="{{ $iconBtnDanger }}"
                                            >
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                            <div x-cloak x-show="openCancel" @keydown.escape.window="openCancel = false" class="relative z-100" aria-labelledby="modal-title-address-{{ $address->id }}" role="alertdialog" aria-modal="true">
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
                                                    @click="openCancel = false"
                                                ></div>

                                                <div class="fixed inset-0 z-100 w-screen overflow-y-auto">
                                                    <div class="flex min-h-full items-end justify-center p-4 sm:items-center sm:p-6">
                                                        <div
                                                            x-show="openCancel"
                                                            x-transition:enter="ease-out duration-300"
                                                            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                            x-transition:leave="ease-in duration-200"
                                                            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                                            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                            class="relative w-full overflow-hidden rounded-xl border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 text-left whitespace-normal sm:max-w-md"
                                                        >
                                                            <div class="flex items-center gap-3 border-b border-gray-200 dark:border-lits-blue-450 px-6 py-4">
                                                                <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-lits-red-50 dark:bg-lits-red-500/15 text-lits-red-500">
                                                                    <i class="fa-regular fa-triangle-exclamation"></i>
                                                                </div>
                                                                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-50" id="modal-title-address-{{ $address->id }}">Desactivar dirección</h3>
                                                            </div>
                                                            <div class="px-6 pt-4 pb-2 text-sm/6 font-normal text-gray-500 dark:text-gray-400">
                                                                ¿Quieres desactivar la dirección <b class="font-semibold text-gray-900 dark:text-gray-50">{{ $address->nickname ?: $address->address }}</b>? Dejará de aparecer en las direcciones de este cliente.
                                                            </div>
                                                            <div class="flex items-center justify-end gap-2 border-t border-gray-200 dark:border-lits-blue-450 px-6 py-3.5 mt-4">
                                                                <button type="button" @click="openCancel = false" class="rounded-md px-3 py-2 text-sm font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-50 hover:cursor-pointer">Cancelar</button>
                                                                <form action="{{route('clients.addresses.destroy', [ 'client' => $client->id, 'address' => $address->id ])}}" method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="rounded-md bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Desactivar</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endcan
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <p class="border-b border-gray-100 dark:border-lits-blue-450/60 py-6 text-sm text-gray-500 dark:text-gray-400">
                        No hay direcciones del cliente en la base de datos.
                    </p>
                @endif
                <button type="button" @click="window.dispatchEvent(new CustomEvent('open-add-client-address-modal'))" class="mt-3.5 inline-flex items-center gap-1.5 text-sm font-semibold text-lits-red-500 hover:underline hover:cursor-pointer"><i class="fa-regular fa-plus text-xs"></i> Agregar dirección</button>
            </section>
        </div>
    </section>
    @can('update', $client)
        <x-modals.edit-client :client="$client" />
    @endcan
    @can('create', \App\Models\User::class)
        <x-modals.add-client-contact :client="$client" />
    @endcan
    <x-modals.add-client-address :client="$client" :countries="$countries" />
</x-layout-admin>
