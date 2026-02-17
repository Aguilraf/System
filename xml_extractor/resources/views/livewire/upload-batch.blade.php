<div class="max-w-4xl mx-auto py-12">
    <div class="school-card p-8 text-center">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Subir Archivos XML</h2>
        
        @if (session()->has('duplicates'))
            <div class="mb-4 p-4 text-left bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700">
                <p class="font-bold">Atención:</p>
                <ul class="list-disc list-inside text-sm">
                    @foreach (session('duplicates') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

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

        {{-- Staging Area --}}
        @if(!empty($sfiles))
            <div class="mt-8 text-left">
                <h3 class="font-semibold text-gray-700 text-lg mb-4">Revisión de Archivos ({{ count($sfiles) }})</h3>
                
                <div class="bg-white rounded-lg shadow overflow-hidden border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-10">
                                    {{-- Global check could go here --}}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Archivo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($sfiles as $index => $file)
                                <tr class="{{ $file['status'] === 'duplicate' ? 'bg-red-50' : ($file['status'] === 'error' ? 'bg-gray-50' : '') }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($file['status'] === 'valid')
                                            <input type="checkbox" wire:click="toggleSelection({{ $index }})" {{ $file['selected'] ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        @else
                                            <span class="text-red-500 text-lg">•</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $file['name'] }}</div>
                                        @if($file['message'])
                                            <div class="text-sm text-red-600 font-bold mt-1">
                                                {{ $file['message'] }}
                                                @if(!empty($file['duplicate_batch_id']))
                                                    <a href="{{ url('/batch/' . $file['duplicate_batch_id']) }}" target="_blank" class="text-blue-600 underline hover:text-blue-800 ml-2">
                                                        (Ver Trabajo)
                                                    </a>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($file['status'] === 'valid')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Listo
                                            </span>
                                        @elseif($file['status'] === 'duplicate')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Duplicado
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                Error
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <button wire:click="removeFile({{ $index }})" class="text-red-600 hover:text-red-900">Quitar</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-6 flex justify-end">
                    @php
                        $hasSelection = collect($sfiles)->contains('selected', true);
                    @endphp
                    <button 
                        wire:click="save" 
                        wire:loading.attr="disabled" 
                        @if(!$hasSelection) disabled @endif
                        class="btn-primary py-3 px-8 text-lg shadow-lg flex items-center {{ !$hasSelection ? 'opacity-50 cursor-not-allowed bg-gray-400 hover:bg-gray-400' : '' }}"
                    >
                        <span wire:loading.remove wire:target="save">Procesar Seleccionados</span>
                        <span wire:loading wire:target="save">Procesando...</span>
                    </button>
                </div>
                @error('files') <span class="block mt-2 text-red-500 text-sm text-right">{{ $message }}</span> @enderror
            </div>
        @endif

        <!-- Progress Bar -->
        <div x-show="isUploading" class="mt-4 w-full bg-gray-200 rounded-full h-2.5">
            <div class="bg-blue-600 h-2.5 rounded-full" :style="'width: ' + progress + '%'"></div>
        </div>
    </div>
</div>
