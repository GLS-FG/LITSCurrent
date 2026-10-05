{{-- Encabezado de columna ordenable. Recarga la página con ?sort=&dir=, que el Excel también recibe. --}}
@php
    $active = $report->sortKey() === $key;
    $current = $report->sortDir();
    $nextDir = $active ? ($current === 'asc' ? 'desc' : 'asc') : ($first ?? 'asc');
    $url = route('reports.index', array_merge(request()->query(), ['sort' => $key, 'dir' => $nextDir]));
@endphp
<th scope="col" aria-sort="{{ $active ? ($current === 'asc' ? 'ascending' : 'descending') : 'none' }}" class="{{ $thClass }} {{ $pad ?? '' }} {{ $align }}">
    <a href="{{ $url }}" class="group inline-flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-200 {{ $active ? 'text-gray-700 dark:text-gray-200' : '' }}">
        {{ $label }}
        <i class="fa-solid {{ $active ? ($current === 'asc' ? 'fa-arrow-up' : 'fa-arrow-down') : 'fa-arrow-up opacity-0 group-hover:opacity-40' }} text-[9px]"></i>
    </a>
</th>
