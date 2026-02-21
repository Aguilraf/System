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
                        wire:click="triggerSave" 
                        wire:loading.attr="disabled" 
                        @if(!$hasSelection) disabled @endif
                        class="btn-primary py-3 px-8 text-lg shadow-lg flex items-center {{ !$hasSelection ? 'opacity-50 cursor-not-allowed bg-gray-400 hover:bg-gray-400' : '' }}"
                    >
                        <span wire:loading.remove wire:target="triggerSave">Procesar Seleccionados</span>
                        <span wire:loading wire:target="triggerSave">Calculando...</span>
                    </button>
                </div>
                @error('files') <span class="block mt-2 text-red-500 text-sm text-right">{{ $message }}</span> @enderror
            </div>
        @endif

        <!-- Progress Bar -->
        <div x-show="isUploading" class="mt-4 w-full bg-gray-200 rounded-full h-2.5">
            <div class="bg-blue-600 h-2.5 rounded-full" :style="'width: ' + progress + '%'"></div>
        </div>
        <!-- Confirmation Modal -->
        @if($showConfirmationModal)
            <div class="fixed inset-0 z-10 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                        Confirmar Carga
                                    </h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">
                                            ¿Deseas generar la carga de facturas por un importe de <strong class="text-gray-800">${{ number_format($processTotal, 2) }}</strong>?
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button wire:click="save" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Sí, procesar
                            </button>
                            <button wire:click="$set('showConfirmationModal', false)" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancelar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
