<div class="min-h-[80vh] flex flex-col items-center justify-center -mt-6">
    {{-- Welcome Header --}}
    <div class="text-center mb-12">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-xl shadow-blue-500/25 mb-6 animate-float">
            <svg class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
            </svg>
        </div>
        <h1 class="text-4xl font-extrabold text-gray-900 mb-3">
            Sistema de <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">Gestión</span>
        </h1>
        <p class="text-lg text-gray-500 max-w-md mx-auto">Selecciona el módulo con el que deseas trabajar</p>
    </div>

    {{-- Module Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 w-full max-w-4xl px-4">

        {{-- XML Module Card --}}
        <a href="/xml" class="group relative overflow-hidden rounded-3xl bg-white border border-gray-200 shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 cursor-pointer">
            {{-- Decorative gradient background --}}
            <div class="absolute inset-0 bg-gradient-to-br from-sky-500/5 via-blue-500/5 to-indigo-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-blue-400/10 to-indigo-500/10 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform duration-700"></div>
            <div class="absolute bottom-0 left-0 w-32 h-32 bg-gradient-to-tr from-sky-400/10 to-blue-500/10 rounded-full translate-y-1/2 -translate-x-1/2 group-hover:scale-150 transition-transform duration-700"></div>

            <div class="relative p-8">
                {{-- Icon --}}
                <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg shadow-blue-500/30 mb-6 group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                    <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                </div>

                {{-- Title --}}
                <h2 class="text-2xl font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">Facturas XML</h2>
                <p class="text-gray-500 mb-6 leading-relaxed">Carga, procesa y extrae datos de tus facturas XML de manera masiva y eficiente.</p>

                {{-- Stats --}}
                <div class="flex items-center gap-4 mb-6">
                    <div class="flex items-center gap-2 bg-blue-50 text-blue-700 px-3 py-1.5 rounded-full text-sm font-medium">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        {{ $xmlBatchCount }} {{ $xmlBatchCount === 1 ? 'lote' : 'lotes' }}
                    </div>
                </div>

                {{-- CTA --}}
                <div class="flex items-center text-blue-600 font-semibold group-hover:gap-3 gap-2 transition-all">
                    <span>Entrar al módulo</span>
                    <svg class="h-5 w-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </div>
            </div>
        </a>

        {{-- Finance Module Card --}}
        <a href="/finance" class="group relative overflow-hidden rounded-3xl bg-white border border-gray-200 shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 cursor-pointer">
            {{-- Decorative gradient background --}}
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 via-teal-500/5 to-green-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-emerald-400/10 to-teal-500/10 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform duration-700"></div>
            <div class="absolute bottom-0 left-0 w-32 h-32 bg-gradient-to-tr from-green-400/10 to-emerald-500/10 rounded-full translate-y-1/2 -translate-x-1/2 group-hover:scale-150 transition-transform duration-700"></div>

            <div class="relative p-8">
                {{-- Icon --}}
                <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/30 mb-6 group-hover:scale-110 group-hover:-rotate-3 transition-all duration-300">
                    <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>

                {{-- Title --}}
                <h2 class="text-2xl font-bold text-gray-900 mb-2 group-hover:text-emerald-600 transition-colors">Finanzas</h2>
                <p class="text-gray-500 mb-6 leading-relaxed">Administra tus ingresos y egresos, visualiza reportes y mantén el control financiero.</p>

                {{-- Stats --}}
                <div class="flex items-center gap-3 mb-6 flex-wrap">
                    <div class="flex items-center gap-1.5 bg-emerald-50 text-emerald-700 px-3 py-1.5 rounded-full text-sm font-medium">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                        {{ $incomeCount }} ingresos
                    </div>
                    <div class="flex items-center gap-1.5 bg-red-50 text-red-600 px-3 py-1.5 rounded-full text-sm font-medium">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                        {{ $expenseCount }} egresos
                    </div>
                </div>

                {{-- CTA --}}
                <div class="flex items-center text-emerald-600 font-semibold group-hover:gap-3 gap-2 transition-all">
                    <span>Entrar al módulo</span>
                    <svg class="h-5 w-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </div>
            </div>
        </a>
    </div>

    {{-- Quick Balance Summary --}}
    @if($totalIncomes > 0 || $totalExpenses > 0)
    <div class="mt-10 px-4 w-full max-w-4xl">
        <div class="bg-white/80 backdrop-blur-sm border border-gray-200 rounded-2xl p-5 flex flex-wrap items-center justify-center gap-8 text-sm">
            <div class="flex items-center gap-2">
                <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                <span class="text-gray-500">Ingresos totales:</span>
                <span class="font-bold text-emerald-600">${{ number_format($totalIncomes, 2) }}</span>
            </div>
            <div class="w-px h-6 bg-gray-200"></div>
            <div class="flex items-center gap-2">
                <div class="w-2.5 h-2.5 rounded-full bg-red-500"></div>
                <span class="text-gray-500">Egresos totales:</span>
                <span class="font-bold text-red-600">${{ number_format($totalExpenses, 2) }}</span>
            </div>
            <div class="w-px h-6 bg-gray-200"></div>
            <div class="flex items-center gap-2">
                <div class="w-2.5 h-2.5 rounded-full {{ ($totalIncomes - $totalExpenses) >= 0 ? 'bg-blue-500' : 'bg-orange-500' }}"></div>
                <span class="text-gray-500">Balance:</span>
                <span class="font-bold {{ ($totalIncomes - $totalExpenses) >= 0 ? 'text-blue-600' : 'text-orange-600' }}">
                    {{ ($totalIncomes - $totalExpenses) >= 0 ? '+' : '-' }}${{ number_format(abs($totalIncomes - $totalExpenses), 2) }}
                </span>
            </div>
        </div>
    </div>
    @endif
</div>
