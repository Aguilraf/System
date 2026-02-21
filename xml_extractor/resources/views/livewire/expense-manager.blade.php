<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
        <div>
            <div class="flex items-center gap-3">
                <a href="/finance" class="text-gray-400 hover:text-blue-600 transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-800">📊 Egresos</h1>
            </div>
            <p class="text-gray-500 mt-1 ml-8">Administración de gastos y facturas</p>
        </div>
        <button wire:click="openForm" class="inline-flex items-center gap-2 bg-gradient-to-r from-red-500 to-red-600 text-white px-5 py-2.5 rounded-xl font-medium shadow-md hover:shadow-lg transition-all duration-200 hover:from-red-600 hover:to-red-700">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nuevo Egreso
        </button>
    </div>

    {{-- Flash Message --}}
    @if(session()->has('message'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-2 animate-pulse">
            <svg class="h-5 w-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('message') }}
        </div>
    @endif

    {{-- Search --}}
    <div class="school-card p-4 mb-6">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar por emisor, RFC, descripción..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all text-sm">
        </div>
    </div>

    {{-- Expense Form Modal --}}
    @if($showForm)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" wire:click="closeForm"></div>
        <div class="flex min-h-full items-start justify-center p-4 pt-16 sm:pt-24">
            <div class="relative w-full max-w-3xl bg-white rounded-2xl shadow-2xl transform transition-all" wire:click.stop>
                {{-- Modal Header --}}
                <div class="flex items-center justify-between p-6 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-gray-800">
                        {{ $editingId ? '✏️ Editar Egreso' : '➕ Nuevo Egreso' }}
                    </h2>
                    <button wire:click="closeForm" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="p-6 space-y-6 max-h-[70vh] overflow-y-auto">
                    {{-- Invoice Toggle --}}
                    <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <div class="relative">
                                <input type="checkbox" wire:model.live="hasInvoice" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-300 rounded-full peer peer-checked:bg-blue-600 transition-colors"></div>
                                <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-700">¿Tiene factura?</span>
                        </label>
                        @if($hasInvoice)
                            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full font-medium">Con factura</span>
                        @endif
                    </div>

                    {{-- Invoice Upload --}}
                    @if($hasInvoice)
                    <div class="space-y-3">
                        <label class="block text-sm font-medium text-gray-700">Subir factura (XML o PDF)</label>
                        <div class="relative">
                            <input type="file" wire:model="invoiceFile" accept=".xml,.pdf" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all">
                            <div wire:loading wire:target="invoiceFile" class="mt-2 text-sm text-blue-600 flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                Procesando archivo...
                            </div>
                        </div>
                        @error('invoiceFile') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        @if($extractedFromInvoice)
                            <div class="flex items-center gap-2 text-sm text-emerald-600 bg-emerald-50 px-3 py-2 rounded-lg">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Datos extraídos de la factura automáticamente
                            </div>
                        @endif
                    </div>
                    @endif

                    {{-- Date --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha *</label>
                        <input type="date" wire:model="fecha" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                        @error('fecha') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>

                    {{-- Invoice Details --}}
                    @if($hasInvoice)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-blue-50/50 rounded-xl border border-blue-100">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">RFC</label>
                            <input type="text" wire:model="rfc" maxlength="13" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all" placeholder="XAXX010101000">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Emisor</label>
                            <input type="text" wire:model="nombre_emisor" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all" placeholder="Razón social">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Método de Pago</label>
                            <select wire:model="metodo_pago" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                                <option value="">Seleccionar...</option>
                                <option value="PUE">PUE - Pago en una sola exhibición</option>
                                <option value="PPD">PPD - Pago en parcialidades o diferido</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Forma de Pago</label>
                            <select wire:model="forma_pago" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                                <option value="">Seleccionar...</option>
                                <option value="01">01 - Efectivo</option>
                                <option value="02">02 - Cheque nominativo</option>
                                <option value="03">03 - Transferencia electrónica</option>
                                <option value="04">04 - Tarjeta de crédito</option>
                                <option value="28">28 - Tarjeta de débito</option>
                                <option value="99">99 - Por definir</option>
                            </select>
                        </div>
                    </div>
                    @endif

                    {{-- Conceptos (Items) --}}
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <label class="text-sm font-medium text-gray-700">Conceptos *</label>
                            <button type="button" wire:click="addItem" class="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Agregar concepto
                            </button>
                        </div>
                        @error('items') <span class="text-sm text-red-600 mb-2 block">{{ $message }}</span> @enderror

                        <div class="space-y-3">
                            @foreach($items as $index => $item)
                            <div class="flex items-start gap-2 p-3 bg-gray-50 rounded-xl border border-gray-200 group" wire:key="item-{{ $index }}">
                                <div class="flex-1 grid grid-cols-12 gap-2">
                                    <div class="col-span-2">
                                        <label class="block text-xs text-gray-500 mb-1">Cant.</label>
                                        <input type="number" wire:model.live.debounce.500ms="items.{{ $index }}.cantidad" step="0.01" min="0.01" class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-200">
                                        @error("items.{$index}.cantidad") <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-span-5">
                                        <label class="block text-xs text-gray-500 mb-1">Descripción</label>
                                        <input type="text" wire:model="items.{{ $index }}.descripcion" class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-200" placeholder="Descripción del concepto">
                                        @error("items.{$index}.descripcion") <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-xs text-gray-500 mb-1">P. Unit.</label>
                                        <input type="number" wire:model.live.debounce.500ms="items.{{ $index }}.precio_unitario" step="0.01" min="0" class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-200">
                                    </div>
                                    <div class="col-span-3">
                                        <label class="block text-xs text-gray-500 mb-1">Importe</label>
                                        <input type="number" wire:model="items.{{ $index }}.importe" step="0.01" min="0" class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm bg-gray-100 focus:border-blue-500 focus:ring-1 focus:ring-blue-200" readonly>
                                    </div>
                                </div>
                                @if(count($items) > 1)
                                <button type="button" wire:click="removeItem({{ $index }})" class="mt-5 text-gray-400 hover:text-red-500 transition-colors opacity-0 group-hover:opacity-100">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Totals --}}
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Subtotal</label>
                            <input type="number" wire:model="subtotal" step="0.01" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-200 {{ !$hasInvoice ? 'bg-gray-100' : '' }}" {{ !$hasInvoice ? 'readonly' : '' }}>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">ISR</label>
                            <input type="number" wire:model="isr" step="0.01" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-200">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">IVA</label>
                            <input type="number" wire:model="iva" step="0.01" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-200">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Descuento</label>
                            <input type="number" wire:model="descuento" step="0.01" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-200">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Total *</label>
                            <input type="number" wire:model="total" step="0.01" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-semibold focus:border-blue-500 focus:ring-1 focus:ring-blue-200 {{ !$hasInvoice ? '' : 'bg-blue-50 border-blue-200' }}">
                            @error('total') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notas (opcional)</label>
                        <textarea wire:model="notas" rows="2" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all" placeholder="Notas adicionales..."></textarea>
                    </div>

                    {{-- Actions --}}
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" wire:click="closeForm" class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-50 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl text-sm font-medium shadow-md hover:shadow-lg hover:from-blue-600 hover:to-blue-700 transition-all flex items-center gap-2" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="save">
                                {{ $editingId ? 'Actualizar' : 'Guardar' }}
                            </span>
                            <span wire:loading wire:target="save" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                Guardando...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- Expenses Table --}}
    <div class="school-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Descripción</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Emisor</th>
                        <th class="text-center px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Factura</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="text-center px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($expenses as $expense)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 text-gray-600">{{ $expense->fecha->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-800">{{ $expense->items->first()->descripcion ?? 'Sin descripción' }}</p>
                            @if($expense->items->count() > 1)
                                <span class="text-xs text-gray-400">+{{ $expense->items->count() - 1 }} conceptos más</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $expense->nombre_emisor ?: '—' }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($expense->has_invoice)
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Sí
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">No</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right font-semibold text-red-600">${{ number_format($expense->total, 2) }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-1">
                                <button wire:click="viewDetail({{ $expense->id }})" class="p-1.5 text-gray-400 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition-all" title="Ver detalle">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                                <button wire:click="edit({{ $expense->id }})" class="p-1.5 text-gray-400 hover:text-amber-600 rounded-lg hover:bg-amber-50 transition-all" title="Editar">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button wire:click="confirmDelete({{ $expense->id }})" class="p-1.5 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition-all" title="Eliminar">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-100 mb-4">
                                    <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <p class="text-gray-500 font-medium">No hay egresos registrados</p>
                                <p class="text-gray-400 text-sm mt-1">Haz clic en "Nuevo Egreso" para comenzar</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($expenses->count())
                <tfoot class="border-t-2 border-gray-200 bg-gray-50">
                    <tr>
                        <td colspan="4" class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Total en página:</td>
                        <td class="px-6 py-3 text-right text-sm font-bold text-red-600">${{ number_format($expenses->sum('total'), 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
        @if($expenses->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $expenses->links() }}
        </div>
        @endif
    </div>

    {{-- Detail Modal --}}
    @if($showDetailModal && $viewingExpense)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" wire:click="closeDetail"></div>
        <div class="flex min-h-full items-start justify-center p-4 pt-16 sm:pt-24">
            <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl" wire:click.stop>
                <div class="flex items-center justify-between p-6 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-gray-800">📋 Detalle del Egreso</h2>
                    <button wire:click="closeDetail" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-6 max-h-[70vh] overflow-y-auto space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-xs text-gray-500">Fecha</span>
                            <p class="font-medium text-gray-800">{{ $viewingExpense->fecha->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500">Factura</span>
                            <p class="font-medium text-gray-800">{{ $viewingExpense->has_invoice ? 'Sí' : 'No' }}</p>
                        </div>
                        @if($viewingExpense->has_invoice)
                        <div>
                            <span class="text-xs text-gray-500">RFC</span>
                            <p class="font-medium text-gray-800">{{ $viewingExpense->rfc ?: '—' }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500">Emisor</span>
                            <p class="font-medium text-gray-800">{{ $viewingExpense->nombre_emisor ?: '—' }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500">Método de Pago</span>
                            <p class="font-medium text-gray-800">{{ $viewingExpense->metodo_pago ?: '—' }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500">Forma de Pago</span>
                            <p class="font-medium text-gray-800">{{ $viewingExpense->forma_pago ?: '—' }}</p>
                        </div>
                        @endif
                    </div>

                    {{-- Conceptos --}}
                    <div class="mt-4">
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">Conceptos</h3>
                        <div class="bg-gray-50 rounded-xl overflow-hidden">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="text-left px-4 py-2 text-xs text-gray-500">Cant.</th>
                                        <th class="text-left px-4 py-2 text-xs text-gray-500">Descripción</th>
                                        <th class="text-right px-4 py-2 text-xs text-gray-500">P. Unit.</th>
                                        <th class="text-right px-4 py-2 text-xs text-gray-500">Importe</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($viewingExpense->items as $item)
                                    <tr>
                                        <td class="px-4 py-2 text-gray-600">{{ number_format($item->cantidad, 2) }}</td>
                                        <td class="px-4 py-2 text-gray-800">{{ $item->descripcion }}</td>
                                        <td class="px-4 py-2 text-right text-gray-600">${{ number_format($item->precio_unitario, 2) }}</td>
                                        <td class="px-4 py-2 text-right font-medium text-gray-800">${{ number_format($item->importe, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Totals --}}
                    <div class="bg-gray-50 rounded-xl p-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Subtotal</span>
                            <span class="text-gray-800">${{ number_format($viewingExpense->subtotal, 2) }}</span>
                        </div>
                        @if($viewingExpense->iva > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">IVA</span>
                            <span class="text-gray-800">${{ number_format($viewingExpense->iva, 2) }}</span>
                        </div>
                        @endif
                        @if($viewingExpense->isr > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">ISR</span>
                            <span class="text-gray-800">-${{ number_format($viewingExpense->isr, 2) }}</span>
                        </div>
                        @endif
                        @if($viewingExpense->descuento > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Descuento</span>
                            <span class="text-gray-800">-${{ number_format($viewingExpense->descuento, 2) }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between text-sm font-bold border-t border-gray-200 pt-2">
                            <span class="text-gray-700">Total</span>
                            <span class="text-red-600">${{ number_format($viewingExpense->total, 2) }}</span>
                        </div>
                    </div>

                    @if($viewingExpense->notas)
                    <div>
                        <span class="text-xs text-gray-500">Notas</span>
                        <p class="text-sm text-gray-700 mt-1">{{ $viewingExpense->notas }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    @if($showDeleteModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" wire:click="cancelDelete"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl p-6 text-center" wire:click.stop>
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-red-100 mx-auto mb-4">
                    <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">¿Eliminar egreso?</h3>
                <p class="text-sm text-gray-500 mb-6">Esta acción no se puede deshacer.</p>
                <div class="flex gap-3 justify-center">
                    <button wire:click="cancelDelete" class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-50 transition-colors">Cancelar</button>
                    <button wire:click="delete" class="px-5 py-2.5 bg-red-600 text-white rounded-xl text-sm font-medium hover:bg-red-700 transition-colors shadow-md">Eliminar</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
