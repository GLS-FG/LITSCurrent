@php
    $flags = [
        'oversize' => ['label' => __('shows.overload'), 'value' => $oversize],
        'hazardous' => ['label' => __('shows.hazardous_material'), 'value' => $hazardous],
        'refrigerated' => ['label' => __('shows.refrigerated'), 'value' => $refrigerated],
        'insurance' => ['label' => __('shows.insurance'), 'value' => $insurance],
        'tarps' => ['label' => __('shows.tarps'), 'value' => $tarps],
    ];
@endphp
<div>
    <div class="text-[11px] text-gray-400 dark:text-gray-500 mb-1.5">Manejo especial</div>
    <div class="flex flex-wrap gap-2">
        @foreach($flags as $property => $flag)
            @if($isEditable)
                <label class="relative inline-flex items-center gap-1.5 rounded-full border border-gray-200 dark:border-lits-blue-450 px-2.5 py-1 text-xs font-semibold text-gray-500 dark:text-gray-400 has-checked:border-transparent has-checked:bg-entity-shipments-50 dark:has-checked:bg-entity-shipments/15 has-checked:text-entity-shipments has-focus-visible:outline-2 has-focus-visible:outline-offset-2 has-focus-visible:outline-indigo-600 hover:cursor-pointer">
                    <input type="checkbox" wire:model.live="{{ $property }}" class="absolute opacity-0 size-0" />
                    {{ $flag['label'] }}
                </label>
            @else
                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $flag['value'] ? 'bg-entity-shipments-50 dark:bg-entity-shipments/15 text-entity-shipments' : 'border border-gray-200 dark:border-lits-blue-450 text-gray-400 dark:text-gray-500' }}">
                    {{ $flag['label'] }}
                </span>
            @endif
        @endforeach
    </div>
</div>
