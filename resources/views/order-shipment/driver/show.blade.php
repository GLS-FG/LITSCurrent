@section('title', 'Sube imágenes')
<x-layout>
    <div class="flex min-h-full flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <img class="mx-auto h-20 w-auto" src="{{ asset('/images/lits.png') }}" alt="Lits" />
        </div>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-3xl">
            @if ($errors->any())
                <x-alerts.error :message="'Hubo un error al gurdar las imágenes:'" :errors="$errors" class="mb-4" />
            @endif
            @if(session()->has('success'))
                <x-alerts.success class="mb-4" :message="session('success')" />
            @endif
            @if(!$shipment->driver_link_used)
                <form action="{{url()->full()}}" enctype="multipart/form-data" method="POST">
                    @csrf
                    <div class="h-fit lg:col-span-2 divide-y divide-gray-200 dark:divide-gray-700 rounded bg-white dark:bg-lits-blue-550 shadow-lits-card">
                        <div class="px-4 py-5 sm:px-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-50">Adjuntar imágenes a embarque</h3>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                <div
                                    class="col-span-full"
                                    x-data="{
                                        isDragging: false,
                                        showFiles: false,
                                        files: [],
                                        imageSrcs: [],
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
                                            this.imageSrcs.splice(index, 1)
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
                                            this.imageSrcs = [];
                                            this.showFiles = true;
                                            const dataTransfer = new DataTransfer();
                                            for(let myFile of this.files) {
                                                dataTransfer.items.add(myFile);
                                                const reader = new FileReader();
                                                reader.onload = (e) => {
                                                    this.imageSrcs.push(e.target.result);
                                                };
                                                reader.readAsDataURL(myFile);
                                            }
                                            $refs.file.files = dataTransfer.files;
                                        }
                                    }"
                                >
                                    <div x-ref="dnd"
                                         class="relative border border-dashed border-gray-900/25 dark:border-white/20 px-6 py-10 rounded cursor-pointer"
                                         :class="isDragging ? 'bg-indigo-50' : ''"
                                    >
                                        <input type="file" id="attachments" name="attachments[]" title="" x-ref="file"
                                               multiple
                                               required
                                               accept="image/*"
                                               capture="environment"
                                               @change="onAddFiles"
                                               class="absolute inset-0 z-50 w-full h-full p-0 m-0 outline-none opacity-0 cursor-pointer"
                                               @dragover="isDragging = true"
                                               @dragleave="isDragging = false"
                                               @drop="isDragging = false"
                                        />
                                        <div class="flex flex-col items-center justify-center py-10 text-center">
                                            <i class="fa-regular fa-image mx-auto text-5xl text-gray-300 dark:text-gray-600"></i>
                                            <p class="mt-1 text-sm/6 text-gray-600 dark:text-gray-400">Arrastra tus archivos aquí, o haz click en esta área para tomar una foto.</p>
                                            <p class="text-xs/6 text-gray-400 dark:text-gray-500">Máx. 25 MB</p>
                                        </div>
                                    </div>
                                    <div x-show="showFiles" class="mt-4 rounded-md border border-gray-200 dark:border-lits-blue-450 divide-y divide-gray-200 dark:divide-gray-700">
                                        <template x-for="(file, index) in files" :key="index">
                                            <div class="flex items-center p-4">
                                                <div>
                                                    <img :src="imageSrcs[index]" alt="Preview" class="size-20 rounded object-cover" />
                                                </div>
                                                <div class="ml-2 space-y-0.5">
                                                    <p class="font-medium text-gray-900 dark:text-gray-50" x-text="file.name"></p>
                                                    <div class="mb-1 grid grid-cols-1 w-fit">
                                                        <select name="document_type_id[]" autocomplete="off" class="col-start-1 row-start-1 appearance-none rounded-md bg-white dark:bg-lits-blue-550 py-1.5 pr-8 pl-3 text-base text-gray-900 dark:text-gray-50 outline-1 -outline-offset-1 outline-gray-300 dark:outline-gray-600 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                            @foreach ($documents as $document)
                                                                <option value="{{ $document->id }}">{{ $document->name }}  </option>
                                                            @endforeach
                                                        </select>
                                                        <i class="fa-regular fa-angle-down pointer-events-none col-start-1 row-start-1 mr-2 text-base self-center justify-self-end text-gray-500 dark:text-gray-400 sm:text-sm"></i>
                                                    </div>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400" x-text="readableSize(file)"></p>
                                                    <p x-show="sizeError(file) != ''" class="text-sm text-red-500 dark:text-red-400" x-text="sizeError(file)"></p>
                                                </div>
                                                <div class="ml-auto pl-3">
                                                    <div class="-mx-1.5 -my-1.5">
                                                        <button @click="removeItem(index)" type="button" class="inline-flex rounded-md  p-1.5 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:ring-2 focus:ring-gray-600 focus:ring-offset-2 focus:ring-offset-gray-50 focus:outline-hidden">
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
                                <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Adjuntar</button>
                            </div>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </div>
</x-layout>
