@section('title', 'Detalles del usuario')
@php
    $actionBtn = 'inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:cursor-pointer';
    $initials = \Illuminate\Support\Str::of($user->name)->explode(' ')->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
@endphp
<x-layout-admin>
    <section>
        <x-navigation.breadcrumbs :links="['Clientes' => route('clients.index'), $client->company_name => route('clients.show',['client' => $client]), $user->name => '#']" />
        @if ($errors->any() && ! old('_drawer'))
            <x-alerts.error :message="'Ocurrieron los siguientes errores:'" :errors="$errors" class="my-4" />
        @endif
        @if(session()->has('success'))
            <x-alerts.success class="mt-4" :message="session('success')" />
        @endif

        <div class="mt-4 space-y-8">
            {{-- Cabecera compacta --}}
            <header class="flex flex-wrap items-center gap-x-4 gap-y-3">
                <div class="flex size-12 flex-none items-center justify-center rounded-full border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 text-[15px] font-bold text-gray-500 dark:text-gray-400" aria-hidden="true">{{ $initials }}</div>
                <div class="min-w-0">
                    <h1 class="text-xl font-semibold tracking-tight text-gray-900 dark:text-gray-50 break-words">{{ $user->name }} {{ $user->last_name }}</h1>
                    <p class="mt-0.5 flex flex-wrap items-center gap-x-2.5 gap-y-1 text-xs text-gray-500 dark:text-gray-400">
                        @foreach($user->getRoleNames() as $role)
                            @php $roleEnum = App\Enums\RolesEnum::from($role); @endphp
                            <span class="inline-flex items-center gap-1.5 font-medium {{ $roleEnum->textColor() }}">
                                <span class="size-1.5 rounded-full {{ $roleEnum->dotColor() }}"></span>
                                {{ $roleEnum->label() }}
                            </span>
                            <span aria-hidden="true">·</span>
                        @endforeach
                        <a href="{{ route('clients.show', ['client' => $client]) }}" class="hover:text-lits-red-500">{{ $client->company_name }}</a>
                        <span aria-hidden="true">·</span>
                        <span>Alta {{ $user->created_at->isoFormat('D MMM YYYY') }}</span>
                    </p>
                </div>
                <div class="flex flex-wrap gap-2 sm:ml-auto">
                    @can('update', $user)
                        <form action="{{ route('users.resetPassword', ['user' => $user]) }}" method="POST">
                            @csrf
                            <button
                                data-tippy-content="Recuperar contraseña"
                                type="submit"
                                class="{{ $actionBtn }}"
                            >
                                <i class="fa-regular fa-user-key"></i>
                                Recuperar contraseña
                            </button>
                        </form>
                        <form action="{{ route('users.welcome', ['user' => $user]) }}" method="POST">
                            @csrf
                            <button
                                data-tippy-content="Enviar correo de activación"
                                type="submit"
                                class="{{ $actionBtn }}"
                            >
                                <i class="fa-regular fa-envelope-circle-user"></i>
                                Correo de activación
                            </button>
                        </form>
                        <button type="button" @click="window.dispatchEvent(new CustomEvent('open-edit-client-contact-modal'))" class="{{ $actionBtn }}">
                            <i class="fa-regular fa-pen-to-square"></i>
                            Editar contacto
                        </button>
                    @endcan
                </div>
            </header>

            {{-- Franja de datos --}}
            <dl class="grid grid-cols-2 gap-x-7 gap-y-5 border-y border-gray-200 dark:border-lits-blue-450 py-5 md:grid-cols-4">
                <div class="min-w-0">
                    <dt class="mb-1 text-xs font-semibold text-gray-400 dark:text-gray-500">Nombre</dt>
                    <dd class="text-sm text-gray-900 dark:text-gray-50 break-words">{{ $user->name }} {{ $user->last_name }}</dd>
                </div>
                <div class="min-w-0">
                    <dt class="mb-1 text-xs font-semibold text-gray-400 dark:text-gray-500">Correo</dt>
                    <dd class="text-sm break-words"><a href="mailto:{{ $user->email }}" class="text-lits-red-500 hover:underline">{{ $user->email }}</a></dd>
                </div>
                <div class="min-w-0">
                    <dt class="mb-1 text-xs font-semibold text-gray-400 dark:text-gray-500">Cliente</dt>
                    <dd class="text-sm break-words">
                        @isset($user->client)
                            <a href="{{ route('clients.show', ['client' => $user->client]) }}" class="text-lits-red-500 hover:underline">{{ $user->client->company_name }}</a>
                        @else
                            <span class="text-gray-900 dark:text-gray-50">—</span>
                        @endisset
                    </dd>
                </div>
                <div class="min-w-0">
                    <dt class="mb-1 text-xs font-semibold text-gray-400 dark:text-gray-500">Permiso</dt>
                    <dd class="text-sm">
                        @foreach($user->getRoleNames() as $role)
                            @php $roleEnum = App\Enums\RolesEnum::from($role); @endphp
                            <span class="inline-flex items-center gap-1.5 font-medium {{ $roleEnum->textColor() }}">
                                <span class="size-1.5 rounded-full {{ $roleEnum->dotColor() }}"></span>
                                {{ $roleEnum->label() }}
                            </span>
                        @endforeach
                    </dd>
                </div>
            </dl>
        </div>
    </section>
    @can('update', $user)
        <x-modals.edit-client-contact :client="$client" :user="$user" />
    @endcan
</x-layout-admin>
