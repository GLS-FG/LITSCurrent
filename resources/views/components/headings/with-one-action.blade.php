<div class="mt-2 md:flex md:items-center md:justify-between">
    <div class="min-w-0 flex-1">
        <h2 class="text-2xl/7 font-bold text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">{{$title}}</h2>
    </div>
    @can('create', $objectClass)
        <div class="mt-4 flex shrink-0 md:mt-0 md:ml-4">
            <a href="{{$buttonAction}}" class="ml-3 inline-flex items-center gap-1 rounded bg-lits-red-500 px-3 py-2 text-sm text-white shadow-xs transition-colors duration-150 ease-in-out hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-350">
                <i class="fa-regular fa-plus"></i>
                {{$buttonLabel}}
            </a>
        </div>
    @endcan
</div>
