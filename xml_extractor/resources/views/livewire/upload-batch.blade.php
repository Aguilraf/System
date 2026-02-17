<div class="max-w-4xl mx-auto py-12">
    <div class="school-card p-8 text-center">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Subir Archivos XML</h2>
        <p class="text-gray-600 mb-8">Arrastra tus facturas aquí o haz clic para seleccionarlas.</p>

        <div
            x-data="{ isDropping: false, isUploading: false, progress: 0 }"
            x-on:livewire-upload-start="isUploading = true"
            x-on:livewire-upload-finish="isUploading = false"
            x-on:livewire-upload-error="isUploading = false"
            x-on:livewire-upload-progress="progress = $event.detail.progress"
            class="relative border-2 border-dashed border-gray-300 rounded-lg p-12 flex flex-col items-center justify-center cursor-pointer transition-colors"
            :class="{ 'border-blue-500 bg-blue-50': isDropping }"
            @dragover.prevent="isDropping = true"
            @dragleave.prevent="isDropping = false"
            @drop.prevent="isDropping = false"
        >
            <input type="file" wire:model="files" multiple class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept=".xml">
            
            <div class="text-6xl mb-4 text-gray-400">📂</div>
            <span class="text-blue-600 font-medium">Seleccionar archivos</span>
        </div>

        @if($files)
            <div class="mt-6 text-left">
                <h3 class="font-semibold text-gray-700">Archivos seleccionados ({{ count($files) }})</h3>
                <ul class="mt-2 text-sm text-gray-500 max-h-40 overflow-y-auto">
                    @foreach($files as $file)
                        <li>{{ $file->getClientOriginalName() }}</li>
                    @endforeach
                </ul>
                
                <div class="mt-6">
                    <button wire:click="save" wire:loading.attr="disabled" class="btn-primary w-full py-3 text-lg">
                        <span wire:loading.remove wire:target="save">Procesar Archivos</span>
                        <span wire:loading wire:target="save">Procesando...</span>
                    </button>
                    
                    @error('files') <span class="block mt-2 text-red-500 text-sm">{{ $message }}</span> @enderror
                    @error('files.*') <span class="block mt-2 text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
        @endif

        <!-- Progress Bar -->
        <div x-show="isUploading" class="mt-4 w-full bg-gray-200 rounded-full h-2.5">
            <div class="bg-blue-600 h-2.5 rounded-full" :style="'width: ' + progress + '%'"></div>
        </div>
    </div>
</div>
