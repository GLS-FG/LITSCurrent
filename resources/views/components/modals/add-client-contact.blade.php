@props(['client'])
{{--
    Modal para crear un contacto del cliente (se abre desde "Crear contacto" de clients.show).
    Mismos campos y validaciones que client/user/create.blade.php (que sigue existiendo):
    validación en vivo (partials.live-validation) y las reglas de UserPostRequest en servidor.
    El permiso (role_id 6) va fijo, igual que en la página vieja.
    Si el servidor rechaza el formulario vuelve al detalle del cliente y el modal se reabre solo
    con los errores y el old() (las contraseñas nunca se reenvían). Al cerrarlo se descarta lo
    escrito. Los ids llevan el prefijo `cc_` para no chocar con los de otros modales de la página.
--}}
@php
    $mine = old('_drawer') === 'client-contact-create';
    $inputClass = 'block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6';
    $labelClass = 'block text-sm/6 font-medium text-gray-900 dark:text-gray-50';
@endphp
<div
    class="relative z-90"
    x-data="{ open: {{ $mine && $errors->any() ? 'true' : 'false' }} }"
    x-init="$watch('open', value => { if (!value) window.dispatchEvent(new CustomEvent('add-client-contact-modal-closed')) })"
    x-on:open-add-client-contact-modal.window="open = true"
    x-on:keydown.escape.window="open = false"
    x-trap.inert.noscroll="open"
    x-cloak
    x-show="open"
    role="dialog"
    aria-modal="true"
    aria-labelledby="add-client-contact-title"
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
                <form id="client-contact-create-form" action="{{ route('clients.users.store', ['client' => $client]) }}" method="POST" autocomplete="off" class="flex min-h-0 flex-1 flex-col">
                    @csrf
                    <input type="hidden" name="_drawer" value="client-contact-create">
                    <input type="hidden" name="role_id" value="6">

                    <div class="flex items-start justify-between gap-3 border-b border-gray-200 dark:border-lits-blue-450 px-6 py-5">
                        <div>
                            <h3 id="add-client-contact-title" class="text-base font-semibold text-gray-900 dark:text-gray-50">Nuevo contacto</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Llena los datos relacionados con el contacto de {{ $client->company_name }}.</p>
                        </div>
                        <button type="button" @click="open = false" class="rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                            <span class="sr-only">Cerrar</span>
                            <i class="fa-regular fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <div class="flex-1 min-h-0 overflow-y-auto px-6 py-5">
                        @if ($mine && $errors->any())
                            <div id="client-contact-create-errors">
                                <x-alerts.error :message="'Para crear el contacto soluciona los siguientes errores:'" :errors="$errors" class="mb-6" />
                            </div>
                        @endif

                        <div class="grid grid-cols-1 gap-x-6 gap-y-8">
                            <div>
                                <label for="cc_name" class="{{ $labelClass }}">Nombre</label>
                                <div class="mt-2">
                                    <input id="cc_name" value="{{ $mine ? old('name') : '' }}" name="name" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div>
                                <label for="cc_email" class="{{ $labelClass }}">Email</label>
                                <div class="mt-2">
                                    <input id="cc_email" value="{{ $mine ? old('email') : '' }}" name="email" type="email" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div>
                                <label for="cc_password" class="{{ $labelClass }}">Contraseña</label>
                                <div class="mt-2">
                                    <input id="cc_password" name="password" type="password" autocomplete="new-password" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div>
                                <label for="cc_password_confirmation" class="{{ $labelClass }}">Confirmar contraseña</label>
                                <div class="mt-2">
                                    <input id="cc_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" class="{{ $inputClass }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-lits-blue-450 px-6 py-4">
                        <button type="button" @click="open = false" class="text-sm font-semibold text-gray-900 dark:text-gray-50 hover:cursor-pointer">Cancelar</button>
                        <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Crear contacto</button>
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
            attachLiveValidation('cc_name', { required: true, max: 255 });
            attachLiveValidation('cc_email', { required: true, email: true });
            attachLiveValidation('cc_password', { required: true, min: 6 });
            attachLiveValidation('cc_password_confirmation', { required: true, match: 'cc_password', matchMessage: 'Las contraseñas no coinciden.', watch: ['cc_password'] });

            const form = document.getElementById('client-contact-create-form');

            // Avoid creating the contact twice on a double click.
            form.addEventListener('submit', function (e) {
                const submit = form.querySelector('button[type="submit"]');
                if (submit.disabled) { e.preventDefault(); return; }
                submit.disabled = true;
            });

            // Closing the modal without saving discards what was typed and clears the error marks.
            window.addEventListener('add-client-contact-modal-closed', function () {
                form.querySelectorAll('input[type="text"], input[type="email"], input[type="password"]').forEach(function (el) {
                    el.value = '';
                    el.classList.remove('outline-red-500', 'dark:outline-red-500');
                    el.classList.add('outline-gray-300', 'dark:outline-gray-600');
                });
                form.querySelectorAll('[id$="-error"]').forEach(function (el) { el.classList.add('hidden'); });
                const serverErrors = document.getElementById('client-contact-create-errors');
                if (serverErrors) { serverErrors.remove(); }
            });
        })();
    </script>
@endpush
