<div class="mt-4 mx-auto flex items-center justify-between gap-x-8 lg:mx-0">
    <div class="flex flex-1 items-center gap-x-6">
        <img src="{{ route('clients.logos', [ 'filename' => str_replace(".","_",str_replace("logos/", "", $order->client->image))]) }}" alt="{{$order->client->trade_name}}" class="object-contain size-16 flex-none rounded-full bg-gray-200 outline -outline-offset-1 outline-black/5" />
        <h1>
            <div class="text-sm/6 text-gray-700">{{$order->code}}</div>
            <div class="mt-1 text-base font-semibold text-gray-900">{{$order->client->trade_name}}</div>
        </h1>
    </div>
    <div>
        @can('update', $order)
            <form action="{{$notify}}" method="POST" class="w-full">
                @csrf
                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs inset-ring inset-ring-gray-300 hover:bg-gray-50 hover:cursor-pointer"
                    data-tippy-content="Notificar al cliente"
                >
                    <i class="fa-regular fa-envelope"></i>
                    Notificar
                </button>
            </form>
        @endcan
    </div>
</div>
