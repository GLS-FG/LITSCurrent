@section('title', 'Nuevo estatus de servicio')
<x-layout-admin>
    <section>
        <x-navigation.breadcrumbs :links="['Estatus Servicios' => route('service-statuses.index'), $serviceType->code => route('service-statuses.service-type-statuses.index', ['service_status' => $serviceType]), 'Nuevo estatus' => '#']" />
        <x-headings.without-action
            :title="'Nuevo estatus de servicio'"
        />
        @if ($errors->any())
            <x-alerts.error :message="'Para crear el estatus soluciona los siguientes errores:'" :errors="$errors" class="my-4" />
        @endif
        <form
            action="{{ route('service-statuses.service-type-statuses.store', ['service_status' => $serviceType]) }}"
            method="POST"
            autocomplete="off"
            class="mt-8"
            x-data="{
                name: @js(old('name', '')),
                color: @js(old('color', 'bg-gray-50 text-gray-600 inset-ring-gray-500/10')),
                count: 0,
                total: {{ $serviceType->serviceClasses->sum(fn ($class) => $class->serviceModes->count()) }},
                boxes() { return [...this.$root.querySelectorAll('input[data-mode]')]; },
                sync() { this.count = this.boxes().filter(b => b.checked).length; },
                toggleAll(checked) { this.boxes().forEach(b => b.checked = checked); this.sync(); },
            }"
            x-init="sync()"
        >
            @csrf
            <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550">
                <div class="grid grid-cols-1 gap-x-10 gap-y-8 p-6 lg:grid-cols-2">
                    {{-- Nombre y color --}}
                    <div class="min-w-0 space-y-8">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-900 dark:text-gray-50">Nombre</label>
                            <input id="name" name="name" type="text" autocomplete="off" x-model="name" class="mt-2 block w-full rounded-lg border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-600 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-50 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-1 focus:outline-indigo-600">
                        </div>
                        <fieldset>
                            <legend class="text-sm font-medium text-gray-900 dark:text-gray-50">Elige un color</legend>
                            <div class="mt-3 flex flex-wrap gap-3">
                                @php
                                    $colorOptions = [
                                        'GRIS'     => ['bg-gray-50 text-gray-600 inset-ring-gray-500/10',         'bg-gray-50 dark:bg-gray-800/60 text-gray-600 dark:text-gray-400 inset-ring-gray-500/10'],
                                        'ROJO'     => ['bg-red-50 text-red-700 inset-ring-red-600/10',            'bg-red-50 dark:bg-red-500/10 text-red-700 dark:text-red-400 inset-ring-red-600/10'],
                                        'AMARILLO' => ['bg-yellow-50 text-yellow-800 inset-ring-yellow-600/20',   'bg-yellow-50 dark:bg-yellow-500/10 text-yellow-800 dark:text-yellow-400 inset-ring-yellow-600/20'],
                                        'VERDE'    => ['bg-green-50 text-green-700 inset-ring-green-600/20',      'bg-green-50 dark:bg-green-500/10 text-green-700 dark:text-green-400 inset-ring-green-600/20'],
                                        'AZUL'     => ['bg-blue-50 text-blue-700 inset-ring-blue-700/10',         'bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 inset-ring-blue-700/10'],
                                        'INDIGO'   => ['bg-indigo-50 text-indigo-700 inset-ring-indigo-700/10',   'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 inset-ring-indigo-700/10'],
                                        'PURPURA'  => ['bg-purple-50 text-purple-700 inset-ring-purple-700/10',   'bg-purple-50 dark:bg-purple-500/10 text-purple-700 dark:text-purple-400 inset-ring-purple-700/10'],
                                        'ROSA'     => ['bg-pink-50 text-pink-700 inset-ring-pink-700/10',         'bg-pink-50 dark:bg-pink-500/10 text-pink-700 dark:text-pink-400 inset-ring-pink-700/10'],
                                    ];
                                @endphp
                                @foreach($colorOptions as $label => [$value, $swatchClasses])
                                    <label class="group relative flex cursor-pointer items-center justify-center rounded-md p-0.5 has-checked:outline-2 has-checked:outline-indigo-600 has-focus-visible:outline-2 has-focus-visible:outline-offset-2 has-focus-visible:outline-indigo-600">
                                        <input type="radio" name="color" value="{{ $value }}" x-model="color" @checked(old('color', 'bg-gray-50 text-gray-600 inset-ring-gray-500/10') === $value) class="absolute inset-0 appearance-none focus:outline-none" />
                                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium inset-ring {{ $swatchClasses }}"><i class="fa-solid fa-check mr-1 hidden group-has-checked:inline"></i>{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <div class="mt-4 flex items-center gap-3 rounded-lg border border-dashed border-gray-200 dark:border-lits-blue-450 px-4 py-3">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Vista previa</span>
                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium inset-ring" :class="color" x-text="name || '—'"></span>
                            </div>
                        </fieldset>
                    </div>

                    {{-- Service Modes --}}
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-end justify-between gap-x-4 gap-y-1">
                            <div>
                                <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-50">Service Modes</h2>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Selecciona los service modes a los que aplica el estatus.</p>
                            </div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-50" aria-live="polite"><span x-text="count"></span> de <span x-text="total"></span> seleccionados</p>
                        </div>
                        <div class="mt-3 overflow-x-auto rounded-xl border border-gray-200 dark:border-lits-blue-450">
                            <table class="min-w-full">
                                <thead>
                                <tr class="border-b border-gray-200 dark:border-lits-blue-450">
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">Service Class</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">Service Mode</th>
                                    <th scope="col" class="px-4 py-3 text-right text-xs font-semibold whitespace-nowrap text-gray-400 dark:text-gray-500">
                                        <label class="inline-flex cursor-pointer items-center gap-2">
                                            Todos
                                            <input type="checkbox" :checked="count === total" @change="toggleAll($event.target.checked)" class="size-4 cursor-pointer rounded-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-lits-blue-600 accent-indigo-600">
                                        </label>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($serviceType->serviceClasses as $serviceClass)
                                    @php $lastClass = $loop->last; @endphp
                                    @foreach($serviceClass->serviceModes as $serviceMode)
                                        @php $lastRow = $lastClass && $loop->last; @endphp
                                        <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.03]">
                                            @if($loop->first)
                                                <th scope="rowgroup" rowspan="{{ count($serviceClass->serviceModes) }}" class="px-4 py-3 text-left align-middle whitespace-nowrap {{ $lastClass ? '' : 'border-b border-gray-200 dark:border-lits-blue-450' }}">
                                                    <span class="rounded-md bg-gray-100 dark:bg-white/10 px-2 py-0.5 text-xs font-semibold text-gray-900 dark:text-gray-50">{{ $serviceClass->code }}</span>
                                                </th>
                                            @endif
                                            <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-gray-50 {{ $lastRow ? '' : ($loop->last ? 'border-b border-gray-200 dark:border-lits-blue-450' : 'border-b border-gray-100 dark:border-lits-blue-450/40') }}">{{ $serviceMode->code }}</td>
                                            <td class="px-4 py-3 text-right {{ $lastRow ? '' : ($loop->last ? 'border-b border-gray-200 dark:border-lits-blue-450' : 'border-b border-gray-100 dark:border-lits-blue-450/40') }}">
                                                <input id="mode-{{ $serviceMode->id }}" data-mode type="checkbox" value="{{ $serviceMode->id }}" name="permissions[]" @checked(in_array($serviceMode->id, old('permissions', []))) @change="sync()" aria-label="{{ $serviceClass->code }} {{ $serviceMode->code }}" class="size-4 cursor-pointer rounded-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-lits-blue-600 accent-indigo-600">
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-x-6 border-t border-gray-200 dark:border-lits-blue-450 bg-gray-50 dark:bg-white/[0.02] px-6 py-4">
                    <a href="{{ route('service-statuses.service-type-statuses.index', ['service_status' => $serviceType]) }}" class="text-sm font-semibold text-gray-900 dark:text-gray-50 hover:underline">Cancelar</a>
                    <button type="submit" class="rounded-lg bg-lits-red-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Guardar</button>
                </div>
            </div>
        </form>
    </section>
</x-layout-admin>
