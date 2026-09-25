<div>
    <script>
        (g=>{var h,a,k,p="The Google Maps JavaScript API",c="google",l="importLibrary",q="__ib__",m=document,b=window;b=b[c]||(b[c]={});var d=b.maps||(b.maps={}),r=new Set,e=new URLSearchParams,u=()=>h||(h=new Promise(async(f,n)=>{await (a=m.createElement("script"));e.set("libraries",[...r]+"");for(k in g)e.set(k.replace(/[A-Z]/g,t=>"_"+t[0].toLowerCase()),g[k]);e.set("callback",c+".maps."+q);a.src=`https://maps.${c}apis.com/maps/api/js?`+e;d[q]=f;a.onerror=()=>h=n(Error(p+" could not load."));a.nonce=m.querySelector("script[nonce]")?.nonce||"";m.head.append(a)}));d[l]?console.warn(p+" only loads once. Ignoring:",g):d[l]=(f,...n)=>r.add(f)&&u().then(()=>d[l](f,...n))})({
            key: "{{config('services.google_maps.key')}}",
            v: "weekly",
        });
        function mapAutocomplete() {
            return {
                async initMap() {
                    await google.maps.importLibrary("places")
                    const autocomplete = new google.maps.places.Autocomplete(this.$refs.autocompleteInput);
                    autocomplete.setFields(['name', 'formatted_address', 'geometry', 'types']);
                    autocomplete.addListener('place_changed', () => {
                        const place = autocomplete.getPlace();
                        if (!place.geometry) return;
                        console.log(place);
                        const hasTypes = ['political', 'route', 'locality', 'street_address'].some(item => place.types.includes(item));
                        let locationName = place.name + ', ' + place.formatted_address;
                        if (hasTypes) {
                            locationName = place.formatted_address;
                        }
                        this.$wire.set('form.location_name', locationName);
                        this.$wire.set('form.latitude', place.geometry.location.lat());
                        this.$wire.set('form.longitude', place.geometry.location.lng());
                    });
                }
            }
        }
    </script>
    <div
        class="relative inline-block text-left"
        x-data="{ openSearch: @entangle('openSearch'), createAddress: @entangle('createAddress') }"
    >
        <button @click="openSearch = true" type="button" class="whitespace-nowrap rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            Libreta de Direcciones
        </button>
        <div x-cloak x-show="openSearch" class="relative z-100" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div
                class="fixed inset-0 bg-gray-500/75 dark:bg-gray-950/75 transition-opacity"
                aria-hidden="true"
                x-show="openSearch"
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
                        x-show="openSearch"
                        x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        class="relative transform overflow-hidden rounded-lg bg-white dark:bg-lits-blue-550 px-4 pt-5 pb-4 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-6xl sm:p-6"
                    >
                        <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
                            <button type="button" @click="openSearch = false" class="rounded-md bg-white dark:bg-lits-blue-550 text-gray-400 dark:text-gray-500 hover:text-gray-500 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-hidden">
                                <span class="sr-only">Close</span>
                                <i class="fa-regular fa-xmark"></i>
                            </button>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:text-left whitespace-normal">
                            <h3 x-show="!createAddress" class="text-base/7 font-semibold text-gray-900 dark:text-gray-50" id="modal-title">Libreta de direcciones</h3>
                            <h3 x-show="createAddress" class="text-base/7 font-semibold text-gray-900 dark:text-gray-50" id="modal-title">Añadir nueva dirección</h3>
                            <div x-show="!createAddress" class="flex justify-end">
                                <button type="button" wire:click="startCreateAddress" class="ml-3 inline-flex items-center gap-1 rounded bg-lits-red-500 px-3 py-2 text-sm text-white shadow-xs transition-colors duration-150 ease-in-out hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-350">
                                    <i class="fa-regular fa-plus"></i>
                                    Crear dirección
                                </button>
                            </div>
                            <div x-show="!createAddress" class="mt-2">
                                <div class="flex-1 flex items-between">
                                    <div class="-mr-px flex-1 flex items-center px-0">
                                        <div class="grid w-full grid-cols-1 relative">
                                            <input wire:model.live="search" wire:keydown.enter.prevent="" type="text" autocomplete="off" placeholder="Busca una dirección..." class="col-start-1 row-start-1 block w-full rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-10 pl-10 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                            <i class="fa-regular fa-magnifying-glass pointer-events-none col-start-1 row-start-1 ml-3 etxt-lg self-center text-gray-400 dark:text-gray-500"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="overflow-x-auto">
                                    <div class="inline-block min-w-full align-middle">
                                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                            <thead class="">
                                            <tr>
                                                <th scope="col" class="py-3.5 pr-3 pl-4 text-left text-sm font-semibold whitespace-nowrap text-gray-900 dark:text-gray-50 sm:pl-6">Nombre</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold whitespace-nowrap text-gray-900 dark:text-gray-50">Contacto</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold whitespace-nowrap text-gray-900 dark:text-gray-50">Dirección</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold whitespace-nowrap text-gray-900 dark:text-gray-50">Ciudad</th>
                                                <th scope="col" class="relative py-3.5 pr-4 pl-3 sm:pr-6"><span class="sr-only">Detalles</span></th>
                                            </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 ">
                                            @forelse($addresses as $address)
                                                <tr wire:key="{{ $address->id }}">
                                                    <td class="py-4 pr-3 pl-4 text-sm text-gray-900 dark:text-gray-50 sm:pl-6 whitespace-nowrap">
                                                        {{ $address->name }}
                                                    </td>
                                                    <td class="px-3 py-4 text-sm text-gray-900 dark:text-gray-50">
                                                        {{ $address->contact_name }}
                                                    </td>
                                                    <td class="px-3 py-4 text-sm text-gray-900 dark:text-gray-50">
                                                        {{ $address->address }}
                                                    </td>
                                                    <td class="px-3 py-4 text-sm text-gray-900 dark:text-gray-50">
                                                        {{ $address->city->name }}, {{ $address->state->name }}, {{ $address->country->name }}
                                                    </td>
                                                    <td class="relative py-4 pr-4 pl-3 text-sm font-medium whitespace-nowrap sm:pr-6 flex items-center justify-end gap-x-1">
                                                        <button wire:click="selectAddress({{ $address->id }})" type="button" class="rounded-md bg-white dark:bg-lits-blue-550 px-2.5 py-1.5 text-sm font-semibold text-gray-900 dark:text-gray-50 shadow-xs inset-ring inset-ring-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">Insertar</button>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="px-3 py-4 text-sm text-center text-gray-500 dark:text-gray-400">
                                                        No hay direcciones con la búsqueda ingresada
                                                    </td>
                                                </tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div x-show="createAddress">
                                <form class="mt-4" wire:submit="save">
                                    <div class="space-y-12">
                                        <div class="border-b border-gray-900/10 dark:border-white/10 pb-12">
                                            <h2 class="text-base/7 font-semibold text-gray-900 dark:text-gray-50">Detalles del contacto</h2>
                                            <p class="mt-1 text-sm/6 text-gray-600 dark:text-gray-400">Llena la información del contacto para poder buscar las direcciones con esta información al llenar los embarques.</p>
                                            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 lg:grid-cols-12">
                                                <div class="sm:col-span-3 lg:col-span-6">
                                                    <label for="name" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Nombre de la dirección</label>
                                                    <div class="mt-2">
                                                        <input wire:model.live.debounce.400ms="form.name" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                        <div>
                                                            @error('form.name') <p class="text-sm text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="sm:col-span-3 lg:col-span-6">
                                                    <label for="trade_name" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Nombre comercial</label>
                                                    <div class="mt-2">
                                                        <input wire:model.live.debounce.400ms="form.trade_name" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                        <div>
                                                            @error('form.trade_name') <p class="text-sm text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="sm:col-span-3 lg:col-span-5">
                                                    <label for="contact_name" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Nombre de la persona de contacto</label>
                                                    <div class="mt-2">
                                                        <input wire:model.blur="form.contact_name" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                        <div>
                                                            @error('form.contact_name') <p class="text-sm text-red-500 dark:text-red-400">El "Nombre de la persona de contacto" es obligatorio.</p> @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="sm:col-span-3 lg:col-span-4">
                                                    <label for="email" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Email</label>
                                                    <div class="mt-2">
                                                        <input wire:model.blur="form.email" type="email" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                        <div>
                                                            @error('form.email') <p class="text-sm text-red-500 dark:text-red-400">El "Email" es obligatorio.</p> @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="sm:col-span-3 lg:col-span-2">
                                                    <label for="phone" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Teléfono</label>
                                                    <div class="mt-2">
                                                        <input wire:model="form.phone" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                        <div>
                                                            @error('form.phone') <p class="text-sm text-red-500 dark:text-red-400">El "Teléfono" es obligatorio.</p> @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="pb-12">
                                            <h2 class="text-base/7 font-semibold text-gray-900 dark:text-gray-50">Detalles de la dirección</h2>
                                            <p class="mt-1 text-sm/6 text-gray-600 dark:text-gray-400">Llena la información de la dirección, esta se prellenará en los embarques.</p>
                                            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 lg:grid-cols-12">
                                                <div class="sm:col-span-2 lg:col-span-4">
                                                    <label for="new_country_id" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">País</label>
                                                    <div class="mt-2">
                                                        <select id="new_country_id" wire:model.live="form.country_id" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                            <option value="">Selecciona un país</option>
                                                            @foreach($countries as $result)
                                                                <option value="{{ $result->id }}">{{ $result->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div>
                                                            @error('form.country_id') <p class="text-sm text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="sm:col-span-2 lg:col-span-4">
                                                    <label for="new_state_id" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Estado</label>
                                                    <div class="mt-2">
                                                        <select id="new_state_id" wire:model.live="form.state_id" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                            <option value="">Selecciona un estado</option>
                                                            @foreach($states as $result)
                                                                <option value="{{ $result->id }}">{{ $result->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div>
                                                            @error('form.state_id') <p class="text-sm text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="sm:col-span-2 lg:col-span-4">
                                                    <label for="new_city_id" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Ciudad</label>
                                                    <div class="mt-2">
                                                        <select id="new_city_id" wire:model.live="form.city_id" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                            <option value="">Selecciona una ciudad</option>
                                                            @foreach($cities as $result)
                                                                <option value="{{ $result->id }}">{{ $result->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div>
                                                            @error('form.city_id') <p class="text-sm text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="sm:col-span-full">
                                                    <label for="address" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Calle y número</label>
                                                    <div class="mt-2">
                                                        <input wire:model.live.debounce.400ms="form.address" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                        <div>
                                                            @error('form.address') <p class="text-sm text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="sm:col-span-3 lg:col-span-6">
                                                    <label for="neighborhood" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Colonia</label>
                                                    <div class="mt-2">
                                                        <input wire:model.live.debounce.400ms="form.neighborhood" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                        <div>
                                                            @error('form.neighborhood') <p class="text-sm text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="sm:col-span-3 lg:col-span-4">
                                                    <label for="postal_code" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Código Postal</label>
                                                    <div class="mt-2">
                                                        <input wire:model.live.debounce.400ms="form.postal_code" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                        <div>
                                                            @error('form.postal_code') <p class="text-sm text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="sm:col-span-full lg:col-span-8">
                                                    <label for="link" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Link de compartir ubicación en maps</label>
                                                    <div class="mt-2">
                                                        <input wire:model="form.link" type="text" autocomplete="off" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                        <div>
                                                            @error('form.link') <p class="text-sm text-red-500 dark:text-red-400">El "Link de compartir ubicación en maps" es obligatorio.</p> @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="sm:col-span-full">
                                                    <label for="location_name" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">Ubicación en maps</label>
                                                    <div x-data="mapAutocomplete()" x-init="initMap()" class="mt-2">
                                                        <input
                                                            type="text"
                                                            x-ref="autocompleteInput"
                                                            wire:model.live="form.location_name"
                                                            autocomplete="off"
                                                            class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                                                        >
                                                        <input wire:model="form.latitude" type="hidden">
                                                        <input wire:model="form.longitude" type="hidden">
                                                        <div>
                                                            @error('form.location_name') <p class="text-sm text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                                                            @error('form.latitude') <p class="text-sm text-red-500 dark:text-red-400">La "Latitud" es obligatoria.</p> @enderror
                                                            @error('form.longitude') <p class="text-sm text-red-500 dark:text-red-400">La "Longitud" es obligatoria.</p> @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-6 flex items-center justify-end gap-x-6">
                                        <button type="button" wire:click="cancelCreateAddress" class="text-sm/6 font-semibold text-gray-900 dark:text-gray-50">Cancelar</button>
                                        <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Crear dirección</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div x-show="!createAddress">
                            @if($addresses->count())
                                <nav>
                                    {{ $addresses->links(data: ['scrollTo' => false]) }}
                                </nav>
                            @endif
                        </div>
                        <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
