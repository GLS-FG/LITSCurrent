@section('title', 'Nuevo contacto')
<x-layout-admin>
    <section>
        <x-navigation.breadcrumbs :links="['Clientes' => route('clients.index'), $client->company_name => route('clients.show', ['client' => $client]), 'Nuevo usuario' => '#']" />
        <x-headings.without-action
            :title="'Nuevo contacto'"
        />
        @if ($errors->any())
            <x-alerts.error :message="'Para crear el contacto soluciona los siguientes errores:'" :errors="$errors" class="my-4" />
        @endif
        <form action="{{ route('clients.users.store', ['client' => $client]) }}" method="POST" class="mt-8">
            @csrf
            <div class="h-fit lg:col-span-2 divide-y divide-gray-200 rounded bg-white shadow-lits-card">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-semibold text-gray-900">Nuevo contacto</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="space-y-12">
                        <div class="pb-12">
                            <h2 class="text-base/7 font-semibold text-gray-900">Datos del contacto</h2>
                            <p class="mt-1 text-sm/6 text-gray-600">Llena los datos relacionados con el contacto.</p>
                            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 lg:grid-cols-12">
                                <input id="role_id" value="6" name="role_id" type="hidden" />
                                <div class="sm:col-span-6">
                                    <label for="name" class="block text-sm/6 font-medium text-gray-900">Nombre</label>
                                    <div class="mt-2">
                                        <input id="name" value="{{old('name')}}" name="name" type="text" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="sm:col-span-6">
                                    <label for="email" class="block text-sm/6 font-medium text-gray-900">Email</label>
                                    <div class="mt-2">
                                        <input id="email" value="{{old('email')}}" name="email" type="email" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="sm:col-span-4">
                                    <label for="password" class="block text-sm/6 font-medium text-gray-900">Contraseña</label>
                                    <div class="mt-2">
                                        <input id="password" value="{{old('password')}}" name="password" type="password" autocomplete="new-password" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="sm:col-span-4">
                                    <label for="password-confirm" class="block text-sm/6 font-medium text-gray-900">Confirmar contraseña</label>
                                    <div class="mt-2">
                                        <input id="password-confirm" value="{{old('password_confirmation')}}" name="password_confirmation" type="password" autocomplete="new-password" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-4 sm:px-6">
                    <div class="mt-6 flex items-center justify-end gap-x-6">
                        <a href="{{ route('clients.show', ['client' => $client]) }}" class="text-sm/6 font-semibold text-gray-900">Cancelar</a>
                        <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Crear contacto</button>
                    </div>
                </div>
            </div>
        </form>
    </section>
</x-layout-admin>
