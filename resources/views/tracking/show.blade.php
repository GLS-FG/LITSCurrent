@section('title', 'Tracking de embarque')
@section('custom_script')
    @isset($transportation)
        <script>
            (g=>{var h,a,k,p="The Google Maps JavaScript API",c="google",l="importLibrary",q="__ib__",m=document,b=window;b=b[c]||(b[c]={});var d=b.maps||(b.maps={}),r=new Set,e=new URLSearchParams,u=()=>h||(h=new Promise(async(f,n)=>{await (a=m.createElement("script"));e.set("libraries",[...r]+"");for(k in g)e.set(k.replace(/[A-Z]/g,t=>"_"+t[0].toLowerCase()),g[k]);e.set("callback",c+".maps."+q);a.src=`https://maps.${c}apis.com/maps/api/js?`+e;d[q]=f;a.onerror=()=>h=n(Error(p+" could not load."));a.nonce=m.querySelector("script[nonce]")?.nonce||"";m.head.append(a)}));d[l]?console.warn(p+" only loads once. Ignoring:",g):d[l]=(f,...n)=>r.add(f)&&u().then(()=>d[l](f,...n))})({
                key: "{{config('services.google_maps.key')}}",
                v: "quarterly"
            });
        </script>
        <script>
            const glsTruckIconImg = document.createElement('img');
            glsTruckIconImg.src = 'https://www.glsgroup.com.mx/img/icons/truck.png';
            let map;
            let marker;
            let infoWindow;
            let center = { lat: 29.076763047209962, lng: -110.95766151719751 };
            let title = "GLS Group";
            @isset($transportation->latestLocation)
                center = { lat: {{$transportation->latestLocation->latitude}}, lng: {{$transportation->latestLocation->longitude}} };
            title = "{{$transportation->latestLocation->name}}";
            @endisset

            async function initMap() {
                await google.maps.importLibrary("maps");
                await google.maps.importLibrary("marker");

                map = new google.maps.Map(document.getElementById("map"), {
                    center,
                    zoom: 12,
                    mapId: "map",
                });

                @isset($transportation->latestLocation)
                addMarker();
                @endisset
            }

            async function addMarker() {
                marker = new google.maps.marker.AdvancedMarkerElement({
                    map,
                    position: center,
                    content: glsTruckIconImg,
                    gmpClickable: true,
                    title: title
                });

                infoWindow = new google.maps.InfoWindow();
                @isset($transportation->latestLocation)
                infoWindow.setContent("<h4 class=\"font-medium\">" + marker.title + "</h4><p class=\"text-gray-500\">{{$transportation->latestLocation->location_date->isoFormat('D MMMM YYYY h:mm a')}}</p>");
                @endisset
                infoWindow.open(marker.map, marker);

                marker.addListener('gmp-click', ({ domEvent, latLng }) => {
                    infoWindow.close();
                    infoWindow.open(marker.map, marker);
                });
            }

            function moveMarker(latitude, longitude, title, dateLabel) {
                marker.position = new google.maps.LatLng(latitude, longitude);
                marker.title = title;
                map.setCenter(marker.position);
                infoWindow.setContent("<h4 class=\"font-medium\">" + marker.title + "</h4><p class=\"text-gray-500\">" + dateLabel + "</p>")
            }

            initMap();
        </script>
    @endisset
@endsection
<x-layout-public>
    <section class="px-4 py-5 sm:p-4 ">
        <div class="shadow-lits-card rounded-2xl bg-wp-card-body">
            <div class="px-10 py-5">
                <form method="GET" action="{{ route('tracking.show') }}" class="w-full block sm:flex items-center gap-2">
                    <input type="text" value="{{ request('tracking_code') }}" autocomplete="off" name="tracking_code" placeholder="Ingresa código de rastreo" class="col-start-1 row-start-1 block w-full rounded-md py-1.5 px-4 text-base text-white outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-500 sm:text-sm/6">
                    <button
                        type="submit"
                        class="rounded-full bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-blue-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 hover:cursor-pointer"
                    >
                        Rastrear
                    </button>
                </form>
            </div>
        </div>
        @isset($notFound)
            <div class="my-4">
                <div class="rounded-md bg-red-50 dark:bg-red-500/10 p-4 border border-red-400">
                    <div class="flex">
                        <div class="shrink-0">
                            <svg class="size-5 text-red-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800 dark:text-red-400">No existen servicios con el código de rastreo: {{ request('tracking_code') }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        @endisset
        @isset($shipment)
            @isset($transportation)
            <div class="mt-4 md:flex md:items-center md:justify-between">
                <div class="min-w-0 flex gap-x-5 justify-between items-start">
                    <div>
                        <h2 class="text-2xl/7 font-bold text-white sm:truncate sm:text-3xl sm:tracking-tight">{{$transportation->transportation_type}}</h2>
                        <p class="text-gray-400 dark:text-gray-500 sm:truncate sm:text-lg sm:tracking-tight">{{$transportation->plates}}</p>
                    </div>
                </div>
            </div>
            <div class="mt-4 flow-root">
                <div class="shadow-lits-card rounded-2xl bg-wp-card-body overflow-hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 h-full">
                        <div class="px-4 md:px-8 h-full">
                            <div class="pt-5 pb-2">
                                <div class="grid grid-cols-1 md:grid-cols-3">
                                    <div class="">
                                        <p class="text-xs/5 text-gray-400 dark:text-gray-500 py-0.5">Agencia</p>
                                        <p class="text-xs/5 text-white py-0.5">{{$transportation->agency->name}}</p>
                                    </div>
                                    <div class="">
                                        <p class="text-xs/5 text-gray-400 dark:text-gray-500 py-0.5">Incoterm</p>
                                        <p class="text-xs/5 text-white py-0.5">{{$transportation->incoterm->name}}</p>
                                    </div>
                                    <div class="">
                                        <p class="text-xs/5 text-gray-400 dark:text-gray-500 py-0.5">Chofer</p>
                                        @isset($transportation->driver)
                                            <p class="text-xs/5 text-white py-0.5">{{$transportation->driver}}</p>
                                        @else
                                            <p class="text-xs/5 text-gray-400 dark:text-gray-500 py-0.5">No asingado</p>
                                        @endisset
                                    </div>
                                </div>
                                <ul role="list" class="space-y-3 p-2 mt-4 rounder rounded ring ring-gray-300 dark:ring-gray-600 overflow-hidden">
                                    <li class="relative flex gap-x-1">
                                        <div class="absolute top-0 -bottom-6 left-0 flex w-6 justify-center">
                                            <div class="w-px bg-gray-500"></div>
                                        </div>
                                        <div class="relative flex size-6 flex-none items-center justify-center bg-wp-card-body">
                                            <div class="size-1.5 rounded-full bg-white dark:bg-lits-blue-550 ring ring-white"></div>
                                        </div>
                                        <div class="flex-auto">
                                            <p class="text-xs/5 text-white py-0.5">{{ $transportation->originCity->name }}, {{ $transportation->originState->name }}, {{ $transportation->originCountry->name }}</p>
                                        </div>
                                    </li>
                                    <li class="relative flex gap-x-1">
                                        <div class="relative flex size-6 flex-none items-center justify-center bg-wp-card-body">
                                            <div class="size-1.5 rounded-full bg-white dark:bg-lits-blue-550 ring ring-white"></div>
                                        </div>
                                        <div class="flex-auto">
                                            <p class="text-xs/5 text-white py-0.5">{{ $transportation->destinationCity->name }}, {{ $transportation->destinationState->name }}, {{ $transportation->destinationCountry->name }}</p>
                                        </div>
                                    </li>
                                </ul>
                                <div class="flex justify-between mt-2">
                                    <div class="flex-auto">
                                        <p class="text-xs/5 text-gray-400 dark:text-gray-500 py-0.5">Salida</p>
                                        <p class="text-xs/5 text-white py-0.5">
                                            {{ $transportation->start_date->isoFormat('D MMMM YYYY') }}
                                        </p>
                                    </div>
                                    <div class="flex-auto">
                                        <p class="text-xs/5 text-gray-400 dark:text-gray-500 py-0.5">Llegada</p>
                                        @isset($transportation->end_date)
                                            <p class="text-xs/5 text-white py-0.5">{{ $transportation->end_date->isoFormat('D MMMM YYYY') }}</p>
                                        @else
                                            <p class="text-xs/5 text-gray-400 dark:text-gray-500 py-0.5">Pendiente</p>
                                        @endisset
                                    </div>
                                </div>
                            </div>
                            <div class="sm:flex sm:justify-between sm:items-center">
                                <p class="text-xl text-white font-semibold mb-4">Seguimiento de transporte</p>
                            </div>
                            <ul role="list" class="space-y-4 h-60 lg:h-96 overflow-y-auto pt-2 pb-5">
                                @forelse($transportation->geolocations as $geolocation)
                                    <li class="relative flex gap-x-2">
                                        @if (!$loop->last)
                                            <div class="absolute top-0 -bottom-6 left-0 flex w-6 justify-center">
                                                <div class="w-px bg-gray-500"></div>
                                            </div>
                                        @endif
                                        @if ($geolocation->shipmentLocation->tracking_type == 4)
                                            <div class="relative flex size-6 flex-none items-center justify-center bg-white dark:bg-lits-blue-550">
                                                <svg viewBox="0 0 24 24" fill="currentColor" data-slot="icon" aria-hidden="true" class="size-6 text-indigo-600 dark:text-indigo-400">
                                                    <path d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" fill-rule="evenodd" />
                                                </svg>
                                            </div>
                                        @else
                                            <div class="relative flex size-6 flex-none items-center justify-center bg-wp-card-body">
                                                <div class="size-1.5 rounded-full bg-white dark:bg-lits-blue-550 ring ring-white"></div>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="flex items-center gap-x-2 text-xs/5 text-gray-400 dark:text-gray-500 flex-wrap">
                                                <p class="text-sm/6 text-white">
                                                    <button
                                                        class="hover:underline hover:cursor-pointer"
                                                        onclick="moveMarker({{$geolocation->latitude}}, {{$geolocation->longitude}}, '{{$geolocation->name}}', '{{$geolocation->location_date->isoFormat('D MMMM YYYY h:mm a')}}')"
                                                    >
                                                        {{$geolocation->shipmentLocation->status->name}}
                                                    </button>
                                                </p>
                                                <svg viewBox="0 0 2 2" class="size-0.5 fill-current">
                                                    <circle r="1" cx="1" cy="1" />
                                                </svg>
                                                <p><time datetime="{{$geolocation->location_date->isoFormat('YYYY-MM-DD')}}">{{$geolocation->location_date->isoFormat('D MMM YYYY')}}&nbsp;&nbsp;{{$geolocation->location_date->isoFormat('h:mm a')}}</time></p>
                                            </div>
                                            <div class="flex items-start gap-x-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-gray-400 dark:text-gray-500">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                                </svg>
                                                <p class="py-0.5 text-xs/5 text-blue-500 dark:text-blue-400 hover:text-blue-400 text-left">
                                                    <button
                                                        class="hover:underline hover:cursor-pointer text-left"
                                                        onclick="moveMarker({{$geolocation->latitude}}, {{$geolocation->longitude}}, '{{$geolocation->name}}', '{{$geolocation->location_date->isoFormat('D MMMM YYYY h:mm a')}}')"
                                                    >
                                                        {{$geolocation->name}}
                                                    </button>
                                                </p>
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    <li class="text-white">
                                        No hay geolocalizaciones guardadas en este transporte
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                        <div class="h-full w-full bg-gray-500">
                            <div class="h-full w-full min-h-60" id="map"></div>
                        </div>
                    </div>
                </div>
            </div>
            @else
                <div class="my-4">
                    <div class="rounded-md bg-yellow-50 dark:bg-yellow-500/10 p-4 border border-yellow-400">
                        <div class="flex">
                            <div class="shrink-0">
                                <svg class="size-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-400">El servicio con código de rastreo {{ request('tracking_code') }} aún no tiene un transporte asignado.</h3>
                            </div>
                        </div>
                    </div>
                </div>
            @endisset
        @endisset
    </section>
</x-layout-public>
