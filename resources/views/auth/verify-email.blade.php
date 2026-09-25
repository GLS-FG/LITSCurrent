@section('title', 'Verificar correo electrónico')
<x-layout>
    <div class="flex min-h-full flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <img class="mx-auto h-20 w-auto" src="{{ asset('/images/lits.png') }}" alt="Lits" />
            <h2 class="mt-6 text-center text-2xl/9 font-bold tracking-tight text-gray-900 dark:text-gray-50">¡Gracias por registrarte en LITS!</h2>
        </div>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
            @if ($errors->any())
                <x-alerts.error :message="'Hubo un error al enviar el link:'" :errors="$errors" class="mb-4" />
            @endif
            @if(session()->has('success'))
                <x-alerts.success class="mb-4" :message="session('success')" />
            @endif
            <div class="bg-white dark:bg-lits-blue-550 px-6 py-12 shadow-sm sm:rounded-lg sm:px-12 space-y-6">
                <p class="text-gray-900 dark:text-gray-50">Antes de empezar, ¿podrías verificar tu dirección de correo electrónico haciendo clic en el enlace que te acabamos de enviar? Si no lo recibiste, con gusto te enviaremos otro.</p>
                <form action="{{ route('verification.send') }}" method="POST">
                    @csrf
                    <div>
                        <button type="submit" class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 hover:cursor-pointer">
                            Reenviar enlace de verificación
                        </button>
                    </div>
                </form>
                <form action="{{route('auth.destroy')}}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button class="font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 text-sm/6">
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layout>
