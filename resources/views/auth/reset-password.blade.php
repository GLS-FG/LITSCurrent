@section('title', 'Restablecer contraseña')
<x-layout>
    <div class="flex min-h-full flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <img class="mx-auto h-20 w-auto" src="{{ asset('/images/lits.png') }}" alt="Lits" />
            <h2 class="mt-6 text-center text-2xl/9 font-bold tracking-tight text-gray-900 dark:text-gray-50">Restablezca su contraseña</h2>
        </div>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
            @if ($errors->any())
                <x-alerts.error :message="'Hubo un error al restablecer la contraseña:'" :errors="$errors" class="mb-4" />
            @endif
            @if(session()->has('success'))
                <x-alerts.success class="mb-4" :message="session('success')" />
            @endif
            <div class="bg-white dark:bg-lits-blue-550 px-6 py-12 shadow-sm sm:rounded-lg sm:px-12">
                <form class="space-y-6" action="{{ route('password.update') }}" method="POST">
                    @csrf
                    <input id="token" value="{{$token}}" name="token" type="hidden" />

                    <div>
                        <label for="email" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Email</label>
                        <div class="mt-2">
                            <input type="email" name="email" id="email" readonly value="{{request('email')}}" autocomplete="email" required class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Contraseña</label>
                        <div class="mt-2">
                            <input type="password" name="password" id="password" value="{{old('password')}}" autocomplete="new-password" required class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Confirmar contraseña</label>
                        <div class="mt-2">
                            <input type="password" name="password_confirmation" id="password_confirmation" value="{{old('password_confirmation')}}" autocomplete="new-password" required class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 hover:cursor-pointer">Restablecer contraseña</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>
