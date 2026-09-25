<dl class="grid grid-cols-1 text-sm/6 sm:grid-cols-5 pb-4">
    <div>
        <dt class="font-semibold text-gray-900 dark:text-gray-50">{{__('shows.overload')}}</dt>
        <dd class="text-gray-500 dark:text-gray-400">
            @if($isEditable)
                <div class="group relative inline-flex w-9 shrink-0 rounded-full bg-gray-100 dark:bg-gray-800 p-0.5 inset-ring inset-ring-gray-900/5 outline-offset-2 outline-blue-600 transition-colors duration-200 ease-in-out has-checked:bg-blue-600 has-focus-visible:outline-2">
                    <span class="relative size-4 rounded-full bg-white dark:bg-lits-blue-550 shadow-xs ring-1 ring-gray-900/5 dark:ring-white/10 transition-transform duration-200 ease-in-out group-has-checked:translate-x-4">
                        <span
                            aria-hidden="true"
                            class="absolute inset-0 flex size-full items-center justify-center opacity-100 transition-opacity duration-200 ease-in group-has-checked:opacity-0 group-has-checked:duration-100 group-has-checked:ease-out text-gray-600 dark:text-gray-400 text-[0.5rem]"
                        >
                            <i class="fa-solid fa-xmark"></i>
                        </span>
                        <span
                            aria-hidden="true"
                            class="absolute inset-0 flex size-full items-center justify-center opacity-0 transition-opacity duration-100 ease-out group-has-checked:opacity-100 group-has-checked:duration-200 group-has-checked:ease-in text-blue-600 dark:text-blue-400 text-[0.5rem]"
                        >
                            <i class="fa-solid fa-check"></i>
                        </span>
                    </span>
                    <input
                        name="oversize"
                        type="checkbox"
                        wire:model.live="oversize"
                        aria-label="Oversize"
                        class="absolute inset-0 size-full appearance-none focus:outline-hidden"
                    />
                </div>
            @else
                {{$shipment->oversize}}
            @endif
        </dd>
    </div>
    <div>
        <dt class="font-semibold text-gray-900 dark:text-gray-50">{{__('shows.hazardous_material')}}</dt>
        <dd class="text-gray-500 dark:text-gray-400">
            @if($isEditable)
                <div class="group relative inline-flex w-9 shrink-0 rounded-full bg-gray-100 dark:bg-gray-800 p-0.5 inset-ring inset-ring-gray-900/5 outline-offset-2 outline-blue-600 transition-colors duration-200 ease-in-out has-checked:bg-blue-600 has-focus-visible:outline-2">
                    <span class="relative size-4 rounded-full bg-white dark:bg-lits-blue-550 shadow-xs ring-1 ring-gray-900/5 dark:ring-white/10 transition-transform duration-200 ease-in-out group-has-checked:translate-x-4">
                        <span
                            aria-hidden="true"
                            class="absolute inset-0 flex size-full items-center justify-center opacity-100 transition-opacity duration-200 ease-in group-has-checked:opacity-0 group-has-checked:duration-100 group-has-checked:ease-out text-gray-600 dark:text-gray-400 text-[0.5rem]"
                        >
                            <i class="fa-solid fa-xmark"></i>
                        </span>
                        <span
                            aria-hidden="true"
                            class="absolute inset-0 flex size-full items-center justify-center opacity-0 transition-opacity duration-100 ease-out group-has-checked:opacity-100 group-has-checked:duration-200 group-has-checked:ease-in text-blue-600 dark:text-blue-400 text-[0.5rem]"
                        >
                            <i class="fa-solid fa-check"></i>
                        </span>
                    </span>
                    <input
                        name="hazardous_material"
                        type="checkbox"
                        wire:model.live="hazardous"
                        aria-label="hazardous_material"
                        class="absolute inset-0 size-full appearance-none focus:outline-hidden"
                    />
                </div>
            @else
                {{$shipment->hazardous_material}}
            @endif
        </dd>
    </div>
    <div>
        <dt class="font-semibold text-gray-900 dark:text-gray-50">{{__('shows.refrigerated')}}</dt>
        <dd class="text-gray-500 dark:text-gray-400">
            @if($isEditable)
                <div class="group relative inline-flex w-9 shrink-0 rounded-full bg-gray-100 dark:bg-gray-800 p-0.5 inset-ring inset-ring-gray-900/5 outline-offset-2 outline-blue-600 transition-colors duration-200 ease-in-out has-checked:bg-blue-600 has-focus-visible:outline-2">
                    <span class="relative size-4 rounded-full bg-white dark:bg-lits-blue-550 shadow-xs ring-1 ring-gray-900/5 dark:ring-white/10 transition-transform duration-200 ease-in-out group-has-checked:translate-x-4">
                        <span
                            aria-hidden="true"
                            class="absolute inset-0 flex size-full items-center justify-center opacity-100 transition-opacity duration-200 ease-in group-has-checked:opacity-0 group-has-checked:duration-100 group-has-checked:ease-out text-gray-600 dark:text-gray-400 text-[0.5rem]"
                        >
                            <i class="fa-solid fa-xmark"></i>
                        </span>
                        <span
                            aria-hidden="true"
                            class="absolute inset-0 flex size-full items-center justify-center opacity-0 transition-opacity duration-100 ease-out group-has-checked:opacity-100 group-has-checked:duration-200 group-has-checked:ease-in text-blue-600 dark:text-blue-400 text-[0.5rem]"
                        >
                            <i class="fa-solid fa-check"></i>
                        </span>
                    </span>
                    <input
                        name="refrigerated"
                        type="checkbox"
                        wire:model.live="refrigerated"
                        aria-label="refrigerated"
                        class="absolute inset-0 size-full appearance-none focus:outline-hidden"
                    />
                </div>
            @else
                {{$shipment->refrigerated}}
            @endif
        </dd>
    </div>
    <div>
        <dt class="font-semibold text-gray-900 dark:text-gray-50">{{__('shows.insurance')}}</dt>
        <dd class="text-gray-500 dark:text-gray-400">
            @if($isEditable)
                <div class="group relative inline-flex w-9 shrink-0 rounded-full bg-gray-100 dark:bg-gray-800 p-0.5 inset-ring inset-ring-gray-900/5 outline-offset-2 outline-blue-600 transition-colors duration-200 ease-in-out has-checked:bg-blue-600 has-focus-visible:outline-2">
                    <span class="relative size-4 rounded-full bg-white dark:bg-lits-blue-550 shadow-xs ring-1 ring-gray-900/5 dark:ring-white/10 transition-transform duration-200 ease-in-out group-has-checked:translate-x-4">
                        <span
                            aria-hidden="true"
                            class="absolute inset-0 flex size-full items-center justify-center opacity-100 transition-opacity duration-200 ease-in group-has-checked:opacity-0 group-has-checked:duration-100 group-has-checked:ease-out text-gray-600 dark:text-gray-400 text-[0.5rem]"
                        >
                            <i class="fa-solid fa-xmark"></i>
                        </span>
                        <span
                            aria-hidden="true"
                            class="absolute inset-0 flex size-full items-center justify-center opacity-0 transition-opacity duration-100 ease-out group-has-checked:opacity-100 group-has-checked:duration-200 group-has-checked:ease-in text-blue-600 dark:text-blue-400 text-[0.5rem]"
                        >
                            <i class="fa-solid fa-check"></i>
                        </span>
                    </span>
                    <input
                        name="insurance"
                        type="checkbox"
                        wire:model.live="insurance"
                        aria-label="insurance"
                        class="absolute inset-0 size-full appearance-none focus:outline-hidden"
                    />
                </div>
            @else
                {{$shipment->insurance}}
            @endif
        </dd>
    </div>
    <div>
        <dt class="font-semibold text-gray-900 dark:text-gray-50">{{__('shows.tarps')}}</dt>
        <dd class="text-gray-500 dark:text-gray-400">
            @if($isEditable)
                <div class="group relative inline-flex w-9 shrink-0 rounded-full bg-gray-100 dark:bg-gray-800 p-0.5 inset-ring inset-ring-gray-900/5 outline-offset-2 outline-blue-600 transition-colors duration-200 ease-in-out has-checked:bg-blue-600 has-focus-visible:outline-2">
                    <span class="relative size-4 rounded-full bg-white dark:bg-lits-blue-550 shadow-xs ring-1 ring-gray-900/5 dark:ring-white/10 transition-transform duration-200 ease-in-out group-has-checked:translate-x-4">
                        <span
                            aria-hidden="true"
                            class="absolute inset-0 flex size-full items-center justify-center opacity-100 transition-opacity duration-200 ease-in group-has-checked:opacity-0 group-has-checked:duration-100 group-has-checked:ease-out text-gray-600 dark:text-gray-400 text-[0.5rem]"
                        >
                            <i class="fa-solid fa-xmark"></i>
                        </span>
                        <span
                            aria-hidden="true"
                            class="absolute inset-0 flex size-full items-center justify-center opacity-0 transition-opacity duration-100 ease-out group-has-checked:opacity-100 group-has-checked:duration-200 group-has-checked:ease-in text-blue-600 dark:text-blue-400 text-[0.5rem]"
                        >
                            <i class="fa-solid fa-check"></i>
                        </span>
                    </span>
                    <input
                        name="tarps"
                        type="checkbox"
                        wire:model.live="tarps"
                        aria-label="tarps"
                        class="absolute inset-0 size-full appearance-none focus:outline-hidden"
                    />
                </div>
            @else
                {{$shipment->tarps}}
            @endif
        </dd>
    </div>
</dl>
