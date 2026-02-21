<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Sistema de Gestión' }}</title>
    <meta name="description" content="Sistema de gestión de facturas XML e ingresos/egresos">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>📂</text></svg>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }
        .school-card { background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb; }
        .btn-primary { background-color: #3b82f6; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; transition: background-color 0.2s; }
        .btn-primary:hover { background-color: #2563eb; }
        .nav-link { position: relative; color: #6b7280; transition: color 0.2s; }
        .nav-link:hover { color: #2563eb; }
        .nav-link.active { color: #2563eb; font-weight: 600; }
        .nav-link.active::after { content: ''; position: absolute; bottom: -1px; left: 0; right: 0; height: 2px; background: linear-gradient(to right, #3b82f6, #6366f1); border-radius: 1px; }
        .nav-divider { width: 1px; height: 24px; background-color: #e5e7eb; }
    </style>
    @livewireStyles
</head>
<body class="min-h-screen flex flex-col">
    <nav class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="flex-shrink-0 flex items-center gap-2 mr-8">
                        <span class="text-xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">Sistema de Gestión</span>
                    </a>
                </div>
                <div class="flex items-center space-x-1">
                    {{-- XML Module Links --}}
                    <a href="/" class="nav-link px-3 py-5 text-sm font-medium {{ request()->is('/') ? 'active' : '' }}">
                        <span class="flex items-center gap-1.5">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            Subir XML
                        </span>
                    </a>
                    <a href="/batches" class="nav-link px-3 py-5 text-sm font-medium {{ request()->is('batches*') || request()->is('batch*') ? 'active' : '' }}">
                        <span class="flex items-center gap-1.5">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            Mis Trabajos
                        </span>
                    </a>

                    <div class="nav-divider mx-2"></div>

                    {{-- Finance Module Links --}}
                    <a href="/finance" class="nav-link px-3 py-5 text-sm font-medium {{ request()->is('finance') ? 'active' : '' }}">
                        <span class="flex items-center gap-1.5">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            Finanzas
                        </span>
                    </a>
                    <a href="/expenses" class="nav-link px-3 py-5 text-sm font-medium {{ request()->is('expenses') ? 'active' : '' }}">
                        <span class="flex items-center gap-1.5">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                            Egresos
                        </span>
                    </a>
                    <a href="/incomes" class="nav-link px-3 py-5 text-sm font-medium {{ request()->is('incomes') ? 'active' : '' }}">
                        <span class="flex items-center gap-1.5">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                            Ingresos
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow max-w-7xl w-full mx-auto py-6 px-4 sm:px-6 lg:px-8">
        {{ $slot }}
    </main>

    <footer class="bg-white border-t border-gray-200 mt-auto">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 text-center text-gray-500 text-sm">
            &copy; {{ date('Y') }} Sistema de Gestión
        </div>
    </footer>

    @livewireScripts
</body>
</html>
