<div
    class="relative"
    x-data="{
        userDropdownIsOpen: false,
        toggleUserDropdown() {
            if (this.userDropdownIsOpen) {
                return this.closeUserDropdown()
            }

            this.$refs.buttonUserDropdown.focus()

            this.userDropdownIsOpen = true
        },
        closeUserDropdown(focusAfter) {
            if (! this.userDropdownIsOpen) return

            this.userDropdownIsOpen = false

            focusAfter && focusAfter.focus()
        }
    }"
    @keydown.escape.prevent.stop="closeUserDropdown($refs.buttonUserDropdown)"
    x-id="['user-dropdown-button']"
>
    <button
        type="button"
        class="-m-1.5 flex items-center p-1.5 group hover:cursor-pointer"
        id="user-menu-button"
        aria-haspopup="true"
        x-ref="buttonUserDropdown"
        @click="toggleUserDropdown()"
        :aria-expanded="userDropdownIsOpen"
        :aria-controls="$id('user-dropdown-button')"

    >
        <span class="sr-only">Open user menu</span>
        @isset(auth()->user()->client)
            <img class="size-7 rounded-full bg-gray-200 dark:bg-gray-700 object-contain" src="{{ route('clients.logos', [ 'filename' => str_replace(".","_",str_replace("logos/", "", auth()->user()->client->image))]) }}" alt="{{auth()->user()->client->company_name}}">
        @else
            <img class="size-7 rounded-full bg-gray-200 dark:bg-gray-700 object-contain" src="/images/gls.png" alt="LITS">
        @endisset
        <span class="hidden lg:flex lg:items-center">
                                      <span class="ml-2.5 text-sm/6 text-gray-500 dark:text-gray-400" aria-hidden="true">
                                          {{ auth()->user()->name ?? 'Anonimo' }}
                                      </span>
                                    </span>
    </button>
    <div
        x-ref="panel"
        x-show="userDropdownIsOpen"
        @click.outside="closeUserDropdown($refs.button)"
        :id="$id('user-dropdown-button')"
        x-cloak
        class="absolute right-0 z-10 mt-2.5 w-32 origin-top-right rounded-md bg-white dark:bg-lits-blue-550 py-2 px-1.5 shadow-lg ring-1 ring-gray-900/5 dark:ring-white/10 focus:outline-hidden"
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
        <!-- Active: "bg-gray-50 outline-hidden", Not Active: "" -->
        {{--<a href="#edit" class="px-2 lg:py-1.5 py-2 text-sm/6 w-full flex items-center rounded-md transition-colors text-left text-gray-800 dark:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700 focus-visible:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
            Mi perfil
        </a>--}}
        <form action="{{route('auth.destroy')}}" method="POST">
            @csrf
            @method('DELETE')
            <button class="px-2 lg:py-1.5 py-2 text-sm/6 w-full flex items-center rounded-md transition-colors text-left text-gray-800 dark:text-gray-100 hover:bg-red-50 hover:text-red-600 focus-visible:bg-red-50 focus-visible:text-red-600 disabled:opacity-50 disabled:cursor-not-allowed hover:cursor-pointer">
                {{__("Sign out")}}
            </button>
        </form>
    </div>
</div>
