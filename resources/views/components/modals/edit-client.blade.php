@props(['client' => null])
{{--
    Modal para editar un cliente. Mismos campos y validaciones que client/edit.blade.php
    (que sigue existiendo): validación en vivo y reglas de ClientPutRequest en servidor.

    - En clients.index se monta sin cliente ($client = null) y se llena al hacer clic en el
      ícono de editar de una fila: ese botón dispara `request-edit-client` con los valores de
      la fila (ya están cargados, no hace falta otra petición) y el modal los pone y se abre.
    - En clients.show se monta con el cliente y el botón "Editar cliente" solo dispara
      `open-edit-client-modal`.
    - Si el servidor rechaza el formulario, Laravel regresa a la página de origen y el modal
      se reabre solo con los errores y el old(). El cliente se recupera de `_record`, casteado
      a entero, nunca usado como URL.
    Al cerrarlo sin guardar se descarta lo escrito (valores y foto vuelven a los guardados).
    Los ids llevan el prefijo `edit_` para no chocar con el modal de crear en la misma página.
--}}
@php
    $mine = old('_drawer') === 'client-edit';
    if (! $client && $mine && (int) old('_record')) {
        $client = \App\Models\Client::find((int) old('_record'));
    }
    $logoUrl = fn ($c) => route('clients.logos', ['filename' => str_replace('.', '_', str_replace('logos/', '', $c->image))]);
    $formAction = $client ? route('clients.update', ['client' => $client->id]) : '';
    $savedValues = collect(['company_name', 'trade_name', 'federal_tax_id', 'national_id', 'phone1', 'email'])
        ->mapWithKeys(fn ($field) => [$field => $client?->{$field} ?? ''])->all();
    $val = fn ($field) => $mine ? old($field, $client?->{$field}) : $client?->{$field};
    $inputClass = 'block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6';
    $labelClass = 'block text-sm/6 font-medium text-gray-900 dark:text-gray-50';
@endphp
<div
    class="relative z-90"
    x-data="{ open: {{ $mine && $errors->any() ? 'true' : 'false' }} }"
    x-init="$watch('open', value => { if (!value) window.dispatchEvent(new CustomEvent('edit-client-modal-closed')) })"
    x-on:open-edit-client-modal.window="open = true"
    x-on:keydown.escape.window="open = false"
    x-trap.inert.noscroll="open"
    x-cloak
    x-show="open"
    role="dialog"
    aria-modal="true"
    aria-labelledby="edit-client-title"
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
                class="relative flex max-h-[calc(100vh-3rem)] w-full flex-col overflow-hidden rounded-lg bg-white dark:bg-lits-blue-550 shadow-xl sm:max-w-2xl"
            >
                <form id="client-edit-form" action="{{ $formAction }}" method="POST" enctype="multipart/form-data" class="flex min-h-0 flex-1 flex-col">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_drawer" value="client-edit">
                    <input type="hidden" id="edit_record_id" name="_record" value="{{ $client?->id }}">

                    <div class="flex items-start justify-between gap-3 border-b border-gray-200 dark:border-lits-blue-450 px-6 py-5">
                        <div>
                            <h3 id="edit-client-title" class="text-base font-semibold text-gray-900 dark:text-gray-50">Editar cliente</h3>
                            <p id="edit_subtitle" class="text-xs text-gray-500 dark:text-gray-400">{{ $client?->company_name }}</p>
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
                                collect($errors->messages())->except(['company_name', 'trade_name', 'federal_tax_id', 'national_id', 'email', 'phone1'])->all()
                            );
                        @endphp
                        @if ($mine && $unmatchedErrors->any())
                            <div id="client-edit-errors">
                                <x-alerts.error :message="'Para editar el cliente soluciona los siguientes errores:'" :errors="$unmatchedErrors" class="mb-6" />
                            </div>
                        @endif

                        <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                            <div class="col-span-full flex items-center gap-x-8">
                                <img id="edit_image_preview" src="{{ $client ? $logoUrl($client) : '' }}" alt="" class="size-24 flex-none rounded-lg bg-gray-100 dark:bg-gray-800 object-contain outline -outline-offset-1 outline-black/5" />
                                <div>
                                    <label for="edit_image" class="inline-block rounded-md bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-50 shadow-xs inset-ring-1 inset-ring-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:cursor-pointer">Cambiar foto</label>
                                    <input accept="image/*" type="file" id="edit_image" name="image" title="" class="hidden" />
                                    <p class="mt-2 text-xs/5 text-gray-500 dark:text-gray-400">JPG, GIF o PNG. 1MB max.</p>
                                </div>
                            </div>

                            <div class="sm:col-span-6">
                                <label for="edit_company_name" class="{{ $labelClass }}">Razón social</label>
                                <div class="mt-2">
                                    <input id="edit_company_name" value="{{ $val('company_name') }}" name="company_name" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div class="sm:col-span-6">
                                <label for="edit_trade_name" class="{{ $labelClass }}">Nombre comercial</label>
                                <div class="mt-2">
                                    <input id="edit_trade_name" value="{{ $val('trade_name') }}" name="trade_name" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div class="sm:col-span-3">
                                <label for="edit_federal_tax_id" class="{{ $labelClass }}">RFC</label>
                                <div class="mt-2">
                                    <input id="edit_federal_tax_id" value="{{ $val('federal_tax_id') }}" name="federal_tax_id" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div class="sm:col-span-3">
                                <label for="edit_national_id" class="{{ $labelClass }}">CURP</label>
                                <div class="mt-2">
                                    <input id="edit_national_id" value="{{ $val('national_id') }}" name="national_id" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div class="sm:col-span-3">
                                <label for="edit_phone1" class="{{ $labelClass }}">Teléfono</label>
                                <div class="mt-2">
                                    <input id="edit_phone1" value="{{ $val('phone1') }}" name="phone1" type="text" autocomplete="off" class="{{ $inputClass }}">
                                </div>
                            </div>
                            <div class="sm:col-span-3">
                                <label for="edit_email" class="{{ $labelClass }}">Email</label>
                                <div class="mt-2">
                                    <input id="edit_email" value="{{ $val('email') }}" name="email" type="text" autocomplete="off" class="{{ $inputClass }}">
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
            const fields = ['company_name', 'trade_name', 'federal_tax_id', 'national_id', 'phone1', 'email'];
            const el = (name) => document.getElementById('edit_' + name);
            const form = document.getElementById('client-edit-form');
            const preview = el('image_preview');
            const fileInput = el('image');

            attachLiveValidation('edit_company_name', { required: true });
            attachLiveValidation('edit_trade_name', { required: true });
            attachLiveValidation('edit_email', { required: true, email: true });
            attachLiveValidation('edit_phone1', { required: true, numeric: true, min: 10 });
            // Sin reglas en el navegador: solo para pintar bajo su campo el error que devuelva el servidor.
            attachLiveValidation('edit_federal_tax_id');
            attachLiveValidation('edit_national_id');
            // Valida todo al enviar y pinta bajo su campo los errores del servidor (solo los de este modal).
            // Va antes del seguro de doble clic: si cancela el envío, ese seguro no debe bloquear el botón.
            enableFormValidation('client-edit-form', { errors: @json($mine ? $errors->messages() : (object) []) });

            fileInput.addEventListener('change', function () {
                const file = fileInput.files[0];
                preview.src = file ? URL.createObjectURL(file) : snapshot.image;
            });

            // Avoid saving twice on a double click.
            form.addEventListener('submit', function (e) {
                const submit = form.querySelector('button[type="submit"]');
                if (e.defaultPrevented) return;
                if (submit.disabled) { e.preventDefault(); return; }
                submit.disabled = true;
            });

            // What the server rendered (or the last row loaded from the list); closing
            // the modal without saving restores it.
            // Taken from the saved record, not from the DOM, which may hold old() input after a rejected save.
            const snapshot = @json($savedValues);
            snapshot.image = preview.getAttribute('src');

            const clearMarks = function () {
                form.querySelectorAll('[id$="-error"]').forEach(function (node) { node.classList.add('hidden'); });
                form.querySelectorAll('input[type="text"]').forEach(function (node) {
                    node.classList.remove('outline-red-500', 'dark:outline-red-500');
                    node.classList.add('outline-gray-300', 'dark:outline-gray-600');
                });
                const serverErrors = document.getElementById('client-edit-errors');
                if (serverErrors) { serverErrors.remove(); }
            };

            window.addEventListener('edit-client-modal-closed', function () {
                fields.forEach(function (field) { el(field).value = snapshot[field]; });
                preview.src = snapshot.image;
                fileInput.value = '';
                clearMarks();
            });

            // List page: fill the modal with the clicked row's values, then open it.
            window.addEventListener('request-edit-client', function (event) {
                const data = event.detail;
                form.action = data.update_url;
                el('record_id').value = data.id;
                document.getElementById('edit_subtitle').textContent = data.company_name;
                fields.forEach(function (field) {
                    el(field).value = data[field] == null ? '' : data[field];
                    snapshot[field] = el(field).value;
                });
                preview.src = data.image_url;
                snapshot.image = data.image_url;
                fileInput.value = '';
                clearMarks();
                window.dispatchEvent(new CustomEvent('open-edit-client-modal'));
            });
        })();
    </script>
@endpush
