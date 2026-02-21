<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
        <div>
            <div class="flex items-center gap-3">
                <a href="/finance" class="text-gray-400 hover:text-blue-600 transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-800">💵 Ingresos</h1>
            </div>
            <p class="text-gray-500 mt-1 ml-8">Administración de entradas de dinero</p>
        </div>
        <button wire:click="openForm" class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white px-5 py-2.5 rounded-xl font-medium shadow-md hover:shadow-lg transition-all duration-200 hover:from-emerald-600 hover:to-emerald-700">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nuevo Ingreso
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
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar por descripción o notas..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all text-sm">
        </div>
    </div>

    {{-- Income Form Modal --}}
    @if($showForm)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" wire:click="closeForm"></div>
        <div class="flex min-h-full items-start justify-center p-4 pt-16 sm:pt-24">
            <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl transform transition-all" wire:click.stop>
                {{-- Modal Header --}}
                <div class="flex items-center justify-between p-6 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-gray-800">
                        {{ $editingId ? '✏️ Editar Ingreso' : '➕ Nuevo Ingreso' }}
                    </h2>
                    <button wire:click="closeForm" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="p-6 space-y-5">
                    {{-- Date --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha *</label>
                        <input type="date" wire:model="fecha" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
                        @error('fecha') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descripción *</label>
                        <input type="text" wire:model="descripcion" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all" placeholder="Ej: Venta de producto, Servicio prestado...">
                        @error('descripcion') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>

                    {{-- Amount fields --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad *</label>
                            <input type="number" wire:model="cantidad" step="0.01" min="0" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all" placeholder="0.00">
                            @error('cantidad') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Total *</label>
                            <input type="number" wire:model="total" step="0.01" min="0" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm font-semibold focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all bg-emerald-50 border-emerald-200" placeholder="0.00">
                            @error('total') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notas (opcional)</label>
                        <textarea wire:model="notas" rows="2" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all" placeholder="Notas adicionales..."></textarea>
                    </div>

                    {{-- Actions --}}
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" wire:click="closeForm" class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-50 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-xl text-sm font-medium shadow-md hover:shadow-lg hover:from-emerald-600 hover:to-emerald-700 transition-all flex items-center gap-2" wire:loading.attr="disabled">
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

    {{-- Incomes Table --}}
    <div class="school-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Descripción</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Cantidad</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="text-center px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($incomes as $income)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 text-gray-600">{{ $income->fecha->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-800">{{ $income->descripcion }}</p>
                            @if($income->notas)
                                <p class="text-xs text-gray-400 mt-0.5">{{ Str::limit($income->notas, 50) }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right text-gray-600">{{ number_format($income->cantidad, 2) }}</td>
                        <td class="px-6 py-4 text-right font-semibold text-emerald-600">${{ number_format($income->total, 2) }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-1">
                                <button wire:click="edit({{ $income->id }})" class="p-1.5 text-gray-400 hover:text-amber-600 rounded-lg hover:bg-amber-50 transition-all" title="Editar">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button wire:click="confirmDelete({{ $income->id }})" class="p-1.5 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition-all" title="Eliminar">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-100 mb-4">
                                    <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <p class="text-gray-500 font-medium">No hay ingresos registrados</p>
                                <p class="text-gray-400 text-sm mt-1">Haz clic en "Nuevo Ingreso" para comenzar</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($incomes->count())
                <tfoot class="border-t-2 border-gray-200 bg-gray-50">
                    <tr>
                        <td colspan="3" class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Total en página:</td>
                        <td class="px-6 py-3 text-right text-sm font-bold text-emerald-600">${{ number_format($incomes->sum('total'), 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
        @if($incomes->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $incomes->links() }}
        </div>
        @endif
    </div>

    {{-- Delete Confirmation Modal --}}
    @if($showDeleteModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" wire:click="cancelDelete"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl p-6 text-center" wire:click.stop>
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-red-100 mx-auto mb-4">
                    <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">¿Eliminar ingreso?</h3>
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
