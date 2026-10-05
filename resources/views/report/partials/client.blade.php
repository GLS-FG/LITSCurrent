@php($label = $name ?: '—')
<div class="flex items-center gap-2.5">
    <div class="size-8 shrink-0 flex items-center justify-center rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700 text-[11px] font-bold text-gray-500 dark:text-gray-300">
        @if($image)
            <img alt="{{ $label }}" src="{{ route('clients.logos', ['filename' => str_replace('.', '_', str_replace('logos/', '', $image))]) }}" />
        @else
            {{ mb_strtoupper(mb_substr($label, 0, 2)) }}
        @endif
    </div>
    <span class="text-sm font-medium text-gray-900 dark:text-gray-50">{{ $label }}</span>
</div>
