<div class="max-w-7xl mx-auto py-12">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Trabajo: {{ $batch->name }}</h2>
            <p class="text-sm text-gray-500">{{ $batch->total_files }} archivos procesados</p>
        </div>
        <div class="space-x-2">
            <a href="/xml/batches" class="text-gray-600 hover:text-blue-600 px-3 py-2">Volver</a>
            <button wire:click="export" class="btn-primary">Descargar Excel</button>
        </div>
    </div>

    <div class="space-y-6">
        <!-- Section: Column Selector -->
        <div class="school-card p-6">
            <h3 class="font-bold text-gray-700 mb-4">1. Selecciona los Campos</h3>
            <div class="mb-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" wire:model.live="selectAll" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-700 font-semibold">Seleccionar Todos</span>
                </label>
            </div>
            
            <!-- Grid for checkboxes -->
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 max-h-96 overflow-y-auto pr-2 border-t pt-4">
                @foreach($availableColumns as $col)
                    <label class="flex items-center space-x-2 p-2 hover:bg-gray-50 rounded transition-colors">
                        <input type="checkbox" value="{{ $col }}" wire:model.live="selectedColumns" class="rounded border-gray-300 text-blue-600 shadow-sm">
                        <span class="text-xs text-gray-600 truncate" title="{{ $col }}">{{ $col }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Section: Data Table -->
        <div class="school-card p-6 overflow-x-auto">
            <h3 class="font-bold text-gray-700 mb-4">2. Datos ({{ count($previewRows) }} registros)</h3>
            
            @if(empty($selectedColumns))
                <div class="text-center py-12 text-gray-500">Selecciona columnas para ver la vista previa</div>
            @else
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            @foreach($selectedColumns as $col)
                                <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap border-b">{{ $col }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($previewRows as $row)
                            <tr>
                                @foreach($selectedColumns as $col)
                                    <td class="px-4 py-2 whitespace-nowrap text-gray-700 border-b">{{ Str::limit($row[$col] ?? '-', 30) }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-100 font-bold">
                        <tr>
                            @foreach($selectedColumns as $col)
                                <td class="px-4 py-3 whitespace-nowrap text-gray-800 border-t border-gray-300">
                                    @if(isset($columnTotals[$col]))
                                        {{ number_format($columnTotals[$col], 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    </tfoot>
                </table>
            @endif
        </div>
    </div>
</div>
