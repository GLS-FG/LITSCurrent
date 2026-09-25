@section('title', 'Detalles del usuario')
<x-layout-admin>
    <section>
        <x-navigation.breadcrumbs :links="['Usuarios' => route('users.index'), $user->name => '#']" />
        <x-headings.without-action
            :title="'Detalles del usuario'"
        />
        @if ($errors->any())
            <x-alerts.error :message="'Ocurrieron los siguientes errores:'" :errors="$errors" class="my-4" />
        @endif
        @if(session()->has('success'))
            <x-alerts.success class="mt-4" :message="session('success')" />
        @endif
        <div class="mt-2 space-y-8">
            <div class="divide-y divide-gray-200 dark:divide-gray-700 rounded bg-white dark:bg-lits-blue-550 shadow-lits-card">
                <div class="px-4 py-5 sm:px-6">
                    <div class="sm:flex sm:flex-wrap sm:items-center sm:justify-end gap-4">
                        @can('update', $user)
                            <form action="{{ route('users.resetPassword', ['user' => $user]) }}" method="POST">
                                @csrf
                                <button
                                    data-tippy-content="Recuperar contraseña"
                                    type="submit"
                                    class="mt-2 inline-flex items-center gap-1 rounded bg-blue-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-blue-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-450 hover:cursor-pointer"
                                >
                                    <i class="fa-regular fa-user-key"></i>
                                    Recuperar contraseña
                                </button>
                            </form>
                            <a href='{{route('users.edit', [ 'user' => $user->id ])}}'
                               class="mt-2 inline-flex items-center gap-1 rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer"
                            >
                                <i class="fa-regular fa-pen-to-square mr-1.5 -ml-0.5"></i>
                                Editar usuario
                            </a>
                        @endcan
                    </div>
                </div>
                <div class="px-4 py-5 sm:p-6 divide-y divide-gray-200 dark:divide-gray-700">
                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                        <dt class="text-sm/6 font-medium text-gray-900 dark:text-gray-50">Nombre</dt>
                        <dd class="mt-1 text-sm/6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">
                            {{$user->name}} {{$user->last_name}}
                        </dd>
                    </div>
                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                        <dt class="text-sm/6 font-medium text-gray-900 dark:text-gray-50">Email</dt>
                        <dd class="mt-1 text-sm/6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">
                            <a href="mailto:{{$user->email}}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 text-sm">{{$user->email}}</a>
                        </dd>
                    </div>
                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                        <dt class="text-sm/6 font-medium text-gray-900 dark:text-gray-50">Cliente</dt>
                        <dd class="mt-1 text-sm/6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">
                            @isset($user->client)
                                {{ $user->client->company_name }}
                            @endisset
                        </dd>
                    </div>
                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                        <dt class="text-sm/6 font-medium text-gray-900 dark:text-gray-50">Permiso</dt>
                        <dd class="mt-1 text-sm/6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">
                            @foreach($user->getRoleNames() as $role)
                                <span class="inline-flex items-center rounded-md  px-2 py-1 text-xs font-medium ring-1  ring-inset {{App\Enums\RolesEnum::from($role)->badgeColor()}}">
                                    {{App\Enums\RolesEnum::from($role)->label()}}
                                </span>
                            @endforeach
                        </dd>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layout-admin>
