<div>
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">💰 Panel Financiero</h1>
        <p class="text-gray-500 mt-1">Resumen de ingresos y egresos</p>
    </div>

    {{-- Period Filter --}}
    <div class="school-card p-4 mb-6">
        <div class="flex flex-wrap items-center gap-4">
            <span class="text-sm font-medium text-gray-600">Filtrar por:</span>
            <div class="flex gap-2">
                <button wire:click="$set('period', 'month')"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ $period === 'month' ? 'bg-blue-600 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Mes
                </button>
                <button wire:click="$set('period', 'year')"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ $period === 'year' ? 'bg-blue-600 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Año
                </button>
                <button wire:click="$set('period', 'all')"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ $period === 'all' ? 'bg-blue-600 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Todo
                </button>
            </div>
            @if($period !== 'all')
                <div class="flex items-center gap-2">
                    @if($period === 'month')
                        <select wire:model.live="selectedMonth" class="rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            @foreach(['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'] as $i => $name)
                                <option value="{{ $i + 1 }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    @endif
                    <select wire:model.live="selectedYear" class="rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        @for($y = now()->year; $y >= now()->year - 5; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            @endif
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        {{-- Income Card --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-700 p-6 text-white shadow-lg transition-transform duration-200 hover:scale-[1.02]">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 h-24 w-24 rounded-full bg-white/10"></div>
            <div class="absolute bottom-0 right-0 -mb-6 -mr-6 h-32 w-32 rounded-full bg-white/5"></div>
            <div class="relative">
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                    </div>
                    <span class="text-sm font-medium text-emerald-100">Total Ingresos</span>
                </div>
                <p class="text-3xl font-bold">${{ number_format($totalIncomes, 2) }}</p>
                <p class="text-sm text-emerald-200 mt-1">{{ $incomes->count() }} registros</p>
            </div>
        </div>

        {{-- Expense Card --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-red-500 to-red-700 p-6 text-white shadow-lg transition-transform duration-200 hover:scale-[1.02]">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 h-24 w-24 rounded-full bg-white/10"></div>
            <div class="absolute bottom-0 right-0 -mb-6 -mr-6 h-32 w-32 rounded-full bg-white/5"></div>
            <div class="relative">
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                    </div>
                    <span class="text-sm font-medium text-red-100">Total Egresos</span>
                </div>
                <p class="text-3xl font-bold">${{ number_format($totalExpenses, 2) }}</p>
                <p class="text-sm text-red-200 mt-1">{{ $expenses->count() }} registros</p>
            </div>
        </div>

        {{-- Balance Card --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br {{ $balance >= 0 ? 'from-blue-500 to-indigo-700' : 'from-orange-500 to-orange-700' }} p-6 text-white shadow-lg transition-transform duration-200 hover:scale-[1.02]">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 h-24 w-24 rounded-full bg-white/10"></div>
            <div class="absolute bottom-0 right-0 -mb-6 -mr-6 h-32 w-32 rounded-full bg-white/5"></div>
            <div class="relative">
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                    </div>
                    <span class="text-sm font-medium {{ $balance >= 0 ? 'text-blue-100' : 'text-orange-100' }}">Balance</span>
                </div>
                <p class="text-3xl font-bold">{{ $balance >= 0 ? '+' : '-' }}${{ number_format(abs($balance), 2) }}</p>
                <p class="text-sm {{ $balance >= 0 ? 'text-blue-200' : 'text-orange-200' }} mt-1">{{ $balance >= 0 ? 'Positivo' : 'Negativo' }}</p>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <a href="/finance/expenses" class="school-card p-6 flex items-center gap-4 hover:shadow-lg transition-all duration-200 hover:border-red-300 group cursor-pointer">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-red-50 text-red-600 group-hover:bg-red-100 transition-colors">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800 group-hover:text-red-600 transition-colors">Gestionar Egresos</h3>
                <p class="text-sm text-gray-500">Registrar y administrar gastos y facturas</p>
            </div>
            <svg class="h-5 w-5 text-gray-400 ml-auto group-hover:text-red-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>

        <a href="/finance/incomes" class="school-card p-6 flex items-center gap-4 hover:shadow-lg transition-all duration-200 hover:border-emerald-300 group cursor-pointer">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 group-hover:bg-emerald-100 transition-colors">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800 group-hover:text-emerald-600 transition-colors">Gestionar Ingresos</h3>
                <p class="text-sm text-gray-500">Registrar y administrar entradas de dinero</p>
            </div>
            <svg class="h-5 w-5 text-gray-400 ml-auto group-hover:text-emerald-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    {{-- Monthly Chart --}}
    <div class="school-card p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-6">Resumen Mensual {{ $selectedYear }}</h2>
        <div class="overflow-x-auto">
            <div class="flex items-end gap-2 min-w-[600px] h-64 pb-8 relative">
                @php
                    $maxValue = max(
                        collect($monthlyData)->max('incomes'),
                        collect($monthlyData)->max('expenses'),
                        1
                    );
                    $monthNames = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
                @endphp

                @foreach($monthlyData as $index => $data)
                    <div class="flex-1 flex flex-col items-center gap-1 relative group">
                        {{-- Tooltip --}}
                        <div class="absolute -top-16 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs rounded-lg py-2 px-3 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-10 pointer-events-none">
                            <p class="text-emerald-400">+${{ number_format($data['incomes'], 2) }}</p>
                            <p class="text-red-400">-${{ number_format($data['expenses'], 2) }}</p>
                        </div>
                        <div class="flex gap-0.5 items-end w-full justify-center" style="height: 200px;">
                            {{-- Income bar --}}
                            <div class="w-[40%] rounded-t transition-all duration-300 hover:opacity-80"
                                 style="height: {{ $maxValue > 0 ? ($data['incomes'] / $maxValue * 100) : 0 }}%; min-height: {{ $data['incomes'] > 0 ? '4px' : '0' }}; background: linear-gradient(to top, #10b981, #34d399);">
                            </div>
                            {{-- Expense bar --}}
                            <div class="w-[40%] rounded-t transition-all duration-300 hover:opacity-80"
                                 style="height: {{ $maxValue > 0 ? ($data['expenses'] / $maxValue * 100) : 0 }}%; min-height: {{ $data['expenses'] > 0 ? '4px' : '0' }}; background: linear-gradient(to top, #ef4444, #f87171);">
                            </div>
                        </div>
                        <span class="text-xs text-gray-500 mt-1">{{ $monthNames[$index] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="flex items-center justify-center gap-6 mt-4 pt-4 border-t border-gray-100">
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                <span class="text-sm text-gray-600">Ingresos</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-red-500"></div>
                <span class="text-sm text-gray-600">Egresos</span>
            </div>
        </div>
    </div>

    {{-- Recent Transactions --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Recent Expenses --}}
        <div class="school-card p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Últimos Egresos</h2>
                <a href="/finance/expenses" class="text-sm text-blue-600 hover:text-blue-800 transition-colors">Ver todos →</a>
            </div>
            @forelse($expenses->take(5) as $expense)
                <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-lg {{ $expense->has_invoice ? 'bg-purple-50 text-purple-600' : 'bg-gray-50 text-gray-500' }}">
                            @if($expense->has_invoice)
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            @else
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $expense->nombre_emisor ?: ($expense->items->first()->descripcion ?? 'Sin descripción') }}</p>
                            <p class="text-xs text-gray-500">{{ $expense->fecha->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    <span class="text-sm font-semibold text-red-600">-${{ number_format($expense->total, 2) }}</span>
                </div>
            @empty
                <p class="text-sm text-gray-400 text-center py-6">No hay egresos registrados</p>
            @endforelse
        </div>

        {{-- Recent Incomes --}}
        <div class="school-card p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Últimos Ingresos</h2>
                <a href="/finance/incomes" class="text-sm text-blue-600 hover:text-blue-800 transition-colors">Ver todos →</a>
            </div>
            @forelse($incomes->take(5) as $income)
                <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $income->descripcion }}</p>
                            <p class="text-xs text-gray-500">{{ $income->fecha->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    <span class="text-sm font-semibold text-emerald-600">+${{ number_format($income->total, 2) }}</span>
                </div>
            @empty
                <p class="text-sm text-gray-400 text-center py-6">No hay ingresos registrados</p>
            @endforelse
        </div>
    </div>
</div>
