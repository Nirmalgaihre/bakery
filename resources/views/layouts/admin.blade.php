<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Deurali Chemicals Pvt Ltd - Admin Terminal')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: { sans: ['Inter', 'sans-serif'] },
                colors: {
                    brandDark: '#0b1329',
                    brandDarkLight: '#111c3a',
                }
            }
        }
    }
    </script>
    <style>
    [x-cloak] { display: none !important; }
    ::-webkit-scrollbar { width: 4px; height: 4px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.2); }
    </style>
</head>

<body class="bg-[#f3f4f6] text-[#1e293b] font-sans h-screen flex overflow-hidden antialiased">

    @if(View::exists('partials.alerts'))
        @include('partials.alerts')
    @endif

    <aside class="w-[280px] bg-brandDark text-slate-300 flex flex-col h-full shrink-0 select-none hidden md:flex border-r border-slate-900">
        
        <div class="p-5 flex items-center gap-3 border-b border-slate-800/60">
            <div class="bg-blue-600 text-white font-bold h-10 w-10 rounded-lg flex items-center justify-center text-lg shadow-md shrink-0">D</div>
            <div class="overflow-hidden">
                <h1 class="text-[11px] font-bold tracking-wider text-blue-400 uppercase leading-none mb-1 truncate">Deurali Chemicals</h1>
                <p class="text-sm font-bold text-white tracking-tight truncate">Chemicals Inventory</p>
                <p class="text-[10px] text-slate-400 mt-0.5 truncate">
                    <i class="fa-solid fa-location-dot text-blue-500 mr-1"></i>Kuleshwor, KTM
                </p>
            </div>
        </div>

        <div class="p-4 px-5 border-b border-slate-800/40 space-y-2.5">
            <div class="flex items-center justify-between text-[11px]">
                <span class="text-slate-400 font-semibold tracking-wider flex items-center gap-1.5 uppercase">
                    <span class="h-2 w-2 bg-emerald-500 rounded-full inline-block animate-pulse"></span>System Service
                </span>
                <span class="bg-blue-950/60 text-blue-300 text-[10px] font-mono px-2 py-0.5 rounded border border-blue-900/60">LV-12 FILAMENT</span>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto py-4 space-y-1 px-3"
            x-data="{ 
                openCustomers: {{ request()->routeIs('admin.customers.*') ? 'true' : 'false' }}, 
                openProducts: {{ request()->routeIs('admin.products.*') ? 'true' : 'false' }},
                openBilling: {{ (request()->is('admin/sales/pos*') || request()->routeIs('admin.sales.index') || request()->routeIs('admin.sales.dashboard')) ? 'true' : 'false' }}
            }">
            
            <div class="text-[10px] font-bold text-slate-500 px-2 mb-2 tracking-wider uppercase">Main Menu</div>

            <a href="{{ route('admin.dashboard') }}"
                class="flex items-center px-4 py-2.5 text-[13px] font-medium rounded-lg transition-all {{ request()->routeIs('admin.dashboard') ? 'text-white bg-blue-600 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-brandDarkLight' }}">
                <i class="fa-solid fa-chart-pie mr-3 w-4 text-center text-base"></i>Dashboard
            </a>

            <div class="rounded-lg overflow-hidden">
                <button @click="openBilling = !openBilling"
                    class="w-full flex items-center justify-between px-4 py-2.5 text-[13px] font-medium transition-all {{ (request()->is('admin/sales/pos*') || request()->routeIs('admin.sales.index') || request()->routeIs('admin.sales.dashboard')) ? 'text-white bg-brandDarkLight/60' : 'text-slate-400 hover:text-white hover:bg-brandDarkLight' }} outline-none">
                    <span class="flex items-center">
                        <i class="fa-solid fa-file-invoice mr-3 w-4 text-center text-sm"></i>Billing System
                    </span>
                    <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200" :class="openBilling ? 'rotate-180' : ''"></i>
                </button>

                <div x-show="openBilling" x-cloak x-collapse class="bg-slate-950/20 pl-4 border-l border-slate-800 my-0.5 py-1 space-y-0.5">
                    <a href="{{ route('admin.sales.create') }}"
                        class="flex items-center px-4 py-2 text-[12px] font-medium rounded-md {{ request()->routeIs('admin.sales.create') && !request()->route()->parameter('product') ? 'text-white bg-blue-600/20 font-semibold' : 'text-slate-400 hover:text-white hover:bg-brandDarkLight/40' }} transition-colors">
                        <i class="fa-solid fa-plus text-[10px] mr-2.5"></i>New Invoice (POS)
                    </a>
                    <a href="{{ route('admin.sales.dashboard') }}"
                        class="flex items-center px-4 py-2 text-[12px] font-medium rounded-md {{ request()->routeIs('admin.sales.dashboard') || request()->routeIs('admin.sales.index') ? 'text-white bg-blue-600/20 font-semibold' : 'text-slate-400 hover:text-white hover:bg-brandDarkLight/40' }} transition-colors">
                        <i class="fa-solid fa-receipt text-[10px] mr-2.5"></i>Sales Logs
                    </a>
                </div>
            </div>

            <div class="rounded-lg overflow-hidden">
                <button @click="openCustomers = !openCustomers"
                    class="w-full flex items-center justify-between px-4 py-2.5 text-[13px] font-medium transition-all {{ request()->routeIs('admin.customers.*') ? 'text-white bg-brandDarkLight/60' : 'text-slate-400 hover:text-white hover:bg-brandDarkLight' }} outline-none">
                    <span class="flex items-center">
                        <i class="fa-solid fa-address-book mr-3 w-4 text-center text-sm"></i>Customers Ledger
                    </span>
                    <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200" :class="openCustomers ? 'rotate-180' : ''"></i>
                </button>

                <div x-show="openCustomers" x-cloak x-collapse class="bg-slate-950/20 pl-4 border-l border-slate-800 my-0.5 py-1 space-y-0.5">
                    <a href="{{ route('admin.customers.create') }}" class="flex items-center px-4 py-2 text-[12px] font-medium rounded-md text-slate-400 hover:text-white hover:bg-brandDarkLight/40">Add Customer</a>
                    <a href="{{ route('admin.customers.index') }}" class="flex items-center px-4 py-2 text-[12px] font-medium rounded-md text-slate-400 hover:text-white hover:bg-brandDarkLight/40">Manage Customers</a>
                </div>
            </div>

            <div class="rounded-lg overflow-hidden">
                <button @click="openProducts = !openProducts"
                    class="w-full flex items-center justify-between px-4 py-2.5 text-[13px] font-medium transition-all {{ request()->routeIs('admin.products.*') ? 'text-white bg-brandDarkLight/60' : 'text-slate-400 hover:text-white hover:bg-brandDarkLight' }} outline-none">
                    <span class="flex items-center">
                        <i class="fa-solid fa-boxes-stacked mr-3 w-4 text-center text-sm"></i>Product Catalog
                    </span>
                    <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200" :class="openProducts ? 'rotate-180' : ''"></i>
                </button>

                <div x-show="openProducts" x-cloak x-collapse class="bg-slate-950/20 pl-4 border-l border-slate-800 my-0.5 py-1 space-y-0.5">
                    <a href="{{ route('admin.products.index') }}" class="flex items-center px-4 py-2 text-[12px] font-medium rounded-md {{ request()->routeIs('admin.products.index') ? 'text-white bg-blue-600/20 font-semibold' : 'text-slate-400 hover:text-white' }}">Warehouse Stock</a>
                    <a href="{{ route('admin.products.create') }}" class="flex items-center px-4 py-2 text-[12px] font-medium rounded-md {{ request()->routeIs('admin.products.create') ? 'text-white bg-blue-600/20 font-semibold' : 'text-slate-400 hover:text-white' }}">Add New Item</a>
                </div>
            </div>
        </nav>
        <div class="p-3 px-5 border-t border-slate-800/60 text-[11px] text-slate-500 bg-slate-950/20 text-center font-mono">Secure-Live</div>
    </aside>

    <div class="flex-1 flex flex-col h-full overflow-hidden">
        
        <div class="bg-white border-b border-slate-200 h-14 px-4 md:px-6 flex items-center justify-between shrink-0 z-10">
            <div class="flex items-center gap-3">
                <span class="w-1 h-4 bg-blue-600 rounded-sm"></span>
                <h2 class="text-xs md:text-sm font-bold tracking-wider uppercase text-slate-800">
                    @yield('panel_title', 'Warehouse Analytical Panel')
                </h2>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-1.5 bg-blue-50 border border-blue-100 px-2.5 py-1 rounded-md text-xs font-semibold text-blue-700">
                    <i class="fa-solid fa-shield text-[10px]"></i> System Admin
                </div>
            </div>
        </div>

        <header class="bg-slate-100 border-b border-slate-200 flex items-stretch p-1 px-4 gap-1 overflow-x-auto select-none shrink-0 z-20 shadow-inner">
            
            {{-- Group 1: QUICK CREATE --}}
            <div class="flex flex-col items-center border-r border-slate-300/70 pr-3 py-1">
                <span class="text-[9px] font-bold text-slate-400 mb-1 tracking-wider uppercase">QUICK CREATE</span>
                <div class="flex gap-1.5">
                    <a href="{{ route('admin.sales.create') }}" class="flex items-center gap-1.5 bg-white border border-slate-200 hover:bg-blue-50 hover:border-blue-300 text-slate-700 text-xs font-medium px-3 py-1 rounded transition-colors shadow-2xs">
                        <i class="fa-solid fa-file-invoice text-slate-400"></i> New Sale
                    </a>
                    <button class="flex items-center gap-1.5 bg-white border border-slate-200 hover:bg-blue-50 hover:border-blue-300 text-slate-700 text-xs font-medium px-3 py-1 rounded transition-colors shadow-2xs">
                        <i class="fa-solid fa-cart-shopping text-slate-400"></i> New Purchase
                    </button>
                </div>
            </div>
            
            {{-- Group 2: MASTERS --}}
            <div class="flex flex-col items-center border-r border-slate-300/70 px-3 py-1">
                <span class="text-[9px] font-bold text-slate-400 mb-1 tracking-wider uppercase">MASTERS</span>
                <div class="flex gap-1.5">
                    <a href="{{ route('admin.customers.create') }}" class="flex items-center gap-1.5 bg-white border border-slate-200 hover:bg-blue-50 hover:border-blue-300 text-slate-700 text-xs font-medium px-3 py-1 rounded transition-colors shadow-2xs">
                        <i class="fa-solid fa-user-plus text-slate-400"></i> Add Party
                    </a>
                    <a href="{{ route('admin.products.create') }}" class="flex items-center gap-1.5 bg-white border border-slate-200 hover:bg-blue-50 hover:border-blue-300 text-slate-700 text-xs font-medium px-3 py-1 rounded transition-colors shadow-2xs">
                        <i class="fa-solid fa-box-open text-slate-400"></i> Add Item
                    </a>
                </div>
            </div>

            {{-- Group 3: OUTPUT --}}
            <div class="flex flex-col items-center border-r border-slate-300/70 px-3 py-1">
                <span class="text-[9px] font-bold text-slate-400 mb-1 tracking-wider uppercase">OUTPUT</span>
                <div class="flex gap-1.5">
                    <button class="flex items-center gap-1.5 bg-white border border-slate-200 hover:bg-blue-50 hover:border-blue-300 text-slate-700 text-xs font-medium px-3 py-1 rounded transition-colors shadow-2xs">
                        <i class="fa-solid fa-chart-line text-slate-400"></i> Reports
                    </button>
                    <button class="flex items-center gap-1.5 bg-white border border-slate-200 hover:bg-blue-50 hover:border-blue-300 text-slate-700 text-xs font-medium px-3 py-1 rounded transition-colors shadow-2xs">
                        <i class="fa-solid fa-print text-slate-400"></i> Print
                    </button>
                    <button class="flex items-center gap-1.5 bg-white border border-slate-200 hover:bg-blue-50 hover:border-blue-300 text-slate-700 text-xs font-medium px-3 py-1 rounded transition-colors shadow-2xs">
                        <i class="fa-solid fa-file-export text-slate-400"></i> Export
                    </button>
                </div>
            </div>

            {{-- Group 4: BACKUP --}}
            <div class="flex flex-col items-center border-r border-slate-300/70 px-3 py-1">
                <span class="text-[9px] font-bold text-slate-400 mb-1 tracking-wider uppercase">BACKUP</span>
                <div class="flex gap-1.5">
                    <button class="flex items-center gap-1.5 bg-white border border-slate-200 hover:bg-blue-50 hover:border-blue-300 text-slate-700 text-xs font-medium px-3 py-1 rounded transition-colors shadow-2xs">
                        <i class="fa-solid fa-cloud-arrow-up text-slate-400"></i> Backup
                    </button>
                    <button class="flex items-center gap-1.5 bg-white border border-slate-200 hover:bg-blue-50 hover:border-blue-300 text-slate-700 text-xs font-medium px-3 py-1 rounded transition-colors shadow-2xs">
                        <i class="fa-solid fa-cloud-arrow-down text-slate-400"></i> Restore
                    </button>
                </div>
            </div>

            {{-- Group 5: FILE & AI --}}
            <div class="flex flex-col items-center px-3 py-1">
                <span class="text-[9px] font-bold text-slate-400 mb-1 tracking-wider uppercase">FILE & AI</span>
                <div class="flex gap-1.5">
                    <button class="flex items-center gap-1.5 bg-white border border-slate-200 hover:bg-blue-50 hover:border-blue-300 text-slate-700 text-xs font-medium px-3 py-1 rounded transition-colors shadow-2xs">
                        <i class="fa-solid fa-building text-slate-400"></i> Switch Co.
                    </button>
                    <button class="flex items-center gap-1.5 bg-blue-600 border border-blue-700 hover:bg-blue-700 text-white text-xs font-bold px-3 py-1 rounded transition-colors shadow-sm animate-pulse">
                        <i class="fa-solid fa-robot"></i> AI
                    </button>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="flex items-center gap-1.5 bg-white border border-slate-200 hover:bg-rose-50 hover:border-rose-300 hover:text-rose-600 text-slate-700 text-xs font-medium px-3 py-1 rounded transition-colors shadow-2xs">
                            <i class="fa-solid fa-right-from-bracket"></i> Exit
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <main class="flex-1 p-4 md:p-6 overflow-y-auto space-y-6">
            @yield('content')
        </main>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>