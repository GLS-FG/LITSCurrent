@props(['fields', 'action', 'ariaLabel', 'keepUrgent' => false])

@php
    // Barra de búsqueda con chips, compartida por Órdenes, Embarques, Aduanas y Almacenes.
    // $fields: clave => ['param' => nombre del parámetro de la URL, 'label' => ..., 'hint' => ...].
    // El primer campo es el que se usa cuando se envía texto pendiente sin elegir uno.
    $searchChips = collect($fields)
        ->map(fn ($field, $key) => ['f' => $key, 'v' => (string) request($field['param'])])
        ->filter(fn ($chip) => $chip['v'] !== '')
        ->values();
    $searchStrings = [
        'searchIn' => __('indexes.search_in'),
        'replaces' => __('indexes.search_replaces'),
        'remove' => __('indexes.search_remove'),
    ];
@endphp
<div
    class="mt-5"
    x-init="restoreFocus()"
    x-data="{
        fields: @js($fields),
        chips: @js($searchChips),
        t: @js($searchStrings),
        urgent: @js((bool) $keepUrgent),
        text: '',
        open: false,
        idx: 0,
        keys() { return Object.keys(this.fields); },
        markFocus() { try { sessionStorage.setItem('listSearchFocus', String(Date.now())); } catch (e) {} },
        restoreFocus() {
            try {
                const t = Number(sessionStorage.getItem('listSearchFocus'));
                sessionStorage.removeItem('listSearchFocus');
                if (t && Date.now() - t < 10000) this.$nextTick(() => this.$refs.q.focus());
            } catch (e) {}
        },
        replaces(f) { return this.chips.some(c => c.f === f); },
        add(f) {
            const v = this.text.trim();
            if (!v) return;
            this.chips = this.chips.filter(c => c.f !== f);
            this.chips.push({ f: f, v: v });
            this.text = '';
            this.idx = 0;
            this.open = false;
        },
        remove(i) { this.chips.splice(i, 1); },
        choose(f) {
            this.add(f);
            this.markFocus();
            this.$nextTick(() => this.$refs.form.submit());
        },
        removeAndSearch(i) {
            this.remove(i);
            this.markFocus();
            const params = new URLSearchParams();
            this.chips.forEach(c => params.append(this.fields[c.f].param, c.v));
            if (this.urgent) params.set('urgent', '1');
            const query = params.toString();
            window.location.href = this.$refs.form.getAttribute('action') + (query ? '?' + query : '');
        },
        submit() {
            if (this.text.trim()) this.add(this.keys()[0]);
            this.markFocus();
            this.$nextTick(() => this.$refs.form.submit());
        },
    }"
>
    <form x-ref="form" method="GET" action="{{ $action }}" @submit.prevent="submit()" class="flex flex-col md:flex-row gap-2">
        <template x-for="c in chips" :key="c.f">
            <input type="hidden" :name="fields[c.f].param" :value="c.v">
        </template>
        {{-- Conserva la pestaña Urgentes al buscar. --}}
        @if($keepUrgent)
            <input type="hidden" name="urgent" value="1">
        @endif

        <div class="relative flex-1 min-w-0 flex flex-wrap items-center gap-1.5 rounded-lg bg-white dark:bg-lits-blue-550 border border-gray-200 dark:border-lits-blue-450 px-3.5 py-2 focus-within:outline-2 focus-within:-outline-offset-1 focus-within:outline-indigo-600">
            <i class="fa-regular fa-magnifying-glass text-gray-400 dark:text-gray-500"></i>
            <template x-for="(c, i) in chips" :key="c.f">
                <span class="inline-flex max-w-full items-center gap-1.5 rounded-md bg-entity-orders-50 dark:bg-entity-orders/15 text-entity-orders dark:text-indigo-400 py-0.5 pr-1 pl-2 text-sm">
                    <span class="font-semibold" x-text="fields[c.f].label + ':'"></span>
                    <span class="break-all" x-text="c.v"></span>
                    <button type="button" @click="removeAndSearch(i)" :aria-label="t.remove + ' ' + fields[c.f].label + ' ' + c.v" class="inline-flex size-5 items-center justify-center rounded text-entity-orders dark:text-indigo-400 hover:bg-black/10 hover:cursor-pointer">
                        <i class="fa-regular fa-xmark text-xs"></i>
                    </button>
                </span>
            </template>
            <input
                x-ref="q"
                type="text"
                x-model="text"
                autocomplete="off"
                placeholder="{{ __('indexes.search_placeholder') }}"
                aria-label="{{ $ariaLabel }}"
                @input="idx = 0; open = text.trim() !== ''"
                @keydown.arrow-down.prevent="if (open) idx = (idx + 1) % keys().length"
                @keydown.arrow-up.prevent="if (open) idx = (idx + keys().length - 1) % keys().length"
                @keydown.enter.prevent="text.trim() ? choose(keys()[idx]) : submit()"
                @keydown.escape="open = false"
                @keydown.backspace="if (!text && chips.length) removeAndSearch(chips.length - 1)"
                @blur="open = false"
                class="flex-1 min-w-32 border-0 bg-transparent p-0 py-1 text-sm text-gray-900 dark:text-gray-50 placeholder:text-gray-400 dark:placeholder:text-gray-500 outline-none"
            >

            <div x-cloak x-show="open" x-transition.opacity.duration.100ms @mousedown.prevent role="listbox" class="absolute inset-x-0 top-full z-20 mt-1.5 rounded-lg border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 p-1 shadow-lg">
                <div class="px-2.5 pt-1.5 pb-1 text-xs text-gray-400 dark:text-gray-500" x-text="t.searchIn.replace(':term', text.trim())"></div>
                <template x-for="(k, n) in keys()" :key="k">
                    <button
                        type="button"
                        role="option"
                        :aria-selected="n === idx"
                        @click="choose(k)"
                        @mouseenter="idx = n"
                        :class="n === idx ? 'bg-gray-50 dark:bg-gray-800' : ''"
                        class="flex w-full items-baseline gap-2 rounded-md px-2.5 py-2 text-left text-sm text-gray-900 dark:text-gray-50 hover:cursor-pointer"
                    >
                        <span class="w-36 shrink-0 font-semibold" x-text="fields[k].label"></span>
                        <span class="truncate text-entity-orders dark:text-indigo-400" x-text="text.trim()"></span>
                        <span class="ml-auto shrink-0 pl-2 text-xs text-gray-400 dark:text-gray-500" x-text="replaces(k) ? t.replaces : fields[k].hint"></span>
                    </button>
                </template>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a x-cloak x-show="chips.length" href="{{ $action }}{{ $keepUrgent ? '?urgent=1' : '' }}" class="px-1 text-sm text-gray-500 dark:text-gray-400 hover:text-lits-red-500 hover:underline">{{ __('indexes.search_clear') }}</a>
            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-lits-red-500 px-3.5 py-2.5 text-sm font-semibold text-white hover:bg-lits-red-600 hover:cursor-pointer">
                {{ __('indexes.search') }}
            </button>
        </div>
    </form>
</div>
