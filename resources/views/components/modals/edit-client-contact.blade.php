@props(['client', 'user'])
{{--
    Modal para editar un contacto del cliente (se abre desde "Editar contacto" de
    clients.users.show). Mismos campos y validaciones que client/user/edit.blade.php (que sigue
    existiendo): validación en vivo y las reglas de UserPutRequest en servidor.
    A diferencia de edit-user NO tiene selector de permiso: un contacto es siempre rol Client y
    el select de edit-user no lista ese rol, así que guardar desde ahí se lo cambiaría. Se manda
    el rol actual del contacto en un campo oculto (el controlador exige role_id) y no se manda
    client_id, así que no cambia de cliente.
    La contraseña es opcional: vacía = no se cambia.
    Si el servidor rechaza el formulario vuelve a la página del contacto y el modal se reabre solo
    con los errores y el old() (las contraseñas no se reenvían). Al cerrarlo sin guardar se
    descarta lo escrito. Los ids llevan el prefijo `ec_`.
--}}
@php
    $mine = old('_drawer') === 'client-contact-edit';
    $primaryRole = $user->roles->pluck('id')->first();
    $val = fn ($field) => $mine ? old($field, $user->{$field}) : $user->{$field};
    $inputClass = 'block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6';
    $labelClass = 'block text-sm/6 font-medium text-gray-900 dark:text-gray-50';
@endphp
<div
    class="relative z-90"
    x-data="{ open: {{ $mine && $errors->any() ? 'true' : 'false' }} }"
    x-init="$watch('open', value => { if (!value) window.dispatchEvent(new CustomEvent('edit-client-contact-modal-closed')) })"
    x-on:open-edit-client-contact-modal.window="open = true"
    x-on:keydown.escape.window="open = false"
    x-trap.inert.noscroll="open"
    x-cloak
    x-show="open"
    role="dialog"
    aria-modal="true"
    aria-labelledby="edit-client-contact-title"
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
                <form id="client-contact-edit-form" action="{{ route('clients.users.update', ['client' => $client, 'user' => $user]) }}" method="POST" autocomplete="off" class="flex min-h-0 flex-1 flex-col">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_drawer" value="client-contact-edit">
                    <input type="hidden" name="role_id" value="{{ $primaryRole }}">

                    <div class="flex items-start justify-between gap-3 border-b border-gray-200 dark:border-lits-blue-450 px-6 py-5">
                        <div>
                            <h3 id="edit-client-contact-title" class="text-base font-semibold text-gray-900 dark:text-gray-50">Editar contacto</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->name }} · {{ $client->company_name }}</p>
                        </div>
                        <button type="button" @click="open = false" class="rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                            <span class="sr-only">Cerrar</span>
                            <i class="fa-regular fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <div class="flex-1 min-h-0 overflow-y-auto px-6 py-5">
                        @if ($mine && $errors->any())
                            <div id="client-contact-edit-errors">
                                <x-alerts.error :message="'Para editar el contacto soluciona los siguientes errores:'" :errors="$errors" class="mb-6" />
                            </div>
                        @endif

                        <div class="grid grid-cols-1 gap-x-6 gap-y-8">
                            <div>
                                <label for="ec_name" class="{{ $labelClass }}">Nombre</label>
                                <div class="mt-2">
                                    <input id="ec_name" value="{{ $val('name') }}" name="name" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div>
                                <label for="ec_email" class="{{ $labelClass }}">Email</label>
                                <div class="mt-2">
                                    <input id="ec_email" value="{{ $val('email') }}" name="email" type="email" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div>
                                <label for="ec_password" class="{{ $labelClass }}">Nueva contraseña</label>
                                <div class="mt-2">
                                    <input id="ec_password" name="password" type="password" autocomplete="new-password" placeholder="Déjala vacía para conservar la actual" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div>
                                <label for="ec_password_confirmation" class="{{ $labelClass }}">Confirmar contraseña</label>
                                <div class="mt-2">
                                    <input id="ec_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" class="{{ $inputClass }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-lits-blue-450 px-6 py-4">
                        <button type="button" @click="open = false" class="text-sm font-semibold text-gray-900 dark:text-gray-50 hover:cursor-pointer">Cancelar</button>
                        <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Guardar</button>
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
            const form = document.getElementById('client-contact-edit-form');
            const el = (name) => document.getElementById('ec_' + name);
            const fields = ['name', 'email'];

            attachLiveValidation('ec_name', { required: true, max: 255 });
            attachLiveValidation('ec_email', { required: true, email: true });
            attachLiveValidation('ec_password', { min: 6 });
            attachLiveValidation('ec_password_confirmation', {
                required: true,
                match: 'ec_password',
                matchMessage: 'Las contraseñas no coinciden.',
                when: () => el('password').value !== '' || el('password_confirmation').value !== '',
                watch: ['ec_password']
            });

            // Avoid saving twice on a double click.
            form.addEventListener('submit', function (e) {
                const submit = form.querySelector('button[type="submit"]');
                if (submit.disabled) { e.preventDefault(); return; }
                submit.disabled = true;
            });

            // What the server rendered: closing the modal without saving restores it.
            const snapshot = {};
            fields.forEach(function (field) { snapshot[field] = @json(['name' => $user->name, 'email' => $user->email])[field]; });

            window.addEventListener('edit-client-contact-modal-closed', function () {
                fields.forEach(function (field) { el(field).value = snapshot[field]; });
                el('password').value = '';
                el('password_confirmation').value = '';
                form.querySelectorAll('[id$="-error"]').forEach(function (node) { node.classList.add('hidden'); });
                form.querySelectorAll('input[type="text"], input[type="email"], input[type="password"]').forEach(function (node) {
                    node.classList.remove('outline-red-500', 'dark:outline-red-500');
                    node.classList.add('outline-gray-300', 'dark:outline-gray-600');
                });
                const serverErrors = document.getElementById('client-contact-edit-errors');
                if (serverErrors) { serverErrors.remove(); }
            });
        })();
    </script>
@endpush
