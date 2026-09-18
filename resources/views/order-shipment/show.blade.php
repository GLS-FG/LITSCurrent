@section('title', __('shows.shipment_details'))
@section('custom_script')
    @parent
    <script type="module">
        (g=>{var h,a,k,p="The Google Maps JavaScript API",c="google",l="importLibrary",q="__ib__",m=document,b=window;b=b[c]||(b[c]={});var d=b.maps||(b.maps={}),r=new Set,e=new URLSearchParams,u=()=>h||(h=new Promise(async(f,n)=>{await (a=m.createElement("script"));e.set("libraries",[...r]+"");for(k in g)e.set(k.replace(/[A-Z]/g,t=>"_"+t[0].toLowerCase()),g[k]);e.set("callback",c+".maps."+q);a.src=`https://maps.${c}apis.com/maps/api/js?`+e;d[q]=f;a.onerror=()=>h=n(Error(p+" could not load."));a.nonce=m.querySelector("script[nonce]")?.nonce||"";m.head.append(a)}));d[l]?console.warn(p+" only loads once. Ignoring:",g):d[l]=(f,...n)=>r.add(f)&&u().then(()=>d[l](f,...n))})({
            key: "{{config('services.google_maps.key')}}",
            v: "weekly",
        });
        var routesInited = false;
        let map;
        let mapAutocomplete;
        let markerAutocomplete;
        let mapAutocompleteEdit;
        let markerAutocompleteEdit;
        let mapInitialized = false;
        let mapAutocompleteInitialized = false;
        let mapAutocompleteEditInitialized = false;
        let center = { lat: 29.076763047209962, lng: -110.95766151719751 };
        @if($shipment->shipFrom != null)
            @if($shipment->shipFrom->latitude != null && $shipment->shipFrom->longitude != null)
            center = { lat: Number("{{$shipment->shipFrom->latitude}}"), lng: Number("{{$shipment->shipFrom->longitude}}") };
            @endif
        @endif
        let infoWindow;
        let markers = [];
        const mapLocations = {{ Js::from($mapLocations) }};
        function formatDate(dateTimeString) {
            const dateTime = dateTimeString.split(" ");
            const [y, m, dy] = dateTime[0].split("-");
            const [h, mi] = dateTime[1].split(":");
            var d = new Date(y, m - 1, dy, h, mi),
                month = "" + (d.getMonth() + 1),
                day = "" + d.getDate(),
                year = d.getFullYear(),
                hours = "" + d.getHours(),
                minutes = "" + d.getMinutes();
            if (month.length < 2) month = "0" + month;
            if (day.length < 2) day = "0" + day;
            if (hours.length < 2) hours = "0" + hours;
            if (minutes.length < 2) minutes = "0" + minutes;
            return [day, month, year].join("/") + " " + [hours, minutes].join(":");
        }
        async function initMap() {
            if(!mapInitialized){
                const { Map, RenderingType } = (await google.maps.importLibrary('maps'));
                map = new Map(document.getElementById('map'), {
                    center,
                    zoom: 12,
                    renderingType: RenderingType.VECTOR,
                    mapId: "locationsMap",
                });
                if(mapLocations.length > 0){
                    createMarkers();
                }
                mapInitialized = true;
            }
        }
        async function initMapAutocomplete() {
            if(!mapAutocompleteInitialized){
                const { Map, RenderingType } = (await google.maps.importLibrary('maps'));
                mapAutocomplete = new Map(document.getElementById('location-map'), {
                    center,
                    zoom: 12,
                    renderingType: RenderingType.VECTOR,
                    mapId: "autocompleteMap",
                });
                markerAutocomplete = new google.maps.Marker({
                    map: mapAutocomplete,
                    draggable: true
                });
                markerAutocomplete.addListener("dragend", (event) => {
                    document.getElementById('latitude').value = event.latLng.lat().toFixed(6);
                    document.getElementById('longitude').value = event.latLng.lng().toFixed(6);
                });
                mapAutocompleteInitialized = true;
            }
        }
        async function initMapAutocompleteEdit() {
            if(!mapAutocompleteEditInitialized){
                const { Map, RenderingType } = (await google.maps.importLibrary('maps'));
                mapAutocompleteEdit = new Map(document.getElementById('edit-location-map'), {
                    center,
                    zoom: 12,
                    renderingType: RenderingType.VECTOR,
                    mapId: "autocompleteEditMap",
                });
                markerAutocompleteEdit = new google.maps.Marker({
                    map: mapAutocompleteEdit,
                    draggable: true
                });
                markerAutocompleteEdit.addListener("dragend", (event) => {
                    document.getElementById('edit-latitude').value = event.latLng.lat().toFixed(6);
                    document.getElementById('edit-longitude').value = event.latLng.lng().toFixed(6);
                });
                mapAutocompleteEditInitialized = true;
            }
        }
        function buildContent(index, type) {
            const content = document.createElement('div');
            content.classList.add('property');
            content.innerHTML = `
                <div class="icon">
                    <span class="${type}">${index}</span>
                </div>
            `;
            return content;
        }
        async function createMarkers() {
            await google.maps.importLibrary("marker");
            let bounds = new google.maps.LatLngBounds();
            infoWindow = new google.maps.InfoWindow();
            for (let i = 0; i < mapLocations.length; i++) {
                let location = mapLocations[i];
                let markerType = "common";
                if(i === 0){
                    markerType = "start";
                } else if(i === mapLocations.length - 1){
                    markerType = "end";
                }
                let marker = new google.maps.marker.AdvancedMarkerElement({
                    map: map,
                    position: new google.maps.LatLng(location.latitude, location.longitude),
                    gmpClickable: true,
                    title: location.name,
                    content: buildContent(i + 1, markerType)
                });
                bounds.extend(marker.position);

                marker.addListener('gmp-click', ({ domEvent, latLng }) => {
                    infoWindow.close();
                    infoWindow.open(marker.map, marker);
                    infoWindow.setContent("<h4 class=\"font-medium\">" + marker.title + "</h4><p class=\"text-gray-500\">" + formatDate(location.location_date) + "</p>")
                });
                markers.push(marker);
            }
            map.fitBounds(bounds);
        }
        async function initAutocomplete() {
            const { PlaceAutocompleteElement } = await google.maps.importLibrary("places");
            const autocompleteElement = new PlaceAutocompleteElement();
            autocompleteElement.style.colorScheme = 'light';
            document.getElementById("location-name-div").appendChild(autocompleteElement);
            autocompleteElement.addEventListener('gmp-select', async ({ placePrediction }) => {
                const place = placePrediction.toPlace();
                await place.fetchFields({ fields: ['displayName', 'formattedAddress', 'location', 'types'] });
                const hasTypes = ['political', 'route', 'locality', 'street_address'].some(item => place.types.includes(item));
                let locationName = place.displayName + ', ' + place.formattedAddress;
                if (hasTypes) {
                    locationName = place.formattedAddress;
                }
                document.getElementById('location-name').value = locationName;
                document.getElementById('latitude').value = place.location.lat().toFixed(6);
                document.getElementById('longitude').value = place.location.lng().toFixed(6);

                mapAutocomplete.setCenter(place.location);
                markerAutocomplete.setPosition(place.location);
            });
            google.maps.event.addListener(map, 'bounds_changed', () => {
                autocompleteElement.locationBias = map.getBounds();
            });
        }
        window.initAutocompletesMap = async function(value) {
            if(value === 2){
                initMapAutocomplete();
            } else if(value === 3){
                initMapAutocompleteEdit();
            }
        }
        window.createRoutes = function(value) {
            if(mapLocations.length > 1 && !routesInited && value){
                routesInited = true;
                const first = mapLocations[0];
                const last = mapLocations[mapLocations.length - 1];
                let body = {
                    "origin": { "location": { "latLng": { "latitude": first.latitude, "longitude": first.longitude } } },
                    "destination": { "location": { "latLng": { "latitude": last.latitude, "longitude": last.longitude } } },
                    "intermediates": [],
                    "travelMode": "DRIVE",
                    "routingPreference": "TRAFFIC_AWARE",
                    "computeAlternativeRoutes": false,
                    "units": "IMPERIAL"
                }
                for (let i = 1; i < mapLocations.length - 1; i++) {
                    const intermediate = mapLocations[i];
                    body.intermediates.push({ "location": { "latLng": { "latitude": intermediate.latitude, "longitude": intermediate.longitude } } })
                }
                $.ajax({
                    url: 'https://routes.googleapis.com/directions/v2:computeRoutes',
                    type: 'POST',
                    data: JSON.stringify(body),
                    contentType: 'application/json',
                    headers: {
                        "X-Goog-Api-Key": "{{config('services.google_maps.key')}}",
                        "X-Goog-FieldMask": "routes.polyline.encodedPolyline"
                    },
                    success: function(response) {
                        let routes = response.routes;
                        if (routes && routes.length > 0) {
                            let route = routes[0];
                            if (route) {
                                let polyline = route.polyline;
                                if (polyline) {
                                    let decoded = googleMapsDecode(polyline.encodedPolyline, 5);
                                    processPolylines(decoded)
                                }
                            }
                        }
                    }
                });
            }
        };
        @if($shipment->show_map)
        initMap();
        if(mapLocations.length > 1){
            createRoutes(true);
        }
        @endif
        initAutocomplete();
        @isset($shipment->latestLocation)
        async function initEditAutocomplete() {
            const { PlaceAutocompleteElement } = await google.maps.importLibrary("places");
            const editAutocompleteElement = new PlaceAutocompleteElement();
            editAutocompleteElement.style.colorScheme = 'light';
            document.getElementById("edit-location-name-div").appendChild(editAutocompleteElement);
            editAutocompleteElement.addEventListener('gmp-select', async ({ placePrediction }) => {
                const place = placePrediction.toPlace();
                await place.fetchFields({ fields: ['displayName', 'formattedAddress', 'location', 'types'] });
                const hasTypes = ['political', 'route', 'locality', 'street_address'].some(item => place.types.includes(item));
                let locationName = place.displayName + ', ' + place.formattedAddress;
                if (hasTypes) {
                    locationName = place.formattedAddress;
                }
                document.getElementById('edit-location-name').value = locationName;
                document.getElementById('edit-latitude').value = place.location.lat().toFixed(6);
                document.getElementById('edit-longitude').value = place.location.lng().toFixed(6);

                mapAutocompleteEdit.setCenter(place.location);
                markerAutocompleteEdit.setPosition(place.location);
            });
            google.maps.event.addListener(map, 'bounds_changed', () => {
                editAutocompleteElement.locationBias = map.getBounds();
            });
        }
        initEditAutocomplete();
        @endisset
        window.initGoogleMapsMap = function(value) {
            if(value){
                initMap();
                if(mapLocations.length > 1){
                    createRoutes(true);
                }
            }
            $.ajax({
                url: "{{ route('orders.shipments.map', ['order' => $order->id, 'shipment' => $shipment->id]) }}",
                type: 'POST',
                data: { show_map: value, _token: '{{ csrf_token() }}', }
            });
        }
        window.openMarker = function(index) {
            markers[index].click()
        };
        async function processPolylines(data) {
            const { LatLng } = await google.maps.importLibrary("core");
            let snappedCoordinates = [];
            for (var i = 0; i < data.length; i++) {
                var latlng = new LatLng(data[i][0], data[i][1]);
                snappedCoordinates.push(latlng);
            }
            drawPolylines(snappedCoordinates)
        }
        function drawPolylines(snappedPoints) {
            const snappedPolyline = new google.maps.Polyline({
                path: snappedPoints,
                geodesic: true,
                strokeColor: "#4285F4", // Modern Blue
                strokeOpacity: 1,
                strokeWeight: 4,
            });
            snappedPolyline.setMap(map);
        }
        window.editLocation = function(location) {
            const { id, comments, latitude, location_date, longitude, name, service_type_status_id } = location;
            $('#edit-location_date').val(location_date);
            $('#edit-service_type_status_id').val(service_type_status_id);
            $('#edit-comments').val(comments);
            $('#edit-location-name').val(name);
            $('#edit-latitude').val(latitude);
            $('#edit-longitude').val(longitude);
            $('#edit-location-form').attr('action', "{{request()->getSchemeAndHttpHost()}}/orders/{{$order->id}}/shipments/{{$shipment->id}}/locations/" + id);
        };
        $('#closeCopyAlert').on('click', () => {
            $('#copyAlert').hide();
        });
        $('#copyFrom').on('click', async () => {
            try {
                let htmlString, plainText = "";
                @if($shipment->ship_from_link)
                htmlString = `<p style="white-space: pre-wrap">{{$shipment->ship_from}}</p><a href="{{$shipment->ship_from_link}}" target="_blank">{{$shipment->ship_from_link}}</a>`;
                plainText = `{{$shipment->ship_from}}` + "\n" + `{{$shipment->ship_from_link}}`;
                @else
                htmlString = `<p style="white-space: pre-wrap">{{$shipment->ship_from}}</p>`;
                plainText = `{{$shipment->ship_from}}`;
                @endif
                const htmlBlob = new Blob([htmlString], { type: "text/html" });
                const textBlob = new Blob([plainText], { type: "text/plain" });
                const data = [ new ClipboardItem({ "text/html": htmlBlob, "text/plain": textBlob }) ];
                await navigator.clipboard.write(data);
                $('#copyFromSuccess').show();
                setTimeout(() => {
                    $('#copyFromSuccess').hide();
                }, 1000);
            } catch (err) {
                console.error('Failed to copy: ', err);
            }
        });
        $('#copyTo').on('click', async () => {
            try {
                let htmlString, plainText = "";
                @if($shipment->ship_to_link)
                    htmlString = `<p style="white-space: pre-wrap">{{$shipment->ship_to}}</p><a href="{{$shipment->ship_to_link}}" target="_blank">{{$shipment->ship_to_link}}</a>`;
                plainText = `{{$shipment->ship_to}}` + "\n" + `{{$shipment->ship_to_link}}`;
                @else
                    htmlString = `<p style="white-space: pre-wrap">{{$shipment->ship_to}}</p>`;
                plainText = `{{$shipment->ship_to}}`;
                @endif
                const htmlBlob = new Blob([htmlString], { type: "text/html" });
                const textBlob = new Blob([plainText], { type: "text/plain" });
                const data = [ new ClipboardItem({ "text/html": htmlBlob, "text/plain": textBlob }) ];
                await navigator.clipboard.write(data);
                $('#copyToSuccess').show();
                setTimeout(() => {
                    $('#copyToSuccess').hide();
                }, 1000);
            } catch (err) {
                console.error('Failed to copy: ', err);
            }
        });
        $('#copyMailButton').on('click', async () => {
            $('<iframe id="mailIframe" src="{{route('orders.shipments.previewNotify', ['order' => $order->id, 'shipment' => $shipment->id])}}" allow="clipboard-write"></iframe>').appendTo('body');
            var $iframe = $('#mailIframe');
            $iframe.hide();
            $iframe.on('load', async function() {
                const htmlString = $iframe.contents().find('body').html();
                try {
                    const htmlBlob = new Blob([htmlString], { type: "text/html" });
                    const textBlob = new Blob([htmlString], { type: "text/plain" });

                    const data = [
                        new ClipboardItem({
                            "text/html": htmlBlob,
                            "text/plain": textBlob,
                        }),
                    ];

                    await navigator.clipboard.write(data);
                    $('#copyAlert').show();
                } catch (err) {
                    console.error('Failed to copy: ', err);
                }
            });
        });
    </script>
@endsection
<x-layout-app>
    <div>
        <x-navigation.breadcrumbs :links="[__('Orders') => route('orders.index'), $order->code => route('orders.show', ['order' => $order->id]), __('Shipment') => '#']" />
        <div class="mt-6 flex justify-between items-center flex-wrap gap-y-2">
            <div class="flex items-center flex-wrap sm:flex-nowrap gap-1">
                @can('update', $shipment)
                    <a
                        href="{{route('orders.shipments.edit', [ 'order' => $order->id, 'shipment' => $shipment->id ])}}"
                        data-tippy-content="{{__('shows.edit_service')}}"
                        role="button"
                        class="inline-flex items-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs inset-ring inset-ring-gray-300 hover:bg-gray-50"
                    >
                        <i class="fa-regular fa-pen-to-square"></i>
                        {{__('shows.edit')}}
                    </a>
                @endcan
                @can('attach', $shipment)
                    <a
                        href='{{route('orders.shipments.documents.create', [ 'order' => $shipment->order, 'shipment' => $shipment ])}}'
                        data-tippy-content="{{__('shows.attach_file')}}"
                        role="button"
                        class="inline-flex items-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs inset-ring inset-ring-gray-300 hover:bg-gray-50"
                    >
                        <i class="fa-regular fa-paperclip"></i>
                        {{__('shows.attach_file')}}
                    </a>
                @endcan
                <a
                    href="{{route('orders.shipments.bol', [ 'order' => $order->id, 'shipment' => $shipment->id ])}}"
                    data-tippy-content="Bill Of Lading"
                    role="button"
                    class="whitespace-nowrap inline-flex items-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs inset-ring inset-ring-gray-300 hover:bg-gray-50"
                >
                    <i class="fa-regular fa-file-contract shrink-0"></i>
                    Bill Of Lading
                </a>
                @can('clone', $shipment)
                        <a
                            href="{{route('clone.order.create', [ 'shipment' => $shipment->id ])}}"
                            data-tippy-content="{{__('shows.duplicate')}}"
                            role="button"
                            class="whitespace-nowrap inline-flex items-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs inset-ring inset-ring-gray-300 hover:bg-gray-50"
                        >
                            <i class="fa-regular fa-copy shrink-0"></i>
                            {{__('shows.duplicate')}}
                        </a>
                @endcan
            </div>
            <div>
                @canany(['update', 'restore'], $shipment)
                    <form action="{{ route('orders.shipments.update.status', ['order' => $order->id, 'shipment' => $shipment->id]) }}" method="POST" class="flex">
                        @csrf
                        @method('PUT')
                        <div class="-mr-px  grid grid-cols-1 focus-within:relative">
                            <select id="order_shipment_status_id" name="order_shipment_status_id" autocomplete="off" aria-label="Country" class="col-start-1 row-start-1 w-full appearance-none rounded-l-md bg-white py-1.5 pr-8 pl-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                @foreach ($statuses as $status)
                                    <option value= {{ $status->id }} @selected($shipment->order_shipment_status_id->value == $status->id)>{{ $status->name }}  </option>
                                @endforeach
                            </select>
                            <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 sm:text-sm"></i>
                        </div>
                        <button data-tippy-content="{{__('shows.save_status')}}" type="submit" class="flex shrink-0 items-center gap-x-1.5 rounded-r-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 outline-1 -outline-offset-1 outline-gray-300 hover:bg-gray-50 focus:relative focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 hover:cursor-pointer">
                            <i class="fa-regular fa-floppy-disk"></i>
                        </button>
                    </form>
                @endcanany
            </div>
        </div>
        @if ($errors->any())
            <x-alerts.error :message="__('shows.service_errors')" :errors="$errors" class="my-4" />
        @endif
        @if(session()->has('success'))
            <x-alerts.success class="mt-4" :message="session('success')" />
        @endif
        <div id="copyAlert" style="display: none" class="mt-2 rounded-md bg-green-50 p-4 border border-green-400">
            <div class="flex items-center">
                <div class="shrink-0">
                    <i class="fa-solid fa-circle-check text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">El correo se copió a tu portapapeles</p>
                </div>
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button id="closeCopyAlert" type="button" class="inline-flex rounded-md bg-green-50 p-1.5 text-green-500 hover:bg-green-100 focus:ring-2 focus:ring-green-600 focus:ring-offset-2 focus:ring-offset-green-50 focus:outline-hidden">
                            <span class="sr-only">Dismiss</span>
                            <i class="fa-regular fa-xmark"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="shadow-lits-card border border-gray-200 bg-white mt-2 -mx-4 sm:mx-0 lg:mx-0">
            <div class="px-4 sm:px-6 pt-4 pb-1">
                <div class="min-w-0 flex gap-x-2 items-center">
                    @if($shipment->urgent)
                        <i class="fa-regular fa-light-emergency-on text-3xl/7 text-red-500"></i>
                    @endif
                    <h2 class="text-2xl/7 font-bold text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                        @can('create', \App\Models\Order::class)
                            {{$shipment->tracking_code}}
                        @else
                            {{$shipment->tracking_number}}
                        @endcan
                    </h2>
                    <span class="inline-flex items-center rounded-md  px-2 py-1 text-sm font-medium  ring-1 ring-inset {{ $shipment->order_shipment_status_id->badgeColor() }}">{{ $shipment->order_shipment_status_id->label() }}</span>
                </div>
            </div>
            <div class="px-4 sm:px-6 pt-1 pb-8 block md:flex md:justify-between">
                <div class="flex flex-1 items-center gap-x-6">
                    <img src="{{ route('clients.logos', [ 'filename' => str_replace(".","_",str_replace("logos/", "", $order->client->image))]) }}" alt="{{$order->client->trade_name}}" class="object-contain size-16 flex-none rounded-full bg-gray-200 outline -outline-offset-1 outline-black/5" />
                    <div>
                        <h1 class="mt-1 text-base font-semibold text-gray-900">{{$order->client->trade_name}}</h1>
                        <p class="text-sm/6 text-gray-700">{{$order->contact->name}}</p>
                    </div>
                </div>
                <div>
                    @can('create', \App\Models\Order::class)
                        <p class="text-xl/6 text-gray-900 font-medium text-left md:text-right">Tracking Number: {{$shipment->tracking_number}}</p>
                    @endcan
                    <p class="text-sm/6 text-gray-700 text-left md:text-right">{{$shipment->reference}}</p>
                    <p class="mt-1 text-sm text-gray-500 text-left md:text-right">{{ $shipment->created_at->isoFormat('DD/MM/YYYY [' . __('shows.at_time') . '] h:mm a') }}</p>
                    <p class="mt-1 text-sm text-gray-500 text-left md:text-right">{{ $shipment->order->createdBy->name }}</p>
                </div>
            </div>
            <div x-data="{ activeTab: {{request()->get('activeTab', 0)}} }">
                <div class="px-4 sm:px-6 border-b border-gray-200">
                    <nav aria-label="Tabs" class="-mb-px flex justify-between">
                        <div class="flex">
                            <button
                                @click="activeTab = 0"
                                class="group inline-flex items-center border-l border-t border-b border-gray-200 px-3 py-2 text-sm font-medium"
                                :class="{ 'border-t-indigo-500 text-t-indigo-600 border-t-2 border-b-white': activeTab === 0, 'text-gray-500 hover:border-gray-300 hover:text-gray-700 hover:cursor-pointer': activeTab !== 0 }"
                            >
                                {{__('shows.services')}}
                            </button>
                            <button
                                @click="activeTab = 1"
                                class="group inline-flex items-center border-l border-t border-b border-gray-200 px-3 py-2 text-sm font-medium"
                                :class="{ 'border-t-indigo-500 text-t-indigo-600 border-t-2 border-b-white': activeTab === 1, 'text-gray-500 hover:border-gray-300 hover:text-gray-700 hover:cursor-pointer': activeTab !== 1 }"

                            >
                                {{__('shows.files')}}
                            </button>
                            <button
                                @click="activeTab = 2"
                                class="group inline-flex items-center border-l border-r border-t border-b border-gray-200 px-3 py-2 text-sm font-medium"
                                :class="{ 'border-t-indigo-500 text-t-indigo-600 border-t-2 border-b-white': activeTab === 2, 'text-gray-500 hover:border-gray-300 hover:text-gray-700 hover:cursor-pointer': activeTab !== 2 }"

                            >
                                {{__('shows.status')}}
                            </button>
                        </div>
                        @can('checklist', $shipment)
                        <button
                            @click="activeTab = 3"
                            class="group inline-flex items-center border-l border-r border-t border-b border-gray-200 px-3 py-2 text-sm font-medium"
                            :class="{ 'border-t-indigo-500 text-t-indigo-600 border-t-2 border-b-white': activeTab === 3, 'text-gray-500 hover:border-gray-300 hover:text-gray-700 hover:cursor-pointer': activeTab !== 3 }"

                        >
                            {{__('shows.internal_control')}}
                        </button>
                        @endcan
                    </nav>
                </div>
                <div x-cloak x-show.transition.in.opacity.duration.600="activeTab === 0">
                    <div class="px-4 py-5 sm:px-6">
                        <div class="space-y-4 divide-y divide-gray-900/5">
                            <dl class="grid grid-cols-1 text-sm/6 sm:grid-cols-4 pb-4">
                                <div>
                                    <dt class="font-semibold text-gray-900">Service Class</dt>
                                    <dd class="text-gray-500">{{$shipment->serviceClass?->name}}</dd>
                                </div>
                                <div>
                                    <dt class="font-semibold text-gray-900">Service Mode</dt>
                                    <dd class="text-gray-500">{{$shipment->serviceMode?->name}}</dd>
                                </div>
                                <div>
                                    <dt class="font-semibold text-gray-900">Class Type</dt>
                                    <dd class="text-gray-500">{{$shipment->classType?->name}}</dd>
                                </div>
                                <div>
                                    <dt class="font-semibold text-gray-900">Service Level</dt>
                                    <dd class="text-gray-500">{{$shipment->serviceLevel?->name}}</dd>
                                </div>
                            </dl>
                            <dl class="grid grid-cols-1 text-sm/6 sm:grid-cols-2 pb-4">
                                <div>
                                    <dt class="font-semibold text-gray-900 flex gap-2 items-center">
                                        <p class="font-semibold text-gray-900">{{__('shows.from')}}</p>
                                        <button id="copyFrom" data-tippy-content="Copiar dirección" class="py-0.5 px-1 rounded hover:bg-gray-100 hover:cursor-pointer">
                                            <i class="fa-regular fa-copy"></i>
                                        </button>
                                    </dt>
                                    <dd id="copyFromSuccess" style="display: none" class="mt-1 text-green-500 text-xs">¡Dirección copiada!</dd>
                                    <dd class="mt-2">
                                        <p class="whitespace-pre-wrap text-xs text-gray-900">{{$shipment->ship_from}}</p>
                                    </dd>
                                    @if($shipment->ship_from_link)
                                    <a href="{{$shipment->ship_from_link}}" target="_blank" class="text-blue-600 underline hover:text-blue-800">
                                        {{$shipment->ship_from_link}}
                                    </a>
                                    @endif
                                </div>
                                <div>
                                    <dt class="font-semibold text-gray-900 flex gap-2 items-center">
                                        <p class="font-semibold text-gray-900">{{__('shows.to')}}</p>
                                        <button id="copyTo" data-tippy-content="Copiar dirección" class="py-0.5 px-1 rounded hover:bg-gray-100 hover:cursor-pointer">
                                            <i class="fa-regular fa-copy"></i>
                                        </button>
                                    </dt>
                                    <dd id="copyToSuccess" style="display: none" class="mt-1 text-green-500 text-xs">¡Dirección copiada!</dd>
                                    <dd class="mt-2">
                                        <p class="whitespace-pre-wrap text-xs text-gray-900">{{$shipment->ship_to}}</p>
                                    </dd>
                                    @if($shipment->ship_to_link)
                                        <a href="{{$shipment->ship_to_link}}" target="_blank" class="text-blue-600 underline hover:text-blue-800">
                                            {{$shipment->ship_to_link}}
                                        </a>
                                    @endif
                                </div>
                            </dl>
                            <livewire:shipment-handling :$shipment />
                            <dl class="grid grid-cols-1 text-sm/6 pb-4">
                                <dt class="font-semibold text-gray-900">{{__('shows.comments')}}</dt>
                                <dd class="mt-2 text-gray-500">
                                    {{ $shipment->comments == null || $shipment->comments == "" ? "Sin comentarios" : $shipment->comments }}
                                </dd>
                            </dl>
                            @can('create', \App\Models\Order::class)
                            <div class="pb-4">
                                <x-cards.transports-section
                                    :order="$order"
                                    :shipment="$shipment"
                                    :statuses="$transportationStatuses"
                                />
                            </div>
                            @endcan
                            <div class="pb-4">
                                <livewire:products-section :order="$shipment" />
                            </div>
                        </div>
                    </div>
                </div>
                <div x-cloak x-show.transition.in.opacity.duration.600="activeTab === 1">
                    @can('update', $shipment)
                    <div
                        class="px-4 py-5 sm:px-6"
                        x-data="{
                            textToCopy: '{{$shipment->driver_link}}',
                            copied: false,
                            copyToClipboard() {
                                navigator.clipboard.writeText(this.textToCopy).then(() => {
                                    this.copied = true;
                                    setTimeout(() => this.copied = false, 2000); // Hide message after 2 seconds
                                }).catch(err => {
                                    console.error('Could not copy text: ', err);
                                });
                            }
                        }"
                    >
                        <div class="border border-gray-300 rounded-lg divide-y divide-gray-300 overflow-hidden">
                            <div class="px-4 py-2 sm:px-6 flex justify-between items-center bg-gray-100">
                                <p class="font-semibold text-gray-900">Liga para chofer</p>
                                <div class="flex gap-x-3">
                                    <form action="{{ route('orders.shipments.drivers.create', ['order' => $order->id, 'shipment' => $shipment->id]) }}" method="POST">
                                        @csrf
                                        <button type="submit" data-tippy-content="Crear liga" class="text-gray-400 hover:text-gray-700 hover:cursor-pointer">
                                            <i class="fa-regular fa-circle-user-circle-plus"></i>
                                        </button>
                                    </form>
                                    <button @click="copyToClipboard()" data-tippy-content="Copiar liga" class="text-gray-400 hover:text-gray-700 hover:cursor-pointer">
                                        <i class="fa-regular fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="px-4 py-2 sm:px-6 text-sm">
                                @if($shipment->driver_link != null)
                                    @if($now < $shipment->driver_link_expires_at)
                                        @if($shipment->driver_link_used)
                                            <p class="text-gray-500">La liga para chofer ya fue utilizada, genera una nueva liga para adjuntar más documentos.</p>
                                        @else
                                            <input type="text" x-model="textToCopy" class="w-full" />
                                            <span x-show="copied" x-cloak class="mt-2 text-green-600">Liga copiada</span>
                                        @endif
                                    @else
                                        <p class="text-gray-500">La liga para chofer ya expiró, genera una nueva liga.</p>
                                    @endif
                                @else
                                    <p class="text-gray-500">Aún no se ha generado una liga para chofer.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endcan
                    <div class="border-b border-gray-300 w-full"></div>
                    <livewire:documents :order="$shipment" />
                </div>
                <div x-cloak x-show.transition.in.opacity.duration.600="activeTab === 2">
                    <div class="px-4 py-5 sm:px-6">
                        <div class="flow-root">
                            <div class="px-4 md:px-8">
                                <div x-data="{ mode: 1, showMap: @js($shipment->show_map) }" x-init="$watch('showMap', value => initGoogleMapsMap(value)); $watch('mode', val => initAutocompletesMap(val))">
                                    <div x-cloak x-show.transition.in.opacity.duration.600="mode === 1">
                                        <div class="space-y-4">
                                            <div class="sm:flex justify-start items-center p-3 border border-gray-200 rounded space-x-3">
                                                <p class="text-gray-900 font-semibold">Seguimiento de embarque</p>
                                                <i class="fa-solid fa-chevron-right text-lg"></i>
                                                <p class="text-gray-900 font-semibold">{{$shipment->tracking_number}}</p>
                                            </div>
                                            @can('update', $shipment)
                                            <div class="w-full flex justify-start gap-2">
                                                @isset($shipment->latestLocation)
                                                    <a
                                                        href="https://wa.me/?text={{ $whatsapp }}"
                                                        target="_blank"
                                                        data-tippy-content="Compartir"
                                                        class="inline-flex items-center justify-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs inset-ring inset-ring-gray-300 hover:bg-gray-50 hover:cursor-pointer"
                                                    >
                                                        <i class="fa-brands fa-whatsapp text-lg"></i>
                                                    </a>
                                                    <button
                                                        id="copyMailButton"
                                                        data-tippy-content="Copiar correo"
                                                        class="inline-flex items-center justify-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs inset-ring inset-ring-gray-300 hover:bg-gray-50 hover:cursor-pointer"
                                                    >
                                                        <i class="fa-regular fa-envelope-open-text"></i>
                                                    </button>

                                                    <form action="{{route('orders.shipments.notify', ['order' => $order->id, 'shipment' => $shipment->id])}}" method="POST">
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
                                                @endisset
                                                <button data-tippy-content="Agregar geolocalización" @click="mode = 2" class="inline-flex items-center justify-center gap-x-1.5 rounded-md bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">
                                                    <i class="fa-regular fa-location-dot"></i>
                                                    Agregar
                                                </button>
                                            </div>
                                            @endcan
                                            <div class="grid grid-cols-1 gap-x-4 gap-y-4 md:grid-cols-4">
                                                <div class="col-span-1">
                                                    <div class="rounded border border-gray-200">
                                                        <div class="p-3">
                                                            <div class="flex space-x-2 items-center justify-between">
                                                                <p class="text-xs font-semibold">
                                                                    {{$shipment->originCity->name}}, {{$shipment->originState->short_name}}, {{$shipment->originCountry->code}}
                                                                </p>
                                                                <i class="fa-solid fa-arrow-right"></i>
                                                                <p class="text-xs font-semibold">
                                                                    {{$shipment->destinationCity->name}}, {{$shipment->destinationState->short_name}}, {{$shipment->destinationCountry->code}}
                                                                </p>
                                                            </div>
                                                            <p class="text-sm pt-2">
                                                                {{$order->client->trade_name}}
                                                            </p>
                                                        </div>
                                                        <div class="h-5 bg-gray-50 border-t border-b border-gray-200">

                                                        </div>
                                                        <div x-data="{ collapsed: false }" @collapse="collapsed = !collapsed" class="flex items-center gap-x-2 pb-0 p-3">
                                                            <button
                                                                @click="$dispatch('collapse', !collapsed)"
                                                                class="rounded-sm bg-white px-2 py-1 text-sm font-semibold text-gray-900 shadow-xs inset-ring inset-ring-gray-300 hover:bg-gray-50"
                                                                x-text="collapsed ? 'Todos los estatus' : 'Último estatus'"
                                                            ></button>

                                                            @can('create', \App\Models\Order::class)
                                                                @if($mapAvailable)
                                                                    <div class="rounded-sm bg-white px-2 py-1 text-sm font-semibold text-gray-900 shadow-xs inset-ring inset-ring-gray-300 flex items-center gap-x-2">
                                                                        <p>Maps</p>
                                                                        <div class="group relative inline-flex w-9 shrink-0 rounded-full bg-red-600 p-0.5 inset-ring inset-ring-gray-900/5 outline-offset-2 outline-green-600 transition-colors duration-200 ease-in-out has-checked:bg-green-600 has-focus-visible:outline-2">
                                                                    <span class="relative size-4 rounded-full bg-white shadow-xs ring-1 ring-gray-900/5 transition-transform duration-200 ease-in-out group-has-checked:translate-x-4">
                                                                        <span
                                                                            aria-hidden="true"
                                                                            class="absolute inset-0 flex size-full items-center justify-center opacity-100 transition-opacity duration-200 ease-in group-has-checked:opacity-0 group-has-checked:duration-100 group-has-checked:ease-out text-red-600 text-[0.5rem]"
                                                                        >
                                                                            <i class="fa-solid fa-xmark"></i>
                                                                        </span>
                                                                        <span
                                                                            aria-hidden="true"
                                                                            class="absolute inset-0 flex size-full items-center justify-center opacity-0 transition-opacity duration-100 ease-out group-has-checked:opacity-100 group-has-checked:duration-200 group-has-checked:ease-in text-green-600 text-[0.5rem]"
                                                                        >
                                                                            <i class="fa-solid fa-check"></i>
                                                                        </span>
                                                                    </span>
                                                                            <input
                                                                                name="setting"
                                                                                type="checkbox"
                                                                                x-model="showMap"
                                                                                aria-label="Use setting"
                                                                                class="absolute inset-0 size-full appearance-none focus:outline-hidden"
                                                                            />
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @endcan
                                                        </div>
                                                        <ul role="list" class="space-y-2 overflow-y-auto flex-1 pt-3 pb-5 px-3" x-data="{ collapsed: false }" @collapse.window="collapsed = $event.detail">
                                                            @forelse($shipment->locations as $location)
                                                                <li class="relative flex gap-x-2" @if (!$loop->last) x-cloak x-show.transition.in.opacity.duration.600="collapsed === false" @click="collapsed = true" @endif>
                                                                    @if (!$loop->last)
                                                                        <div class="absolute top-0 -bottom-6 left-0 flex w-6 justify-center">
                                                                            <div class="w-px bg-gray-300"></div>
                                                                        </div>
                                                                    @endif
                                                                    @if ($location->service_type_status_id == 20)
                                                                        <div class="relative flex size-6 flex-none items-center justify-center bg-white">
                                                                            <i class="fa-solid fa-circle-check text-lg text-indigo-600"></i>
                                                                        </div>
                                                                    @else
                                                                        <div class="relative flex size-6 flex-none items-center justify-center bg-white">
                                                                            <div class="size-1.5 rounded-full bg-gray-500 ring ring-gray-500"></div>
                                                                        </div>
                                                                    @endif
                                                                    <div x-data="{ collapsed: false }" @collapse.window="collapsed = $event.detail">
                                                                        <div class="flex items-center gap-x-1.5 text-gray-500 flex-wrap">
                                                                            <p class="text-sm/6 text-gray-900">
                                                                                {{$location->status->name}}
                                                                            </p>
                                                                            @hasanyrole([App\Enums\RolesEnum::OPERATORADMIN, App\Enums\RolesEnum::OPERATOR])
                                                                            @if ($loop->last)
                                                                                <button @click="mode = 3; editLocation({{$location}})" class="rounded-md p-1 bg-green-100 flex items-center justify-center text-sm font-semibold text-green-500 hover:text-green-800 hover:bg-green-200">
                                                                                    <i class="fa-regular fa-pen-to-square"></i>
                                                                                </button>
                                                                            @endif
                                                                            @endhasanyrole
                                                                            @can('delete', $shipment)
                                                                                <button @click="mode = 3; editLocation({{$location}})" class="rounded-md p-1 bg-green-100 flex items-center justify-center text-sm font-semibold text-green-500 hover:text-green-800 hover:bg-green-200">
                                                                                    <i class="fa-regular fa-pen-to-square"></i>
                                                                                </button>
                                                                                <div
                                                                                    class="relative inline-block text-left"
                                                                                    x-data="{ openCancel: false }"
                                                                                >
                                                                                    <button
                                                                                        type="button"
                                                                                        @click="openCancel = true"
                                                                                        data-tippy-content="Eliminar estatus"
                                                                                        class="rounded-md p-1 bg-red-100 flex items-center justify-center text-sm font-semibold text-red-500 hover:text-red-800 hover:bg-red-200"
                                                                                    >
                                                                                        <i class="fa-regular fa-trash-can"></i>
                                                                                    </button>
                                                                                    <div x-cloak x-show="openCancel" class="relative z-100" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                                                                        <div
                                                                                            class="fixed inset-0 bg-gray-500/75 transition-opacity"
                                                                                            aria-hidden="true"
                                                                                            x-show="openCancel"
                                                                                            x-transition:enter="ease-out duration-300"
                                                                                            x-transition:enter-start="opacity-0"
                                                                                            x-transition:enter-end="opacity-100"
                                                                                            x-transition:leave="ease-in duration-200"
                                                                                            x-transition:leave-start="opacity-100"
                                                                                            x-transition:leave-end="opacity-0"
                                                                                        ></div>

                                                                                        <div class="fixed inset-0 z-100 w-screen overflow-y-auto">
                                                                                            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                                                                                                <div
                                                                                                    x-show="openCancel"
                                                                                                    x-transition:enter="ease-out duration-300"
                                                                                                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                                                                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                                                                    x-transition:leave="ease-in duration-200"
                                                                                                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                                                                                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                                                                    class="relative transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6"
                                                                                                >
                                                                                                    <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
                                                                                                        <button type="button" @click="openCancel = false" class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-hidden">
                                                                                                            <span class="sr-only">Close</span>
                                                                                                            <i class="fa-regular fa-xmark"></i>
                                                                                                        </button>
                                                                                                    </div>
                                                                                                    <div class="sm:flex sm:items-start">
                                                                                                        <div class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:size-10">
                                                                                                            <i class="fa-regular fa-triangle-exclamation text-lg text-red-600"></i>
                                                                                                        </div>
                                                                                                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left whitespace-normal">
                                                                                                            <h3 class="text-base font-semibold text-gray-900" id="modal-title">Eliminar estatus</h3>
                                                                                                            <div class="mt-2">
                                                                                                                <p class="text-sm text-gray-500 font-normal">¿Estás seguro de eliminar el estatus?</p>
                                                                                                                <p class="text-sm text-red-500 font-medium">{{$location->status->name}}</p>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                                                                                        <form action="{{route('orders.shipments.locations.destroy', ['order' => $shipment->order->id, 'shipment' => $shipment->id, 'location' => $location])}}" method="POST">
                                                                                                            @csrf
                                                                                                            @method('DELETE')
                                                                                                            <button type="submit" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500 sm:ml-3 sm:w-auto">Eliminar</button>
                                                                                                        </form>
                                                                                                        <button type="button" @click="openCancel = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs ring-1 ring-gray-300 ring-inset hover:bg-gray-50 sm:mt-0 sm:w-auto">Regresar</button>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            @endcan
                                                                        </div>
                                                                        <div>
                                                                            <div class="flex items-start gap-x-1">
                                                                                <i class="fa-regular fa-calendar-clock text-gray-500 text-lg"></i>
                                                                                <p class="py-0.5 text-xs/5 text-gray-500 text-left"><time datetime="{{$location->location_date->isoFormat('YYYY-MM-DD')}}">{{$location->location_date->isoFormat('D MMM YYYY')}}&nbsp;&nbsp;{{$location->location_date->isoFormat('h:mm a')}}</time></p>
                                                                            </div>
                                                                            @if($location->name != null)
                                                                                <div class="flex items-start gap-x-1">
                                                                                    <i class="fa-regular fa-location-dot text-gray-500 text-lg"></i>
                                                                                    <button
                                                                                        class="hover:underline hover:cursor-pointer py-0.5 text-xs/5 text-blue-700 hover:text-blue-500 text-left"
                                                                                        x-cloak x-show="showMap"
                                                                                        @click="showMap = true; openMarker({{$loop->index}})"
                                                                                    >
                                                                                        {{$location->name}}
                                                                                    </button>
                                                                                    <p class="text-gray-500 text-xs/5" x-cloak x-show="!showMap">{{$location->name}}</p>
                                                                                </div>
                                                                            @endif
                                                                            @isset($location->comments)
                                                                                <div class="flex items-start gap-x-1">
                                                                                    <i class="fa-regular fa-message-lines text-gray-500 text-lg"></i>
                                                                                    <p class="py-0.5 text-xs/5 text-gray-500 text-left">
                                                                                        {{$location->comments}}
                                                                                    </p>
                                                                                </div>
                                                                            @endisset
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            @empty
                                                                <li>
                                                                    <div class="bg-yellow-50 p-3 mb-4">
                                                                        <div class="flex">
                                                                            <div class="shrink-0">
                                                                                <i class="fa-solid fa-triangle-exclamation text-yellow-400"></i>
                                                                            </div>
                                                                            <div class="ml-3">
                                                                                <h3 class="text-sm font-medium text-yellow-800">Sin actualizaciones</h3>
                                                                                <div class="mt-2 text-sm text-yellow-700">
                                                                                    <p>Aún no se han agregado actualizaciones de estatus.</p>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            @endforelse
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="col-span-3">
                                                    <div class="w-full" x-cloak x-show="showMap">
                                                        <div class="h-60 w-full mb-3" id="map"></div>
                                                    </div>
                                                    <div class="grid grid-cols-1 gap-x-4 gap-y-4 md:grid-cols-3">
                                                        <div class="rounded border border-gray-200 col-span-2 divide-y divide-gray-200">
                                                            <div class="flex flex-1 items-center gap-x-2 p-3">
                                                                <img src="{{ route('clients.logos', [ 'filename' => str_replace(".","_",str_replace("logos/", "", $order->client->image))]) }}" alt="{{$order->client->trade_name}}" class="object-contain size-10 flex-none rounded-full bg-gray-200 outline -outline-offset-1 outline-black/5" />
                                                                <div>
                                                                    <h1 class="mt-1 text-sm font-semibold text-gray-900">{{$order->client->trade_name}}</h1>
                                                                    <p class="text-xs/5 text-gray-700">{{$order->contact->name}}</p>
                                                                </div>
                                                            </div>
                                                            <div class="flex flex-1 items-center justify-end bg-gray-50 py-1 p-3">
                                                                <p class="text-xs/5 text-gray-500">
                                                                    Última actualización: @if ($shipment->latestLocation) <span><time datetime="{{$shipment->latestLocation->location_date->isoFormat('YYYY-MM-DD')}}">{{$shipment->latestLocation->location_date->isoFormat('D MMM YYYY')}}&nbsp;&nbsp;{{$shipment->latestLocation->location_date->isoFormat('h:mm a')}}</time></span>@else Sin estatus @endif
                                                                </p>
                                                            </div>
                                                            <div class="flex space-x-2 items-center justify-around p-3">
                                                                <p class="text-sm">
                                                                    {{$shipment->originCity->name}}, {{$shipment->originState->short_name}}, {{$shipment->originCountry->code}}
                                                                </p>
                                                                <i class="fa-solid fa-arrow-right"></i>
                                                                <p class="text-sm">
                                                                    {{$shipment->destinationCity->name}}, {{$shipment->destinationState->short_name}}, {{$shipment->destinationCountry->code}}
                                                                </p>
                                                            </div>
                                                            @if($shipment->latestLocation)
                                                                <div class="p-3 {{$shipment->latestLocation->status->color}}">
                                                                    <p class="text-center text-sm font-semibold">
                                                                        {{$shipment->latestLocation->status->name}}
                                                                    </p>
                                                                    <p class="text-center text-xs">
                                                                        {{$shipment->latestLocation->location_date->isoFormat('D MMM YYYY')}}&nbsp;&nbsp;{{$shipment->latestLocation->location_date->isoFormat('h:mm a')}}
                                                                    </p>
                                                                </div>
                                                            @endif
                                                            <div class="grid grid-cols-1 md:grid-cols-2 p-3 gap-y-2 gap-x-3">
                                                                <div class="col-span-1">
                                                                    <p class="text-xs/5 text-gray-500">Estimated Time of Departure</p>
                                                                    <p class="text-sm/6 text-gray-900">{{ $shipment->estimated_time_departure?->isoFormat('DD/MM/YYYY') }}</p>
                                                                </div>
                                                                <div class="col-span-1">
                                                                    <p class="text-xs/5 text-gray-500">Estimated Time of Arrival</p>
                                                                    <p class="text-sm/6 text-gray-900">{{ $shipment->estimated_time_arrival?->isoFormat('DD/MM/YYYY') }}</p>
                                                                </div>
                                                                <div class="col-span-1">
                                                                    <p class="text-xs/5 text-gray-500">Actual Time of Departure</p>
                                                                    <p class="text-sm/6 text-gray-900">
                                                                        @if($shipment->start_date)
                                                                            {{ $shipment->start_date?->isoFormat('DD/MM/YYYY') }}
                                                                        @else
                                                                            Pendiente
                                                                        @endif
                                                                    </p>
                                                                </div>
                                                                <div class="col-span-1">
                                                                    <p class="text-xs/5 text-gray-500">Actual Time of Arrival</p>
                                                                    <p class="text-sm/6 text-gray-900">
                                                                        @if($shipment->end_date)
                                                                            {{ $shipment->end_date?->isoFormat('DD/MM/YYYY') }}
                                                                        @else
                                                                            Pendiente
                                                                        @endif
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="rounded border border-gray-200 col-span-1 p-3">
                                                            <p class="text-sm font-semibold pb-2">
                                                                Notas
                                                            </p>
                                                            <ul class="space-y-3">
                                                                @forelse(collect($shipment->locations)->whereNotNull('comments') as $location)
                                                                    <li>
                                                                        <p class="text-sm">{{$location->comments}}</p>
                                                                        <p class="text-xs/5 text-gray-500"><time datetime="{{$location->location_date->isoFormat('YYYY-MM-DD')}}">{{$location->location_date->isoFormat('D MMM YYYY')}}&nbsp;&nbsp;{{$location->location_date->isoFormat('h:mm a')}}</time></p>
                                                                    </li>
                                                                @empty
                                                                    <li>
                                                                        <p class="text-gray-200">No hay observaciones en los estatus del embarque.</p>
                                                                    </li>
                                                                @endforelse
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-span-1">

                                                </div>
                                            </div>
                                            <div>
                                            </div>
                                        </div>
                                    </div>
                                    @can('update', $shipment)
                                        <div x-cloak x-show.transition.in.opacity.duration.600="mode === 2">
                                            <form action="{{ route('orders.shipments.locations.store', ['order' => $order->id, 'shipment' => $shipment->id]) }}" method="POST" class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-4" autocomplete="off">
                                                @csrf
                                                <div class="sm:col-span-full">
                                                    <h2 class="text-base/7 font-semibold text-gray-900">Nuevo estatus</h2>
                                                    <p class="mt-1 text-sm/6 text-gray-600">Agrega un estatus al embarque, puedes buscar una dirección para ingresar una nueva geolocalización.</p>
                                                </div>
                                                <div class="sm:col-span-2">
                                                    <div class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-4">
                                                        <div class="sm:col-span-2">
                                                            <label for="location_date" class="block text-sm/6 font-medium text-gray-900">Fecha</label>
                                                            <div class="mt-2">
                                                                <input id="location_date" required value="{{old('location_date', $now->format('Y-m-d\TH:i'))}}" name="location_date" type="datetime-local" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                            </div>
                                                        </div>
                                                        <div class="sm:col-span-2">
                                                            <label for="service_type_status_id" class="block text-sm/6 font-medium text-gray-900">Estatus</label>
                                                            <div class="mt-2 grid grid-cols-1">
                                                                <select id="service_type_status_id" name="service_type_status_id" required autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white py-1.5 pr-8 pl-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                                    <option value="" selected disabled>Selecciona un nuevo estatus</option>
                                                                    @if ($shipment->serviceMode)
                                                                        @foreach ($shipment->serviceMode->statuses as $status)
                                                                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                                <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 sm:text-sm"></i>
                                                            </div>
                                                        </div>
                                                        <div class="sm:col-span-full">
                                                            <label for="comments" class="block text-sm/6 font-medium text-gray-900">Comentarios</label>
                                                            <div class="mt-2">
                                                                <textarea id="comments" name="comments" autocomplete="off" class="@error('comments') outline-red-400 @else outline-gray-300 @enderror block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">{{old('comments')}}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="sm:col-span-full">
                                                            <label for="name" class="block text-sm/6 font-medium text-gray-900">Ubicación</label>
                                                            <div class="mt-2" id="location-name-div">
                                                                <input id="location-name" value="{{old('location-name')}}" name="name" type="hidden">
                                                                <input id="latitude" value="{{old('latitude')}}" name="latitude" type="hidden">
                                                                <input id="longitude" value="{{old('longitude')}}" name="longitude" type="hidden">
                                                            </div>
                                                        </div>
                                                        <div class="sm:col-span-full">
                                                            <div class="flex items-center justify-end gap-x-6">
                                                                <button
                                                                    class="text-sm/6 font-semibold text-gray-900 hover:cursor-pointer hover:text-gray-500"
                                                                    @click="mode = 1"
                                                                    type="button"
                                                                >
                                                                    Cancelar
                                                                </button>
                                                                <button type="submit" class="inline-flex items-center justify-center gap-x-1.5 rounded-md bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">
                                                                    <i class="fa-regular fa-location-dot"></i>
                                                                    Agregar
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="sm:col-span-2">
                                                    <div class="h-full w-full aspect-square rounded-3xl" id="location-map"></div>
                                                </div>
                                            </form>
                                        </div>
                                        @isset($shipment->latestLocation)
                                        <div x-cloak x-show.transition.in.opacity.duration.600="mode === 3">
                                            <form id="edit-location-form" method="POST" class="grid grid-cols-1 gap-x-4 gap-y-4 lg:grid-cols-4" autocomplete="off">
                                                @csrf
                                                @method('PUT')
                                                <div class="sm:col-span-full">
                                                    <h2 class="text-base/7 font-semibold text-gray-900">Editar estatus</h2>
                                                    <p class="mt-1 text-sm/6 text-gray-600">Edita el estatus del embarque, puedes buscar una dirección para editar la geolocalización.</p>
                                                </div>
                                                <div class="sm:col-span-2">
                                                    <div class="grid grid-cols-1 gap-x-4 gap-y-4 lg:grid-cols-4">
                                                        <div class="sm:col-span-2">
                                                            <label for="edit-location_date" class="block text-sm/6 font-medium text-gray-900">Fecha</label>
                                                            <div class="mt-2">
                                                                <input id="edit-location_date" required name="location_date" type="datetime-local" autocomplete="off" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                            </div>
                                                        </div>
                                                        <div class="sm:col-span-2">
                                                            <label for="edit-service_type_status_id" class="block text-sm/6 font-medium text-gray-900">Estatus</label>
                                                            <div class="mt-2 grid grid-cols-1">
                                                                <select id="edit-service_type_status_id" name="service_type_status_id" required autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white py-1.5 pr-8 pl-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                                    @foreach ($shipment->serviceMode?->statuses as $status)
                                                                        <option value= {{ $status->id }} >{{ $status->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 sm:text-sm"></i>
                                                            </div>
                                                        </div>
                                                        <div class="sm:col-span-full">
                                                            <label for="edit-comments" class="block text-sm/6 font-medium text-gray-900">Comentarios</label>
                                                            <div class="mt-2">
                                                                <textarea id="edit-comments" name="comments" autocomplete="off" class="@error('comments') outline-red-400 @else outline-gray-300 @enderror block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="sm:col-span-full">
                                                            <label for="name" class="block text-sm/6 font-medium text-gray-900">Ubicación</label>
                                                            <div class="mt-2" id="edit-location-name-div">
                                                                <input id="edit-location-name" name="name" type="hidden">
                                                                <input id="edit-latitude" name="latitude" type="hidden">
                                                                <input id="edit-longitude" name="longitude" type="hidden">
                                                            </div>
                                                        </div>
                                                        <div class="sm:col-span-full">
                                                            <div class="flex items-center justify-end gap-x-6">
                                                                <button
                                                                    class="text-sm/6 font-semibold text-gray-900 hover:cursor-pointer hover:text-gray-500"
                                                                    @click="mode = 1"
                                                                    type="button"
                                                                >
                                                                    Cancelar
                                                                </button>
                                                                <button type="submit" class="inline-flex items-center justify-center gap-x-1.5 rounded-md bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">
                                                                    <i class="fa-regular fa-location-dot"></i>
                                                                    Editar
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="sm:col-span-2">
                                                    <div class="h-full w-full aspect-square rounded-3xl" id="edit-location-map"></div>
                                                </div>
                                            </form>
                                        </div>
                                        @endisset
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @can('checklist', $shipment)
                <div x-cloak x-show.transition.in.opacity.duration.600="activeTab === 3">
                    <div class="px-4 py-5 sm:px-6">
                        <div class="mb-2 md:flex md:items-center md:justify-between">
                            <div class="">
                                <h2 class="text-2xl/7 font-bold text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">Checklist</h2>
                            </div>
                            <div class="mt-3 flex sm:mt-0 sm:ml-4">
                                @can('updateChecklist', $shipment)
                                    <a
                                        href='{{route('orders.shipments.privates.create', [ 'order' => $shipment->order, 'shipment' => $shipment ])}}'
                                        data-tippy-content="Adjuntar archivo interno"
                                        role="button"
                                        class="mr-3 inline-flex items-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs inset-ring inset-ring-gray-300 hover:bg-gray-50 whitespace-nowrap"
                                    >
                                        <i class="fa-regular fa-paperclip"></i>
                                        Adjuntar Interno
                                    </a>
                                    <div
                                        class="relative inline-block text-left"
                                        x-data="{ openComments: false }"
                                    >
                                        <button
                                            type="button"
                                            x-on:click="openComments = true"
                                            class="mr-3 inline-flex items-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs inset-ring inset-ring-gray-300 hover:bg-gray-50 hover:cursor-pointer"
                                        >
                                            Comentarios
                                        </button>
                                        <div x-cloak x-show="openComments" class="relative z-100" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                            <div
                                                class="fixed inset-0 bg-gray-500/75 transition-opacity"
                                                aria-hidden="true"
                                                x-show="openComments"
                                                x-transition:enter="ease-out duration-300"
                                                x-transition:enter-start="opacity-0"
                                                x-transition:enter-end="opacity-100"
                                                x-transition:leave="ease-in duration-200"
                                                x-transition:leave-start="opacity-100"
                                                x-transition:leave-end="opacity-0"
                                            ></div>

                                            <div class="fixed inset-0 z-100 w-screen overflow-y-auto">
                                                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                                                    <div
                                                        x-show="openComments"
                                                        x-transition:enter="ease-out duration-300"
                                                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                        x-transition:leave="ease-in duration-200"
                                                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                        class="relative transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6"
                                                    >
                                                        <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
                                                            <button type="button" @click="openComments = false" class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-hidden">
                                                                <span class="sr-only">Close</span>
                                                                <i class="fa-regular fa-xmark"></i>
                                                            </button>
                                                        </div>
                                                        <form action="{{route('orders.shipments.update.checklist', ['order' => $order, 'shipment' => $shipment])}}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="mt-3 text-center sm:mt-0 sm:text-left whitespace-normal">
                                                                <h3 class="text-base font-semibold text-gray-900" id="modal-title">Editar checklist</h3>
                                                                <div class="mt-2">
                                                                    <p class="text-sm text-gray-500 font-normal">Edita los comentarios del checklist</p>
                                                                    <div class="col-span-full">
                                                                        <label for="checklist_comments" class="block text-sm/6 font-medium text-gray-900">Comentarios</label>
                                                                        <div class="mt-2">
                                                                            <textarea id="checklist_comments" name="checklist_comments" autocomplete="off" class=" block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">{{old('checklist_comments', $shipment->checklist_comments)}}</textarea>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                                                <button type="submit" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500 sm:ml-3 sm:w-auto">Guardar</button>
                                                                <button type="button" @click="openComments = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs ring-1 ring-gray-300 ring-inset hover:bg-gray-50 sm:mt-0 sm:w-auto">Cancelar</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endcan
                                <a
                                    href="{{route('orders.shipments.printCL', [ 'order' => $order->id, 'shipment' => $shipment->id ])}}"
                                    data-tippy-content="Descargar checklist"
                                    role="button"
                                    class="mr-3 inline-flex items-center gap-x-1.5 rounded-md bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer"
                                >
                                    <i class="fa-regular fa-arrow-down-to-bracket"></i>
                                    Descargar
                                </a>
                            </div>
                        </div>
                        <div class="mt-8">
                            <div class="-mx-4 sm:mx-0 overflow-auto">
                                <div class="bg-white shadow-lits-card w-fit h-fit mx-auto border" style="width: 816px;padding: 48px;">
                                    <x-cards.checklist :order="$order" :service="$shipment" :documentTypes="$documentTypes" :milestones="$milestones" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <x-cards.privatedocuments
                        :order="$shipment"
                        :documents="$shipment->privateDocuments"
                        :create="route('orders.shipments.privates.create', [ 'order' => $shipment->order, 'shipment' => $shipment ])"
                        :download="route('orders.shipments.privates.index', ['order' => $order->id, 'shipment' => $shipment->id])"
                        :destroyRoute="'orders.privates.destroy'"
                        :destroyParams="['order' => $order->id]"
                    />
                </div>
                @endcan
            </div>
        </div>
    </div>
</x-layout-app>
