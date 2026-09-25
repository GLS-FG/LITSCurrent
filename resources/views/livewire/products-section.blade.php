<div class="flow-root sm:col-span-2 mt-4">
    <div class="flex flex-wrap items-center justify-between sm:flex-nowrap mb-8">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-50">{{__('Products')}}</h3>
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
                            class="inline-flex items-center gap-x-1.5 rounded-md bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-50 shadow-xs inset-ring inset-ring-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800"
                        >
                            <i class="fa-regular fa-copy"></i>
                            {{__('Copy')}}
                        </button>
                        <div x-cloak x-show="open" class="relative z-100" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                            <div
                                class="fixed inset-0 bg-gray-500/75 dark:bg-gray-950/75 transition-opacity"
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
                                        class="relative transform overflow-hidden rounded-lg bg-white dark:bg-lits-blue-550 px-4 pt-5 pb-4 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6"
                                    >
                                        <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
                                            <button type="button" @click="open = false" class="rounded-md bg-white dark:bg-lits-blue-550 text-gray-400 dark:text-gray-500 hover:text-gray-500 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-hidden">
                                                <span class="sr-only">Close</span>
                                                <i class="fa-regular fa-xmark"></i>
                                            </button>
                                        </div>
                                        <form action="{{route('orders.products.copy', ['order' => $order->order->id])}}" method="POST">
                                            @csrf
                                            <div class="mt-3 text-center sm:mt-0 sm:text-left whitespace-normal">
                                                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-50" id="modal-title">{{__('Copy from')}}</h3>
                                                <div class="mt-10">
                                                    <input type="hidden" name="copy_type" value="{{$orderType}}">
                                                    <input type="hidden" name="copy_id" value="{{$order->id}}">
                                                    <input type="hidden" name="serviceable_type" x-model="serviceableType">
                                                    <fieldset aria-label="Ordenes" class="-space-y-px rounded-md bg-white dark:bg-lits-blue-550">
                                                        @foreach($services as $service)
                                                            <label aria-label="{{$service->service->reference}}" aria-description="This project would be available to anyone who has the link" class="group flex border border-gray-200 dark:border-lits-blue-450 p-4 first:rounded-tl-md first:rounded-tr-md last:rounded-br-md last:rounded-bl-md focus:outline-hidden has-checked:relative has-checked:border-indigo-200 has-checked:bg-indigo-50">
                                                                <input type="radio" name="serviceable_id" required value="{{$service->service->id}}" x-on:change="serviceableType = '{{$service->slug}}'" class="relative mt-0.5 size-4 shrink-0 appearance-none rounded-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-lits-blue-550 before:absolute before:inset-1 before:rounded-full before:bg-white not-checked:before:hidden checked:border-indigo-600 checked:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:before:bg-gray-400 forced-colors:appearance-auto forced-colors:before:hidden" />
                                                                <span class="ml-3 flex flex-col">
                                                                    <span class="block text-sm font-medium text-gray-900 dark:text-gray-50 group-has-checked:text-indigo-900">{{$service->serviceType}}</span>
                                                                    <span class="block text-sm text-gray-500 dark:text-gray-400 group-has-checked:text-indigo-700">{{$service->service->reference}}</span>
                                                                </span>
                                                            </label>
                                                        @endforeach
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                                <button type="submit" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500 sm:ml-3 sm:w-auto">{{__('Copy')}}</button>
                                                <button type="button" @click="open = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-50 shadow-xs ring-1 ring-gray-300 dark:ring-gray-600 ring-inset hover:bg-gray-50 dark:hover:bg-gray-800 sm:mt-0 sm:w-auto">{{__('Go back')}}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <button
                    type="button"
                    wire:click="create"
                    data-tippy-content="{{__('Add product')}}"
                    class="inline-flex items-center gap-x-1.5 rounded-md bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-50 shadow-xs inset-ring inset-ring-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800"
                >
                    <i class="fa-regular fa-plus"></i>
                    {{__('Add product')}}
                </button>
                <div
                    class="relative inline-block text-left"
                    x-data="{ openForm: @entangle('openForm'), isStore: @entangle('isStore') }"
                >
                    <div x-cloak x-show="openForm" class="relative z-100" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                        <div
                            class="fixed inset-0 bg-gray-500/75 dark:bg-gray-950/75 transition-opacity"
                            aria-hidden="true"
                            x-show="openForm"
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
                                    x-show="openForm"
                                    x-transition:enter="ease-out duration-300"
                                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                    x-transition:leave="ease-in duration-200"
                                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                    class="relative transform overflow-hidden rounded-lg bg-white dark:bg-lits-blue-550 px-4 pt-5 pb-4 text-left shadow-xl transition-all w-full sm:my-8 sm:w-full sm:max-w-3xl sm:p-6"
                                >
                                    <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
                                        <button type="button" @click="openForm = false" class="rounded-md bg-white dark:bg-lits-blue-550 text-gray-400 dark:text-gray-500 hover:text-gray-500 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-hidden">
                                            <span class="sr-only">Close</span>
                                            <i class="fa-regular fa-xmark"></i>
                                        </button>
                                    </div>
                                    <div class="mt-3 text-center sm:mt-0 sm:text-left whitespace-normal">
                                        <h3 x-show="isStore" class="text-base font-semibold text-gray-900 dark:text-gray-50" id="modal-title">{{__('Add product')}}</h3>
                                        <h3 x-show="!isStore" class="text-base font-semibold text-gray-900 dark:text-gray-50" id="modal-title">{{__('Edit product')}}</h3>
                                        <div class="grid grid-cols-3 gap-x-2 gap-y-4 sm:grid-cols-12">
                                            <div
                                                x-data="{ showSaved: false }"
                                                x-on:saved.window="showSaved = true; setTimeout(() => showSaved = false, 1300);"
                                                class="col-span-full"
                                            >
                                                <div class="rounded-md bg-green-50 dark:bg-green-500/10 p-4 border border-green-400 mt-2" x-show="showSaved" x-cloak>
                                                    <div class="flex items-center">
                                                        <div class="shrink-0">
                                                            <i class="fa-solid fa-circle-check text-green-400"></i>
                                                        </div>
                                                        <div class="ml-3">
                                                            <p class="text-sm font-medium text-green-800 dark:text-green-400">El producto se guardó con éxito.</p>
                                                        </div>
                                                        <div class="ml-auto pl-3">
                                                            <div class="-mx-1.5 -my-1.5">
                                                                <button @click="showSaved = false" type="button" class="inline-flex rounded-md bg-green-50 dark:bg-green-500/10 p-1.5 text-green-500 dark:text-green-400 hover:bg-green-100 focus:ring-2 focus:ring-green-600 focus:ring-offset-2 focus:ring-offset-green-50 focus:outline-hidden">
                                                                    <span class="sr-only">Dismiss</span>
                                                                    <i class="fa-regular fa-xmark"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-span-3 sm:col-span-12">
                                                <label for="product" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">{{__('Description')}}</label>
                                                <div class="mt-2">
                                                    <input id="product" wire:model="form.product" placeholder="{{__('Product description')}}" type="text" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                    @error('form.product') <p class="text-sm text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                                                </div>
                                            </div>
                                            <div class="col-span-3 sm:col-span-12">
                                                <label for="reference" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">{{__('Reference')}}</label>
                                                <div class="mt-2">
                                                    <input id="reference" required wire:model="form.reference" placeholder="{{__('Product reference')}}" type="text" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                    @error('form.reference') <p class="text-sm text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                                                </div>
                                            </div>
                                            <div class="sm:col-span-3">
                                                <label for="length" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">{{__('length')}}</label>
                                                <div class="mt-2">
                                                    <input id="length" placeholder="{{__('length')}}" type="number" wire:model="form.length" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                    @error('form.length') <p class="text-sm text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                                                </div>
                                            </div>
                                            <div class="sm:col-span-3">
                                                <label for="width" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">{{__('Width')}}</label>
                                                <div class="mt-2">
                                                    <input id="width" placeholder="{{__('Width')}}" type="number" wire:model="form.width" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                    @error('form.width') <p class="text-sm text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                                                </div>
                                            </div>
                                            <div class="sm:col-span-3">
                                                <label for="height" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">{{__('Height')}}</label>
                                                <div class="mt-2">
                                                    <input id="height" placeholder="{{__('Height')}}" type="number" wire:model="form.height" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                    @error('form.height') <p class="text-sm text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                                                </div>
                                            </div>
                                            <div class="sm:col-span-3">
                                                <label for="unit_measure" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">{{__('Size unit')}}</label>
                                                <div class="mt-2 grid grid-cols-1">
                                                    <select id="unit_measure" wire:model="form.unit_measure" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                        <option value="IN">IN</option>
                                                        <option value="FT">FT</option>
                                                        <option value="MM">MM</option>
                                                        <option value="CM">CM</option>
                                                        <option value="MTS">MTS</option>
                                                    </select>
                                                    <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 dark:text-gray-400 sm:text-sm"></i>
                                                </div>
                                                @error('form.unit_measure') <p class="text-sm text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                                            </div>
                                            <div class="sm:col-span-12">
                                                <label for="dimensions" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">{{__('Other dimensions (Optional)')}}</label>
                                                <div class="mt-2">
                                                    <input id="dimensions" placeholder="VARIOS" type="text" wire:model="form.dimensions" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                    @error('form.dimensions') <p class="text-sm text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                                                </div>
                                            </div>
                                            <div class="sm:col-span-3">
                                                <label for="weight" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">{{__('Weight')}}</label>
                                                <div class="mt-2">
                                                    <input id="weight" placeholder="{{__('Weight')}}" type="text" wire:model="form.weight" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                    @error('form.weight') <p class="text-sm text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                                                </div>
                                            </div>
                                            <div class="sm:col-span-3">
                                                <label for="weight_measure" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">{{__('Weight unit')}}</label>
                                                <div class="mt-2 grid grid-cols-1">
                                                    <select id="weight_measure" wire:model="form.weight_measure" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                        <option value="KG">KG</option>
                                                        <option value="LBS">LBS</option>
                                                    </select>
                                                    <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 dark:text-gray-400 sm:text-sm"></i>
                                                </div>
                                                @error('form.weight_measure') <p class="text-sm text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                                            </div>
                                            <div class="sm:col-span-6">
                                                <label for="container" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">{{__('Packaging type')}}</label>
                                                <div class="mt-2 grid grid-cols-1">
                                                    <select id="container" wire:model="form.container" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                        <option value="Pallet">Pallet</option>
                                                        <option value="Crate">Crate</option>
                                                        <option value="Drum">Drum</option>
                                                        <option value="Tote">Tote</option>
                                                        <option value="Bulk">Bulk</option>
                                                        <option value="Carton">Carton</option>
                                                    </select>
                                                    <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 dark:text-gray-400 sm:text-sm"></i>
                                                </div>
                                                @error('form.container') <p class="text-sm text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                                            </div>
                                            <div class="sm:col-span-3">
                                                <label for="quantity" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">{{__('Quantity')}}</label>
                                                <div class="mt-2">
                                                    <input id="quantity" type="number" placeholder="{{__('Quantity')}}" min="0" wire:model="form.quantity" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                    @error('form.quantity') <p class="text-sm text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                                                </div>
                                            </div>
                                            <div class="sm:col-span-3">
                                                <label for="value" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">{{__('Value')}}</label>
                                                <div class="mt-2">
                                                    <input id="value" type="number" placeholder="$999.99" wire:model="form.value" class="block w-full rounded-md bg-white dark:bg-lits-blue-550 px-3 py-1.5 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                                    @error('form.value') <p class="text-sm text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                                                </div>
                                            </div>
                                            <div class="sm:col-span-6">
                                                <label for="incoterm_id" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-50">INCOTERM</label>
                                                <div class="mt-2 grid grid-cols-1">
                                                    <select id="incoterm_id" wire:model="form.incoterm_id" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                        @foreach ($incoterms as $incoterm)
                                                            <option value="{{ $incoterm->id }}">{{ $incoterm->code }} ({{ $incoterm->name }})</option>
                                                        @endforeach
                                                    </select>
                                                    <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 dark:text-gray-400 sm:text-sm"></i>
                                                </div>
                                                @error('form.incoterm_id') <p class="text-sm text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                        <button type="button" x-show="isStore" wire:click="store('single')" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500 sm:ml-3 sm:w-auto">{{__('Save and close')}}</button>
                                        <button type="button" x-show="isStore" wire:click="store('multiple')" class="inline-flex w-full justify-center rounded-md bg-red-100 dark:bg-red-500/15 px-3 py-2 text-sm font-semibold text-red-500 dark:text-red-400 shadow-xs hover:bg-red-200 sm:ml-3 sm:w-auto">{{__('Save and add another')}}</button>
                                        <button type="button" x-show="!isStore" wire:click="update" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500 sm:ml-3 sm:w-auto">{{__('Save')}}</button>
                                        <button type="button" @click="openForm = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-50 shadow-xs ring-1 ring-gray-300 dark:ring-gray-600 ring-inset hover:bg-gray-50 dark:hover:bg-gray-800 sm:mt-0 sm:w-auto">{{__('Go back')}}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan
            @can('delete', $order)
                <div
                    class="relative inline-block text-left"
                    x-data="{ openDelete: @entangle('openDelete'), productName: @entangle('productName') }"
                >
                    <div x-cloak x-show="openDelete" class="relative z-100" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                        <div
                            class="fixed inset-0 bg-gray-500/75 dark:bg-gray-950/75 transition-opacity"
                            aria-hidden="true"
                            x-show="openDelete"
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
                                    x-show="openDelete"
                                    x-transition:enter="ease-out duration-300"
                                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                    x-transition:leave="ease-in duration-200"
                                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                    class="relative transform overflow-hidden rounded-lg bg-white dark:bg-lits-blue-550 px-4 pt-5 pb-4 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6"
                                >
                                    <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
                                        <button type="button" @click="openDelete = false" class="rounded-md bg-white dark:bg-lits-blue-550 text-gray-400 dark:text-gray-500 hover:text-gray-500 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-hidden">
                                            <span class="sr-only">Close</span>
                                            <i class="fa-regular fa-xmark"></i>
                                        </button>
                                    </div>
                                    <div class="sm:flex sm:items-start">
                                        <div class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-500/15 sm:mx-0 sm:size-10">
                                            <i class="fa-regular fa-triangle-exclamation text-red-600 dark:text-red-400 text-lg"></i>
                                        </div>
                                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left whitespace-normal">
                                            <h3 class="text-base font-semibold text-gray-900 dark:text-gray-50" id="modal-title">{{__('Delete product')}}</h3>
                                            <div class="mt-2">
                                                <p class="text-sm text-gray-500 dark:text-gray-400 font-normal">Are you sure to remove the following product from the service?{{__('Are you sure to remove the following product from the service?')}}</p>
                                                <p class="text-sm text-red-500 dark:text-red-400 font-medium" x-text="productName"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                        <button type="button" wire:click="destroy" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500 sm:ml-3 sm:w-auto">Eliminar</button>
                                        <button type="button" @click="openDelete = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-lits-blue-550 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-50 shadow-xs ring-1 ring-gray-300 dark:ring-gray-600 ring-inset hover:bg-gray-50 dark:hover:bg-gray-800 sm:mt-0 sm:w-auto">Cancelar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan
        </div>
    </div>
    <div class="overflow-x-auto border border-gray-300 dark:border-gray-600 rounded">
        <div class="inline-block min-w-full align-middle">
            <table class="relative min-w-full divide-y divide-gray-300 dark:divide-gray-600">
                <thead>
                <tr class="divide-x divide-gray-200 dark:divide-gray-700">
                    <th scope="col" class="py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-gray-900 dark:text-gray-50 sm:pl-3">{{__('Transaction')}}</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-50">{{__('Description')}}</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-50">{{__('Reference')}}</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-50">{{__('Dimensions')}}</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-50">{{__('Quantity')}}</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-50">{{__('Value')}}</th>
                    <th scope="col" class="py-3.5 pr-4 pl-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-50 sm:pr-3">{{__('Actions')}}</th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-lits-blue-550 divide-y divide-gray-300 dark:divide-gray-600">
                @forelse($products as $product)
                    <tr class="even:bg-gray-50 divide-x divide-gray-200 dark:divide-gray-700">
                        <td class="py-4 pr-3 pl-4 text-xs text-gray-500 dark:text-gray-400 sm:pl-3">
                            <p class="font-medium whitespace-nowrap text-gray-900 dark:text-gray-50"># {{$product->id}}</p>
                        </td>
                        <td class="px-3 py-4 text-xs text-gray-500 dark:text-gray-400">{{$product->product}}</td>
                        <td class="px-3 py-4 text-xs text-gray-500 dark:text-gray-400">{{$product->reference}}</td>
                        <td class="px-3 py-4 text-xs text-gray-500 dark:text-gray-400">
                            <div class="flex space-x-1.5 items-center text-gray-500 dark:text-gray-400 mb-1.5">
                                <i class="fa-regular fa-ruler-combined text-gray-900 dark:text-gray-50 text-lg"></i>
                                <p class="whitespace-nowrap">
                                    @if($product->dimensions)
                                        {{$product->dimensions}}
                                    @else
                                        {{(float)$product->length}} X {{(float)$product->width}} X {{(float)$product->height}} {{$product->unit_measure}}
                                    @endif
                                </p>
                            </div>
                            <div class="flex space-x-1.5 items-center text-gray-500 dark:text-gray-400 mb-1.5">
                                <i class="fa-regular fa-weight-hanging text-gray-900 dark:text-gray-50 text-lg"></i>
                                <p>{{$product->weight}} {{$product->weight_measure}}</p>
                            </div>
                            <div class="flex space-x-1.5 items-center text-gray-500 dark:text-gray-400">
                                <i class="fa-regular fa-box-isometric-tape text-gray-900 dark:text-gray-50 text-lg"></i>
                                <p>{{$product->container}}</p>
                            </div>
                        </td>
                        <td class="px-3 py-4 text-xs whitespace-nowrap text-gray-500 dark:text-gray-400">{{(float)$product->quantity}}</td>
                        <td class="px-3 py-4 text-xs whitespace-nowrap text-gray-500 dark:text-gray-400">{{$product->value}}</td>
                        <td class="py-4 pr-4 pl-3 text-right text-xs font-medium whitespace-nowrap sm:pr-3">
                            <div class="flex items-center justify-center gap-x-1">
                                @can('update', $order)
                                    <button
                                        type="button"
                                        wire:click="edit({{$product->id}})"
                                        data-tippy-content="{{__('indexes.edit')}}"
                                        class="size-7 shrink-0 rounded-md bg-green-100 dark:bg-green-500/15 flex items-center justify-center font-semibold text-green-500 dark:text-green-400 hover:text-green-800 hover:bg-green-200 hover:cursor-pointer"
                                    >
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </button>
                                @endcan
                                @can('delete', $order)
                                    <button
                                        type="button"
                                        wire:click="delete({{$product->id}})"
                                        data-tippy-content="{{__('Delete')}}"
                                        class="size-7 shrink-0 rounded-md bg-red-100 dark:bg-red-500/15 flex items-center justify-center font-semibold text-red-500 dark:text-red-400 hover:text-red-800 hover:bg-red-200 hover:cursor-pointer"
                                    >
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="text-sm text-center text-gray-400 dark:text-gray-500 py-5 px-4">
                        <td colspan="6" class="py-4 px-4 text-xs text-gray-500 dark:text-gray-400">
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
