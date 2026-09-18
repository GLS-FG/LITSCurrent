@section('title', 'Bill of lading')
@section('custom_script')
    <script type="module">
        $('#printButton').on('click', async () => {
            const blob = await fetch("{{route('orders.shipments.print', [ 'order' => $order->id, 'shipment' => $shipment->id ])}}").then(resp => resp.blob());
            const blobUrl = URL.createObjectURL(blob);
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.src = blobUrl;
            iframe.onload = () => {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
                setTimeout(() => {
                    document.body.removeChild(iframe);
                    URL.revokeObjectURL(blobUrl);
                }, 1000);
            };
            document.body.appendChild(iframe);
        });
    </script>
@endsection
<x-layout-app>
    <section>
        <x-navigation.breadcrumbs :links="['Ordenes' => route('orders.index'), $order->code => route('orders.show', ['order' => $order->id]), 'Embarque' => route('orders.shipments.show', ['order' => $order->id, 'shipment' => $shipment->id]), 'Bill of lading' => '#']" />
        <div class="mt-2 md:flex md:items-center md:justify-between">
            <div class="">
                <h2 class="text-2xl/7 font-bold text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">{{$order->code}}</h2>
                <h2 class="mt-1 text-sm text-gray-500">{{ $shipment->created_at->isoFormat('D [de] MMMM [del] YYYY [a las] h:mm a') }}</h2>
            </div>
            <div class="mt-3 flex sm:mt-0 sm:ml-4 gap-1">
                <button
                    id="printButton"
                    role="button"
                    class="whitespace-nowrap inline-flex items-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs inset-ring inset-ring-gray-300 hover:bg-gray-50 hover:cursor-pointer"
                >
                    <i class="fa-solid fa-print"></i>
                    Imprimir
                </button>
                <a
                    href="{{route('orders.shipments.print', [ 'order' => $order->id, 'shipment' => $shipment->id ])}}"
                    role="button"
                    class="mr-3 inline-flex items-center gap-x-1.5 rounded-md bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer"
                >
                    <i class="fa-regular fa-arrow-down-to-bracket"></i>
                    Descargar
                </a>
            </div>
        </div>
        @if ($errors->any())
            <x-alerts.error :message="'Ocurrieron los siguientes errores:'" :errors="$errors" class="my-4" />
        @endif
        @if(session()->has('success'))
            <x-alerts.success class="mt-4" :message="session('success')" />
        @endif
        <div class="mt-8">
            <div class="-mx-4 sm:mx-0 overflow-auto">
                <div class="bg-white shadow-lits-card w-fit h-fit mx-auto" style="width: 816px;padding: 48px;">
                    <x-cards.bill-of-lading
                        :order="$order"
                        :shipment="$shipment"
                        :products="$shipment->products"
                        :transportation="$shipment->transportation"
                    />
                </div>
            </div>
        </div>
    </section>
</x-layout-app>
