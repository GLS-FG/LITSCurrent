@props(['roles'])
{{--
    Modal para crear un usuario (se abre desde el botón "Nuevo usuario" de users.index).
    Mismos campos y validaciones que user/create.blade.php (que sigue existiendo):
    validación en vivo (partials.live-validation) y las reglas de UserPostRequest en servidor.
    Si el servidor rechaza el formulario vuelve a la lista y el modal se reabre solo con los
    errores y el old() capturado (las contraseñas nunca se reenvían). Al cerrarlo se descarta
    lo escrito.
--}}
@php
    $mine = old('_drawer') === 'user-create';
    $inputClass = 'block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6';
    $labelClass = 'block text-sm/6 font-medium text-gray-900 dark:text-gray-50';
@endphp
<div
    class="relative z-90"
    x-data="{ open: {{ $mine && $errors->any() ? 'true' : 'false' }} }"
    x-init="$watch('open', value => { if (!value) window.dispatchEvent(new CustomEvent('add-user-modal-closed')) })"
    x-on:open-add-user-modal.window="open = true"
    x-on:keydown.escape.window="open = false"
    x-trap.inert.noscroll="open"
    x-cloak
    x-show="open"
    role="dialog"
    aria-modal="true"
    aria-labelledby="add-user-title"
>
    <div
        class="fixed inset-0 bg-gray-500/75 dark:bg-gray-950/75 transition-opacity"
        aria-hidden="true"
        x-show="open"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="open = false"
    ></div>

    <div class="fixed inset-0 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 sm:items-center sm:p-6">
            <div
                x-show="open"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative flex max-h-[calc(100vh-3rem)] w-full flex-col overflow-hidden rounded-lg bg-white dark:bg-lits-blue-550 shadow-xl sm:max-w-xl"
            >
                <form id="user-create-form" action="{{ route('users.store') }}" method="POST" class="flex min-h-0 flex-1 flex-col">
                    @csrf
                    <input type="hidden" name="_drawer" value="user-create">

                    <div class="flex items-start justify-between gap-3 border-b border-gray-200 dark:border-lits-blue-450 px-6 py-5">
                        <div>
                            <h3 id="add-user-title" class="text-base font-semibold text-gray-900 dark:text-gray-50">Nuevo usuario</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Llena los datos relacionados con el usuario.</p>
                        </div>
                        <button type="button" @click="open = false" class="rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                            <span class="sr-only">Cerrar</span>
                            <i class="fa-regular fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <div class="flex-1 min-h-0 overflow-y-auto px-6 py-5">
                        @php
                            // Los errores de estos campos se muestran bajo su input; el aviso solo conserva los que no tienen campo.
                            $unmatchedErrors = new \Illuminate\Support\MessageBag(
                                collect($errors->messages())->except(['name', 'email', 'password', 'password_confirmation', 'role_id'])->all()
                            );
                        @endphp
                        @if ($mine && $unmatchedErrors->any())
                            <div id="user-create-errors">
                                <x-alerts.error :message="'Para crear el usuario soluciona los siguientes errores:'" :errors="$unmatchedErrors" class="mb-6" />
                            </div>
                        @endif

                        <div class="grid grid-cols-1 gap-x-6 gap-y-8">
                            <div>
                                <label for="name" class="{{ $labelClass }}">Nombre</label>
                                <div class="mt-2">
                                    <input id="name" value="{{ $mine ? old('name') : '' }}" name="name" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div>
                                <label for="email" class="{{ $labelClass }}">Email</label>
                                <div class="mt-2">
                                    <input id="email" value="{{ $mine ? old('email') : '' }}" name="email" type="email" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div>
                                <label for="password" class="{{ $labelClass }}">Contraseña</label>
                                <div class="mt-2">
                                    <input id="password" name="password" type="password" autocomplete="new-password" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div>
                                <label for="password-confirm" class="{{ $labelClass }}">Confirmar contraseña</label>
                                <div class="mt-2">
                                    <input id="password-confirm" name="password_confirmation" type="password" autocomplete="new-password" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div>
                                <label for="role_id" class="{{ $labelClass }}">Permiso</label>
                                <div class="mt-2 grid grid-cols-1">
                                    <select id="role_id" name="role_id" autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" @selected($mine && old('role_id') == $role->id)>{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                    <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 dark:text-gray-400 sm:text-sm"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-lits-blue-450 px-6 py-4">
                        <button type="button" @click="open = false" class="text-sm font-semibold text-gray-900 dark:text-gray-50 hover:cursor-pointer">Cancelar</button>
                        <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Crear usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('custom_script')
    @include('partials.live-validation')
    <script type="module">
        (function () {
            attachLiveValidation('name', { required: true, max: 255 });
            attachLiveValidation('email', { required: true, email: true });
            attachLiveValidation('password', { required: true, min: 6 });
            attachLiveValidation('password-confirm', { required: true, match: 'password', matchMessage: 'Las contraseñas no coinciden.', watch: ['password'] });
            // Sin reglas en el navegador: solo para pintar bajo su campo el error que devuelva el servidor.
            attachLiveValidation('role_id');
            // Valida todo al enviar y pinta bajo su campo los errores del servidor (solo los de este modal).
            // Va antes del seguro de doble clic: si cancela el envío, ese seguro no debe bloquear el botón.
            enableFormValidation('user-create-form', { errors: @json($mine ? $errors->messages() : (object) []) });

            const form = document.getElementById('user-create-form');

            // Avoid creating the user twice on a double click.
            form.addEventListener('submit', function (e) {
                const submit = form.querySelector('button[type="submit"]');
                if (e.defaultPrevented) return;
                if (submit.disabled) { e.preventDefault(); return; }
                submit.disabled = true;
            });

            // Closing the modal without saving discards what was typed: empty every
            // field, put the role back on its first option and clear the error marks.
            window.addEventListener('add-user-modal-closed', function () {
                form.querySelectorAll('input[type="text"], input[type="email"], input[type="password"]').forEach(function (el) {
                    el.value = '';
                    el.classList.remove('outline-red-500', 'dark:outline-red-500');
                    el.classList.add('outline-gray-300', 'dark:outline-gray-600');
                });
                form.querySelector('#role_id').selectedIndex = 0;
                form.querySelectorAll('[id$="-error"]').forEach(function (el) { el.classList.add('hidden'); });
                const serverErrors = document.getElementById('user-create-errors');
                if (serverErrors) { serverErrors.remove(); }
            });
        })();
    </script>
@endpush
