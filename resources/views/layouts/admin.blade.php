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
                    /* Sleek slate-900 baseline */
                    brandDarkLight: '#1e293b',
                    /* Slate-800 subtle hover */
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

<body class="bg-[#f8fafc] text-[#1e293b] font-sans h-screen flex overflow-hidden antialiased">

    @if(View::exists('partials.alerts'))
    @include('partials.alerts')
    @endif

    <aside
        class="w-[260px] bg-brandDark text-slate-300 flex flex-col h-full shrink-0 select-none hidden md:flex border-r border-slate-800">

        <div class="p-4 flex items-center gap-3 border-b border-slate-800/70">
            <div
                class="bg-blue-600 text-white font-bold h-9 w-9 rounded-lg flex items-center justify-center text-base shadow-lg shadow-blue-600/20 shrink-0">
                D
            </div>
            <div class="overflow-hidden">
                <h1 class="text-[11px] font-bold tracking-wider text-blue-400 uppercase leading-none mb-0.5 truncate">
                    Deurali Chemicals
                </h1>
                <p class="text-xs font-semibold text-white tracking-tight truncate">Inventory Terminal</p>
            </div>
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

        <nav class="flex-1 overflow-y-auto py-3 space-y-1 px-3" x-data="{ 
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

            <div class="text-[10px] font-bold text-slate-500 px-3 mb-2 tracking-widest uppercase">Main Operations</div>

            <!-- Dashboards Dropdown -->
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
                    <a href="#"
                        class="flex items-center px-3 py-1.5 text-[12px] font-medium rounded-md transition-all text-slate-400 hover:text-slate-200">
                        <i
                            class="fa-solid fa-basket-shopping mr-2 text-[11px] text-slate-500 w-3 text-center"></i>Purchase
                        Dashboard
                    </a>
                </div>
            </div>

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
                        <i class="fa-solid fa-list-check mr-2 text-[11px] text-slate-500 w-3 text-center"></i>Manage
                        Categories
                    </a>
                </div>
            </div>

            <!-- Products -->
            <div class="space-y-0.5">
                <button @click="openProducts = !openProducts"
                    class="w-full flex items-center justify-between px-3 py-2 text-[13px] font-medium rounded-md transition-all {{ request()->routeIs('admin.products.*') ? 'text-white bg-brandDarkLight/40' : 'text-slate-400 hover:text-slate-200 hover:bg-brandDarkLight' }} outline-none">
                    <span class="flex items-center">
                        <i class="fa-solid fa-boxes-stacked mr-3 w-4 text-center text-sm text-slate-500"></i>Products
                    </span>
                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-500 transition-transform duration-200"
                        :class="openProducts ? 'rotate-180 text-slate-300' : ''"></i>
                </button>
                <div x-show="openProducts" x-cloak x-collapse
                    class="pl-4 space-y-0.5 border-l border-slate-800 ml-5 mt-0.5">
                    <a href="{{ route('admin.products.index') }}"
                        class="flex items-center px-3 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.products.index') ? 'text-blue-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                        <i class="fa-solid fa-eye mr-2 text-[11px] text-slate-500 w-3 text-center"></i>View Products
                    </a>
                    <a href="{{ route('admin.products.create') }}"
                        class="flex items-center px-3 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.products.create') ? 'text-blue-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                        <i class="fa-solid fa-plus mr-2 text-[11px] text-slate-500 w-3 text-center"></i>Add Product
                    </a>
                </div>
            </div>

            <!-- Inventory -->
            <div class="space-y-0.5">
                <button @click="openInventory = !openInventory"
                    class="w-full flex items-center justify-between px-3 py-2 text-[13px] font-medium rounded-md transition-all {{ request()->is('admin/inventory*') ? 'text-white bg-brandDarkLight/40' : 'text-slate-400 hover:text-slate-200 hover:bg-brandDarkLight' }} outline-none">
                    <span class="flex items-center">
                        <i class="fa-solid fa-warehouse mr-3 w-4 text-center text-sm text-slate-500"></i>Inventory
                    </span>
                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-500 transition-transform duration-200"
                        :class="openInventory ? 'rotate-180 text-slate-300' : ''"></i>
                </button>
                <div x-show="openInventory" x-cloak x-collapse
                    class="pl-4 space-y-0.5 border-l border-slate-800 ml-5 mt-0.5">
                    <a href="{{ route('admin.products.index') }}"
                        class="flex items-center px-3 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->is('admin/inventory/logs*') ? 'text-blue-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                        <i class="fa-solid fa-clipboard-list mr-2 text-[11px] text-slate-500 w-3 text-center"></i>Stock
                        Logs
                    </a>
                    <a href="{{ route('admin.products.index') }}"
                        class="flex items-center px-3 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->is('admin/inventory/adjustments*') ? 'text-blue-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                        <i class="fa-solid fa-sliders mr-2 text-[11px] text-slate-500 w-3 text-center"></i>Adjustments
                    </a>
                </div>
            </div>

            <!-- Customers -->
            <div class="space-y-0.5">
                <button @click="openCustomers = !openCustomers"
                    class="w-full flex items-center justify-between px-3 py-2 text-[13px] font-medium rounded-md transition-all {{ request()->routeIs('admin.customers.*') ? 'text-white bg-brandDarkLight/40' : 'text-slate-400 hover:text-slate-200 hover:bg-brandDarkLight' }} outline-none">
                    <span class="flex items-center">
                        <i class="fa-solid fa-address-book mr-3 w-4 text-center text-sm text-slate-500"></i>Customers
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
                        <i class="fa-solid fa-book mr-2 text-[11px] text-slate-500 w-3 text-center"></i>Customer Ledger
                    </a>
                </div>
            </div>

            <div class="space-y-0.5" x-data="{ openBilling: {{ request()->is('admin/sales*') ? 'true' : 'false' }} }">
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
                    <a href="{{ route('admin.sales.dashboard') }}"
                        class="flex items-center px-3 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.sales.dashboard') ? 'text-blue-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                        <i class="fa-solid fa-chart-pie mr-2 text-[11px] text-slate-500 w-3 text-center"></i>Sales
                        Dashboard
                    </a>

                    <a href="{{ route('admin.sales.create') }}"
                        class="flex items-center px-3 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.sales.create') ? 'text-blue-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                        <i class="fa-solid fa-cash-register mr-2 text-[11px] text-slate-500 w-3 text-center"></i>New
                        Sale (POS)
                    </a>

                    <a href="{{ route('admin.sales.index') }}"
                        class="flex items-center px-3 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.sales.index') ? 'text-blue-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                        <i class="fa-solid fa-list mr-2 text-[11px] text-slate-500 w-3 text-center"></i>Sales Register
                    </a>
                </div>
            </div>

            <!-- Invoices -->
            <div class="space-y-0.5">
                <button @click="openInvoices = !openInvoices"
                    class="w-full flex items-center justify-between px-3 py-2 text-[13px] font-medium rounded-md transition-all {{ request()->is('admin/invoices*') ? 'text-white bg-brandDarkLight/40' : 'text-slate-400 hover:text-slate-200 hover:bg-brandDarkLight' }} outline-none">
                    <span class="flex items-center">
                        <i
                            class="fa-solid fa-file-invoice-dollar mr-3 w-4 text-center text-sm text-slate-500"></i>Invoices
                    </span>
                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-500 transition-transform duration-200"
                        :class="openInvoices ? 'rotate-180 text-slate-300' : ''"></i>
                </button>
                <div x-show="openInvoices" x-cloak x-collapse
                    class="pl-4 space-y-0.5 border-l border-slate-800 ml-5 mt-0.5">
                    <a href="{{ route('admin.invoices.create') }}"
                        class="flex items-center px-3 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.invoices.create') ? 'text-blue-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                        <i class="fa-solid fa-file-circle-plus mr-2 text-[11px] text-slate-500 w-3 text-center"></i>New
                        Invoice
                    </a>
                    <a href="{{ route('admin.invoices.index') }}"
                        class="flex items-center px-3 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.invoices.index') ? 'text-blue-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                        <i class="fa-solid fa-history mr-2 text-[11px] text-slate-500 w-3 text-center"></i>History Log
                    </a>
                </div>
            </div>

            <!-- Returns & Spoils -->
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
                        <i class="fa-solid fa-reply mr-2 text-[11px] text-slate-500 w-3 text-center"></i>Add Return
                    </a>
                    <a href="{{ route('admin.wastage.index') }}"
                        class="flex items-center px-3 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.wastage.index') ? 'text-blue-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                        <i class="fa-solid fa-folder-open mr-2 text-[11px] text-slate-500 w-3 text-center"></i>Return
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
                        <i class="fa-solid fa-pen-to-square mr-2 text-[11px] text-slate-500 w-3 text-center"></i>Record
                        Cheque
                    </a>
                    <a href="{{ route('admin.cheques.index') }}"
                        class="flex items-center px-3 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.cheques.index') ? 'text-blue-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                        <i class="fa-solid fa-vault mr-2 text-[11px] text-slate-500 w-3 text-center"></i>Cheque Ledger
                    </a>
                </div>
            </div>

            <!-- Backups -->
            <div class="space-y-0.5">
                <button @click="openBackupMenu = !openBackupMenu"
                    class="w-full flex items-center justify-between px-3 py-2 text-[13px] font-medium rounded-md transition-all {{ request()->is('admin/backups*') ? 'text-white bg-brandDarkLight/40' : 'text-slate-400 hover:text-slate-200 hover:bg-brandDarkLight' }} outline-none">
                    <span class="flex items-center">
                        <i class="fa-solid fa-folder-open mr-3 w-4 text-center text-sm text-slate-500"></i>Backups
                    </span>
                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-500 transition-transform duration-200"
                        :class="openBackupMenu ? 'rotate-180 text-slate-300' : ''"></i>
                </button>

                <div x-show="openBackupMenu" x-cloak x-collapse
                    class="pl-4 pb-2 space-y-2 border-l border-slate-800 ml-5 mt-1">
                    <form action="{{ route('admin.backups.store') }}" method="POST" class="px-2 pt-1">
                        @csrf
                        <div class="flex items-center gap-1">
                            <select name="backup_scope"
                                class="bg-slate-900 border border-slate-700 text-slate-300 text-[11px] rounded p-1 w-full focus:outline-none focus:border-blue-500">
                                <option value="all">Full Backup</option>
                                <option value="database">DB Only</option>
                                <option value="files">Files Only</option>
                            </select>
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-500 text-white px-2 py-1 rounded text-[11px] font-medium transition-colors">
                                Run
                            </button>
                        </div>
                    </form>
                    <div class="h-px bg-slate-800/60 mx-2"></div>
                    <a href="{{ route('admin.backups.index') }}"
                        class="flex items-center px-2 py-1 text-[12px] transition-all {{ request()->routeIs('admin.backups.index') ? 'text-blue-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                        <i class="fa-solid fa-cloud-arrow-down mr-2 text-[10px] text-slate-500 w-3 text-center"></i>
                        History Log
                    </a>
                </div>
            </div>

            <div class="pt-4 mt-2 border-t border-slate-800/60"
                x-data="{ openAdminSection: {{ request()->is('admin/staff*') || request()->is('admin/roles*') || request()->is('admin/logs*') ? 'true' : 'false' }} }">

                <div class="text-[10px] font-bold text-slate-500 px-3 mb-2 tracking-widest uppercase">Admin Settings
                </div>

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
        </nav>

        <div
            class="p-3 border-t border-slate-800/60 text-[11px] text-slate-500 bg-slate-950/40 text-center font-mono tracking-wider">
            SYSTEM SECURE
        </div>
    </aside>

    <div class="flex-1 flex flex-col h-full overflow-hidden">

        <div
            class="bg-white border-b border-slate-200 h-14 px-4 md:px-6 flex items-center justify-between shrink-0 z-10">
            <div class="flex items-center gap-3">
                <span class="w-1 h-4 bg-blue-600 rounded-sm"></span>
                <h2 class="text-xs md:text-sm font-bold tracking-wider uppercase text-slate-800">
                    @yield('panel_title', 'Warehouse Analytical Panel')
                </h2>
            </div>

            <div class="flex items-center gap-4">
                <div class="relative" x-data="{ 
                    openNotification: false,
                    readItems: new Set(),
                    markAsRead(id) { this.readItems.add(id); } 
                }">
                    <button @click="openNotification = !openNotification"
                        class="relative text-slate-500 hover:text-blue-600 transition-all p-2 rounded-full hover:bg-slate-100">
                        <i class="fa-solid fa-bell text-sm"></i>

                        @if($notificationCount > 0)
                        <span x-show="readItems.size < {{ $notificationCount }}"
                            class="absolute top-0 right-0 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[9px] font-bold text-white shadow-sm ring-2 ring-white">
                            {{ $notificationCount }}
                        </span>
                        @endif
                    </button>

                    <div x-show="openNotification" @click.away="openNotification = false" x-cloak
                        class="absolute right-0 mt-3 w-80 bg-white rounded-xl shadow-xl border border-slate-200/60 z-50 overflow-hidden transform origin-top-right transition-all">

                        <div class="px-4 py-3 border-b border-slate-100 bg-white flex justify-between items-center">
                            <h3 class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Notifications
                            </h3>
                            <button @click="readItems = new Set([...Array({{ $notificationCount }}).keys()])"
                                class="text-[10px] text-blue-600 hover:text-blue-800 font-bold uppercase">Mark all
                                read</button>
                        </div>

                        <div class="max-h-80 overflow-y-auto bg-slate-50/50">
                            @if($notificationCount > 0)
                            @foreach($notifications['lowStock'] as $index => $product)
                            <a href="{{ route('admin.products.index') }}" @click="markAsRead('stock-{{$index}}')"
                                :class="readItems.has('stock-{{$index}}') ? 'opacity-50 bg-slate-100' : 'bg-white'"
                                class="block px-4 py-3 border-b border-slate-100 transition-all hover:bg-slate-50">
                                <div class="flex items-start gap-3">
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
                            <a href="{{ route('admin.cheques.index') }}" @click="markAsRead('cheque-{{$index}}')"
                                :class="readItems.has('cheque-{{$index}}') ? 'opacity-50 bg-slate-100' : 'bg-white'"
                                class="block px-4 py-3 border-b border-slate-100 transition-all hover:bg-slate-50">
                                <div class="flex items-start gap-3">
                                    <div class="mt-0.5 bg-blue-100 p-1.5 rounded-md text-blue-600"><i
                                            class="fa-solid fa-money-check text-xs"></i></div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-800">Cheque Due Today</p>
                                        <p class="text-[11px] text-slate-500 mt-0.5">Ref:
                                            {{ $cheque->reference_no ?? 'N/A' }}</p>
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

                <div
                    class="flex items-center gap-1.5 bg-blue-50 border border-blue-100 px-2.5 py-1 rounded-md text-xs font-semibold text-blue-700">
                    <i class="fa-solid fa-shield text-[10px]"></i> Admin
                </div>
            </div>
        </div>

        <header
            class="bg-white border-b border-slate-200 flex items-start px-3 py-2 gap-4 shadow-sm shrink-0 overflow-x-auto">
            <div class="flex flex-col items-center pt-1.5 px-3 border-r border-slate-200">
                <i class="fa-solid fa-compass text-slate-400 text-lg mb-0.5"></i>
                <span
                    class="text-[9px] font-bold text-slate-400 uppercase tracking-wider whitespace-nowrap">Actions</span>
            </div>

            <div class="flex items-start gap-4">
                <div class="flex flex-col items-center gap-1">
                    <div class="flex gap-1">
                        <a href="{{ route('admin.sales.create') }}"
                            class="flex flex-col items-center gap-1 p-1.5 w-16 hover:bg-slate-50 rounded-md text-slate-600 hover:text-blue-600 transition-colors">
                            <i class="fa-solid fa-file-invoice text-base"></i>
                            <span class="text-[10px] font-medium">New Sale</span>
                        </a>
                        <a href="{{ route('admin.products.create') }}"
                            class="flex flex-col items-center gap-1 p-1.5 w-16 hover:bg-slate-50 rounded-md text-slate-600 hover:text-blue-600 transition-colors">
                            <i class="fa-solid fa-cart-shopping text-base"></i>
                            <span class="text-[10px] font-medium">New Item</span>
                        </a>
                    </div>
                    <span
                        class="text-[9px] font-bold text-slate-400 uppercase tracking-tight border-t border-slate-100 w-full text-center pt-0.5">Entry</span>
                </div>

                <div class="w-px h-10 bg-slate-200 self-center"></div>

                <div class="flex flex-col items-center gap-1">
                    <div class="flex gap-1">
                        <a href="{{ route('admin.customers.create') }}"
                            class="flex flex-col items-center gap-1 p-1.5 w-16 hover:bg-slate-50 rounded-md text-slate-600 hover:text-blue-600 transition-colors">
                            <i class="fa-solid fa-user-plus text-base"></i>
                            <span class="text-[10px] font-medium">Add Party</span>
                        </a>
                        <a href="{{ route('admin.cheques.create') }}"
                            class="flex flex-col items-center gap-1 p-1.5 w-16 hover:bg-slate-50 rounded-md text-slate-600 hover:text-blue-600 transition-colors">
                            <i class="fa-solid fa-money-check-dollar text-base"></i>
                            <span class="text-[10px] font-medium">Cheque</span>
                        </a>
                    </div>
                    <span
                        class="text-[9px] font-bold text-slate-400 uppercase tracking-tight border-t border-slate-100 w-full text-center pt-0.5">Ledgers</span>
                </div>

                <div class="w-px h-10 bg-slate-200 self-center"></div>

                <div class="flex flex-col items-center gap-1">
                    <div class="flex gap-1">
                        <a href="{{ route('admin.sales.index') }}"
                            class="flex flex-col items-center gap-1 p-1.5 w-16 hover:bg-slate-50 rounded-md text-slate-600 hover:text-blue-600 transition-colors">
                            <i class="fa-solid fa-chart-line text-base"></i>
                            <span class="text-[10px] font-medium">Reports</span>
                        </a>
                        <a href="{{ route('admin.backups.index') }}"
                            class="flex flex-col items-center gap-1 p-1.5 w-16 hover:bg-slate-50 rounded-md text-slate-600 hover:text-blue-600 transition-colors">
                            <i class="fa-solid fa-shield-halved text-base"></i>
                            <span class="text-[10px] font-medium">Backups</span>
                        </a>
                    </div>
                    <span
                        class="text-[9px] font-bold text-slate-400 uppercase tracking-tight border-t border-slate-100 w-full text-center pt-0.5">System</span>
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