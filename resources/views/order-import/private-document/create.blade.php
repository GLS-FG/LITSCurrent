@section('title', 'Adjuntar documento interno')
<x-layout-app>
    <section>
        <x-navigation.breadcrumbs :links="['Ordenes' => route('orders.index'), $order->code => route('orders.show', ['order' => $order->id]), 'Importación' => route('orders.imports.show', ['order' => $order->id, 'import' => $import->id]), 'Adjuntar documento interno' => '#']" />
        <x-headings.without-action
            :title="$order->code"
        />
        @if ($errors->any())
            <x-alerts.error :message="'Para adjuntar el documento soluciona los siguientes errores:'" :errors="$errors" class="my-4" />
        @endif
        <form action="{{ route('orders.imports.privates.store', ['order' => $order, 'import' => $import]) }}" enctype="multipart/form-data" method="POST" class="mt-8 grid grid-cols-1 gap-x-8 gap-y-8 lg:grid-cols-3">
            @csrf
            <div class="h-fit lg:col-span-2 divide-y divide-gray-200 rounded bg-white shadow-lits-card">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-semibold text-gray-900">Adjuntar documento interno a orden</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                        <div class="sm:col-span-3">
                            <label for="private_document_type_id" class="block text-sm/6 font-medium text-gray-900">Tipo de documento</label>
                            <div class="mt-2 grid grid-cols-1">
                                <select id="private_document_type_id" name="private_document_type_id" autocomplete="off" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white py-1.5 pr-8 pl-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    @foreach ($documents as $document)
                                        <option value= {{ $document->id }} @selected(old('private_document_type_id') == $document->id)>{{ $document->name }}  </option>
                                    @endforeach
                                </select>
                                <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 sm:text-sm"></i>
                            </div>
                        </div>


                        <div
                            class="col-span-full"
                            x-data="{
                                        isDragging: false,
                                        showFiles: false,
                                        files: [],
                                        readableSize(file) {
                                            if(file.size < 1000){
                                                return Math.floor(file.size) + ' B'
                                            } else if(file.size < 1000000){
                                                return Math.floor(file.size/1000) + ' KB'
                                            } else {
                                                return Math.floor(file.size/1000000) + ' MB'
                                            }
                                        },
                                        sizeError(file) {
                                            if(file.size > 26214400){
                                                return 'El archivo es muy grande (max. 25 MB)'
                                            }
                                            return ''
                                        },
                                        removeItem(index) {
                                            this.files.splice(index, 1)
                                            if(this.files.length < 1){
                                                this.showFiles = false;
                                                $refs.file.value = null
                                            } else {
                                                const dataTransfer = new DataTransfer();
                                                for(let myFile of this.files) {
                                                    dataTransfer.items.add(myFile);
                                                }
                                                $refs.file.files = dataTransfer.files;
                                            }
                                        },
                                        onAddFiles() {
                                            var addedFilesArray = Array.from($refs.file.files);
                                            var filesArray = [...this.files, ...addedFilesArray];
                                            this.files = filesArray;
                                            this.showFiles = true;
                                            const dataTransfer = new DataTransfer();
                                            for(let myFile of this.files) {
                                                dataTransfer.items.add(myFile);
                                            }
                                            $refs.file.files = dataTransfer.files;
                                        }
                                    }"
                        >
                            <div x-ref="dnd"
                                 class="relative border border-dashed border-gray-900/25 px-6 py-10 rounded cursor-pointer"
                                 :class="isDragging ? 'bg-indigo-50' : ''"
                            >
                                <input accept="*" type="file" id="attachments" name="attachments[]" title="" x-ref="file"
                                       multiple
                                       required
                                       @change="onAddFiles"
                                       class="absolute inset-0 z-50 w-full h-full p-0 m-0 outline-none opacity-0 cursor-pointer"
                                       @dragover="isDragging = true"
                                       @dragleave="isDragging = false"
                                       @drop="isDragging = false"
                                />
                                <div class="flex flex-col items-center justify-center py-10 text-center">
                                    <i class="fa-regular fa-image mx-auto text-5xl text-gray-300"></i>
                                    <p class="mt-1 text-sm/6 text-gray-600">Arrastra tus archivos aquí, o haz click en esta área.</p>
                                    <p class="text-xs/6 text-gray-400">Máx. 25 MB</p>
                                </div>
                            </div>
                            <div x-show="showFiles" class="mt-4 rounded-md border border-gray-200 divide-y divide-gray-200">
                                <template x-for="(file, index) in files" :key="index">
                                    <div class="flex items-center p-4">
                                        <div class="space-y-0.5">
                                            <p class="font-medium text-gray-900" x-text="file.name"></p>
                                            <p class="text-xs text-gray-500" x-text="readableSize(file)"></p>
                                            <p x-show="sizeError(file) != ''" class="text-sm text-red-500" x-text="sizeError(file)"></p>
                                        </div>
                                        <div class="ml-auto pl-3">
                                            <div class="-mx-1.5 -my-1.5">
                                                <button @click="removeItem(index)" type="button" class="inline-flex rounded-md  p-1.5 text-gray-500 hover:bg-gray-100 focus:ring-2 focus:ring-gray-600 focus:ring-offset-2 focus:ring-offset-gray-50 focus:outline-hidden">
                                                    <span class="sr-only">Dismiss</span>
                                                    <i class="fa-regular fa-xmark"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="px-4 py-4 sm:px-6">
                    <div class="mt-6 flex items-center justify-end gap-x-6">
                        <a href="{{ route('orders.imports.show', ['order' => $order->id, 'import' => $import->id]) }}" class="text-sm/6 font-semibold text-gray-900">Cancelar</a>
                        <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Adjuntar</button>
                    </div>
                </div>
            </div>

            <div class="divide-y divide-gray-200 rounded bg-white shadow-xs h-fit">
                <div class="px-4 py-5 sm:px-6">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <h3 class="text-lg font-semibold text-gray-900">Orden de servicio</h3>
                        <div class="flex shrink-0">
                            <span class="inline-flex items-center rounded-md  px-2 py-1 text-xs font-medium  ring-1 ring-inset {{ $order->order_status_id->badgeColor() }}">{{ $order->order_status_id->label() }}</span>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-5 sm:p-6 space-y-6">
                    <div class="">
                        <dt class="text-xs text-gray-400"># Orden</dt>
                        <dd class="mt-1 text-sm text-gray-700 sm:col-span-2 sm:mt-0">{{ $order->code }}</dd>
                    </div>
                    <div class="">
                        <dt class="text-xs text-gray-400">Referencia</dt>
                        <dd class="mt-1 text-sm text-gray-700 sm:col-span-2 sm:mt-0">{{ $order->reference }}</dd>
                    </div>
                    <div class="">
                        <dt class="text-xs text-gray-400">Cliente</dt>
                        <div class="grow flex items-center">
                            <div class="size-8 shrink-0">
                                <img alt="{{$order->client->trade_name}}" src="{{ route('clients.logos', [ 'filename' => str_replace(".","_",str_replace("logos/", "", $order->client->image))]) }}" class="size-8 rounded-full object-contain bg-gray-200"/>
                            </div>
                            <div class="ml-2">
                                <div class="text-sm font-medium text-gray-900">{{ $order->client->trade_name }}</div>
                                <div class="text-gray-500 text-xs">{{$order->contact->name}}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</x-layout-app>
