<div x-data="{ open: true }" {{ $attributes }}>
    <div x-show="open" class="rounded-md bg-green-50 dark:bg-green-500/10 p-4 border border-green-400">
        <div class="flex items-center">
            <div class="shrink-0">
                <i class="fa-solid fa-circle-check text-green-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800 dark:text-green-400">{{$message}}</p>
            </div>
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button @click="open = false" type="button" class="inline-flex rounded-md bg-green-50 dark:bg-green-500/10 p-1.5 text-green-500 dark:text-green-400 hover:bg-green-100 focus:ring-2 focus:ring-green-600 focus:ring-offset-2 focus:ring-offset-green-50 focus:outline-hidden">
                        <span class="sr-only">Dismiss</span>
                        <i class="fa-regular fa-xmark"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
