@section('title', 'Adjuntar documento')
<x-layout-app>
    <section>
        <x-navigation.breadcrumbs :links="['Transportistas' => route('transportation-agencies.index'), $transportationAgency->name => route('transportation-agencies.show', ['transportation_agency' => $transportationAgency->id]), 'Adjuntar documento' => '#']" />
        <x-headings.without-action
            :title="$transportationAgency->name"
        />
        @if ($errors->any())
            <x-alerts.error :message="'Para adjuntar el documento soluciona los siguientes errores:'" :errors="$errors" class="my-4" />
        @endif
        <form action="{{ route('transportation-agencies.documents.store', ['transportation_agency' => $transportationAgency]) }}" enctype="multipart/form-data" method="POST">
            @csrf
            <div class="divide-y divide-gray-200 rounded bg-white shadow-lits-card">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-semibold text-gray-900">Adjuntar documento a transportista</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                        <input id="document_type_id" name="document_type_id" type="hidden" value="5"/>
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
                        <a href="{{ route('transportation-agencies.show', ['transportation_agency' => $transportationAgency->id]) }}" class="text-sm/6 font-semibold text-gray-900">Cancelar</a>
                        <button type="submit" class="rounded bg-lits-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-lits-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lits-red-450 hover:cursor-pointer">Adjuntar</button>
                    </div>
                </div>
            </div>
        </form>
    </section>
</x-layout-app>
