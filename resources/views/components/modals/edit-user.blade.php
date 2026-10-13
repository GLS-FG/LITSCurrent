@props(['roles', 'user' => null])
{{--
    Modal para editar un usuario. Mismos campos y validaciones que user/edit.blade.php
    (que sigue existiendo): validación en vivo y reglas de UserPutRequest en servidor.

    - En users.index se monta sin usuario ($user = null) y se llena al hacer clic en el ícono
      de editar de una fila: ese botón dispara `request-edit-user` con los valores de la fila
      (ya están cargados, no hace falta otra petición) y el modal los pone y se abre.
    - En users.show se monta con el usuario y el botón "Editar usuario" solo dispara
      `open-edit-user-modal`.
    - Si el servidor rechaza el formulario, Laravel regresa a la página de origen y el modal
      se reabre solo con los errores y el old() (las contraseñas no se reenvían). El usuario
      se recupera de `_record`, casteado a entero, nunca usado como URL.
    La contraseña es opcional al editar: vacía = no se cambia.
    Al cerrarlo sin guardar se descarta lo escrito. Los ids llevan el prefijo `edit_` para no
    chocar con el modal de crear en la misma página.
--}}
@php
    $mine = old('_drawer') === 'user-edit';
    if (! $user && $mine && (int) old('_record')) {
        $user = \App\Models\User::find((int) old('_record'));
    }
    $primaryRole = $user?->roles->pluck('id')->first();
    $formAction = $user ? route('users.update', ['user' => $user->id]) : '';
    $val = fn ($field) => $mine ? old($field, $user?->{$field}) : $user?->{$field};
    $selectedRole = $mine ? old('role_id', $primaryRole) : $primaryRole;
    $inputClass = 'block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6';
    $labelClass = 'block text-sm/6 font-medium text-gray-900 dark:text-gray-50';
@endphp
<div
    class="relative z-90"
    x-data="{ open: {{ $mine && $errors->any() ? 'true' : 'false' }} }"
    x-init="$watch('open', value => { if (!value) window.dispatchEvent(new CustomEvent('edit-user-modal-closed')) })"
    x-on:open-edit-user-modal.window="open = true"
    x-on:keydown.escape.window="open = false"
    x-trap.inert.noscroll="open"
    x-cloak
    x-show="open"
    role="dialog"
    aria-modal="true"
    aria-labelledby="edit-user-title"
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
                <form id="user-edit-form" action="{{ $formAction }}" method="POST" class="flex min-h-0 flex-1 flex-col">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_drawer" value="user-edit">
                    <input type="hidden" id="edit_record_id" name="_record" value="{{ $user?->id }}">

                    <div class="flex items-start justify-between gap-3 border-b border-gray-200 dark:border-lits-blue-450 px-6 py-5">
                        <div>
                            <h3 id="edit-user-title" class="text-base font-semibold text-gray-900 dark:text-gray-50">Editar usuario</h3>
                            <p id="edit_subtitle" class="text-xs text-gray-500 dark:text-gray-400">{{ $user?->name }}</p>
                        </div>
                        <button type="button" @click="open = false" class="rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                            <span class="sr-only">Cerrar</span>
                            <i class="fa-regular fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <div class="flex-1 min-h-0 overflow-y-auto px-6 py-5">
                        @if ($mine && $errors->any())
                            <div id="user-edit-errors">
                                <x-alerts.error :message="'Para editar el usuario soluciona los siguientes errores:'" :errors="$errors" class="mb-6" />
                            </div>
                        @endif

                        <div class="grid grid-cols-1 gap-x-6 gap-y-8">
                            <div>
                                <label for="edit_name" class="{{ $labelClass }}">Nombre</label>
                                <div class="mt-2">
                                    <input id="edit_name" value="{{ $val('name') }}" name="name" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div>
                                <label for="edit_email" class="{{ $labelClass }}">Email</label>
                                <div class="mt-2">
                                    <input id="edit_email" value="{{ $val('email') }}" name="email" type="email" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div>
                                <label for="edit_password" class="{{ $labelClass }}">Contraseña</label>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Déjala en blanco para conservar la actual.</p>
                                <div class="mt-2">
                                    <input id="edit_password" name="password" type="password" autocomplete="new-password" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div>
                                <label for="edit_password-confirm" class="{{ $labelClass }}">Confirmar contraseña</label>
                                <div class="mt-2">
                                    <input id="edit_password-confirm" name="password_confirmation" type="password" autocomplete="new-password" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div>
                                <label for="edit_role_id" class="{{ $labelClass }}">Permiso</label>
                                <div class="mt-2 grid grid-cols-1">
                                    <select id="edit_role_id" name="role_id" autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" @selected($selectedRole == $role->id)>{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                    <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 dark:text-gray-400 sm:text-sm"></i>
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
            const fields = ['name', 'email'];
            const el = (name) => document.getElementById('edit_' + name);
            const form = document.getElementById('user-edit-form');

            attachLiveValidation('edit_name', { required: true, max: 255 });
            attachLiveValidation('edit_email', { required: true, email: true });
            attachLiveValidation('edit_password', { min: 6 });
            attachLiveValidation('edit_password-confirm', {
                required: true,
                match: 'edit_password',
                matchMessage: 'Las contraseñas no coinciden.',
                when: () => el('password').value !== '' || el('password-confirm').value !== '',
                watch: ['edit_password']
            });

            // Avoid saving twice on a double click.
            form.addEventListener('submit', function (e) {
                const submit = form.querySelector('button[type="submit"]');
                if (submit.disabled) { e.preventDefault(); return; }
                submit.disabled = true;
            });

            // What the server rendered (or the last row loaded from the list); closing
            // the modal without saving restores it.
            const snapshot = { role_id: el('role_id').value };
            fields.forEach(function (field) { snapshot[field] = el(field).value; });

            const clearMarks = function () {
                form.querySelectorAll('[id$="-error"]').forEach(function (node) { node.classList.add('hidden'); });
                form.querySelectorAll('input[type="text"], input[type="email"], input[type="password"]').forEach(function (node) {
                    node.classList.remove('outline-red-500', 'dark:outline-red-500');
                    node.classList.add('outline-gray-300', 'dark:outline-gray-600');
                });
                const serverErrors = document.getElementById('user-edit-errors');
                if (serverErrors) { serverErrors.remove(); }
            };

            window.addEventListener('edit-user-modal-closed', function () {
                fields.forEach(function (field) { el(field).value = snapshot[field]; });
                el('role_id').value = snapshot.role_id;
                el('password').value = '';
                el('password-confirm').value = '';
                clearMarks();
            });

            // List page: fill the modal with the clicked row's values, then open it.
            window.addEventListener('request-edit-user', function (event) {
                const data = event.detail;
                form.action = data.update_url;
                el('record_id').value = data.id;
                document.getElementById('edit_subtitle').textContent = data.name;
                fields.forEach(function (field) {
                    el(field).value = data[field] == null ? '' : data[field];
                    snapshot[field] = el(field).value;
                });
                el('role_id').value = data.role_id == null ? '' : data.role_id;
                snapshot.role_id = el('role_id').value;
                el('password').value = '';
                el('password-confirm').value = '';
                clearMarks();
                window.dispatchEvent(new CustomEvent('open-edit-user-modal'));
            });
        })();
    </script>
@endpush
