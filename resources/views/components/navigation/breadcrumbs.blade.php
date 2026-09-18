<nav class="flex" aria-label="Breadcrumb">
    <ol role="list" class="flex items-center space-x-1.5">
        <li>
            <div>
                <a href="{{route('dashboard')}}" class="text-lits-red-500 hover:text-lits-red-600">
                    <i class="fa-regular fa-house text-lits-red-500 shrink-0"></i>
                    <span class="sr-only">Home</span>
                </a>
            </div>
        </li>
        @foreach($links as $label => $link)
            <li>
                <div class="flex items-center">
                    <i class="fa-regular fa-angle-right text-gray-400 shrink-0"></i>
                    @if ($link === "#")
                        <span class="ml-1.5 text-sm text-gray-500 ">{{$label}}</span>
                    @else
                        <a href="{{$link}}" class="ml-1.5 text-sm text-lits-red-500 hover:text-lits-red-600">{{$label}}</a>
                    @endif
                </div>
            </li>
        @endforeach
    </ol>
</nav>
