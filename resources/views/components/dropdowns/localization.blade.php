<div
    x-data="{
                        openLocalization: false,
                        toggleLocalization() {
                            if (this.openLocalization) {
                                return this.closeLocalization()
                            }

                            this.$refs.localizationDropdown.focus()

                            this.openLocalization = true
                        },
                        closeLocalization(focusAfter) {
                            if (! this.openLocalization) return

                            this.openLocalization = false

                            focusAfter && focusAfter.focus()
                        }
                    }"
    @keydown.escape.prevent.stop="closeLocalization($refs.localizationDropdown)"
    @focusin.window="! $refs.panel.contains($event.target) && closeLocalization()"
    x-id="['localization-dropdown-button']"
    class="relative">
    <button
        type="button"
        class="inline-flex justify-center items-center gap-x-1.5 rounded-md  px-3 py-2 text-sm ring-1  ring-inset bg-white text-gray-900 ring-gray-300 hover:bg-gray-50 hover:cursor-pointer"
        aria-haspopup="true"
        x-ref="localizationDropdown"
        @click="toggleLocalization()"
        :aria-expanded="openLocalization"
        :aria-controls="$id('localization-dropdown-button')"
        id="sort-menu-button"
    >
        @if(App::currentLocale() == 'es')
            Español
        @else
            English
        @endif
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" class="size-4"><path d="M300.3 440.8C312.9 451 331.4 450.3 343.1 438.6L471.1 310.6C480.3 301.4 483 287.7 478 275.7C473 263.7 461.4 256 448.5 256L192.5 256C179.6 256 167.9 263.8 162.9 275.8C157.9 287.8 160.7 301.5 169.9 310.6L297.9 438.6L300.3 440.8z"/></svg>
    </button>
    <div
        class="absolute left-0 sm:left-auto sm:right-0 z-10 mt-2 w-28 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-hidden"
        x-ref="panel"
        x-show="openLocalization"
        @click.outside="closeLocalization($refs.button)"
        :id="$id('localization-dropdown-button')"
        x-cloak
        role="menu"
        aria-orientation="vertical"
        aria-labelledby="user-menu-button"
        tabindex="-1"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
    >
        <div class="py-1" role="none">
            <a href="{{ route('language.switch', ['locale' => 'es']) }}" class="block px-4 py-2 text-sm hover:bg-gray-100 hover:outline-hidden text-gray-900" role="menuitem" tabindex="-1" id="menu-item-0">Español</a>
            <a href="{{ route('language.switch', ['locale' => 'en']) }}" class="block px-4 py-2 text-sm hover:bg-gray-100 hover:outline-hidden text-gray-900" role="menuitem" tabindex="-1" id="menu-item-0">English</a>
        </div>
    </div>
</div>

