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

        <div class="mt-5 flex items-start justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-2.5 flex-wrap">
                <div class="size-9 shrink-0 rounded-lg flex items-center justify-center bg-entity-shipments-50 dark:bg-entity-shipments/15 text-entity-shipments">
                    <i class="fa-regular fa-route text-base"></i>
                </div>
                @if($shipment->urgent)
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-lits-red-50 dark:bg-lits-red-500/15 px-2.5 py-1 text-xs font-bold text-lits-red-600 dark:text-lits-red-400">
                        <i class="fa-regular fa-light-emergency-on"></i>
                        {{__('Urgent')}}
                    </span>
                @endif
                <h1 class="text-xl font-bold tracking-tight text-gray-900 dark:text-gray-50">
                    @can('create', \App\Models\Order::class)
                        {{$shipment->tracking_code}}
                    @else
                        {{$shipment->tracking_number}}
                    @endcan
                </h1>
                <span class="inline-flex items-center gap-1.5 text-xs font-medium {{ $shipment->order_shipment_status_id->textColor() }}">
                    <span class="size-1.5 rounded-full {{ $shipment->order_shipment_status_id->dotColor() }}"></span>
                    {{ $shipment->order_shipment_status_id->label() }}
                </span>
            </div>
            <div class="flex items-center gap-1.5 flex-wrap">
                @can('update', $shipment)
                    <button
                        type="button"
                        @click="window.dispatchEvent(new CustomEvent('open-edit-shipment-drawer'))"
                        data-tippy-content="{{__('shows.edit_service')}}"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:cursor-pointer"
                    >
                        <i class="fa-regular fa-pen-to-square"></i>
                        {{__('shows.edit')}}
                    </button>
                @endcan
                @can('attach', $shipment)
                    <a
                        href='{{route('orders.shipments.documents.create', [ 'order' => $shipment->order, 'shipment' => $shipment ])}}'
                        data-tippy-content="{{__('shows.attach_file')}}"
                        role="button"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800"
                    >
                        <i class="fa-regular fa-paperclip"></i>
                        {{__('shows.attach_file')}}
                    </a>
                @endcan
                <a
                    href="{{route('orders.shipments.bol', [ 'order' => $order->id, 'shipment' => $shipment->id ])}}"
                    data-tippy-content="Bill Of Lading"
                    role="button"
                    class="whitespace-nowrap inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800"
                >
                    <i class="fa-regular fa-file-contract shrink-0"></i>
                    Bill Of Lading
                </a>
                @can('clone', $shipment)
                    <a
                        href="{{route('clone.order.create', [ 'shipment' => $shipment->id ])}}"
                        data-tippy-content="{{__('shows.duplicate')}}"
                        role="button"
                        class="whitespace-nowrap inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800"
                    >
                        <i class="fa-regular fa-copy shrink-0"></i>
                        {{__('shows.duplicate')}}
                    </a>
                @endcan
                @canany(['update', 'restore'], $shipment)
                    <form action="{{ route('orders.shipments.update.status', ['order' => $order->id, 'shipment' => $shipment->id]) }}" method="POST" class="flex">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 focus-within:relative">
                            <select id="order_shipment_status_id" name="order_shipment_status_id" autocomplete="off" aria-label="{{__('indexes.status')}}" class="col-start-1 row-start-1 w-full appearance-none rounded-l-lg border border-r-0 border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 py-2 pr-8 pl-3 text-sm text-gray-900 dark:text-gray-50 outline-none focus:ring-2 focus:ring-indigo-600">
                                @foreach ($statuses as $status)
                                    <option value= {{ $status->id }} @selected($shipment->order_shipment_status_id->value == $status->id)>{{ $status->name }}  </option>
                                @endforeach
                            </select>
                            <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2.5 self-center justify-self-end text-xs text-gray-400 dark:text-gray-500"></i>
                        </div>
                        <button data-tippy-content="{{__('shows.save_status')}}" type="submit" class="flex items-center gap-x-1.5 rounded-r-lg border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:cursor-pointer">
                            <i class="fa-regular fa-floppy-disk"></i>
                        </button>
                    </form>
                @endcanany
            </div>
        </div>

        @if(session()->has('success'))
            <x-alerts.success class="mt-4" :message="session('success')" />
        @endif
        @if ($errors->any() && old('_drawer') !== 'shipment-edit')
            <x-alerts.error :message="__('shows.service_errors')" :errors="$errors" class="my-4" />
        @endif
        <div id="copyAlert" style="display: none" class="mt-2 rounded-md bg-green-50 dark:bg-green-500/10 p-4 border border-green-400">
            <div class="flex items-center">
                <div class="shrink-0">
                    <i class="fa-solid fa-circle-check text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800 dark:text-green-400">El correo se copió a tu portapapeles</p>
                </div>
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button id="closeCopyAlert" type="button" class="inline-flex rounded-md bg-green-50 dark:bg-green-500/10 p-1.5 text-green-500 dark:text-green-400 hover:bg-green-100 focus:ring-2 focus:ring-green-600 focus:ring-offset-2 focus:ring-offset-green-50 focus:outline-hidden">
                            <span class="sr-only">Dismiss</span>
                            <i class="fa-regular fa-xmark"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-5 flex items-start justify-between gap-4 flex-wrap py-4 border-t border-b border-gray-200 dark:border-lits-blue-450">
            <div class="flex items-center gap-3">
                <img src="{{ route('clients.logos', [ 'filename' => str_replace(".","_",str_replace("logos/", "", $order->client->image))]) }}" alt="{{$order->client->trade_name}}" class="object-contain size-11 flex-none rounded-full bg-gray-200 dark:bg-gray-700 outline -outline-offset-1 outline-black/5" />
                <div>
                    <div class="text-sm font-bold text-gray-900 dark:text-gray-50">{{$order->client->trade_name}}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{$order->contact->name}}</div>
                </div>
            </div>
            <div class="flex items-start gap-6 flex-wrap">
                @can('create', \App\Models\Order::class)
                    <div>
                        <div class="text-[11px] text-gray-400 dark:text-gray-500 mb-0.5">Tracking Number</div>
                        <div class="text-sm font-medium text-gray-700 dark:text-gray-300">{{$shipment->tracking_number}}</div>
                    </div>
                @endcan
                <div>
                    <div class="text-[11px] text-gray-400 dark:text-gray-500 mb-0.5">{{__('indexes.reference')}}</div>
                    <div class="text-sm font-medium text-gray-700 dark:text-gray-300 max-w-xs break-words">{{$shipment->reference}}</div>
                </div>
                <div>
                    <div class="text-[11px] text-gray-400 dark:text-gray-500 mb-0.5">{{__('shows.created_at')}}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $shipment->created_at->isoFormat('DD/MM/YYYY ['. __('shows.at_time') .'] h:mm a') }}</div>
                </div>
                <div>
                    <div class="text-[11px] text-gray-400 dark:text-gray-500 mb-0.5">Creada por</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $shipment->order->createdBy->name }}</div>
                </div>
            </div>
        </div>

        <div x-data="{ activeTab: {{request()->get('activeTab', 0)}} }" class="mt-6">
                <div class="flex items-center gap-6">
                    <button
                        @click="activeTab = 0"
                        class="py-3 text-sm border-b-2 hover:cursor-pointer"
                        :class="{ 'border-lits-red-500 font-semibold text-gray-900 dark:text-gray-50': activeTab === 0, 'border-transparent font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200': activeTab !== 0 }"
                    >
                        {{__('shows.services')}}
                    </button>
                    <button
                        @click="activeTab = 1"
                        class="py-3 text-sm border-b-2 hover:cursor-pointer"
                        :class="{ 'border-lits-red-500 font-semibold text-gray-900 dark:text-gray-50': activeTab === 1, 'border-transparent font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200': activeTab !== 1 }"
                    >
                        {{__('shows.files')}}
                    </button>
                    <button
                        @click="activeTab = 2"
                        class="py-3 text-sm border-b-2 hover:cursor-pointer"
                        :class="{ 'border-lits-red-500 font-semibold text-gray-900 dark:text-gray-50': activeTab === 2, 'border-transparent font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200': activeTab !== 2 }"
                    >
                        {{__('shows.status')}}
                    </button>
                    @can('checklist', $shipment)
                    <button
                        @click="activeTab = 3"
                        class="py-3 text-sm border-b-2 hover:cursor-pointer"
                        :class="{ 'border-lits-red-500 font-semibold text-gray-900 dark:text-gray-50': activeTab === 3, 'border-transparent font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200': activeTab !== 3 }"
                    >
                        {{__('shows.internal_control')}}
                    </button>
                    @endcan
                </div>
                <div class="border-b border-gray-200 dark:border-lits-blue-450 -mt-px mb-5"></div>
                <div x-cloak x-show.transition.in.opacity.duration.600="activeTab === 0" class="pt-2">
                    <div class="flex flex-col gap-6">
                        <div class="grid grid-cols-1 md:grid-cols-6 items-start gap-6">
                            <div class="md:col-span-2">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-50">{{__('shows.from')}}</span>
                                    <button id="copyFrom" data-tippy-content="Copiar dirección" class="size-6 shrink-0 rounded-md flex items-center justify-center text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-200 hover:cursor-pointer">
                                        <i class="fa-regular fa-copy text-xs"></i>
                                    </button>
                                </div>
                                <p id="copyFromSuccess" style="display: none" class="mt-1 text-xs text-green-500 dark:text-green-400">¡Dirección copiada!</p>
                                <p class="mt-2 whitespace-pre-wrap text-sm text-gray-500 dark:text-gray-400">{{$shipment->ship_from}}</p>
                                @if($shipment->ship_from_link)
                                    <a href="{{$shipment->ship_from_link}}" target="_blank" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">{{$shipment->ship_from_link}}</a>
                                @endif
                            </div>
                            <div class="md:col-span-2">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-50">{{__('shows.to')}}</span>
                                    <button id="copyTo" data-tippy-content="Copiar dirección" class="size-6 shrink-0 rounded-md flex items-center justify-center text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-200 hover:cursor-pointer">
                                        <i class="fa-regular fa-copy text-xs"></i>
                                    </button>
                                </div>
                                <p id="copyToSuccess" style="display: none" class="mt-1 text-xs text-green-500 dark:text-green-400">¡Dirección copiada!</p>
                                <p class="mt-2 whitespace-pre-wrap text-sm text-gray-500 dark:text-gray-400">{{$shipment->ship_to}}</p>
                                @if($shipment->ship_to_link)
                                    <a href="{{$shipment->ship_to_link}}" target="_blank" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">{{$shipment->ship_to_link}}</a>
                                @endif
                            </div>
                            <div class="md:col-span-1">
                                <div class="text-[11px] text-gray-400 dark:text-gray-500 mb-1.5">Servicio</div>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach([
                                        'Service Class' => $shipment->serviceClass?->code,
                                        'Service Mode' => $shipment->serviceMode?->code,
                                        'Class Type' => $shipment->classType?->code,
                                        'Service Level' => $shipment->serviceLevel?->code,
                                    ] as $label => $code)
                                        @if($code)
                                            <span data-tippy-content="{{ $label }}" class="text-[10.5px] font-semibold px-1.5 py-0.5 rounded border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 text-gray-400 dark:text-gray-500 hover:cursor-help">{{ $code }}</span>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            <div class="md:col-span-1">
                                <livewire:shipment-handling :$shipment />
                            </div>
                        </div>

                        @can('create', \App\Models\Order::class)
                            <div class="pt-6 border-t border-gray-200 dark:border-lits-blue-450/60">
                                <x-cards.transports-section
                                    :order="$order"
                                    :shipment="$shipment"
                                    :statuses="$transportationStatuses"
                                />
                            </div>
                        @endcan

                        <div class="pt-6 border-t border-gray-200 dark:border-lits-blue-450/60">
                            <div class="text-[11px] text-gray-400 dark:text-gray-500 mb-1">{{__('shows.comments')}}</div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $shipment->comments == null || $shipment->comments == "" ? "Sin comentarios" : $shipment->comments }}
                            </p>
                        </div>

                        <div class="pt-6 border-t border-gray-200 dark:border-lits-blue-450/60">
                            <livewire:products-section :order="$shipment" />
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
                        <div class="border border-gray-300 dark:border-gray-600 rounded-lg divide-y divide-gray-300 dark:divide-gray-600 overflow-hidden">
                            <div class="px-4 py-2 sm:px-6 flex justify-between items-center bg-gray-100 dark:bg-gray-800">
                                <p class="font-semibold text-gray-900 dark:text-gray-50">Liga para chofer</p>
                                <div class="flex gap-x-3">
                                    <form action="{{ route('orders.shipments.drivers.create', ['order' => $order->id, 'shipment' => $shipment->id]) }}" method="POST">
                                        @csrf
                                        <button type="submit" data-tippy-content="Crear liga" class="text-gray-400 dark:text-gray-500 hover:text-gray-700 hover:cursor-pointer">
                                            <i class="fa-regular fa-circle-user-circle-plus"></i>
                                        </button>
                                    </form>
                                    <button @click="copyToClipboard()" data-tippy-content="Copiar liga" class="text-gray-400 dark:text-gray-500 hover:text-gray-700 hover:cursor-pointer">
                                        <i class="fa-regular fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="px-4 py-2 sm:px-6 text-sm">
                                @if($shipment->driver_link != null)
                                    @if($now < $shipment->driver_link_expires_at)
                                        @if($shipment->driver_link_used)
                                            <p class="text-gray-500 dark:text-gray-400">La liga para chofer ya fue utilizada, genera una nueva liga para adjuntar más documentos.</p>
                                        @else
                                            <input type="text" x-model="textToCopy" class="w-full" />
                                            <span x-show="copied" x-cloak class="mt-2 text-green-600 dark:text-green-400">Liga copiada</span>
                                        @endif
                                    @else
                                        <p class="text-gray-500 dark:text-gray-400">La liga para chofer ya expiró, genera una nueva liga.</p>
                                    @endif
                                @else
                                    <p class="text-gray-500 dark:text-gray-400">Aún no se ha generado una liga para chofer.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endcan
                    <div class="border-b border-gray-300 dark:border-gray-600 w-full"></div>
                    <livewire:documents :order="$shipment" />
                </div>
                <div x-cloak x-show.transition.in.opacity.duration.600="activeTab === 2" class="pt-2">
                    <div x-data="{ mode: 1, showMap: @js($shipment->show_map) }" x-init="$watch('showMap', value => initGoogleMapsMap(value)); $watch('mode', val => initAutocompletesMap(val))">
                        <div x-cloak x-show.transition.in.opacity.duration.600="mode === 1">
                            <div class="flex flex-col gap-6">
                                <div class="flex items-center justify-between gap-3 flex-wrap">
                                    <div>
                                        <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-50">Seguimiento de embarque</h2>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{$shipment->tracking_number}}</p>
                                    </div>
                                    @can('update', $shipment)
                                        <div class="flex items-center gap-2 flex-wrap">
                                            @isset($shipment->latestLocation)
                                                <a
                                                    href="https://wa.me/?text={{ $whatsapp }}"
                                                    target="_blank"
                                                    data-tippy-content="Compartir por WhatsApp"
                                                    class="inline-flex items-center justify-center size-9 rounded-lg border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:cursor-pointer"
                                                >
                                                    <i class="fa-brands fa-whatsapp"></i>
                                                </a>
                                                <button
                                                    id="copyMailButton"
                                                    data-tippy-content="Copiar correo"
                                                    class="inline-flex items-center justify-center size-9 rounded-lg border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:cursor-pointer"
                                                >
                                                    <i class="fa-regular fa-envelope-open-text"></i>
                                                </button>
                                                <form action="{{route('orders.shipments.notify', ['order' => $order->id, 'shipment' => $shipment->id])}}" method="POST">
                                                    @csrf
                                                    <button
                                                        type="submit"
                                                        data-tippy-content="Notificar al cliente"
                                                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-lits-blue-450 bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:cursor-pointer"
                                                    >
                                                        <i class="fa-regular fa-envelope"></i>
                                                        Notificar
                                                    </button>
                                                </form>
                                            @endisset
                                            <button data-tippy-content="Agregar geolocalización" @click="mode = 2" class="inline-flex items-center gap-1.5 rounded-lg bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">
                                                <i class="fa-regular fa-location-dot"></i>
                                                Agregar
                                            </button>
                                        </div>
                                    @endcan
                                </div>

                                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                                    <div class="lg:col-span-5">
                                        <div class="rounded-lg border border-gray-200 dark:border-lits-blue-450 p-4">
                                            @php
                                                $locations = $shipment->locations;
                                                $total = $locations->count();
                                                $newest = $locations->last();
                                                $oldest = $total > 1 ? $locations->first() : null;
                                                $middle = $total > 2 ? $locations->slice(1, $total - 2) : collect();
                                            @endphp
                                            <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 mb-4">
                                                {{ $total }} {{ $total == 1 ? 'evento' : 'eventos' }}
                                            </p>
                                            <ul role="list" x-data="{ expanded: false }">
                                                @if($newest)
                                                    <li class="relative flex gap-x-2 @if($total > 1) pb-6 @endif">
                                                        @if($total > 1)
                                                            <div class="absolute top-0 -bottom-0 left-0 flex w-6 justify-center">
                                                                <div class="w-px bg-gray-200 dark:bg-lits-blue-450"></div>
                                                            </div>
                                                        @endif
                                                        @if ($newest->service_type_status_id == 20)
                                                            <div class="relative flex size-6 flex-none items-center justify-center bg-body dark:bg-lits-blue-600">
                                                                <i class="fa-solid fa-circle-check text-lg text-indigo-600 dark:text-indigo-400"></i>
                                                            </div>
                                                        @else
                                                            <div class="relative flex size-6 flex-none items-center justify-center bg-body dark:bg-lits-blue-600">
                                                                <div class="size-1.5 rounded-full bg-entity-shipments shadow-[0_0_8px_2px_#0891B2]"></div>
                                                            </div>
                                                        @endif
                                                        <div class="flex-1 min-w-0">
                                                            <div class="flex items-center gap-x-1 text-gray-500 dark:text-gray-400 flex-wrap">
                                                                <p class="text-sm/6 text-gray-900 dark:text-gray-50 font-medium">
                                                                    {{$newest->status->name}}
                                                                </p>
                                                                @hasanyrole([App\Enums\RolesEnum::OPERATORADMIN, App\Enums\RolesEnum::OPERATOR])
                                                                    <button @click="mode = 3; editLocation({{$newest}})" data-tippy-content="Editar estatus" class="size-7 shrink-0 rounded-md flex items-center justify-center text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-200">
                                                                        <i class="fa-regular fa-pen-to-square text-xs"></i>
                                                                    </button>
                                                                @endhasanyrole
                                                                @can('delete', $shipment)
                                                                    <button @click="mode = 3; editLocation({{$newest}})" data-tippy-content="Editar estatus" class="size-7 shrink-0 rounded-md flex items-center justify-center text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-200">
                                                                        <i class="fa-regular fa-pen-to-square text-xs"></i>
                                                                    </button>
                                                                    <div
                                                                        class="relative inline-block text-left"
                                                                        x-data="{ openCancel: false }"
                                                                    >
                                                                        <button
                                                                            type="button"
                                                                            @click="openCancel = true"
                                                                            data-tippy-content="Eliminar estatus"
                                                                            class="size-7 shrink-0 rounded-md flex items-center justify-center text-gray-400 dark:text-gray-500 hover:bg-red-50 dark:hover:bg-red-500/15 hover:text-red-600 dark:hover:text-red-400 hover:cursor-pointer"
                                                                        >
                                                                            <i class="fa-regular fa-trash-can text-xs"></i>
                                                                        </button>
                                                                        <div x-cloak x-show="openCancel" class="relative z-100" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                                                            <div
                                                                                class="fixed inset-0 bg-gray-500/75 dark:bg-gray-950/75 transition-opacity"
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
                                                                                        class="relative transform overflow-hidden rounded-lg bg-white dark:bg-lits-blue-550 px-4 pt-5 pb-4 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6"
                                                                                    >
                                                                                        <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
                                                                                            <button type="button" @click="openCancel = false" class="rounded-md bg-white dark:bg-lits-blue-550 text-gray-400 dark:text-gray-500 hover:text-gray-500 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-hidden">
                                                                                                <span class="sr-only">Close</span>
                                                                                                <i class="fa-regular fa-xmark"></i>
                                                                                            </button>
                                                                                        </div>
                                                                                        <div class="sm:flex sm:items-start">
                                                                                            <div class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-500/15 sm:mx-0 sm:size-10">
                                                                                                <i class="fa-regular fa-triangle-exclamation text-lg text-red-600 dark:text-red-400"></i>
                                                                                            </div>
                                                                                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left whitespace-normal">
                                                                                                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-50" id="modal-title">Eliminar estatus</h3>
                                                                                                <div class="mt-2">
                                                                                                    <p class="text-sm text-gray-500 dark:text-gray-400 font-normal">¿Estás seguro de eliminar el estatus?</p>
                                                                                                    <p class="text-sm text-red-500 dark:text-red-400 font-medium">{{$newest->status->name}}</p>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                                                                            <form action="{{route('orders.shipments.locations.destroy', ['order' => $shipment->order->id, 'shipment' => $shipment->id, 'location' => $newest])}}" method="POST">
                                                                                                @csrf
                                                                                                @method('DELETE')
                                                                                                <button type="submit" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500 sm:ml-3 sm:w-auto">Eliminar</button>
                                                                                            </form>
                                                                                            <button type="button" @click="openCancel = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-50 shadow-xs ring-1 ring-gray-300 dark:ring-gray-600 ring-inset hover:bg-gray-50 dark:hover:bg-gray-800 sm:mt-0 sm:w-auto">Regresar</button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endcan
                                                            </div>
                                                            <div class="mt-1">
                                                                <div class="flex items-start gap-x-1">
                                                                    <i class="fa-regular fa-calendar-clock text-gray-400 dark:text-gray-500 text-xs mt-0.5"></i>
                                                                    <p class="text-xs text-gray-500 dark:text-gray-400 text-left"><time datetime="{{$newest->location_date->isoFormat('YYYY-MM-DD')}}">{{$newest->location_date->isoFormat('D MMM YYYY')}}&nbsp;&nbsp;{{$newest->location_date->isoFormat('h:mm a')}}</time></p>
                                                                </div>
                                                                @if($newest->name != null)
                                                                    <div class="flex items-start gap-x-1 mt-0.5">
                                                                        <i class="fa-regular fa-location-dot text-gray-400 dark:text-gray-500 text-xs mt-0.5"></i>
                                                                        <button
                                                                            class="hover:underline hover:cursor-pointer text-xs text-blue-700 dark:text-blue-400 hover:text-blue-500 text-left"
                                                                            x-cloak x-show="showMap"
                                                                            @click="showMap = true; openMarker({{ $total - 1 }})"
                                                                        >
                                                                            {{$newest->name}}
                                                                        </button>
                                                                        <p class="text-gray-500 dark:text-gray-400 text-xs" x-cloak x-show="!showMap">{{$newest->name}}</p>
                                                                    </div>
                                                                @endif
                                                                @isset($newest->comments)
                                                                    <div class="flex items-start gap-x-1 mt-0.5">
                                                                        <i class="fa-regular fa-message-lines text-gray-400 dark:text-gray-500 text-xs mt-0.5"></i>
                                                                        <p class="text-xs text-gray-500 dark:text-gray-400 text-left">
                                                                            {{$newest->comments}}
                                                                        </p>
                                                                    </div>
                                                                @endisset
                                                            </div>
                                                        </div>
                                                    </li>
                                                @else
                                                    <li>
                                                        <div class="rounded-md bg-yellow-50 dark:bg-yellow-500/10 p-3">
                                                            <div class="flex">
                                                                <div class="shrink-0">
                                                                    <i class="fa-solid fa-triangle-exclamation text-yellow-400"></i>
                                                                </div>
                                                                <div class="ml-3">
                                                                    <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-400">Sin actualizaciones</h3>
                                                                    <div class="mt-1 text-sm text-yellow-700 dark:text-yellow-400">
                                                                        <p>Aún no se han agregado actualizaciones de estatus.</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endif

                                                @if($middle->isNotEmpty())
                                                    <li class="relative flex gap-x-2 pb-6">
                                                        <div class="absolute top-0 -bottom-0 left-0 flex w-6 justify-center">
                                                            <div class="w-px bg-gray-200 dark:bg-lits-blue-450"></div>
                                                        </div>
                                                        <div class="relative flex size-6 flex-none items-center justify-center bg-body dark:bg-lits-blue-600">
                                                            <div class="size-1.5 rounded-full bg-gray-300 dark:bg-gray-600"></div>
                                                        </div>
                                                        <button
                                                            type="button"
                                                            @click="expanded = !expanded"
                                                            class="flex-1 min-w-0 flex items-center gap-1.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:cursor-pointer"
                                                        >
                                                            <i class="fa-regular text-[10px]" :class="expanded ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                                            <span x-text="expanded ? 'Ocultar {{ $middle->count() }} {{ $middle->count() == 1 ? 'estatus intermedio' : 'estatus intermedios' }}' : 'Ver {{ $middle->count() }} {{ $middle->count() == 1 ? 'estatus intermedio' : 'estatus intermedios' }}'"></span>
                                                        </button>
                                                    </li>
                                                    @foreach($middle->reverse() as $index => $location)
                                                        <li x-cloak x-show="expanded" x-transition class="relative flex gap-x-2 pb-6">
                                                            <div class="absolute top-0 -bottom-0 left-0 flex w-6 justify-center">
                                                                <div class="w-px bg-gray-200 dark:bg-lits-blue-450"></div>
                                                            </div>
                                                            @if ($location->service_type_status_id == 20)
                                                                <div class="relative flex size-6 flex-none items-center justify-center bg-body dark:bg-lits-blue-600">
                                                                    <i class="fa-solid fa-circle-check text-lg text-indigo-600 dark:text-indigo-400"></i>
                                                                </div>
                                                            @else
                                                                <div class="relative flex size-6 flex-none items-center justify-center bg-body dark:bg-lits-blue-600">
                                                                    <div class="size-1.5 rounded-full bg-gray-400 dark:bg-gray-600"></div>
                                                                </div>
                                                            @endif
                                                            <div class="flex-1 min-w-0">
                                                                <p class="text-sm/6 text-gray-900 dark:text-gray-50 font-medium">{{$location->status->name}}</p>
                                                                <div class="mt-1">
                                                                    <div class="flex items-start gap-x-1">
                                                                        <i class="fa-regular fa-calendar-clock text-gray-400 dark:text-gray-500 text-xs mt-0.5"></i>
                                                                        <p class="text-xs text-gray-500 dark:text-gray-400 text-left"><time datetime="{{$location->location_date->isoFormat('YYYY-MM-DD')}}">{{$location->location_date->isoFormat('D MMM YYYY')}}&nbsp;&nbsp;{{$location->location_date->isoFormat('h:mm a')}}</time></p>
                                                                    </div>
                                                                    @if($location->name != null)
                                                                        <div class="flex items-start gap-x-1 mt-0.5">
                                                                            <i class="fa-regular fa-location-dot text-gray-400 dark:text-gray-500 text-xs mt-0.5"></i>
                                                                            <button
                                                                                class="hover:underline hover:cursor-pointer text-xs text-blue-700 dark:text-blue-400 hover:text-blue-500 text-left"
                                                                                x-cloak x-show="showMap"
                                                                                @click="showMap = true; openMarker({{ $index }})"
                                                                            >
                                                                                {{$location->name}}
                                                                            </button>
                                                                            <p class="text-gray-500 dark:text-gray-400 text-xs" x-cloak x-show="!showMap">{{$location->name}}</p>
                                                                        </div>
                                                                    @endif
                                                                    @isset($location->comments)
                                                                        <div class="flex items-start gap-x-1 mt-0.5">
                                                                            <i class="fa-regular fa-message-lines text-gray-400 dark:text-gray-500 text-xs mt-0.5"></i>
                                                                            <p class="text-xs text-gray-500 dark:text-gray-400 text-left">
                                                                                {{$location->comments}}
                                                                            </p>
                                                                        </div>
                                                                    @endisset
                                                                </div>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                @endif

                                                @if($oldest)
                                                    <li class="relative flex gap-x-2">
                                                        @if ($oldest->service_type_status_id == 20)
                                                            <div class="relative flex size-6 flex-none items-center justify-center bg-body dark:bg-lits-blue-600">
                                                                <i class="fa-solid fa-circle-check text-lg text-indigo-600 dark:text-indigo-400"></i>
                                                            </div>
                                                        @else
                                                            <div class="relative flex size-6 flex-none items-center justify-center bg-body dark:bg-lits-blue-600">
                                                                <div class="size-1.5 rounded-full bg-gray-400 dark:bg-gray-600"></div>
                                                            </div>
                                                        @endif
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-sm/6 text-gray-900 dark:text-gray-50 font-medium">{{$oldest->status->name}}</p>
                                                            <div class="mt-1">
                                                                <div class="flex items-start gap-x-1">
                                                                    <i class="fa-regular fa-calendar-clock text-gray-400 dark:text-gray-500 text-xs mt-0.5"></i>
                                                                    <p class="text-xs text-gray-500 dark:text-gray-400 text-left"><time datetime="{{$oldest->location_date->isoFormat('YYYY-MM-DD')}}">{{$oldest->location_date->isoFormat('D MMM YYYY')}}&nbsp;&nbsp;{{$oldest->location_date->isoFormat('h:mm a')}}</time></p>
                                                                </div>
                                                                @if($oldest->name != null)
                                                                    <div class="flex items-start gap-x-1 mt-0.5">
                                                                        <i class="fa-regular fa-location-dot text-gray-400 dark:text-gray-500 text-xs mt-0.5"></i>
                                                                        <button
                                                                            class="hover:underline hover:cursor-pointer text-xs text-blue-700 dark:text-blue-400 hover:text-blue-500 text-left"
                                                                            x-cloak x-show="showMap"
                                                                            @click="showMap = true; openMarker(0)"
                                                                        >
                                                                            {{$oldest->name}}
                                                                        </button>
                                                                        <p class="text-gray-500 dark:text-gray-400 text-xs" x-cloak x-show="!showMap">{{$oldest->name}}</p>
                                                                    </div>
                                                                @endif
                                                                @isset($oldest->comments)
                                                                    <div class="flex items-start gap-x-1 mt-0.5">
                                                                        <i class="fa-regular fa-message-lines text-gray-400 dark:text-gray-500 text-xs mt-0.5"></i>
                                                                        <p class="text-xs text-gray-500 dark:text-gray-400 text-left">
                                                                            {{$oldest->comments}}
                                                                        </p>
                                                                    </div>
                                                                @endisset
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="lg:col-span-7 flex flex-col gap-6">
                                        <div class="rounded-lg border border-gray-200 dark:border-lits-blue-450 divide-y divide-gray-200 dark:divide-lits-blue-450">
                                            <div class="flex items-center gap-x-2 p-4">
                                                <img src="{{ route('clients.logos', [ 'filename' => str_replace(".","_",str_replace("logos/", "", $order->client->image))]) }}" alt="{{$order->client->trade_name}}" class="object-contain size-10 flex-none rounded-full bg-gray-200 dark:bg-gray-700 outline -outline-offset-1 outline-black/5" />
                                                <div>
                                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-50">{{$order->client->trade_name}}</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{$order->contact->name}}</p>
                                                </div>
                                            </div>
                                            <div class="p-4">
                                                <div class="flex items-center justify-between text-xs font-semibold text-gray-700 dark:text-gray-300 gap-2">
                                                    <span>{{$shipment->originCity->name}}, {{$shipment->originState->short_name}}, {{$shipment->originCountry->code}}</span>
                                                    <i class="fa-solid fa-arrow-right text-gray-300 dark:text-gray-600 shrink-0"></i>
                                                    <span class="text-right">{{$shipment->destinationCity->name}}, {{$shipment->destinationState->short_name}}, {{$shipment->destinationCountry->code}}</span>
                                                </div>
                                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-3">
                                                    Última actualización: @if ($shipment->latestLocation) <time datetime="{{$shipment->latestLocation->location_date->isoFormat('YYYY-MM-DD')}}">{{$shipment->latestLocation->location_date->isoFormat('D MMM YYYY')}}&nbsp;&nbsp;{{$shipment->latestLocation->location_date->isoFormat('h:mm a')}}</time>@else Sin estatus @endif
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
                                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-y-3 gap-x-3 p-4">
                                                <div>
                                                    <p class="text-[11px] text-gray-400 dark:text-gray-500">ETD</p>
                                                    <p class="text-sm text-gray-900 dark:text-gray-50">{{ $shipment->estimated_time_departure?->isoFormat('DD/MM/YYYY') ?? '—' }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-[11px] text-gray-400 dark:text-gray-500">ETA</p>
                                                    <p class="text-sm text-gray-900 dark:text-gray-50">{{ $shipment->estimated_time_arrival?->isoFormat('DD/MM/YYYY') ?? '—' }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-[11px] text-gray-400 dark:text-gray-500">ATD</p>
                                                    <p class="text-sm text-gray-900 dark:text-gray-50">{{ $shipment->start_date?->isoFormat('DD/MM/YYYY') ?? 'Pendiente' }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-[11px] text-gray-400 dark:text-gray-500">ATA</p>
                                                    <p class="text-sm text-gray-900 dark:text-gray-50">{{ $shipment->end_date?->isoFormat('DD/MM/YYYY') ?? 'Pendiente' }}</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="rounded-lg border border-gray-200 dark:border-lits-blue-450 p-4">
                                            <div class="flex items-center justify-between mb-3">
                                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-50">Mapa</p>
                                                @can('create', \App\Models\Order::class)
                                                    @if($mapAvailable)
                                                        <label class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                                            Mostrar mapa
                                                            <div class="group relative inline-flex w-9 shrink-0 rounded-full bg-red-600 p-0.5 inset-ring inset-ring-gray-900/5 outline-offset-2 outline-green-600 transition-colors duration-200 ease-in-out has-checked:bg-green-600 has-focus-visible:outline-2">
                                                                <span class="relative size-4 rounded-full bg-white dark:bg-lits-blue-550 shadow-xs ring-1 ring-gray-900/5 dark:ring-white/10 transition-transform duration-200 ease-in-out group-has-checked:translate-x-4">
                                                                    <span
                                                                        aria-hidden="true"
                                                                        class="absolute inset-0 flex size-full items-center justify-center opacity-100 transition-opacity duration-200 ease-in group-has-checked:opacity-0 group-has-checked:duration-100 group-has-checked:ease-out text-red-600 dark:text-red-400 text-[0.5rem]"
                                                                    >
                                                                        <i class="fa-solid fa-xmark"></i>
                                                                    </span>
                                                                    <span
                                                                        aria-hidden="true"
                                                                        class="absolute inset-0 flex size-full items-center justify-center opacity-0 transition-opacity duration-100 ease-out group-has-checked:opacity-100 group-has-checked:duration-200 group-has-checked:ease-in text-green-600 dark:text-green-400 text-[0.5rem]"
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
                                                        </label>
                                                    @endif
                                                @endcan
                                            </div>
                                            <div x-cloak x-show="showMap">
                                                <div class="h-60 w-full rounded-md overflow-hidden" id="map"></div>
                                            </div>
                                            <p x-cloak x-show="!showMap" class="text-xs text-gray-400 dark:text-gray-500 text-center py-8">El mapa está oculto. Actívalo con el switch de arriba.</p>
                                        </div>

                                        <div class="rounded-lg border border-gray-200 dark:border-lits-blue-450 p-4">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-50 mb-3">Notas</p>
                                            <ul class="divide-y divide-gray-200 dark:divide-lits-blue-450">
                                                @forelse(collect($shipment->locations)->whereNotNull('comments') as $location)
                                                    <li class="@if(!$loop->first) pt-3 @endif @if(!$loop->last) pb-3 @endif">
                                                        <p class="text-sm text-gray-700 dark:text-gray-300">{{$location->comments}}</p>
                                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5"><time datetime="{{$location->location_date->isoFormat('YYYY-MM-DD')}}">{{$location->location_date->isoFormat('D MMM YYYY')}}&nbsp;&nbsp;{{$location->location_date->isoFormat('h:mm a')}}</time></p>
                                                    </li>
                                                @empty
                                                    <li>
                                                        <p class="text-sm text-gray-400 dark:text-gray-500">No hay observaciones en los estatus del embarque.</p>
                                                    </li>
                                                @endforelse
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                                    @can('update', $shipment)
                                        <div x-cloak x-show.transition.in.opacity.duration.600="mode === 2">
                                            <form action="{{ route('orders.shipments.locations.store', ['order' => $order->id, 'shipment' => $shipment->id]) }}" method="POST" class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-4" autocomplete="off">
                                                @csrf
                                                <div class="sm:col-span-full">
                                                    <h2 class="text-base/7 font-semibold text-gray-900 dark:text-gray-50">Nuevo estatus</h2>
                                                    <p class="mt-1 text-sm/6 text-gray-600 dark:text-gray-400">Agrega un estatus al embarque, puedes buscar una dirección para ingresar una nueva geolocalización.</p>
                                                </div>
                                                <div class="sm:col-span-2">
                                                    <div class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-4">
                                                        <div class="sm:col-span-2">
                                                            <label for="location_date" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Fecha</label>
                                                            <div class="mt-2">
                                                                <input id="location_date" required value="{{old('location_date', $now->format('Y-m-d\TH:i'))}}" name="location_date" type="datetime-local" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                            </div>
                                                        </div>
                                                        <div class="sm:col-span-2">
                                                            <label for="service_type_status_id" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Estatus</label>
                                                            <div class="mt-2 grid grid-cols-1">
                                                                <select id="service_type_status_id" name="service_type_status_id" required autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                                    <option value="" selected disabled>Selecciona un nuevo estatus</option>
                                                                    @if ($shipment->serviceMode)
                                                                        @foreach ($shipment->serviceMode->statuses as $status)
                                                                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                                <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 dark:text-gray-400 sm:text-sm"></i>
                                                            </div>
                                                        </div>
                                                        <div class="sm:col-span-full">
                                                            <label for="comments" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Comentarios</label>
                                                            <div class="mt-2">
                                                                <textarea id="comments" name="comments" autocomplete="off" class="@error('comments') outline-red-400 @else outline-gray-300 dark:outline-gray-600 @enderror block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">{{old('comments')}}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="sm:col-span-full">
                                                            <label for="name" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Ubicación</label>
                                                            <div class="mt-2" id="location-name-div">
                                                                <input id="location-name" value="{{old('location-name')}}" name="name" type="hidden">
                                                                <input id="latitude" value="{{old('latitude')}}" name="latitude" type="hidden">
                                                                <input id="longitude" value="{{old('longitude')}}" name="longitude" type="hidden">
                                                            </div>
                                                        </div>
                                                        <div class="sm:col-span-full">
                                                            <div class="flex items-center justify-end gap-x-6">
                                                                <button
                                                                    class="text-sm/6 font-semibold text-gray-900 dark:text-gray-50 hover:cursor-pointer hover:text-gray-500"
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
                                                    <h2 class="text-base/7 font-semibold text-gray-900 dark:text-gray-50">Editar estatus</h2>
                                                    <p class="mt-1 text-sm/6 text-gray-600 dark:text-gray-400">Edita el estatus del embarque, puedes buscar una dirección para editar la geolocalización.</p>
                                                </div>
                                                <div class="sm:col-span-2">
                                                    <div class="grid grid-cols-1 gap-x-4 gap-y-4 lg:grid-cols-4">
                                                        <div class="sm:col-span-2">
                                                            <label for="edit-location_date" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Fecha</label>
                                                            <div class="mt-2">
                                                                <input id="edit-location_date" required name="location_date" type="datetime-local" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                            </div>
                                                        </div>
                                                        <div class="sm:col-span-2">
                                                            <label for="edit-service_type_status_id" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Estatus</label>
                                                            <div class="mt-2 grid grid-cols-1">
                                                                <select id="edit-service_type_status_id" name="service_type_status_id" required autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                                    @foreach ($shipment->serviceMode?->statuses as $status)
                                                                        <option value= {{ $status->id }} >{{ $status->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 dark:text-gray-400 sm:text-sm"></i>
                                                            </div>
                                                        </div>
                                                        <div class="sm:col-span-full">
                                                            <label for="edit-comments" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Comentarios</label>
                                                            <div class="mt-2">
                                                                <textarea id="edit-comments" name="comments" autocomplete="off" class="@error('comments') outline-red-400 @else outline-gray-300 dark:outline-gray-600 @enderror block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="sm:col-span-full">
                                                            <label for="name" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Ubicación</label>
                                                            <div class="mt-2" id="edit-location-name-div">
                                                                <input id="edit-location-name" name="name" type="hidden">
                                                                <input id="edit-latitude" name="latitude" type="hidden">
                                                                <input id="edit-longitude" name="longitude" type="hidden">
                                                            </div>
                                                        </div>
                                                        <div class="sm:col-span-full">
                                                            <div class="flex items-center justify-end gap-x-6">
                                                                <button
                                                                    class="text-sm/6 font-semibold text-gray-900 dark:text-gray-50 hover:cursor-pointer hover:text-gray-500"
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
                @can('checklist', $shipment)
                <div x-cloak x-show.transition.in.opacity.duration.600="activeTab === 3">
                    <div class="px-4 py-5 sm:px-6">
                        <div class="mb-2 md:flex md:items-center md:justify-between">
                            <div class="">
                                <h2 class="text-2xl/7 font-bold text-gray-900 dark:text-gray-50 sm:truncate sm:text-3xl sm:tracking-tight">Checklist</h2>
                            </div>
                            <div class="mt-3 flex sm:mt-0 sm:ml-4">
                                @can('updateChecklist', $shipment)
                                    <a
                                        href='{{route('orders.shipments.privates.create', [ 'order' => $shipment->order, 'shipment' => $shipment ])}}'
                                        data-tippy-content="Adjuntar archivo interno"
                                        role="button"
                                        class="mr-3 inline-flex items-center gap-x-1.5 rounded-md bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-50 shadow-xs inset-ring inset-ring-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 whitespace-nowrap"
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
                                            class="mr-3 inline-flex items-center gap-x-1.5 rounded-md bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-50 shadow-xs inset-ring inset-ring-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:cursor-pointer"
                                        >
                                            Comentarios
                                        </button>
                                        <div x-cloak x-show="openComments" class="relative z-100" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                            <div
                                                class="fixed inset-0 bg-gray-500/75 dark:bg-gray-950/75 transition-opacity"
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
                                                        class="relative transform overflow-hidden rounded-lg bg-white dark:bg-lits-blue-550 px-4 pt-5 pb-4 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6"
                                                    >
                                                        <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
                                                            <button type="button" @click="openComments = false" class="rounded-md bg-white dark:bg-lits-blue-550 text-gray-400 dark:text-gray-500 hover:text-gray-500 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-hidden">
                                                                <span class="sr-only">Close</span>
                                                                <i class="fa-regular fa-xmark"></i>
                                                            </button>
                                                        </div>
                                                        <form action="{{route('orders.shipments.update.checklist', ['order' => $order, 'shipment' => $shipment])}}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="mt-3 text-center sm:mt-0 sm:text-left whitespace-normal">
                                                                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-50" id="modal-title">Editar checklist</h3>
                                                                <div class="mt-2">
                                                                    <p class="text-sm text-gray-500 dark:text-gray-400 font-normal">Edita los comentarios del checklist</p>
                                                                    <div class="col-span-full">
                                                                        <label for="checklist_comments" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Comentarios</label>
                                                                        <div class="mt-2">
                                                                            <textarea id="checklist_comments" name="checklist_comments" autocomplete="off" class=" block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">{{old('checklist_comments', $shipment->checklist_comments)}}</textarea>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                                                <button type="submit" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500 sm:ml-3 sm:w-auto">Guardar</button>
                                                                <button type="button" @click="openComments = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-50 shadow-xs ring-1 ring-gray-300 dark:ring-gray-600 ring-inset hover:bg-gray-50 dark:hover:bg-gray-800 sm:mt-0 sm:w-auto">Cancelar</button>
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
                                <div class="bg-white shadow-lits-card w-fit h-fit mx-auto border border-gray-200" style="width: 816px;padding: 25px;">
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
    @can('update', $shipment)
        <x-drawers.edit-shipment
            :order="$order"
            :shipment="$shipment"
            :serviceClasses="$serviceClasses"
            :defaultServiceClass="$defaultServiceClass"
        />
    @endcan
</x-layout-app>
