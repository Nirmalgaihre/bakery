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
                fontFamily: {
                    sans: ['Inter', 'sans-serif']
                },
                colors: {
                    brandDark: '#0f172a',
                    brandDarkLight: '#1e293b',
                }
            }
        }
    }
    </script>
    <style>
    [x-cloak] {
        display: none !important;
    }

    ::-webkit-scrollbar {
        width: 4px;
        height: 4px;
    }

    ::-webkit-scrollbar-track {
        background: transparent;
    }

    ::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.2);
    }
    </style>
</head>

<body x-data="{ mobileSidebarOpen: false }"
    :class="mobileSidebarOpen ? 'overflow-hidden md:overflow-hidden' : 'overflow-hidden'"
    class="bg-[#f8fafc] text-[#1e293b] font-sans h-dvh flex antialiased">

    @if(View::exists('partials.alerts'))
    @include('partials.alerts')
    @endif

    <div x-show="mobileSidebarOpen" x-cloak @click="mobileSidebarOpen = false"
        class="fixed inset-0 z-30 bg-slate-950/60 backdrop-blur-sm md:hidden"></div>

    <aside
        class="fixed inset-y-0 left-0 z-40 flex h-dvh w-[min(86vw,280px)] shrink-0 select-none flex-col border-r border-slate-800 bg-brandDark text-slate-300 shadow-2xl transition-transform duration-300 ease-out md:static md:z-auto md:w-[260px] md:translate-x-0 md:shadow-none"
        :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full'">

        <div class="flex items-center justify-between border-b border-slate-800/70 px-3 py-2">
            <img src="{{ asset('storage/img/dcl.png') }}" alt="Deurali Chemicals Logo"
                class="h-16 w-auto object-contain" />
            <button type="button" @click="mobileSidebarOpen = false"
                class="flex h-9 w-9 items-center justify-center rounded-md text-slate-400 transition-colors hover:bg-slate-800 hover:text-white md:hidden"
                aria-label="Close navigation">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <div class="px-4 py-2.5 border-b border-slate-800/40 bg-slate-900/40">
            <div class="flex items-center justify-between text-[11px]">
                <span class="text-slate-400 font-medium tracking-wider flex items-center gap-1.5 uppercase">
                    <span class="h-1.5 w-1.5 bg-emerald-500 rounded-full inline-block animate-pulse"></span>Kuleshwor,
                    KTM
                </span>
                <span class="text-slate-500 font-mono text-[10px]">LV-12</span>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto py-3 space-y-4 px-3"
            @click="if ($event.target.closest('a')) mobileSidebarOpen = false" x-data="{
        openDashboard: {{ request()->is('admin/dashboard*') || request()->routeIs('admin.dashboard') ? 'true' : 'false' }},
        openCategories: {{ request()->routeIs('admin.categories.*') ? 'true' : 'false' }},
        openCustomers: {{ request()->routeIs('admin.customers.*') ? 'true' : 'false' }},
        openProducts: {{ request()->routeIs('admin.products.*') ? 'true' : 'false' }},
        openInventory: {{ request()->is('admin/inventory*') ? 'true' : 'false' }},
        openBilling: {{ request()->is('admin/sales*') ? 'true' : 'false' }},
        openInvoices: {{ request()->is('admin/invoices*') ? 'true' : 'false' }},
        openWastage: {{ request()->is('admin/returns-wastage*') ? 'true' : 'false' }},
        openChequesMenu: {{ request()->is('admin/cheques*') ? 'true' : 'false' }},
        openBackupMenu: {{ request()->is('admin/backups*') ? 'true' : 'false' }},
        openAdminSection: {{ request()->is('admin/staff*') || request()->is('admin/roles*') || request()->is('admin/logs*') ? 'true' : 'false' }}
           }">

            <!-- Dashboard Section -->
            <div>
                <div class="text-[10px] font-bold text-slate-500 px-3 mb-2 tracking-widest uppercase">Dashboard Section
                </div>

                <div class="space-y-0.5">
                    <button @click="openDashboard = !openDashboard"
                        class="w-full flex items-center justify-between px-3 py-2 text-[13px] font-medium rounded-md transition-all {{ request()->is('admin/dashboard*') || request()->routeIs('admin.dashboard') ? 'text-white bg-brandDarkLight/40' : 'text-slate-400 hover:text-slate-200 hover:bg-brandDarkLight' }} outline-none">
                        <span class="flex items-center">
                            <i class="fa-solid fa-chart-pie mr-3 w-4 text-center text-sm text-slate-500"></i>Dashboards
                        </span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-500 transition-transform duration-200"
                            :class="openDashboard ? 'rotate-180 text-slate-300' : ''"></i>
                    </button>

                    <div x-show="openDashboard" x-cloak x-collapse
                        class="pl-4 space-y-0.5 border-l border-slate-800 ml-5 mt-0.5">
                        <a href="{{ route('admin.dashboard') }}"
                            class="flex items-center px-3 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.dashboard') ? 'text-blue-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                            <i class="fa-solid fa-cubes mr-2 text-[11px] text-slate-500 w-3 text-center"></i>Inventory
                            Dashboard
                        </a>
                        <a href="{{ route('admin.sales.dashboard') }}"
                            class="flex items-center px-3 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.sales.dashboard') ? 'text-blue-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                            <i class="fa-solid fa-chart-line mr-2 text-[11px] text-slate-500 w-3 text-center"></i>Sales
                            Dashboard
                        </a>
                        <a href="{{ route('admin.purchases.dashboard') }}"
                            class="flex items-center px-3 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.purchases.dashboard') ? 'text-blue-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                            <i
                                class="fa-solid fa-basket-shopping mr-2 text-[11px] text-slate-500 w-3 text-center"></i>Purchase
                            Dashboard
                        </a>
                    </div>
                </div>
            </div>

            <!-- Master Data Section -->
            <div>
                <div class="text-[10px] font-bold text-slate-500 px-3 mb-2 tracking-widest uppercase">Master Data</div>

                <div class="space-y-2">
                    <!-- Categories -->
                    <div class="space-y-0.5">
                        <button @click="openCategories = !openCategories"
                            class="w-full flex items-center justify-between px-3 py-2 text-[13px] font-medium rounded-md transition-all {{ request()->routeIs('admin.categories.*') ? 'text-white bg-brandDarkLight/40' : 'text-slate-400 hover:text-slate-200 hover:bg-brandDarkLight' }} outline-none">
                            <span class="flex items-center">
                                <i class="fa-solid fa-tags mr-3 w-4 text-center text-sm text-slate-500"></i>Categories
                            </span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-500 transition-transform duration-200"
                                :class="openCategories ? 'rotate-180 text-slate-300' : ''"></i>
                        </button>

                        <div x-show="openCategories" x-cloak x-collapse
                            class="pl-4 space-y-0.5 border-l border-slate-800 ml-5 mt-0.5">
                            <a href="{{ route('admin.categories.index') }}"
                                class="flex items-center px-3 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.categories.index') ? 'text-blue-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                                <i
                                    class="fa-solid fa-list-check mr-2 text-[11px] text-slate-500 w-3 text-center"></i>Manage
                                Categories
                            </a>
                        </div>
                    </div>

                    <!-- Products -->
                    <div class="space-y-0.5">
                        <button @click="openProducts = !openProducts"
                            class="w-full flex items-center justify-between px-3 py-2 text-[13px] font-medium rounded-md transition-all {{ request()->routeIs('admin.products.*') ? 'text-white bg-brandDarkLight/40' : 'text-slate-400 hover:text-slate-200 hover:bg-brandDarkLight' }} outline-none">
                            <span class="flex items-center">
                                <i
                                    class="fa-solid fa-boxes-stacked mr-3 w-4 text-center text-sm text-slate-500"></i>Products
                            </span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-500 transition-transform duration-200"
                                :class="openProducts ? 'rotate-180 text-slate-300' : ''"></i>
                        </button>

                        <div x-show="openProducts" x-cloak x-collapse
                            class="pl-4 space-y-0.5 border-l border-slate-800 ml-5 mt-0.5">
                            <a href="{{ route('admin.products.create') }}"
                                class="flex items-center px-3 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.products.create') ? 'text-blue-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                                <i class="fa-solid fa-plus mr-2 text-[11px] text-slate-500 w-3 text-center"></i>Add
                                Product
                            </a>
                            <a href="{{ route('admin.products.index') }}"
                                class="flex items-center px-3 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.products.index') ? 'text-blue-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                                <i class="fa-solid fa-eye mr-2 text-[11px] text-slate-500 w-3 text-center"></i>View
                                Products
                            </a>
                        </div>
                    </div>

                    <!-- Customers -->
                    <div class="space-y-0.5">
                        <button @click="openCustomers = !openCustomers"
                            class="w-full flex items-center justify-between px-3 py-2 text-[13px] font-medium rounded-md transition-all {{ request()->routeIs('admin.customers.*') ? 'text-white bg-brandDarkLight/40' : 'text-slate-400 hover:text-slate-200 hover:bg-brandDarkLight' }} outline-none">
                            <span class="flex items-center">
                                <i
                                    class="fa-solid fa-address-book mr-3 w-4 text-center text-sm text-slate-500"></i>Customers
                            </span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-500 transition-transform duration-200"
                                :class="openCustomers ? 'rotate-180 text-slate-300' : ''"></i>
                        </button>

                        <div x-show="openCustomers" x-cloak x-collapse
                            class="pl-4 space-y-0.5 border-l border-slate-800 ml-5 mt-0.5">
                            <a href="{{ route('admin.customers.create') }}"
                                class="flex items-center px-3 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.customers.create') ? 'text-blue-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                                <i class="fa-solid fa-user-plus mr-2 text-[11px] text-slate-500 w-3 text-center"></i>Add
                                Customer
                            </a>
                            <a href="{{ route('admin.customers.index') }}"
                                class="flex items-center px-3 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.customers.index') ? 'text-blue-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                                <i class="fa-solid fa-book mr-2 text-[11px] text-slate-500 w-3 text-center"></i>Customer
                                Ledger
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inventory & Purchasing Section -->
            <div>
                <div class="text-[10px] font-bold text-slate-500 px-3 mb-2 tracking-widest uppercase">Inventory &
                    Purchasing</div>

                <div class="space-y-2">
                    <!-- Inventory -->
                    <div class="space-y-0.5">
                        <button @click="openInventory = !openInventory"
                            class="w-full flex items-center justify-between px-3 py-2 text-[13px] font-medium rounded-md transition-all {{ request()->is('admin/inventory*') ? 'text-white bg-brandDarkLight/40' : 'text-slate-400 hover:text-slate-200 hover:bg-brandDarkLight' }} outline-none">
                            <span class="flex items-center">
                                <i
                                    class="fa-solid fa-warehouse mr-3 w-4 text-center text-sm text-slate-500"></i>Inventory
                            </span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-500 transition-transform duration-200"
                                :class="openInventory ? 'rotate-180 text-slate-300' : ''"></i>
                        </button>

                        <div x-show="openInventory" x-cloak x-collapse
                            class="pl-4 space-y-0.5 border-l border-slate-800 ml-5 mt-0.5">
                            <a href="{{ route('admin.inventory.add') }}"
                                class="flex items-center px-3 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->is('admin/inventory/add*') ? 'text-blue-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                                <i class="fa-solid fa-box mr-2 text-[11px] text-slate-500 w-3 text-center"></i>Add Stock
                            </a>
                            <a href="{{ route('admin.inventory.low_stock_manager') }}"
                                class="flex items-center px-3 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->is('admin/inventory/low-stock*') ? 'text-blue-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                                <i
                                    class="fa-solid fa-triangle-exclamation mr-2 text-[11px] text-orange-500 w-3 text-center"></i>Low
                                Stock
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales & Finance Section -->
            <div>
                <div class="text-[10px] font-bold text-slate-500 px-3 mb-2 tracking-widest uppercase">Sales & Finance
                </div>

                <div class="space-y-2">
                    <!-- Sales -->
                    <div class="space-y-0.5"
                        x-data="{ openBilling: {{ request()->is('admin/sales*') ? 'true' : 'false' }} }">
                        <button @click="openBilling = !openBilling"
                            class="w-full flex items-center justify-between px-3 py-2 text-[13px] font-medium rounded-md transition-all {{ request()->is('admin/sales*') ? 'text-white bg-brandDarkLight/40' : 'text-slate-400 hover:text-slate-200 hover:bg-brandDarkLight' }} outline-none">
                            <span class="flex items-center">
                                <i class="fa-solid fa-receipt mr-3 w-4 text-center text-sm text-slate-500"></i>Sales
                            </span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-500 transition-transform duration-200"
                                :class="openBilling ? 'rotate-180 text-slate-300' : ''"></i>
                        </button>

                        <div x-show="openBilling" x-cloak x-collapse
                            class="pl-4 space-y-0.5 border-l border-slate-800 ml-5 mt-0.5">
                            <a href="{{ route('admin.sales.create') }}"
                                class="flex items-center px-3 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.sales.create') ? 'text-blue-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                                <i
                                    class="fa-solid fa-cash-register mr-2 text-[11px] text-slate-500 w-3 text-center"></i>New
                                Sale (POS)
                            </a>
                            <a href="{{ route('admin.sales.index') }}"
                                class="flex items-center px-3 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.sales.index') ? 'text-blue-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                                <i class="fa-solid fa-list mr-2 text-[11px] text-slate-500 w-3 text-center"></i>Sales
                                Register
                            </a>
                            <!-- Added Link Below -->
                            <a href="{{ route('admin.sales.all') }}"
                                class="flex items-center px-3 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.sales.all') ? 'text-blue-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                                <i
                                    class="fa-solid fa-magnifying-glass mr-2 text-[11px] text-slate-500 w-3 text-center"></i>Find
                                All Sales
                            </a>
                        </div>
                    </div>

                    <!-- Invoices -->
                    <div class="space-y-0.5">
                        <button @click="openInvoices = !openInvoices"
                            class="w-full flex items-center justify-between px-3 py-2 text-[13px] font-medium rounded-md transition-all {{ request()->is('admin/invoices*') ? 'text-white bg-brandDarkLight/40' : 'text-slate-400 hover:text-slate-200 hover:bg-brandDarkLight' }} outline-none">
                            <span class="flex items-center">
                                <i
                                    class="fa-solid fa-file-invoice mr-3 w-4 text-center text-sm text-slate-500"></i>Invoices
                            </span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-500 transition-transform duration-200"
                                :class="openInvoices ? 'rotate-180 text-slate-300' : ''"></i>
                        </button>

                        <div x-show="openInvoices" x-cloak x-collapse
                            class="pl-4 space-y-0.5 border-l border-slate-800 ml-5 mt-0.5">
                            <a href="{{ route('admin.invoices.index') }}"
                                class="flex items-center px-3 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.invoices.index') ? 'text-blue-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                                <i class="fa-solid fa-list mr-2 text-[11px] text-slate-500 w-3 text-center"></i>Invoice
                                Ledger
                            </a>
                        </div>
                    </div>

                    <!-- Returns -->
                    <div class="space-y-0.5">
                        <button @click="openWastage = !openWastage" type="button"
                            class="w-full flex items-center justify-between px-3 py-2 text-[13px] font-medium rounded-md transition-all {{ request()->is('admin/returns-wastage*') ? 'text-white bg-brandDarkLight/40' : 'text-slate-400 hover:text-slate-200 hover:bg-brandDarkLight' }} outline-none">
                            <span class="flex items-center">
                                <i
                                    class="fa-solid fa-dumpster-fire mr-3 w-4 text-center text-sm {{ request()->is('admin/returns-wastage*') ? 'text-white' : 'text-slate-500' }}"></i>Returns
                            </span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-500 transition-transform duration-200"
                                :class="openWastage ? 'rotate-180 text-slate-300' : ''"></i>
                        </button>

                        <div x-show="openWastage" x-cloak x-collapse
                            class="pl-4 space-y-0.5 border-l border-slate-800 ml-5 mt-0.5">
                            <a href="{{ route('admin.wastage.create') }}"
                                class="flex items-center px-3 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.wastage.create') ? 'text-blue-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                                <i class="fa-solid fa-reply mr-2 text-[11px] text-slate-500 w-3 text-center"></i>Add
                                Return
                            </a>
                            <a href="{{ route('admin.wastage.index') }}"
                                class="flex items-center px-3 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.wastage.index') ? 'text-blue-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                                <i
                                    class="fa-solid fa-folder-open mr-2 text-[11px] text-slate-500 w-3 text-center"></i>Return
                                Ledger
                            </a>
                        </div>
                    </div>

                    <!-- Cheques -->
                    <div class="space-y-0.5">
                        <button @click="openChequesMenu = !openChequesMenu"
                            class="w-full flex items-center justify-between px-3 py-2 text-[13px] font-medium rounded-md transition-all {{ request()->is('admin/cheques*') ? 'text-white bg-brandDarkLight/40' : 'text-slate-400 hover:text-slate-200 hover:bg-brandDarkLight' }} outline-none">
                            <span class="flex items-center">
                                <i
                                    class="fa-solid fa-money-check-dollar mr-3 w-4 text-center text-sm text-slate-500"></i>Cheques
                            </span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-500 transition-transform duration-200"
                                :class="openChequesMenu ? 'rotate-180 text-slate-300' : ''"></i>
                        </button>

                        <div x-show="openChequesMenu" x-cloak x-collapse
                            class="pl-4 space-y-0.5 border-l border-slate-800 ml-5 mt-0.5">
                            <a href="{{ route('admin.cheques.create') }}"
                                class="flex items-center px-3 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.cheques.create') ? 'text-blue-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                                <i
                                    class="fa-solid fa-pen-to-square mr-2 text-[11px] text-slate-500 w-3 text-center"></i>Record
                                Cheque
                            </a>
                            <a href="{{ route('admin.cheques.index') }}"
                                class="flex items-center px-3 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.cheques.index') ? 'text-blue-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                                <i class="fa-solid fa-vault mr-2 text-[11px] text-slate-500 w-3 text-center"></i>Cheque
                                Ledger
                            </a>
                        </div>
                    </div>
                    <!-- Release Notes Link -->
                    <div class="space-y-0.5 mt-4">
                        <a href="{{ route('admin.release-notes.index') }}"
                            class="flex items-center px-3 py-2 text-[13px] font-medium rounded-md transition-all {{ request()->routeIs('admin.release-notes.*') ? 'text-white bg-brandDarkLight/40' : 'text-slate-400 hover:text-slate-200 hover:bg-brandDarkLight' }}">
                            <i class="fa-solid fa-code-branch mr-3 w-4 text-center text-sm text-slate-500"></i>
                            Release Notes
                        </a>
                    </div>
                </div>
            </div>

            <!-- Admin Only -->
            @if(auth()->check() && auth()->user()->role === 'admin')
            <div class="pt-4 mt-2 border-t border-slate-800/60"
                x-data="{ openBackupMenu: {{ request()->is('admin/backups*') ? 'true' : 'false' }}, openAdminSection: {{ request()->is('admin/staff*') || request()->is('admin/roles*') || request()->is('admin/logs*') ? 'true' : 'false' }} }">
                <div
                    class="flex items-center gap-2 px-3 mb-2 text-[10px] font-bold text-amber-500 tracking-widest uppercase">
                    <i class="fa-solid fa-bolt text-[9px]"></i>
                    Admin Only
                </div>

                <!-- Backups -->
                <div class="space-y-0.5">
                    <a href="{{ route('admin.backups.index') }}"
                        class="flex items-center px-3 py-2 text-[13px] font-medium rounded-md transition-all {{ request()->is('admin/backups*') ? 'text-white bg-brandDarkLight/40' : 'text-slate-400 hover:text-slate-200 hover:bg-brandDarkLight' }}">
                        <i class="fa-solid fa-cloud-arrow-down mr-3 w-4 text-center text-sm text-slate-500"></i>
                        Backup & Restore
                    </a>
                </div>

                <!-- User Controls -->
                <div class="space-y-0.5">
                    <button @click="openAdminSection = !openAdminSection"
                        class="w-full flex items-center justify-between px-3 py-2 text-[13px] font-medium rounded-md transition-all {{ request()->is('admin/staff*') || request()->is('admin/roles*') || request()->is('admin/logs*') ? 'text-white bg-brandDarkLight/40' : 'text-slate-400 hover:text-slate-200 hover:bg-brandDarkLight' }} outline-none">
                        <span class="flex items-center">
                            <i class="fa-solid fa-user-shield mr-3 w-4 text-center text-sm text-slate-500"></i>User
                            Controls
                        </span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-500 transition-transform duration-200"
                            :class="openAdminSection ? 'rotate-180 text-slate-300' : ''"></i>
                    </button>

                    <div x-show="openAdminSection" x-cloak x-collapse
                        class="pl-4 space-y-0.5 border-l border-slate-800 ml-5 mt-0.5">
                        <a href="{{ route('admin.staff.index') }}"
                            class="flex items-center px-3 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.staff.*') ? 'text-blue-400 font-semibold bg-slate-800/20' : 'text-slate-400 hover:text-slate-200' }}">
                            <i class="fa-solid fa-users-gear mr-2 text-[11px] text-slate-500 w-3 text-center"></i>Staff
                            Directory
                        </a>
                        <a href="{{ route('admin.roles.index') }}"
                            class="flex items-center px-3 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.roles.*') ? 'text-blue-400 font-semibold bg-slate-800/20' : 'text-slate-400 hover:text-slate-200' }}">
                            <i class="fa-solid fa-key mr-2 text-[11px] text-slate-500 w-3 text-center"></i>Roles &
                            Permissions
                        </a>
                        <a href="{{ route('admin.logs.index') }}"
                            class="flex items-center px-3 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.logs.*') ? 'text-blue-400 font-semibold bg-slate-800/20' : 'text-slate-400 hover:text-slate-200' }}">
                            <i
                                class="fa-solid fa-clock-rotate-left mr-2 text-[11px] text-slate-500 w-3 text-center"></i>Activity
                            Logs
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </nav>

        <div class="border-t border-slate-800/60 bg-slate-950/40 p-3">
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="mb-3 flex w-full items-center justify-center rounded-md border border-red-900/30 bg-red-950/20 px-3 py-2 text-[13px] font-medium text-red-300 transition-all hover:border-red-800/60 hover:bg-red-900/30 hover:text-red-200">
                    <i class="fa-solid fa-right-from-bracket mr-2 text-sm"></i>
                    Logout
                </button>
            </form>
            <div class="text-center font-mono text-[11px] tracking-wider text-slate-500">
                SYSTEM SECURE
            </div>
        </div>
    </aside>

    <div class="flex-1 flex min-w-0 flex-col h-full overflow-hidden">

        <div
            class="bg-white/95 border-b border-slate-200 min-h-16 px-3 md:px-6 flex items-center justify-between gap-3 shrink-0 z-10 shadow-sm backdrop-blur">
            <div class="flex min-w-0 items-center gap-3">
                <button type="button" @click="mobileSidebarOpen = true"
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-md border border-slate-200 bg-white text-slate-600 shadow-sm transition-colors hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700 md:hidden"
                    aria-label="Open navigation">
                    <i class="fa-solid fa-bars text-sm"></i>
                </button>
                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-700 ring-1 ring-blue-100">
                    <i class="fa-solid fa-chart-line text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="truncate text-[10px] font-bold uppercase tracking-widest text-slate-400">Control Center
                    </p>
                    <h2 class="truncate text-sm md:text-base font-bold text-slate-900">
                        @yield('panel_title', 'Warehouse Analytical Panel')
                    </h2>
                </div>
            </div>

            <div class="flex shrink-0 items-center gap-2 md:gap-4">
                <div class="relative" x-data="{
                        openNotification: false,
                        readItems: $persist([]),
                        markAsRead(id) {
                            if (!this.readItems.includes(id)) {
                                this.readItems.push(id);
                            }
                        }
                    }">
                    <button @click="openNotification = !openNotification"
                        class="relative flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 shadow-sm transition-all hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700">
                        <i class="fa-solid fa-bell text-sm"></i>

                        @if($notificationCount > 0)
                        <span x-show="readItems.size < {{ $notificationCount }}"
                            class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1 text-[9px] font-bold text-white shadow-sm ring-2 ring-white">
                            {{ $notificationCount - count(array_intersect(array_merge($notifications['lowStock']->pluck('id')->map(fn($id) => 'stock-'.$id)->all(), $notifications['cheques']->pluck('id')->map(fn($id) => 'cheque-'.$id)->all()), [])) }}
                        </span>
                        @endif
                    </button>

                    <div x-show="openNotification" @click.away="openNotification = false" x-cloak
                        class="fixed left-3 right-3 top-16 z-50 max-h-[calc(100dvh-5rem)] overflow-hidden rounded-xl border border-slate-200/60 bg-white shadow-xl transition-all md:absolute md:left-auto md:right-0 md:top-auto md:mt-3 md:w-80 md:origin-top-right">

                        <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50 px-4 py-3">
                            <div>
                                <h3 class="text-[12px] font-bold text-slate-800">Notification Panel</h3>
                                <p class="text-[10px] font-medium text-slate-400">{{ $notificationCount }} active system
                                    alerts</p>
                            </div>
                            <button @click="readItems = [
                                ...@json($notifications['lowStock']->pluck('id')->map(fn($id) => 'stock-'.$id)),
                                ...@json($notifications['cheques']->pluck('id')->map(fn($id) => 'cheque-'.$id))
                                ];"
                                class="rounded-md bg-white px-2 py-1 text-[10px] font-bold uppercase text-blue-600 ring-1 ring-slate-200 hover:text-blue-800">Mark
                                all
                                read</button>
                        </div>

                        <div class="max-h-80 overflow-y-auto bg-slate-50/50">
                            @if($notificationCount > 0)
                            @foreach($notifications['lowStock'] as $index => $product)
                            <a href="{{ route('admin.products.index') }}" @click="markAsRead('stock-{{$product->id}}')"
                                class="block px-4 py-3 border-b border-slate-100 transition-all hover:bg-slate-50">
                                <div class="flex items-start gap-3"
                                    :class="readItems.includes('stock-{{$product->id}}') ? 'opacity-50' : ''">
                                    <div class="mt-0.5 bg-red-100 p-1.5 rounded-md text-red-600"><i
                                            class="fa-solid fa-box-open text-xs"></i></div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-800">Low Stock Alert</p>
                                        <p class="text-[11px] text-slate-500 mt-0.5">{{ $product->name }} (Qty:
                                            {{(float)$product->initial_stock}})</p>
                                    </div>
                                </div>
                            </a>
                            @endforeach

                            @foreach($notifications['cheques'] as $index => $cheque)
                            <a href="{{ route('admin.cheques.index') }}" @click="markAsRead('cheque-{{$cheque->id}}')"
                                class="block px-4 py-3 border-b border-slate-100 transition-all hover:bg-slate-50">
                                <div class="flex items-start gap-3"
                                    :class="readItems.includes('cheque-{{$cheque->id}}') ? 'opacity-50' : ''">
                                    <div class="mt-0.5 bg-blue-100 p-1.5 rounded-md text-blue-600"><i
                                            class="fa-solid fa-money-check text-xs"></i></div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-800">Cheque Due Today</p>
                                        <p class="text-[11px] text-slate-500 mt-0.5">Ref:
                                            {{ $cheque->cheque_no ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </a>
                            @endforeach
                            @else
                            <div class="px-6 py-8 text-center text-slate-400">
                                <i class="fa-solid fa-check-double text-lg mb-1"></i>
                                <p class="text-xs">All caught up!</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- User Dropdown Menu -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 hover:bg-blue-200 focus:outline-none transition-colors border border-blue-200">
                        <i class="fa-solid fa-user text-blue-700 text-lg"></i>
                    </button>

                    <div x-show="open" @click.away="open = false" x-transition
                        class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-100 py-1 z-[9999]">

                        <div class="px-4 py-2 text-xs text-gray-500 uppercase tracking-wider font-semibold border-b">
                            {{ auth()->user()->role ?? 'User' }}
                        </div>

                        <a href="{{ route('admin.profile.edit') }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50">Profile</a>
                        <a href="{{ route('admin.profile.change') }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50">Change Password</a>
                        <a href="{{ route('admin.user-guide') }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50">User Guide</a>

                        <div class="border-t mt-1">
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <button type="submit"
                                    class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <header class="shrink-0 border-b border-slate-200 bg-white shadow-sm">
            <div class="flex min-h-9 items-end gap-1 overflow-x-auto border-b border-slate-100 px-3 pt-1">
                <a href="{{ route('admin.dashboard') }}"
                    class="rounded-t-md border border-b-0 px-4 py-2 text-[12px] font-semibold transition-colors {{ request()->routeIs('admin.dashboard') ? 'border-slate-200 bg-white text-blue-700' : 'border-transparent text-slate-500 hover:bg-slate-50 hover:text-slate-800' }}">
                    Home
                </a>
                <a href="{{ route('admin.sales.index') }}"
                    class="rounded-t-md border border-b-0 px-4 py-2 text-[12px] font-semibold transition-colors {{ request()->is('admin/sales*') || request()->is('admin/invoices*') || request()->is('admin/purchases*') ? 'border-slate-200 bg-white text-blue-700' : 'border-transparent text-slate-500 hover:bg-slate-50 hover:text-slate-800' }}">
                    Transactions
                </a>
                <a href="{{ route('admin.reports.index') }}"
                    class="rounded-t-md border border-b-0 px-4 py-2 text-[12px] font-semibold transition-colors {{ request()->is('admin/reports*') ? 'border-slate-200 bg-white text-blue-700' : 'border-transparent text-slate-500 hover:bg-slate-50 hover:text-slate-800' }}">
                    Reports
                </a>
                @if(auth()->check() && auth()->user()->role === 'admin')
                <a href="{{ route('admin.backups.index') }}"
                    class="rounded-t-md border border-b-0 px-4 py-2 text-[12px] font-semibold transition-colors {{ request()->is('admin/backups*') || request()->is('admin/staff*') || request()->is('admin/roles*') || request()->is('admin/logs*') ? 'border-slate-200 bg-white text-blue-700' : 'border-transparent text-slate-500 hover:bg-slate-50 hover:text-slate-800' }}">
                    Tools
                </a>
                @endif
            </div>

            <div class="flex items-stretch gap-2 overflow-x-auto overscroll-x-contain px-3 py-2">
                <div class="flex min-w-max flex-col justify-between gap-1 border-r border-slate-200 pr-3">
                    <div class="flex gap-1">
                        <a href="{{ route('admin.sales.create') }}"
                            class="flex h-14 w-16 flex-col items-center justify-center gap-1 rounded-md text-slate-600 transition-colors hover:bg-blue-50 hover:text-blue-700">
                            <i class="fa-solid fa-file-invoice text-lg"></i>
                            <span class="text-[10px] font-medium leading-none">

                            </span>
                        </a>
                        <a href="{{ route('admin.purchases.create') }}"
                            class="flex h-14 w-16 flex-col items-center justify-center gap-1 rounded-md text-slate-600 transition-colors hover:bg-blue-50 hover:text-blue-700">
                            <i class="fa-solid fa-cart-flatbed text-lg"></i>
                            <span class="text-[10px] font-medium leading-none">Purchase</span>
                        </a>
                        <a href="{{ route('admin.invoices.create') }}"
                            class="flex h-14 w-16 flex-col items-center justify-center gap-1 rounded-md text-slate-600 transition-colors hover:bg-blue-50 hover:text-blue-700">
                            <i class="fa-solid fa-pen-to-square text-lg"></i>
                            <span class="text-[10px] font-medium leading-none">Invoice</span>
                        </a>
                    </div>
                    <span class="text-center text-[9px] font-bold uppercase text-slate-400">Quick Create</span>
                </div>

                <div class="flex min-w-max flex-col justify-between gap-1 border-r border-slate-200 pr-3">
                    <div class="flex gap-1">
                        <a href="{{ route('admin.customers.create') }}"
                            class="flex h-14 w-16 flex-col items-center justify-center gap-1 rounded-md text-slate-600 transition-colors hover:bg-blue-50 hover:text-blue-700">
                            <i class="fa-solid fa-user-plus text-lg"></i>
                            <span class="text-[10px] font-medium leading-none">Add Party</span>
                        </a>
                        <a href="{{ route('admin.products.create') }}"
                            class="flex h-14 w-16 flex-col items-center justify-center gap-1 rounded-md text-slate-600 transition-colors hover:bg-blue-50 hover:text-blue-700">
                            <i class="fa-solid fa-cart-shopping text-lg"></i>
                            <span class="text-[10px] font-medium leading-none">Add Item</span>
                        </a>
                        <a href="{{ route('admin.inventory.add') }}"
                            class="flex h-14 w-16 flex-col items-center justify-center gap-1 rounded-md text-slate-600 transition-colors hover:bg-blue-50 hover:text-blue-700">
                            <i class="fa-solid fa-box text-lg"></i>
                            <span class="text-[10px] font-medium leading-none">Add Stock</span>
                        </a>
                    </div>
                    <span class="text-center text-[9px] font-bold uppercase text-slate-400">Masters</span>
                </div>

                <div class="flex min-w-max flex-col justify-between gap-1 border-r border-slate-200 pr-3">
                    <div class="flex gap-1">
                        <a href="{{ route('admin.reports.index') }}"
                            class="flex h-14 w-16 flex-col items-center justify-center gap-1 rounded-md text-slate-600 transition-colors hover:bg-blue-50 hover:text-blue-700">
                            <i class="fa-solid fa-chart-simple text-lg"></i>
                            <span class="text-[10px] font-medium leading-none">Reports</span>
                        </a>
                        <a href="{{ route('admin.sales.index') }}"
                            class="flex h-14 w-16 flex-col items-center justify-center gap-1 rounded-md text-slate-600 transition-colors hover:bg-blue-50 hover:text-blue-700">
                            <i class="fa-solid fa-print text-lg"></i>
                            <span class="text-[10px] font-medium leading-none">Print</span>
                        </a>
                        <a href="{{ route('admin.invoices.index') }}"
                            class="flex h-14 w-16 flex-col items-center justify-center gap-1 rounded-md text-slate-600 transition-colors hover:bg-blue-50 hover:text-blue-700">
                            <i class="fa-solid fa-file-export text-lg"></i>
                            <span class="text-[10px] font-medium leading-none">Export</span>
                        </a>
                    </div>
                    <span class="text-center text-[9px] font-bold uppercase text-slate-400">Output</span>
                </div>

                <div class="flex min-w-max flex-col justify-between gap-1 border-r border-slate-200 pr-3">
                    <div class="flex gap-1">
                        @if(auth()->check() && auth()->user()->role === 'admin')
                        <a href="{{ route('admin.backups.index') }}"
                            class="flex h-14 w-16 flex-col items-center justify-center gap-1 rounded-md text-slate-600 transition-colors hover:bg-blue-50 hover:text-blue-700">
                            <i class="fa-solid fa-cloud-arrow-down text-lg"></i>
                            <span class="text-[10px] font-medium leading-none">Backup</span>
                        </a>
                        @endif
                        <a href="{{ route('admin.wastage.index') }}"
                            class="flex h-14 w-16 flex-col items-center justify-center gap-1 rounded-md text-slate-600 transition-colors hover:bg-blue-50 hover:text-blue-700">
                            <i class="fa-solid fa-reply text-lg"></i>
                            <span class="text-[10px] font-medium leading-none">Returns</span>
                        </a>
                        <a href="{{ route('admin.inventory.low_stock_manager') }}"
                            class="flex h-14 w-16 flex-col items-center justify-center gap-1 rounded-md text-slate-600 transition-colors hover:bg-orange-50 hover:text-orange-600">
                            <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                            <span class="text-[10px] font-medium leading-none">Low Stock</span>
                        </a>
                    </div>
                    <span class="text-center text-[9px] font-bold uppercase text-slate-400">Tools</span>
                </div>

                <div class="flex min-w-max flex-col justify-between gap-1 pr-3">
                    <div class="flex gap-1">
                        <a href="{{ route('admin.cheques.create') }}"
                            class="flex h-14 w-16 flex-col items-center justify-center gap-1 rounded-md text-slate-600 transition-colors hover:bg-blue-50 hover:text-blue-700">
                            <i class="fa-solid fa-money-check-dollar text-lg"></i>
                            <span class="text-[10px] font-medium leading-none">Cheque</span>
                        </a>
                        <a href="{{ route('admin.cheques.index') }}"
                            class="flex h-14 w-16 flex-col items-center justify-center gap-1 rounded-md text-slate-600 transition-colors hover:bg-blue-50 hover:text-blue-700">
                            <i class="fa-solid fa-vault text-lg"></i>
                            <span class="text-[10px] font-medium leading-none">Cheque Log</span>
                        </a>
                    </div>
                    <span class="text-center text-[9px] font-bold uppercase text-slate-400">Payment</span>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto bg-slate-50 p-3 sm:p-4 md:p-6">
            <div class="mx-auto max-w-7xl space-y-6">

                @yield('content')

            </div>
        </main>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/persist@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>

</html>