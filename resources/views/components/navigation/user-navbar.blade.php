<div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
    <div class="flex-1"></div>
    <div class="flex items-center gap-x-4 lg:gap-x-4">
        <x-dropdowns.localization />
        @unlessrole('Client')
        <livewire:notifications-list />
        @endunlessrole
        <x-dropdowns.user />
    </div>
</div>
