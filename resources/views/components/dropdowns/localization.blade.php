@php
    $isSpanish = App::currentLocale() === 'es';
    $nextLocale = $isSpanish ? 'en' : 'es';
@endphp
<a
    href="{{ route('language.switch', ['locale' => $nextLocale]) }}"
    class="flex items-center gap-2 text-xs font-semibold"
    aria-label="{{ __('Switch language') }}"
>
    <span class="{{ $isSpanish ? 'text-gray-900' : 'text-gray-400' }}">ES</span>
    <span class="relative inline-flex h-[21px] w-[38px] shrink-0 items-center rounded-full bg-gray-200 dark:bg-gray-700">
        <span
            class="absolute top-0.5 size-[17px] rounded-full bg-lits-red-500 shadow-sm transition-all duration-200"
            style="left: {{ $isSpanish ? '2px' : '19px' }};"
        ></span>
    </span>
    <span class="{{ $isSpanish ? 'text-gray-400' : 'text-gray-900' }}">EN</span>
</a>
