@section('title', 'Nuevo usuario')
<x-layout-admin>
    <section>
        <x-navigation.breadcrumbs :links="['Usuarios' => route('users.index'), 'Nuevo usuario' => '#']" />
        <x-headings.without-action
            :title="'Nuevo usuario'"
        />
        @if ($errors->any())
            <x-alerts.error :message="'Para crear el usuario soluciona los siguientes errores:'" :errors="$errors" class="my-4" />
        @endif
        <form action="{{ route('users.store') }}" method="POST" class="mt-8">
            @csrf
            <div class="h-fit lg:col-span-2 divide-y divide-gray-200 dark:divide-gray-700 rounded bg-white dark:bg-lits-blue-550 shadow-lits-card">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-50">Nuevo usuario</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="space-y-12">
                        <div class="pb-12">
                            <h2 class="text-base/7 font-semibold text-gray-900 dark:text-gray-50">Datos del usuario</h2>
                            <p class="mt-1 text-sm/6 text-gray-600 dark:text-gray-400">Llena los datos relacionados con el usuario.</p>
                            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 lg:grid-cols-12">
                                <div class="sm:col-span-6">
                                    <label for="name" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Nombre</label>
                                    <div class="mt-2">
                                        <input id="name" value="{{old('name')}}" name="name" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="sm:col-span-6">
                                    <label for="email" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Email</label>
                                    <div class="mt-2">
                                        <input id="email" value="{{old('email')}}" name="email" type="email" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="sm:col-span-4">
                                    <label for="password" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Contraseña</label>
                                    <div class="mt-2">
                                        <input id="password" value="{{old('password')}}" name="password" type="password" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="sm:col-span-4">
                                    <label for="password-confirm" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Confirmar contraseña</label>
                                    <div class="mt-2">
                                        <input id="password-confirm" value="{{old('password_confirmation')}}" name="password_confirmation" type="password" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="sm:col-span-4">
                                    <label for="role_id" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Permiso</label>
                                    <div class="mt-2 grid grid-cols-1">
                                        <select id="role_id" name="role_id" autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                            @foreach($roles as $role)
                                                <option value='{{$role->id}}' @selected(old('role_id') == $role->id)>{{$role->name}}</option>
                                            @endforeach
                                        </select>
                                        <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 dark:text-gray-400 sm:text-sm"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-4 sm:px-6">
                    <div class="mt-6 flex items-center justify-end gap-x-6">
                        <a href="{{ route('users.index') }}" class="text-sm/6 font-semibold text-gray-900 dark:text-gray-50">Cancelar</a>
                        <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Crear usuario</button>
                    </div>
                </div>
            </div>
        </form>
    </section>
</x-layout-admin>
