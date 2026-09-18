<div class="flow-root sm:col-span-2 mt-4">
    <div class="flex flex-wrap items-center justify-between sm:flex-nowrap mb-8">
        <h3 class="text-lg font-semibold text-gray-900">{{__('Products')}}</h3>
        <div class="flex space-x-2">
            @can('update', $order)
                @if (count($services) > 0)
                    <div
                        class="relative inline-block text-left"
                        x-data="{
                            open: false,
                            serviceableType: ''
                        }"
                    >
                        <button
                            type="button"
                            @click="open = true"
                            data-tippy-content="{{__('Copy')}}"
                            class="inline-flex items-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs inset-ring inset-ring-gray-300 hover:bg-gray-50"
                        >
                            <i class="fa-regular fa-copy"></i>
                            {{__('Copy')}}
                        </button>
                        <div x-cloak x-show="open" class="relative z-100" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                            <div
                                class="fixed inset-0 bg-gray-500/75 transition-opacity"
                                aria-hidden="true"
                                x-show="open"
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
                                        x-show="open"
                                        x-transition:enter="ease-out duration-300"
                                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                        x-transition:leave="ease-in duration-200"
                                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                        class="relative transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6"
                                    >
                                        <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
                                            <button type="button" @click="open = false" class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-hidden">
                                                <span class="sr-only">Close</span>
                                                <i class="fa-regular fa-xmark"></i>
                                            </button>
                                        </div>
                                        <form action="{{route('orders.products.copy', ['order' => $order->order->id])}}" method="POST">
                                            @csrf
                                            <div class="mt-3 text-center sm:mt-0 sm:text-left whitespace-normal">
                                                <h3 class="text-base font-semibold text-gray-900" id="modal-title">{{__('Copy from')}}</h3>
                                                <div class="mt-10">
                                                    <input type="hidden" name="copy_type" value="{{$orderType}}">
                                                    <input type="hidden" name="copy_id" value="{{$order->id}}">
                                                    <input type="hidden" name="serviceable_type" x-model="serviceableType">
                                                    <fieldset aria-label="Ordenes" class="-space-y-px rounded-md bg-white">
                                                        @foreach($services as $service)
                                                            <label aria-label="{{$service->service->reference}}" aria-description="This project would be available to anyone who has the link" class="group flex border border-gray-200 p-4 first:rounded-tl-md first:rounded-tr-md last:rounded-br-md last:rounded-bl-md focus:outline-hidden has-checked:relative has-checked:border-indigo-200 has-checked:bg-indigo-50">
                                                                <input type="radio" name="serviceable_id" required value="{{$service->service->id}}" x-on:change="serviceableType = '{{$service->slug}}'" class="relative mt-0.5 size-4 shrink-0 appearance-none rounded-full border border-gray-300 bg-white before:absolute before:inset-1 before:rounded-full before:bg-white not-checked:before:hidden checked:border-indigo-600 checked:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:before:bg-gray-400 forced-colors:appearance-auto forced-colors:before:hidden" />
                                                                <span class="ml-3 flex flex-col">
                                                                    <span class="block text-sm font-medium text-gray-900 group-has-checked:text-indigo-900">{{$service->serviceType}}</span>
                                                                    <span class="block text-sm text-gray-500 group-has-checked:text-indigo-700">{{$service->service->reference}}</span>
                                                                </span>
                                                            </label>
                                                        @endforeach
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                                <button type="submit" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500 sm:ml-3 sm:w-auto">{{__('Copy')}}</button>
                                                <button type="button" @click="open = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs ring-1 ring-gray-300 ring-inset hover:bg-gray-50 sm:mt-0 sm:w-auto">{{__('Go back')}}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div
                    class="relative inline-block text-left"
                    x-data="{ open: false }"
                >
                    <button
                        type="button"
                        @click="open = true"
                        data-tippy-content="{{__('Add product')}}"
                        class="inline-flex items-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs inset-ring inset-ring-gray-300 hover:bg-gray-50"
                    >
                        <i class="fa-regular fa-plus"></i>
                        {{__('Add product')}}
                    </button>
                    <div x-cloak x-show="open" class="relative z-100" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                        <div
                            class="fixed inset-0 bg-gray-500/75 transition-opacity"
                            aria-hidden="true"
                            x-show="open"
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
                                    x-show="open"
                                    x-transition:enter="ease-out duration-300"
                                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                    x-transition:leave="ease-in duration-200"
                                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                    class="relative transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all w-full sm:my-8 sm:w-full sm:max-w-3xl sm:p-6"
                                >
                                    <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
                                        <button type="button" @click="open = false" class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-hidden">
                                            <span class="sr-only">Close</span>
                                            <i class="fa-regular fa-xmark"></i>
                                        </button>
                                    </div>
                                    <form action="{{$store}}" method="POST">
                                        @csrf
                                        <div class="mt-3 text-center sm:mt-0 sm:text-left whitespace-normal">
                                            <h3 class="text-base font-semibold text-gray-900" id="modal-title">{{__('Add product')}}</h3>
                                            <div class="mt-10 grid grid-cols-3 gap-x-2 gap-y-4 sm:grid-cols-12">
                                                <div class="col-span-3 sm:col-span-12">
                                                    <label for="product" class="block text-sm/6 font-medium text-gray-900">{{__('Description')}}</label>
                                                    <div class="mt-2">
                                                        <input id="product" autocomplete="off" required placeholder="{{__('Product description')}}" type="text" name="product" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                    </div>
                                                </div>
                                                <div class="col-span-3 sm:col-span-12">
                                                    <label for="reference" class="block text-sm/6 font-medium text-gray-900">{{__('Reference')}}</label>
                                                    <div class="mt-2">
                                                        <input id="reference" autocomplete="off" required placeholder="{{__('Product reference')}}" type="text" name="reference" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                    </div>
                                                </div>
                                                <div class="sm:col-span-3">
                                                    <label for="length" class="block text-sm/6 font-medium text-gray-900">{{__('length')}}</label>
                                                    <div class="mt-2">
                                                        <input id="length" placeholder="{{__('length')}}" type="number" name="length" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                    </div>
                                                </div>
                                                <div class="sm:col-span-3">
                                                    <label for="width" class="block text-sm/6 font-medium text-gray-900">{{__('Width')}}</label>
                                                    <div class="mt-2">
                                                        <input id="width" placeholder="{{__('Width')}}" type="number" name="width" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                    </div>
                                                </div>
                                                <div class="sm:col-span-3">
                                                    <label for="height" class="block text-sm/6 font-medium text-gray-900">{{__('Height')}}</label>
                                                    <div class="mt-2">
                                                        <input id="height" placeholder="{{__('Height')}}" type="number" name="height" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                    </div>
                                                </div>
                                                <div class="sm:col-span-3">
                                                    <label for="unit_measure" class="block text-sm/6 font-medium text-gray-900">{{__('Size unit')}}</label>
                                                    <div class="mt-2 grid grid-cols-1">
                                                        <select id="unit_measure" name="unit_measure" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white py-1.5 pr-8 pl-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                            <option value="IN">IN</option>
                                                            <option value="FT">FT</option>
                                                            <option value="MM">MM</option>
                                                            <option value="CM">CM</option>
                                                            <option value="MTS">MTS</option>
                                                        </select>
                                                        <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 sm:text-sm"></i>
                                                    </div>
                                                </div>
                                                <div class="sm:col-span-12">
                                                    <label for="dimensions" class="block text-sm/6 font-medium text-gray-900">{{__('Other dimensions (Optional)')}}</label>
                                                    <div class="mt-2">
                                                        <input id="dimensions" autocomplete="off" placeholder="VARIOS" type="text" name="dimensions" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                    </div>
                                                </div>
                                                <div class="sm:col-span-3">
                                                    <label for="weight" class="block text-sm/6 font-medium text-gray-900">{{__('Weight')}}</label>
                                                    <div class="mt-2">
                                                        <input id="weight" placeholder="{{__('Weight')}}" type="text" name="weight" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                    </div>
                                                </div>
                                                <div class="sm:col-span-3">
                                                    <label for="weight_measure" class="block text-sm/6 font-medium text-gray-900">{{__('Weight unit')}}</label>
                                                    <div class="mt-2 grid grid-cols-1">
                                                        <select id="weight_measure" name="weight_measure" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white py-1.5 pr-8 pl-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                            <option value="KG">KG</option>
                                                            <option value="LBS">LBS</option>
                                                        </select>
                                                        <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 sm:text-sm"></i>
                                                    </div>
                                                </div>
                                                <div class="sm:col-span-6">
                                                    <label for="container" class="block text-sm/6 font-medium text-gray-900">{{__('Packaging type')}}</label>
                                                    <div class="mt-2 grid grid-cols-1">
                                                        <select id="container" name="container" autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white py-1.5 pr-8 pl-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                            <option value="Pallet">Pallet</option>
                                                            <option value="Crate">Crate</option>
                                                            <option value="Drum">Drum</option>
                                                            <option value="Tote">Tote</option>
                                                            <option value="Bulk">Bulk</option>
                                                            <option value="Carton">Carton</option>
                                                        </select>
                                                        <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 sm:text-sm"></i>
                                                    </div>
                                                </div>
                                                <div class="sm:col-span-3">
                                                    <label for="quantity" class="block text-sm/6 font-medium text-gray-900">{{__('Quantity')}}</label>
                                                    <div class="mt-2">
                                                        <input id="quantity" type="number" placeholder="{{__('Quantity')}}" min="0" name="quantity" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                    </div>
                                                </div>
                                                <div class="sm:col-span-3">
                                                    <label for="value" class="block text-sm/6 font-medium text-gray-900">{{__('Value')}}</label>
                                                    <div class="mt-2">
                                                        <input id="value" type="number" placeholder="$999.99" name="value" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                    </div>
                                                </div>
                                                <div class="sm:col-span-6">
                                                    <label for="incoterm_id" class="block text-sm/6 font-medium text-gray-900">INCOTERM</label>
                                                    <div class="mt-2 grid grid-cols-1">
                                                        <select id="incoterm_id" name="incoterm_id" autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white py-1.5 pr-8 pl-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                            @foreach ($incoterms as $incoterm)
                                                                <option value="{{ $incoterm->id }}">{{ $incoterm->code }} ({{ $incoterm->name }})</option>
                                                            @endforeach
                                                        </select>
                                                        <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 sm:text-sm"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                            <button type="submit" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500 sm:ml-3 sm:w-auto">{{__('Save')}}</button>
                                            <button type="button" @click="open = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs ring-1 ring-gray-300 ring-inset hover:bg-gray-50 sm:mt-0 sm:w-auto">{{__('Go back')}}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan
        </div>
    </div>
    <div class="overflow-x-auto border border-gray-300 rounded">
        <div class="inline-block min-w-full align-middle">
            <table class="relative min-w-full divide-y divide-gray-300">
                <thead>
                <tr class="divide-x divide-gray-200">
                    <th scope="col" class="py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-gray-900 sm:pl-3">{{__('Transaction')}}</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{__('Description')}}</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{__('Reference')}}</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{__('Dimensions')}}</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{__('Quantity')}}</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{__('Value')}}</th>
                    <th scope="col" class="py-3.5 pr-4 pl-3 text-left text-sm font-semibold text-gray-900 sm:pr-3">{{__('Actions')}}</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-300">
                @forelse($products as $product)
                    <tr class="even:bg-gray-50 divide-x divide-gray-200">
                        <td class="py-4 pr-3 pl-4 text-xs text-gray-500 sm:pl-3">
                            <p class="font-medium whitespace-nowrap text-gray-900"># {{$product->id}}</p>
                        </td>
                        <td class="px-3 py-4 text-xs text-gray-500">{{$product->product}}</td>
                        <td class="px-3 py-4 text-xs text-gray-500">{{$product->reference}}</td>
                        <td class="px-3 py-4 text-xs text-gray-500">
                            <div class="flex space-x-1.5 items-center text-gray-500 mb-1.5">
                                <i class="fa-regular fa-ruler-combined text-gray-900 text-lg"></i>
                                <p class="whitespace-nowrap">
                                    @if($product->dimensions)
                                        {{$product->dimensions}}
                                    @else
                                        {{(float)$product->length}} X {{(float)$product->width}} X {{(float)$product->height}} {{$product->unit_measure}}
                                    @endif
                                </p>
                            </div>
                            <div class="flex space-x-1.5 items-center text-gray-500 mb-1.5">
                                <i class="fa-regular fa-weight-hanging text-gray-900 text-lg"></i>
                                <p>{{$product->weight}} {{$product->weight_measure}}</p>
                            </div>
                            <div class="flex space-x-1.5 items-center text-gray-500">
                                <i class="fa-regular fa-box-isometric-tape text-gray-900 text-lg"></i>
                                <p>{{$product->container}}</p>
                            </div>
                        </td>
                        <td class="px-3 py-4 text-xs whitespace-nowrap text-gray-500">{{(float)$product->quantity}}</td>
                        <td class="px-3 py-4 text-xs whitespace-nowrap text-gray-500">{{$product->value}}</td>
                        <td class="py-4 pr-4 pl-3 text-right text-xs font-medium whitespace-nowrap sm:pr-3">
                            <div class="flex items-center justify-center gap-x-1">
                                @can('update', $order)
                                    <div
                                        class="relative inline-block text-left"
                                        x-data="{ open: false }"
                                    >
                                        <button
                                            type="button"
                                            @click="open = true"
                                            data-tippy-content="{{__('indexes.edit')}}"
                                            class="size-7 shrink-0 rounded-md bg-green-100 flex items-center justify-center font-semibold text-green-500 hover:text-green-800 hover:bg-green-200 hover:cursor-pointer"
                                        >
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </button>
                                        <div x-cloak x-show="open" class="relative z-100" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                            <div
                                                class="fixed inset-0 bg-gray-500/75 transition-opacity"
                                                aria-hidden="true"
                                                x-show="open"
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
                                                        x-show="open"
                                                        x-transition:enter="ease-out duration-300"
                                                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                        x-transition:leave="ease-in duration-200"
                                                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                        class="relative transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-3xl sm:p-6"
                                                    >
                                                        <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
                                                            <button type="button" @click="open = false" class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-hidden">
                                                                <span class="sr-only">Close</span>
                                                                <i class="fa-regular fa-xmark"></i>
                                                            </button>
                                                        </div>
                                                        <form action="{{route('orders.products.update', ['order' => $order->order->id, 'product' => $product->id])}}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="mt-3 text-center sm:mt-0 sm:text-left whitespace-normal">
                                                                <h3 class="text-base font-semibold text-gray-900" id="modal-title">{{__('Edit product')}}</h3>
                                                                <div class="mt-10 grid grid-cols-3 gap-x-2 gap-y-4 sm:grid-cols-12">
                                                                    <div class="col-span-3 sm:col-span-12">
                                                                        <label for="product" class="block text-sm/6 font-medium text-gray-900">{{__('Description')}}</label>
                                                                        <div class="mt-2">
                                                                            <input id="product" autocomplete="off" required value="{{$product->product}}" placeholder="{{__('Product description')}}" type="text" name="product" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-span-3 sm:col-span-12">
                                                                        <label for="reference" class="block text-sm/6 font-medium text-gray-900">{{__('Reference')}}</label>
                                                                        <div class="mt-2">
                                                                            <input id="reference" autocomplete="off" required value="{{$product->reference}}" placeholder="{{__('Product reference')}}" type="text" name="reference" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                                        </div>
                                                                    </div>
                                                                    <div class="sm:col-span-3">
                                                                        <label for="length" class="block text-sm/6 font-medium text-gray-900">{{__('Length')}}</label>
                                                                        <div class="mt-2">
                                                                            <input id="length" placeholder="{{__('Length')}}" value="{{$product->length}}" type="number" step="0.0001" name="length" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                                        </div>
                                                                    </div>
                                                                    <div class="sm:col-span-3">
                                                                        <label for="width" class="block text-sm/6 font-medium text-gray-900">{{__('Width')}}</label>
                                                                        <div class="mt-2">
                                                                            <input id="width" placeholder="{{__('Width')}}" value="{{$product->width}}" type="number" step="0.0001" name="width" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                                        </div>
                                                                    </div>
                                                                    <div class="sm:col-span-3">
                                                                        <label for="height" class="block text-sm/6 font-medium text-gray-900">{{__('Height')}}</label>
                                                                        <div class="mt-2">
                                                                            <input id="height" placeholder="{{__('Height')}}" value="{{$product->height}}" type="number" step="0.0001" name="height" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                                        </div>
                                                                    </div>
                                                                    <div class="sm:col-span-3">
                                                                        <label for="unit_measure" class="block text-sm/6 font-medium text-gray-900">{{__('Size unit')}}</label>
                                                                        <div class="mt-2 grid grid-cols-1">
                                                                            <select id="unit_measure" name="unit_measure" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white py-1.5 pr-8 pl-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                                                <option value="IN" @selected($product->unit_measure == "IN")>IN</option>
                                                                                <option value="FT" @selected($product->unit_measure == "FT")>FT</option>
                                                                                <option value="MM" @selected($product->unit_measure == "MM")>MM</option>
                                                                                <option value="CM" @selected($product->unit_measure == "CM")>CM</option>
                                                                                <option value="MTS" @selected($product->unit_measure == "MTS")>MTS</option>
                                                                            </select>
                                                                            <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 sm:text-sm"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div class="sm:col-span-12">
                                                                        <label for="dimensions" class="block text-sm/6 font-medium text-gray-900">{{__('Other dimensions (Optional)')}}</label>
                                                                        <div class="mt-2">
                                                                            <input id="dimensions" autocomplete="off" value="{{$product->dimensions}}" placeholder="VARIOS" type="text" name="dimensions" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                                        </div>
                                                                    </div>
                                                                    <div class="sm:col-span-3">
                                                                        <label for="weight" class="block text-sm/6 font-medium text-gray-900">{{__('Weight')}}</label>
                                                                        <div class="mt-2">
                                                                            <input id="weight" placeholder="{{__('Weight')}}" value="{{$product->weight}}" type="text" name="weight" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                                        </div>
                                                                    </div>
                                                                    <div class="sm:col-span-3">
                                                                        <label for="weight_measure" class="block text-sm/6 font-medium text-gray-900">{{__('Weight unit')}}</label>
                                                                        <div class="mt-2 grid grid-cols-1">
                                                                            <select id="weight_measure" name="weight_measure" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white py-1.5 pr-8 pl-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                                                <option value="KG" @selected($product->weight_measure == "KG")>KG</option>
                                                                                <option value="LBS" @selected($product->weight_measure == "LBS")>LBS</option>
                                                                            </select>
                                                                            <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 sm:text-sm"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div class="sm:col-span-6">
                                                                        <label for="container" class="block text-sm/6 font-medium text-gray-900">{{__('Packaging type')}}</label>
                                                                        <div class="mt-2 grid grid-cols-1">
                                                                            <select id="container" name="container" autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white py-1.5 pr-8 pl-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                                                <option value="Pallet"  @selected($product->container == "Pallet")>Pallet</option>
                                                                                <option value="Crate"  @selected($product->container == "Crate")>Crate</option>
                                                                                <option value="Drum"  @selected($product->container == "Drum")>Drum</option>
                                                                                <option value="Tote"  @selected($product->container == "Tote")>Tote</option>
                                                                                <option value="Bulk"  @selected($product->container == "Bulk")>Bulk</option>
                                                                                <option value="Carton"  @selected($product->container == "Carton")>Carton</option>
                                                                            </select>
                                                                            <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 sm:text-sm"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div class="sm:col-span-3">
                                                                        <label for="quantity" class="block text-sm/6 font-medium text-gray-900">{{__('Quantity')}}</label>
                                                                        <div class="mt-2">
                                                                            <input id="quantity" value="{{$product->quantity}}" type="number" min="0" placeholder="{{__('Quantity')}}" name="quantity" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                                        </div>
                                                                    </div>
                                                                    <div class="sm:col-span-3">
                                                                        <label for="value" class="block text-sm/6 font-medium text-gray-900">{{__('Value')}}</label>
                                                                        <div class="mt-2">
                                                                            <input id="value" value="{{$product->value}}" type="number" placeholder="$999.99"  step="0.01" name="value" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                                        </div>
                                                                    </div>
                                                                    <div class="sm:col-span-6">
                                                                        <label for="incoterm_id" class="block text-sm/6 font-medium text-gray-900">INCOTERM</label>
                                                                        <div class="mt-2 grid grid-cols-1">
                                                                            <select id="incoterm_id" name="incoterm_id" autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white py-1.5 pr-8 pl-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                                                @foreach ($incoterms as $incoterm)
                                                                                    <option value="{{ $incoterm->id }}"   @selected($product->incoterm_id == $incoterm->id)>{{ $incoterm->code }} ({{ $incoterm->name }})</option>
                                                                                @endforeach
                                                                            </select>
                                                                            <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 sm:text-sm"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                                                <button type="submit" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500 sm:ml-3 sm:w-auto">{{__('Save')}}</button>
                                                                <button type="button" @click="open = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs ring-1 ring-gray-300 ring-inset hover:bg-gray-50 sm:mt-0 sm:w-auto">{{__('Go back')}}</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endcan
                                @can('delete', $order)
                                    <div
                                        class="relative inline-block text-left"
                                        x-data="{ openCancel: false }"
                                    >
                                        <button
                                            type="button"
                                            @click="openCancel = true"
                                            data-tippy-content="{{__('Delete')}}"
                                            class="size-7 shrink-0 rounded-md bg-red-100 flex items-center justify-center font-semibold text-red-500 hover:text-red-800 hover:bg-red-200 hover:cursor-pointer"
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
                                                                <i class="fa-regular fa-triangle-exclamation text-red-600 text-lg"></i>
                                                            </div>
                                                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left whitespace-normal">
                                                                <h3 class="text-base font-semibold text-gray-900" id="modal-title">{{__('Delete product')}}</h3>
                                                                <div class="mt-2">
                                                                    <p class="text-sm text-gray-500 font-normal">Are you sure to remove the following product from the service?{{__('Are you sure to remove the following product from the service?')}}</p>
                                                                    <p class="text-sm text-red-500 font-medium">{{$product->product}}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                                            <form action="{{route('orders.products.destroy', ['order' => $order->order->id, 'product' => $product->id])}}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500 sm:ml-3 sm:w-auto">Eliminar</button>
                                                            </form>
                                                            <button type="button" @click="openCancel = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs ring-1 ring-gray-300 ring-inset hover:bg-gray-50 sm:mt-0 sm:w-auto">Cancelar</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="text-sm text-center text-gray-400 py-5 px-4">
                        <td colspan="6" class="py-4 px-4 text-xs text-gray-500">
                            No hay mercancías en la orden
                            @can('update', $order)
                                <span>, agrega una nuevo haciendo click en el botón <span class="font-semibold">Agregar</span></span>
                            @endcan
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
