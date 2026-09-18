
<div class="flex grow flex-col gap-y-5 overflow-y-auto border-r border-gray-200 bg-white pb-4 px-6">
    <div class="flex h-16 shrink-0 items-center">
        <img
            class="h-14 w-auto"
            src="{{asset('/images/lits.png')}}"
            alt="LITS GLS Group"
        />
    </div>
    {{ $slot }}
</div>
