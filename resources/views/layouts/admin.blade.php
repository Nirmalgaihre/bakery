<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Deurali Chemicals Pvt Ltd - Admin Terminal')</title>
    
    <!-- Framework & Icon Assets -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: ['Inter', 'sans-serif']
                },
                colors: {
                    brandDark: '#0b1329',
                    brandDarkLight: '#162238',
                    brandAccent: '#3b82f6'
                }
            }
        }
    }
    </script>
    <style>
    [x-cloak] {
        display: none !important;
    }

    /* Custom Modern Thin Scrollbars */
    ::-webkit-scrollbar {
        width: 5px;
        height: 5px;
    }

    ::-webkit-scrollbar-track {
        background: transparent;
    }

    ::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.12);
        border-radius: 9999px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: rgba(59, 130, 246, 0.4);
    }
    
    /* Smooth Scroll Behavior */
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    </style>

    @stack('styles')
</head>

{{--
    =========================================================
    ROLE-BASED ACCESS CONTROL (RBAC) — SINGLE SOURCE OF TRUTH
    =========================================================
    $userRole        -> normalized (lowercased, trimmed) role string on the
                         authenticated user.
    $isAdmin         -> Administrator: unrestricted access.
    $isAccountant    -> Accountant: operational + financial access.
    $canManageOps    -> true for Admin OR Accountant.
    $isAdminOnly     -> alias of $isAdmin.
--}}
@php
// Grab the raw role value off the authenticated user.
$rawRole = auth()->check() ? auth()->user()->role : null;

if (is_object($rawRole)) {
    $rawRole = $rawRole->value ?? $rawRole->name ?? (string) $rawRole;
}

$userRole = $rawRole ? strtolower(trim($rawRole)) : null;
$isAdmin = $userRole === 'admin';
$isAccountant = $userRole === 'accountant';
$canManageOps = $isAdmin || $isAccountant;
$isAdminOnly = $isAdmin;
@endphp

<body x-data="{ mobileSidebarOpen: false }"
    :class="mobileSidebarOpen ? 'overflow-hidden md:overflow-hidden' : 'overflow-hidden'"
    class="bg-[#f8fafc] text-[#1e293b] font-sans h-dvh flex antialiased selection:bg-blue-500 selection:text-white">

    @if(View::exists('partials.alerts'))
        @include('partials.alerts')
    @endif

    <!-- Mobile Drawer Overlay Backdrop -->
    <div x-show="mobileSidebarOpen" 
         x-cloak 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="mobileSidebarOpen = false"
         class="fixed inset-0 z-40 bg-slate-950/70 backdrop-blur-md md:hidden"></div>

    {{-- =========================================================
         SIDEBAR NAVIGATION
    ========================================================== --}}
    <aside
        class="fixed inset-y-0 left-0 z-50 flex h-dvh w-[min(84vw,270px)] shrink-0 select-none flex-col border-r border-slate-800/80 bg-slate-900 text-slate-300 shadow-2xl transition-transform duration-300 ease-out md:static md:z-auto md:w-[260px] md:translate-x-0 md:shadow-none"
        :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full'">

        <!-- Logo & Mobile Exit Button Header -->
        <div class="flex h-16 items-center justify-between border-b border-slate-800/80 px-4 bg-slate-950/40">
            <div class="flex items-center gap-3 overflow-hidden">
                <img src="https://deuralichemicals.com.np/storage/img/dcl.png" alt="Deurali Chemicals Logo"
                    class="h-10 w-auto object-contain drop-shadow-md" />
            </div>
            <button type="button" @click="mobileSidebarOpen = false"
                class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition-colors hover:bg-slate-800 hover:text-white md:hidden"
                aria-label="Close navigation">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <!-- Location Status & User Tier Badge Card -->
        <div class="px-3 py-2.5 border-b border-slate-800/60 bg-slate-950/20">
            <div class="flex items-center justify-between text-[11px] bg-slate-950/40 rounded-lg px-2.5 py-1.5 ring-1 ring-slate-800/50">
                <span class="text-slate-300 font-medium tracking-wide flex items-center gap-1.5 text-[11px]">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    Kuleshwor, KTM
                </span>
                <span class="text-slate-400 font-mono text-[10px] font-semibold bg-slate-800/80 px-1.5 py-0.5 rounded">LV-12</span>
            </div>

            <!-- Current Role Indicator Badge -->
            <div class="mt-2 flex items-center gap-1.5 px-0.5">
                @if($isAdmin)
                <span class="inline-flex w-full items-center justify-center gap-1.5 rounded-md bg-amber-500/10 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-amber-400 ring-1 ring-amber-500/30">
                    <i class="fa-solid fa-crown text-[10px]"></i> Administrator Mode
                </span>
                @elseif($isAccountant)
                <span class="inline-flex w-full items-center justify-center gap-1.5 rounded-md bg-blue-500/10 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-blue-400 ring-1 ring-blue-500/30">
                    <i class="fa-solid fa-calculator text-[10px]"></i> Accountant Access
                </span>
                @else
                <span class="inline-flex w-full items-center justify-center gap-1.5 rounded-md bg-slate-500/10 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-400 ring-1 ring-slate-500/30">
                    <i class="fa-solid fa-user text-[10px]"></i> {{ $userRole ? ucfirst($userRole) : 'Guest User' }}
                </span>
                @endif
            </div>
        </div>

        <!-- Sidebar Scrollable Navigation Menu -->
        <nav class="flex-1 overflow-y-auto py-3 space-y-3 px-3 no-scrollbar"
            @click="if ($event.target.closest('a')) mobileSidebarOpen = false" x-data="{
         openDashboard: {{ request()->is('admin/dashboard*') || request()->routeIs('admin.dashboard') ? 'true' : 'false' }},
         openCategories: {{ request()->routeIs('admin.categories.*') ? 'true' : 'false' }},
         openSuppliers: {{ request()->routeIs('admin.suppliers.*') ? 'true' : 'false' }},
         openCustomers: {{ request()->routeIs('admin.customers.*') ? 'true' : 'false' }},
         openProducts: {{ request()->routeIs('admin.products.*') ? 'true' : 'false' }},
         openInventory: {{ request()->is('admin/inventory*') ? 'true' : 'false' }},
         openBilling: {{ request()->is('admin/sales*') ? 'true' : 'false' }},
         openInvoices: {{ request()->is('admin/invoices*') ? 'true' : 'false' }},
         openWastage: {{ request()->is('admin/returns-wastage*') ? 'true' : 'false' }},
         openChequesMenu: {{ request()->is('admin/cheques*') ? 'true' : 'false' }},
         openReports: {{ request()->is('admin/reports*') || request()->routeIs('admin.sales.item-analysis') || request()->routeIs('admin.reports.stock-movement') ? 'true' : 'false' }},
         openBackupMenu: {{ request()->is('admin/backups*') ? 'true' : 'false' }},
         openAdminSection: {{ request()->is('admin/staff*') || request()->is('admin/roles*') || request()->is('admin/logs*') || request()->is('admin/trash*') ? 'true' : 'false' }}
     }">

            @if($canManageOps)

            {{-- Dashboards: Admin only --}}
            @if($isAdmin)
            <div>
                <div class="text-[10px] font-bold text-slate-400/80 px-2 mb-1 tracking-widest uppercase flex items-center justify-between">
                    <span>Dashboards</span>
                </div>
                <div class="space-y-0.5">
                    <button @click="openDashboard = !openDashboard"
                        class="w-full flex items-center justify-between px-2.5 py-2 text-[12.5px] font-medium rounded-lg transition-all duration-200 {{ request()->is('admin/dashboard*') || request()->routeIs('admin.dashboard') ? 'text-white bg-slate-800/80 shadow-sm border-l-2 border-blue-500' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }} outline-none">
                        <div class="flex items-center">
                            <i class="fa-solid fa-chart-line mr-2.5 w-4 text-center text-sm text-slate-400"></i>
                            <span>Dashboards</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-500 transition-transform duration-200"
                            :class="openDashboard ? 'rotate-180 text-blue-400' : ''"></i>
                    </button>
                    <div x-show="openDashboard" x-cloak x-collapse
                        class="border-l-2 border-slate-800 ml-4 pl-2 space-y-0.5 my-1">
                        <a href="{{ route('admin.dashboard') }}"
                            class="flex items-center px-2.5 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.dashboard') ? 'text-blue-400 font-semibold bg-blue-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            <i class="fa-solid fa-cubes mr-2 text-[11px] text-slate-400 w-3.5 text-center"></i>Inventory Dashboard
                        </a>
                        <a href="{{ route('admin.sales.dashboard') }}"
                            class="flex items-center px-2.5 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.sales.dashboard') ? 'text-blue-400 font-semibold bg-blue-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            <i class="fa-solid fa-chart-bar mr-2 text-[11px] text-slate-400 w-3.5 text-center"></i>Sales Dashboard
                        </a>
                        <a href="{{ route('admin.purchases.dashboard') }}"
                            class="flex items-center px-2.5 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.purchases.dashboard') ? 'text-blue-400 font-semibold bg-blue-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            <i class="fa-solid fa-cart-flatbed mr-2 text-[11px] text-slate-400 w-3.5 text-center"></i>Purchase Dashboard
                        </a>
                    </div>
                </div>
            </div>
            @endif

            {{-- Categories --}}
            <div>
                <a href="{{ route('admin.categories.index') }}"
                    class="flex items-center px-2.5 py-2 text-[12.5px] font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('admin.categories.*') ? 'text-white bg-slate-800/80 shadow-sm border-l-2 border-blue-500' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }}">
                    <i class="fa-solid fa-layer-group mr-2.5 w-4 text-center text-sm text-slate-400"></i>
                    <span>Manage Categories</span>
                </a>
            </div>

            {{-- Suppliers --}}
            <div>
                <div class="text-[10px] font-bold text-slate-400/80 px-2 mb-1 tracking-widest uppercase">Suppliers</div>
                <div class="space-y-0.5">
                    <button @click="openSuppliers = !openSuppliers"
                        class="w-full flex items-center justify-between px-2.5 py-2 text-[12.5px] font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('admin.suppliers.*') ? 'text-white bg-slate-800/80 shadow-sm border-l-2 border-blue-500' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }} outline-none">
                        <div class="flex items-center">
                            <i class="fa-solid fa-truck-field mr-2.5 w-4 text-center text-sm text-slate-400"></i>
                            <span>Suppliers</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-500 transition-transform duration-200"
                            :class="openSuppliers ? 'rotate-180 text-blue-400' : ''"></i>
                    </button>
                    <div x-show="openSuppliers" x-cloak x-collapse
                        class="border-l-2 border-slate-800 ml-4 pl-2 space-y-0.5 my-1">
                        <a href="{{ route('admin.suppliers.create') }}"
                            class="flex items-center px-2.5 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.suppliers.create') ? 'text-blue-400 font-semibold bg-blue-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            <i class="fa-solid fa-user-plus mr-2 text-[11px] text-slate-400 w-3.5 text-center"></i>Create Supplier
                        </a>
                        <a href="{{ route('admin.suppliers.index') }}"
                            class="flex items-center px-2.5 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.suppliers.index') ? 'text-blue-400 font-semibold bg-blue-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            <i class="fa-solid fa-users mr-2 text-[11px] text-slate-400 w-3.5 text-center"></i>Manage Suppliers
                        </a>
                    </div>
                </div>
            </div>

            {{-- Products --}}
            <div>
                <div class="text-[10px] font-bold text-slate-400/80 px-2 mb-1 tracking-widest uppercase">Products</div>
                <div class="space-y-0.5">
                    <button @click="openProducts = !openProducts"
                        class="w-full flex items-center justify-between px-2.5 py-2 text-[12.5px] font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('admin.products.*') ? 'text-white bg-slate-800/80 shadow-sm border-l-2 border-blue-500' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }} outline-none">
                        <div class="flex items-center">
                            <i class="fa-solid fa-box mr-2.5 w-4 text-center text-sm text-slate-400"></i>
                            <span>Products</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-500 transition-transform duration-200"
                            :class="openProducts ? 'rotate-180 text-blue-400' : ''"></i>
                    </button>
                    <div x-show="openProducts" x-cloak x-collapse
                        class="border-l-2 border-slate-800 ml-4 pl-2 space-y-0.5 my-1">
                        <a href="{{ route('admin.products.create') }}"
                            class="flex items-center px-2.5 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.products.create') ? 'text-blue-400 font-semibold bg-blue-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            <i class="fa-solid fa-circle-plus mr-2 text-[11px] text-slate-400 w-3.5 text-center"></i>Add Product
                        </a>
                        <a href="{{ route('admin.products.index') }}"
                            class="flex items-center px-2.5 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.products.index') ? 'text-blue-400 font-semibold bg-blue-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            <i class="fa-solid fa-list mr-2 text-[11px] text-slate-400 w-3.5 text-center"></i>View Products
                        </a>
                        <a href="{{ route('admin.products.index') }}?group=primary"
                            class="flex items-center px-2.5 py-1.5 text-[12px] font-medium rounded-md transition-all text-slate-400 hover:text-slate-200 hover:bg-slate-800/40">
                            <i class="fa-solid fa-sitemap mr-2 text-[11px] text-slate-400 w-3.5 text-center"></i>Stock Groups
                        </a>
                    </div>
                </div>
            </div>

            {{-- Customers --}}
            <div>
                <div class="text-[10px] font-bold text-slate-400/80 px-2 mb-1 tracking-widest uppercase">Customers</div>
                <div class="space-y-0.5">
                    <button @click="openCustomers = !openCustomers"
                        class="w-full flex items-center justify-between px-2.5 py-2 text-[12.5px] font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('admin.customers.*') ? 'text-white bg-slate-800/80 shadow-sm border-l-2 border-blue-500' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }} outline-none">
                        <div class="flex items-center">
                            <i class="fa-solid fa-users mr-2.5 w-4 text-center text-sm text-slate-400"></i>
                            <span>Customers</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-500 transition-transform duration-200"
                            :class="openCustomers ? 'rotate-180 text-blue-400' : ''"></i>
                    </button>
                    <div x-show="openCustomers" x-cloak x-collapse
                        class="border-l-2 border-slate-800 ml-4 pl-2 space-y-0.5 my-1">
                        <a href="{{ route('admin.customers.create') }}"
                            class="flex items-center px-2.5 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.customers.create') ? 'text-blue-400 font-semibold bg-blue-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            <i class="fa-solid fa-user-plus mr-2 text-[11px] text-slate-400 w-3.5 text-center"></i>Add Customer
                        </a>
                        <a href="{{ route('admin.customers.manage') }}"
                            class="flex items-center px-2.5 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.customers.manage') ? 'text-blue-400 font-semibold bg-blue-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            <i class="fa-solid fa-sliders mr-2 text-[11px] text-slate-400 w-3.5 text-center"></i>Manage Customers
                        </a>
                        <a href="{{ route('admin.customers.index') }}"
                            class="flex items-center px-2.5 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.customers.index') ? 'text-blue-400 font-semibold bg-blue-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            <i class="fa-solid fa-address-book mr-2 text-[11px] text-slate-400 w-3.5 text-center"></i>Customer Ledger
                        </a>
                    </div>
                </div>
            </div>

            {{-- Sales & Invoices --}}
            <div>
                <div class="text-[10px] font-bold text-slate-400/80 px-2 mb-1 tracking-widest uppercase">Sales & Invoices</div>
                <div class="space-y-1">
                    <div class="space-y-0.5">
                        <button @click="openBilling = !openBilling"
                            class="w-full flex items-center justify-between px-2.5 py-2 text-[12.5px] font-medium rounded-lg transition-all duration-200 {{ request()->is('admin/sales*') ? 'text-white bg-slate-800/80 shadow-sm border-l-2 border-blue-500' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }} outline-none">
                            <div class="flex items-center">
                                <i class="fa-solid fa-cash-register mr-2.5 w-4 text-center text-sm text-slate-400"></i>
                                <span>Sales</span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-500 transition-transform duration-200"
                                :class="openBilling ? 'rotate-180 text-blue-400' : ''"></i>
                        </button>
                        <div x-show="openBilling" x-cloak x-collapse
                            class="border-l-2 border-slate-800 ml-4 pl-2 space-y-0.5 my-1">
                            <a href="{{ route('admin.sales.create') }}"
                                class="flex items-center px-2.5 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.sales.create') ? 'text-blue-400 font-semibold bg-blue-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                                <i class="fa-solid fa-cart-shopping mr-2 text-[11px] text-slate-400 w-3.5 text-center"></i>New Sale (POS)
                            </a>
                            <a href="{{ route('admin.sales.index') }}"
                                class="flex items-center px-2.5 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.sales.index') ? 'text-blue-400 font-semibold bg-blue-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                                <i class="fa-solid fa-list mr-2 text-[11px] text-slate-400 w-3.5 text-center"></i>Sales Register
                            </a>
                            <a href="{{ route('admin.sales.all') }}"
                                class="flex items-center px-2.5 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.sales.all') ? 'text-blue-400 font-semibold bg-blue-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                                <i class="fa-solid fa-magnifying-glass mr-2 text-[11px] text-slate-400 w-3.5 text-center"></i>Find All Sales
                            </a>
                            <a href="{{ route('admin.sales.manage') }}"
                                class="flex items-center px-2.5 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.sales.manage') ? 'text-blue-400 font-semibold bg-blue-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                                <i class="fa-solid fa-sliders mr-2 text-[11px] text-slate-400 w-3.5 text-center"></i>Manage Sales
                            </a>
                        </div>
                    </div>
                    
                    <div class="space-y-0.5">
                        <button @click="openInvoices = !openInvoices"
                            class="w-full flex items-center justify-between px-2.5 py-2 text-[12.5px] font-medium rounded-lg transition-all duration-200 {{ request()->is('admin/invoices*') ? 'text-white bg-slate-800/80 shadow-sm border-l-2 border-blue-500' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }} outline-none">
                            <div class="flex items-center">
                                <i class="fa-solid fa-file-invoice-dollar mr-2.5 w-4 text-center text-sm text-slate-400"></i>
                                <span>Invoices</span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-500 transition-transform duration-200"
                                :class="openInvoices ? 'rotate-180 text-blue-400' : ''"></i>
                        </button>
                        <div x-show="openInvoices" x-cloak x-collapse
                            class="border-l-2 border-slate-800 ml-4 pl-2 space-y-0.5 my-1">
                            <a href="{{ route('admin.invoices.index') }}"
                                class="flex items-center px-2.5 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.invoices.index') ? 'text-blue-400 font-semibold bg-blue-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                                <i class="fa-solid fa-folder-open mr-2 text-[11px] text-slate-400 w-3.5 text-center"></i>Invoice Ledger
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Inventory --}}
            <div>
                <div class="text-[10px] font-bold text-slate-400/80 px-2 mb-1 tracking-widest uppercase">Inventory</div>
                <div class="space-y-0.5">
                    <button @click="openInventory = !openInventory"
                        class="w-full flex items-center justify-between px-2.5 py-2 text-[12.5px] font-medium rounded-lg transition-all duration-200 {{ request()->is('admin/inventory*') ? 'text-white bg-slate-800/80 shadow-sm border-l-2 border-blue-500' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }} outline-none">
                        <div class="flex items-center">
                            <i class="fa-solid fa-warehouse mr-2.5 w-4 text-center text-sm text-slate-400"></i>
                            <span>Inventory</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-500 transition-transform duration-200"
                            :class="openInventory ? 'rotate-180 text-blue-400' : ''"></i>
                    </button>
                    <div x-show="openInventory" x-cloak x-collapse
                        class="border-l-2 border-slate-800 ml-4 pl-2 space-y-0.5 my-1">
                        <a href="{{ route('admin.inventory.position') }}"
                            class="flex items-center px-2.5 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.inventory.position') ? 'text-blue-400 font-semibold bg-blue-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            <i class="fa-solid fa-chart-pie mr-2 text-[11px] text-slate-400 w-3.5 text-center"></i>Stock Position
                        </a>
                        <a href="{{ route('admin.inventory.add') }}"
                            class="flex items-center px-2.5 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->is('admin/inventory/add*') ? 'text-blue-400 font-semibold bg-blue-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            <i class="fa-solid fa-dolly mr-2 text-[11px] text-slate-400 w-3.5 text-center"></i>Add Stock
                        </a>
                        <a href="{{ route('admin.inventory.low_stock_manager') }}"
                            class="flex items-center px-2.5 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->is('admin/inventory/low-stock*') ? 'text-amber-400 font-semibold bg-amber-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            <i class="fa-solid fa-triangle-exclamation mr-2 text-[11px] text-amber-500 w-3.5 text-center"></i>Low Stock
                        </a>
                    </div>
                </div>
            </div>

            {{-- Returns --}}
            <div>
                <div class="text-[10px] font-bold text-slate-400/80 px-2 mb-1 tracking-widest uppercase">Returns</div>
                <div class="space-y-0.5">
                    <button @click="openWastage = !openWastage" type="button"
                        class="w-full flex items-center justify-between px-2.5 py-2 text-[12.5px] font-medium rounded-lg transition-all duration-200 {{ request()->is('admin/returns-wastage*') ? 'text-white bg-slate-800/80 shadow-sm border-l-2 border-blue-500' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }} outline-none">
                        <div class="flex items-center">
                            <i class="fa-solid fa-arrow-rotate-left mr-2.5 w-4 text-center text-sm text-slate-400"></i>
                            <span>Returns</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-500 transition-transform duration-200"
                            :class="openWastage ? 'rotate-180 text-blue-400' : ''"></i>
                    </button>
                    <div x-show="openWastage" x-cloak x-collapse
                        class="border-l-2 border-slate-800 ml-4 pl-2 space-y-0.5 my-1">
                        <a href="{{ route('admin.wastage.create') }}"
                            class="flex items-center px-2.5 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.wastage.create') ? 'text-blue-400 font-semibold bg-blue-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            <i class="fa-solid fa-plus mr-2 text-[11px] text-slate-400 w-3.5 text-center"></i>Add Return
                        </a>
                        <a href="{{ route('admin.wastage.index') }}"
                            class="flex items-center px-2.5 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.wastage.index') ? 'text-blue-400 font-semibold bg-blue-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            <i class="fa-solid fa-folder mr-2 text-[11px] text-slate-400 w-3.5 text-center"></i>Return Ledger
                        </a>
                    </div>
                </div>
            </div>

            {{-- Reports --}}
            <div>
                <div class="text-[10px] font-bold text-slate-400/80 px-2 mb-1 tracking-widest uppercase">Reports</div>
                <div class="space-y-0.5">
                    <button @click="openReports = !openReports"
                        class="w-full flex items-center justify-between px-2.5 py-2 text-[12.5px] font-medium rounded-lg transition-all duration-200 {{ request()->is('admin/reports*') ? 'text-white bg-slate-800/80 shadow-sm border-l-2 border-blue-500' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }} outline-none">
                        <div class="flex items-center">
                            <i class="fa-solid fa-chart-column mr-2.5 w-4 text-center text-sm text-slate-400"></i>
                            <span>Reports</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-500 transition-transform duration-200"
                            :class="openReports ? 'rotate-180 text-blue-400' : ''"></i>
                    </button>
                    <div x-show="openReports" x-cloak x-collapse
                        class="border-l-2 border-slate-800 ml-4 pl-2 space-y-0.5 my-1">
                        <a href="{{ route('admin.reports.index') }}"
                            class="flex items-center px-2.5 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.reports.index') ? 'text-blue-400 font-semibold bg-blue-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            <i class="fa-solid fa-table mr-2 text-[11px] text-slate-400 w-3.5 text-center"></i>Overview
                        </a>
                        <a href="{{ route('admin.reports.stock_ageing') }}"
                            class="flex items-center px-2.5 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.reports.stock_ageing') ? 'text-blue-400 font-semibold bg-blue-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            <i class="fa-solid fa-hourglass mr-2 text-[11px] text-slate-400 w-3.5 text-center"></i>Stock Ageing
                        </a>
                        <a href="{{ route('admin.reports.monthly-movement') }}"
                            class="flex items-center px-2.5 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.reports.monthly-movement') ? 'text-blue-400 font-semibold bg-blue-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            <i class="fa-solid fa-calendar mr-2 text-[11px] text-slate-400 w-3.5 text-center"></i>Monthly Movement
                        </a>
                        <a href="{{ route('admin.reports.stock-movement') }}"
                            class="flex items-center px-2.5 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.reports.stock-movement') ? 'text-blue-400 font-semibold bg-blue-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            <i class="fa-solid fa-arrows-left-right mr-2 text-[11px] text-slate-400 w-3.5 text-center"></i>Stock Movement
                        </a>
                        <a href="{{ route('admin.sales.item-analysis') }}"
                            class="flex items-center px-2.5 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.sales.item-analysis') ? 'text-amber-400 font-semibold bg-amber-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            <i class="fa-solid fa-star mr-2 text-[11px] text-amber-400 w-3.5 text-center"></i>Best Sellers
                        </a>
                        @if(isset($product))
                        <a href="{{ route('admin.reports.stock-movement', $product->id) }}"
                            class="flex items-center px-2.5 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.reports.stock-movement') ? 'text-purple-400 font-semibold bg-purple-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            <i class="fa-solid fa-route mr-2 text-[11px] text-purple-400 w-3.5 text-center"></i>Traceability
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Cheques: Admin only --}}
            @if($isAdmin)
            <div>
                <div class="text-[10px] font-bold text-slate-400/80 px-2 mb-1 tracking-widest uppercase">Cheques</div>
                <div class="space-y-0.5">
                    <button @click="openChequesMenu = !openChequesMenu"
                        class="w-full flex items-center justify-between px-2.5 py-2 text-[12.5px] font-medium rounded-lg transition-all duration-200 {{ request()->is('admin/cheques*') ? 'text-white bg-slate-800/80 shadow-sm border-l-2 border-blue-500' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }} outline-none">
                        <div class="flex items-center">
                            <i class="fa-solid fa-money-check mr-2.5 w-4 text-center text-sm text-slate-400"></i>
                            <span>Cheques</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-500 transition-transform duration-200"
                            :class="openChequesMenu ? 'rotate-180 text-blue-400' : ''"></i>
                    </button>
                    <div x-show="openChequesMenu" x-cloak x-collapse
                        class="border-l-2 border-slate-800 ml-4 pl-2 space-y-0.5 my-1">
                        <a href="{{ route('admin.cheques.create') }}"
                            class="flex items-center px-2.5 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.cheques.create') ? 'text-blue-400 font-semibold bg-blue-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            <i class="fa-solid fa-pen mr-2 text-[11px] text-slate-400 w-3.5 text-center"></i>Record Cheque
                        </a>
                        <a href="{{ route('admin.cheques.index') }}"
                            class="flex items-center px-2.5 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.cheques.index') ? 'text-blue-400 font-semibold bg-blue-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            <i class="fa-solid fa-building-columns mr-2 text-[11px] text-slate-400 w-3.5 text-center"></i>Cheque Ledger
                        </a>
                    </div>
                </div>
            </div>
            @endif

            {{-- Release Notes --}}
            <div class="pt-1">
                <a href="{{ route('admin.release-notes.index') }}"
                    class="flex items-center px-2.5 py-2 text-[12.5px] font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('admin.release-notes.*') ? 'text-white bg-slate-800/80 shadow-sm border-l-2 border-blue-500' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }}">
                    <i class="fa-solid fa-code-branch mr-2.5 w-4 text-center text-sm text-slate-400"></i>
                    <span>Release Notes</span>
                </a>
            </div>

            @endif

            {{-- System Controls (Admin / Restricted) --}}
            @if($isAdminOnly)
            <div class="pt-3 mt-3 border-t border-slate-800/80">
                <div class="flex items-center gap-1.5 px-2 mb-1.5 text-[10px] font-bold text-amber-400 tracking-widest uppercase">
                    <i class="fa-solid fa-shield-halved text-[10px]"></i> Administration
                </div>
                <div class="space-y-0.5">
                    <a href="{{ route('admin.backups.index') }}"
                        class="flex items-center px-2.5 py-2 text-[12.5px] font-medium rounded-lg transition-all duration-200 {{ request()->is('admin/backups*') ? 'text-white bg-slate-800/80 shadow-sm border-l-2 border-amber-500' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }}">
                        <i class="fa-solid fa-database mr-2.5 w-4 text-center text-sm text-slate-400"></i>
                        <span>Backup & Restore</span>
                    </a>
                    <a href="{{ route('admin.trash.index') }}"
                        class="flex items-center px-2.5 py-2 text-[12.5px] font-medium rounded-lg transition-all duration-200 {{ request()->is('admin/trash*') ? 'text-red-400 bg-red-950/30 border-l-2 border-red-500' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }}">
                        <i class="fa-solid fa-trash mr-2.5 w-4 text-center text-sm text-red-400/80"></i>
                        <span>Trash / Deleted</span>
                    </a>
                </div>
                <div class="space-y-0.5 mt-0.5">
                    <button @click="openAdminSection = !openAdminSection"
                        class="w-full flex items-center justify-between px-2.5 py-2 text-[12.5px] font-medium rounded-lg transition-all duration-200 {{ request()->is('admin/staff*') || request()->is('admin/roles*') || request()->is('admin/logs*') ? 'text-white bg-slate-800/80 shadow-sm border-l-2 border-blue-500' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }} outline-none">
                        <div class="flex items-center">
                            <i class="fa-solid fa-user-lock mr-2.5 w-4 text-center text-sm text-slate-400"></i>
                            <span>User Controls</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-500 transition-transform duration-200"
                            :class="openAdminSection ? 'rotate-180 text-blue-400' : ''"></i>
                    </button>
                    <div x-show="openAdminSection" x-cloak x-collapse
                        class="border-l-2 border-slate-800 ml-4 pl-2 space-y-0.5 my-1">
                        <a href="{{ route('admin.staff.index') }}"
                            class="flex items-center px-2.5 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.staff.*') ? 'text-blue-400 font-semibold bg-blue-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            <i class="fa-solid fa-users mr-2 text-[11px] text-slate-400 w-3.5 text-center"></i>Staff Directory
                        </a>
                        <a href="{{ route('admin.roles.index') }}"
                            class="flex items-center px-2.5 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.roles.*') ? 'text-blue-400 font-semibold bg-blue-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            <i class="fa-solid fa-id-badge mr-2 text-[11px] text-slate-400 w-3.5 text-center"></i>Roles & Permissions
                        </a>
                        <a href="{{ route('admin.logs.index') }}"
                            class="flex items-center px-2.5 py-1.5 text-[12px] font-medium rounded-md transition-all {{ request()->routeIs('admin.logs.*') ? 'text-blue-400 font-semibold bg-blue-500/10' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            <i class="fa-solid fa-clock-rotate-left mr-2 text-[11px] text-slate-400 w-3.5 text-center"></i>Activity Logs
                        </a>
                    </div>
                </div>
            </div>
            @elseif($isAccountant)
            <div class="pt-3 mt-2 border-t border-slate-800/80 px-1">
                <div class="flex items-start gap-2 rounded-lg bg-slate-950/60 px-3 py-2.5 text-[11px] text-slate-400 ring-1 ring-slate-800">
                    <i class="fa-solid fa-lock mt-0.5 text-amber-500/90 text-xs shrink-0"></i>
                    <span>Admin tools (Backups, Trash, User Controls) are locked for non-admins.</span>
                </div>
            </div>
            @endif
        </nav>
    </aside>

    {{-- =========================================================
         MAIN APPLICATION CONTENT COLUMN
    ========================================================== --}}
    <div class="flex-1 flex min-w-0 flex-col h-full overflow-hidden">

        <!-- Top Header Navigation Bar -->
        <header class="bg-white/95 border-b border-slate-200 h-16 px-4 md:px-6 flex items-center justify-between gap-3 shrink-0 z-20 shadow-xs backdrop-blur-md">
            <div class="flex min-w-0 items-center gap-3">
                <!-- Mobile Navigation Toggle Button -->
                <button type="button" @click="mobileSidebarOpen = true"
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 shadow-xs transition-colors hover:border-blue-300 hover:bg-blue-50 hover:text-blue-600 md:hidden"
                    aria-label="Open navigation">
                    <i class="fa-solid fa-bars text-sm"></i>
                </button>
                
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600 ring-1 ring-blue-100/80">
                    <i class="fa-solid fa-chart-line text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="truncate text-[10px] font-bold uppercase tracking-widest text-slate-400">Control Center</p>
                    <h1 class="truncate text-xs md:text-sm font-bold text-slate-800">
                        @yield('panel_title', 'Warehouse Analytical Panel')
                    </h1>
                </div>
            </div>

            <!-- Header Right Action Group -->
            <div class="flex shrink-0 items-center gap-2 md:gap-3">
                @if($canManageOps)
                <!-- System Notifications Panel -->
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
                        class="relative flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 shadow-xs transition-all hover:border-blue-300 hover:bg-blue-50 hover:text-blue-600">
                        <i class="fa-solid fa-bell text-sm"></i>

                        @if($notificationCount > 0)
                        <span x-show="readItems.length < {{ $notificationCount }}"
                            class="absolute -right-1 -top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[9px] font-bold text-white shadow-xs ring-2 ring-white">
                            {{ $notificationCount - count(array_intersect(array_merge($notifications['lowStock']->pluck('id')->map(fn($id) => 'stock-'.$id)->all(), $notifications['cheques']->pluck('id')->map(fn($id) => 'cheque-'.$id)->all()), [])) }}
                        </span>
                        @endif
                    </button>

                    <div x-show="openNotification" @click.away="openNotification = false" x-cloak
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="fixed left-3 right-3 top-16 z-50 max-h-[calc(100dvh-5rem)] overflow-hidden rounded-xl border border-slate-200/80 bg-white shadow-xl transition-all md:absolute md:left-auto md:right-0 md:top-auto md:mt-2 md:w-80 md:origin-top-right">

                        <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/80 px-4 py-2.5">
                            <div>
                                <h3 class="text-xs font-bold text-slate-800">Notifications</h3>
                                <p class="text-[10px] font-medium text-slate-400">{{ $notificationCount }} active system alerts</p>
                            </div>
                            <button @click="readItems = [
                                ...@json($notifications['lowStock']->pluck('id')->map(fn($id) => 'stock-'.$id)),
                                ...@json($notifications['cheques']->pluck('id')->map(fn($id) => 'cheque-'.$id))
                                ];"
                                class="rounded-md bg-white px-2 py-1 text-[10px] font-bold uppercase text-blue-600 ring-1 ring-slate-200 hover:text-blue-800">Mark all read</button>
                        </div>

                        <div class="max-h-80 overflow-y-auto bg-slate-50/30 divide-y divide-slate-100">
                            @if($notificationCount > 0)
                            @foreach($notifications['lowStock'] as $index => $product)
                            <a href="{{ route('admin.products.index') }}" @click="markAsRead('stock-{{$product->id}}')"
                                class="block px-4 py-3 transition-all hover:bg-slate-50">
                                <div class="flex items-start gap-3"
                                    :class="readItems.includes('stock-{{$product->id}}') ? 'opacity-50' : ''">
                                    <div class="mt-0.5 bg-red-50 p-1.5 rounded-lg text-red-600 ring-1 ring-red-100"><i class="fa-solid fa-box-open text-xs"></i></div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-800">Low Stock Alert</p>
                                        <p class="text-[11px] text-slate-500 mt-0.5">{{ $product->name }} (Qty: {{(float)$product->initial_stock}})</p>
                                    </div>
                                </div>
                            </a>
                            @endforeach

                            @foreach($notifications['cheques'] as $index => $cheque)
                            <a href="{{ route('admin.cheques.index') }}" @click="markAsRead('cheque-{{$cheque->id}}')"
                                class="block px-4 py-3 transition-all hover:bg-slate-50">
                                <div class="flex items-start gap-3"
                                    :class="readItems.includes('cheque-{{$cheque->id}}') ? 'opacity-50' : ''">
                                    <div class="mt-0.5 bg-blue-50 p-1.5 rounded-lg text-blue-600 ring-1 ring-blue-100"><i class="fa-solid fa-money-check text-xs"></i></div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-800">Cheque Due Today</p>
                                        <p class="text-[11px] text-slate-500 mt-0.5">Ref: {{ $cheque->cheque_no ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </a>
                            @endforeach
                            @else
                            <div class="px-6 py-8 text-center text-slate-400">
                                <i class="fa-solid fa-check-double text-xl mb-1.5 text-slate-300"></i>
                                <p class="text-xs font-medium">All notifications cleared!</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- User Dropdown & Profile Menu -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="flex items-center justify-center w-9 h-9 rounded-full bg-blue-50 hover:bg-blue-100 focus:outline-none transition-colors border border-blue-200 shadow-xs">
                        <i class="fa-solid fa-user text-blue-600 text-sm"></i>
                    </button>

                    <div x-show="open" @click.away="open = false" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-slate-100 py-1 z-50">

                        <div class="px-4 py-2 text-[10px] text-slate-400 uppercase tracking-wider font-bold border-b border-slate-100">
                            Signed in as {{ auth()->user()->role ?? 'User' }}
                        </div>

                        <a href="{{ route('admin.profile.edit') }}"
                            class="block px-4 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-blue-600 transition-colors">Profile</a>
                        <a href="{{ route('admin.profile.change') }}"
                            class="block px-4 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-blue-600 transition-colors">Change Password</a>
                        <a href="{{ route('admin.user-guide') }}"
                            class="block px-4 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-blue-600 transition-colors">User Guide</a>

                        <div class="border-t border-slate-100 mt-1 pt-1">
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <button type="submit"
                                    class="block w-full text-left px-4 py-2 text-xs font-semibold text-red-600 hover:bg-red-50 transition-colors">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- SECONDARY NAVIGATION / ACTION RIBBON BAR --}}
        @if($canManageOps)
        <header class="shrink-0 border-b border-slate-200 bg-white shadow-2xs">
            <!-- Navigation Tabs -->
            <div class="flex min-h-9 items-end gap-1 overflow-x-auto border-b border-slate-100 px-4 pt-1 no-scrollbar">
                <a href="{{ $isAdmin ? route('admin.dashboard') : route('admin.sales.index') }}"
                    class="rounded-t-md border border-b-0 px-3.5 py-1.5 text-[11.5px] font-semibold transition-colors {{ (request()->routeIs('admin.dashboard') || (!$isAdmin && request()->routeIs('admin.sales.index'))) ? 'border-slate-200 bg-white text-blue-600 shadow-2xs' : 'border-transparent text-slate-500 hover:bg-slate-50 hover:text-slate-800' }}">
                    Home
                </a>
                <a href="{{ route('admin.sales.index') }}"
                    class="rounded-t-md border border-b-0 px-3.5 py-1.5 text-[11.5px] font-semibold transition-colors {{ request()->is('admin/sales*') || request()->is('admin/invoices*') || request()->is('admin/purchases*') ? 'border-slate-200 bg-white text-blue-600 shadow-2xs' : 'border-transparent text-slate-500 hover:bg-slate-50 hover:text-slate-800' }}">
                    Transactions
                </a>
                <a href="{{ route('admin.reports.index') }}"
                    class="rounded-t-md border border-b-0 px-3.5 py-1.5 text-[11.5px] font-semibold transition-colors {{ request()->is('admin/reports*') ? 'border-slate-200 bg-white text-blue-600 shadow-2xs' : 'border-transparent text-slate-500 hover:bg-slate-50 hover:text-slate-800' }}">
                    Reports
                </a>
                @if($isAdminOnly)
                <a href="{{ route('admin.backups.index') }}"
                    class="rounded-t-md border border-b-0 px-3.5 py-1.5 text-[11.5px] font-semibold transition-colors {{ request()->is('admin/backups*') || request()->is('admin/staff*') || request()->is('admin/roles*') || request()->is('admin/logs*') ? 'border-slate-200 bg-white text-blue-600 shadow-2xs' : 'border-transparent text-slate-500 hover:bg-slate-50 hover:text-slate-800' }}">
                    Tools
                </a>
                @endif
            </div>

            <!-- Quick Access Toolbar Groups -->
            <div class="flex items-stretch gap-2 overflow-x-auto overscroll-x-contain px-4 py-2 no-scrollbar">
                <!-- Quick Create Group -->
                <div class="flex min-w-max flex-col justify-between gap-1 border-r border-slate-200 pr-3">
                    <div class="flex gap-1">
                        <a href="{{ route('admin.sales.create') }}"
                            class="flex h-12 w-15 flex-col items-center justify-center gap-1 rounded-lg text-slate-600 transition-all hover:bg-blue-50 hover:text-blue-600">
                            <i class="fa-solid fa-file-invoice text-base"></i>
                            <span class="text-[10px] font-medium leading-none">Sale</span>
                        </a>
                        <a href="{{ route('admin.purchases.create') }}"
                            class="flex h-12 w-15 flex-col items-center justify-center gap-1 rounded-lg text-slate-600 transition-all hover:bg-blue-50 hover:text-blue-600">
                            <i class="fa-solid fa-cart-flatbed text-base"></i>
                            <span class="text-[10px] font-medium leading-none">Purchase</span>
                        </a>
                        <a href="{{ route('admin.invoices.create') }}"
                            class="flex h-12 w-15 flex-col items-center justify-center gap-1 rounded-lg text-slate-600 transition-all hover:bg-blue-50 hover:text-blue-600">
                            <i class="fa-solid fa-pen-to-square text-base"></i>
                            <span class="text-[10px] font-medium leading-none">Invoice</span>
                        </a>
                    </div>
                    <span class="text-center text-[9px] font-bold uppercase tracking-wider text-slate-400">Quick Create</span>
                </div>

                <!-- Masters Group -->
                <div class="flex min-w-max flex-col justify-between gap-1 border-r border-slate-200 pr-3">
                    <div class="flex gap-1">
                        <a href="{{ route('admin.customers.create') }}"
                            class="flex h-12 w-15 flex-col items-center justify-center gap-1 rounded-lg text-slate-600 transition-all hover:bg-blue-50 hover:text-blue-600">
                            <i class="fa-solid fa-user-plus text-base"></i>
                            <span class="text-[10px] font-medium leading-none">Add Party</span>
                        </a>
                        <a href="{{ route('admin.products.create') }}"
                            class="flex h-12 w-15 flex-col items-center justify-center gap-1 rounded-lg text-slate-600 transition-all hover:bg-blue-50 hover:text-blue-600">
                            <i class="fa-solid fa-cart-shopping text-base"></i>
                            <span class="text-[10px] font-medium leading-none">Add Item</span>
                        </a>
                        <a href="{{ route('admin.inventory.add') }}"
                            class="flex h-12 w-15 flex-col items-center justify-center gap-1 rounded-lg text-slate-600 transition-all hover:bg-blue-50 hover:text-blue-600">
                            <i class="fa-solid fa-box text-base"></i>
                            <span class="text-[10px] font-medium leading-none">Add Stock</span>
                        </a>
                    </div>
                    <span class="text-center text-[9px] font-bold uppercase tracking-wider text-slate-400">Masters</span>
                </div>

                <!-- Output Group -->
                <div class="flex min-w-max flex-col justify-between gap-1 border-r border-slate-200 pr-3">
                    <div class="flex gap-1">
                        <a href="{{ route('admin.reports.index') }}"
                            class="flex h-12 w-15 flex-col items-center justify-center gap-1 rounded-lg text-slate-600 transition-all hover:bg-blue-50 hover:text-blue-600">
                            <i class="fa-solid fa-chart-simple text-base"></i>
                            <span class="text-[10px] font-medium leading-none">Reports</span>
                        </a>
                        <a href="{{ route('admin.sales.index') }}"
                            class="flex h-12 w-15 flex-col items-center justify-center gap-1 rounded-lg text-slate-600 transition-all hover:bg-blue-50 hover:text-blue-600">
                            <i class="fa-solid fa-print text-base"></i>
                            <span class="text-[10px] font-medium leading-none">Print</span>
                        </a>
                        <a href="{{ route('admin.invoices.index') }}"
                            class="flex h-12 w-15 flex-col items-center justify-center gap-1 rounded-lg text-slate-600 transition-all hover:bg-blue-50 hover:text-blue-600">
                            <i class="fa-solid fa-file-export text-base"></i>
                            <span class="text-[10px] font-medium leading-none">Export</span>
                        </a>
                    </div>
                    <span class="text-center text-[9px] font-bold uppercase tracking-wider text-slate-400">Output</span>
                </div>

                <!-- Analytics Group -->
                <div class="flex min-w-max flex-col justify-between gap-1 border-r border-slate-200 pr-3">
                    <div class="flex gap-1">
                        <a href="{{ route('admin.reports.stock_ageing') }}"
                            class="flex h-12 w-15 flex-col items-center justify-center gap-1 rounded-lg text-slate-600 transition-all hover:bg-teal-50 hover:text-teal-700">
                            <i class="fa-solid fa-hourglass-half text-base"></i>
                            <span class="text-[10px] font-medium leading-none">Ageing</span>
                        </a>
                        <a href="{{ route('admin.reports.monthly-movement') }}"
                            class="flex h-12 w-15 flex-col items-center justify-center gap-1 rounded-lg text-slate-600 transition-all hover:bg-teal-50 hover:text-teal-700">
                            <i class="fa-solid fa-calendar-days text-base"></i>
                            <span class="text-[10px] font-medium leading-none">Monthly</span>
                        </a>
                        <a href="{{ route('admin.products.index') }}"
                            title="Pick a product to view its traceability report"
                            class="flex h-12 w-15 flex-col items-center justify-center gap-1 rounded-lg text-slate-600 transition-all hover:bg-teal-50 hover:text-teal-700">
                            <i class="fa-solid fa-route text-base"></i>
                            <span class="text-[10px] font-medium leading-none">Traceability</span>
                        </a>
                    </div>
                    <span class="text-center text-[9px] font-bold uppercase tracking-wider text-slate-400">Analytics</span>
                </div>

                <!-- Tools Group -->
                <div class="flex min-w-max flex-col justify-between gap-1 border-r border-slate-200 pr-3">
                    <div class="flex gap-1">
                        @if($isAdminOnly)
                        <a href="{{ route('admin.backups.index') }}"
                            class="flex h-12 w-15 flex-col items-center justify-center gap-1 rounded-lg text-slate-600 transition-all hover:bg-blue-50 hover:text-blue-600">
                            <i class="fa-solid fa-cloud-arrow-down text-base"></i>
                            <span class="text-[10px] font-medium leading-none">Backup</span>
                        </a>
                        @endif
                        <a href="{{ route('admin.wastage.index') }}"
                            class="flex h-12 w-15 flex-col items-center justify-center gap-1 rounded-lg text-slate-600 transition-all hover:bg-blue-50 hover:text-blue-600">
                            <i class="fa-solid fa-reply text-base"></i>
                            <span class="text-[10px] font-medium leading-none">Returns</span>
                        </a>
                        <a href="{{ route('admin.inventory.low_stock_manager') }}"
                            class="flex h-12 w-15 flex-col items-center justify-center gap-1 rounded-lg text-slate-600 transition-all hover:bg-amber-50 hover:text-amber-600">
                            <i class="fa-solid fa-triangle-exclamation text-base"></i>
                            <span class="text-[10px] font-medium leading-none">Low Stock</span>
                        </a>
                    </div>
                    <span class="text-center text-[9px] font-bold uppercase tracking-wider text-slate-400">Tools</span>
                </div>

                <!-- Payment Group (Admin Only) -->
                @if($isAdmin)
                <div class="flex min-w-max flex-col justify-between gap-1">
                    <div class="flex gap-1">
                        <a href="{{ route('admin.cheques.create') }}"
                            class="flex h-12 w-15 flex-col items-center justify-center gap-1 rounded-lg text-slate-600 transition-all hover:bg-blue-50 hover:text-blue-600">
                            <i class="fa-solid fa-money-check-dollar text-base"></i>
                            <span class="text-[10px] font-medium leading-none">Cheque</span>
                        </a>
                        <a href="{{ route('admin.cheques.index') }}"
                            class="flex h-12 w-15 flex-col items-center justify-center gap-1 rounded-lg text-slate-600 transition-all hover:bg-blue-50 hover:text-blue-600">
                            <i class="fa-solid fa-vault text-base"></i>
                            <span class="text-[10px] font-medium leading-none">Cheque Log</span>
                        </a>
                    </div>
                    <span class="text-center text-[9px] font-bold uppercase tracking-wider text-slate-400">Payment</span>
                </div>
                @endif
            </div>
        </header>
        @endif

        <!-- Main Dynamic Body Area -->
        <main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">
            <div class="mx-auto max-w-7xl space-y-6">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Alpine Plugins & Core -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/persist@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>

</html>