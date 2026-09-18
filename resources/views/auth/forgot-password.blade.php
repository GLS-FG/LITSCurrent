@section('title', 'Olvidé mi contraseña')
<x-layout>
    <div class="flex min-h-full flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <img class="mx-auto h-20 w-auto" src="{{ asset('/images/lits.png') }}" alt="Lits" />
            <h2 class="mt-6 text-center text-2xl/9 font-bold tracking-tight text-gray-900">¿Olvidó su contraseña?</h2>
        </div>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
            @if ($errors->any())
                <x-alerts.error :message="'Hubo un error al enviar el link:'" :errors="$errors" class="mb-4" />
            @endif
            @if(session()->has('success'))
                <x-alerts.success class="mb-4" :message="session('success')" />
            @endif
            <div class="bg-white px-6 py-12 shadow-sm sm:rounded-lg sm:px-12">
                <form class="space-y-6" action="{{ route('password.email') }}" method="POST">
                    @csrf

                    <p class="text-gray-900">Ingrese su email y le enviaremos un link para reiniciar su contraseña.</p>

                    <div>
                        <label for="email" class="block text-sm/6 font-medium text-gray-900">Email</label>
                        <div class="mt-2">
                            <input type="email" name="email" id="email" value="{{old('email')}}" autocomplete="email" required class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 hover:cursor-pointer">Enviar link</button>
                    </div>

                    <div>
                        <a href="{{route('login')}}" class="font-semibold text-indigo-600 hover:text-indigo-500 text-sm/6">Regresar a inicio de sesión</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>
