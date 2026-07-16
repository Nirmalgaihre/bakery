@extends('layouts.admin')

@section('title', 'Release Notes - Deurali Chemicals')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100">

    <!-- Hero Header Section -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-3">
                        <div class="p-2 bg-white/20 rounded-lg">
                            <i class="fa-solid fa-rocket text-white text-xl"></i>
                        </div>
                        <h1 class="text-3xl sm:text-4xl font-bold text-white tracking-tight">Release Notes</h1>
                    </div>
                    <p class="text-blue-100 text-base sm:text-lg max-w-2xl">
                        Stay updated with the latest features, improvements, and bug fixes in Deurali Chemicals. 
                        We're continuously improving your experience.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="text-right hidden sm:block">
                        <p class="text-white text-2xl font-bold">v1.2.0</p>
                        <p class="text-blue-200 text-sm">Latest Version</p>
                    </div>
                    <div class="p-3 bg-white/20 rounded-xl">
                        <i class="fa-solid fa-code-branch text-white text-2xl"></i>
                    </div>
                </div>
            </div>
            
            <!-- Quick Stats -->
            <div class="mt-8 grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                    <p class="text-blue-200 text-xs font-medium uppercase tracking-wider">Total Releases</p>
                    <p class="text-white text-2xl font-bold mt-1">12</p>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                    <p class="text-blue-200 text-xs font-medium uppercase tracking-wider">New Features</p>
                    <p class="text-white text-2xl font-bold mt-1">47</p>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                    <p class="text-blue-200 text-xs font-medium uppercase tracking-wider">Bug Fixes</p>
                    <p class="text-white text-2xl font-bold mt-1">156</p>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                    <p class="text-blue-200 text-xs font-medium uppercase tracking-wider">Last Update</p>
                    <p class="text-white text-2xl font-bold mt-1">Jun 30</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
        
        <!-- Search & Filter Bar -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-8">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1 relative">
                    <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" 
                           placeholder="Search release notes..." 
                           class="w-full pl-10 pr-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>
                <div class="flex gap-2">
                    <select class="px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white">
                        <option>All Versions</option>
                        <option>v1.2.x</option>
                        <option>v1.1.x</option>
                        <option>v1.0.x</option>
                    </select>
                    <select class="px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white">
                        <option>All Types</option>
                        <option>Features</option>
                        <option>Improvements</option>
                        <option>Bug Fixes</option>
                        <option>Security</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Timeline Container -->
        <div class="relative">
            <!-- Timeline Line -->
            <div class="absolute left-4 sm:left-6 top-0 bottom-0 w-0.5 bg-gradient-to-b from-blue-600 via-indigo-500 to-slate-300"></div>

            <!-- Version 1.2.0 -->
            <div class="relative mb-12 pl-12 sm:pl-16">
                <!-- Timeline Dot -->
                <div class="absolute left-2 sm:left-4 top-6 w-6 h-6 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-full border-4 border-white shadow-lg flex items-center justify-center">
                    <div class="w-2 h-2 bg-white rounded-full"></div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-shadow duration-300">
                    <!-- Version Header -->
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-5 sm:px-6 py-4 border-b border-slate-200">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <span class="px-3 py-1 bg-blue-600 text-white text-xs font-bold rounded-full">v1.2.0</span>
                                <span class="flex items-center gap-2 text-slate-600 text-sm">
                                    <i class="fa-regular fa-calendar"></i>
                                    June 30, 2026
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold uppercase tracking-wider rounded-full border border-emerald-200 flex items-center gap-1">
                                    <i class="fa-solid fa-star text-[10px]"></i>
                                    Latest
                                </span>
                                <span class="text-xs text-slate-500 font-medium">Build 2026.06.30</span>
                            </div>
                        </div>
                    </div>

                    <!-- Version Content -->
                    <div class="p-5 sm:p-6">
                        <!-- Overview -->
                        <div class="mb-6 pb-6 border-b border-slate-100">
                            <p class="text-slate-700 text-sm leading-relaxed">
                                This release introduces the new <strong>Release Notes Dashboard</strong> for better transparency, 
                                along with significant performance improvements across the inventory management system. 
                                We've also enhanced security protocols and improved the overall user experience.
                            </p>
                        </div>

                        <!-- New Features -->
                        <div class="mb-6">
                            <div class="flex items-center gap-2 mb-4">
                                <div class="p-2 bg-emerald-100 rounded-lg">
                                    <i class="fa-solid fa-sparkles text-emerald-600 text-sm"></i>
                                </div>
                                <h3 class="text-base font-bold text-slate-900 uppercase tracking-wide">New Features</h3>
                            </div>
                            <div class="space-y-3">
                                <div class="flex gap-3 p-4 bg-emerald-50 rounded-xl border border-emerald-100">
                                    <div class="flex-shrink-0 w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                                        <i class="fa-solid fa-file-lines text-emerald-600 text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-semibold text-slate-900 mb-1">Release Notes Dashboard</h4>
                                        <p class="text-xs text-slate-600 leading-relaxed">
                                            New centralized page to track all system updates, improvements, and bug fixes with a clean timeline view.
                                        </p>
                                    </div>
                                </div>
                                <div class="flex gap-3 p-4 bg-emerald-50 rounded-xl border border-emerald-100">
                                    <div class="flex-shrink-0 w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                                        <i class="fa-solid fa-chart-line text-emerald-600 text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-semibold text-slate-900 mb-1">Advanced Analytics Widgets</h4>
                                        <p class="text-xs text-slate-600 leading-relaxed">
                                            Added 6 new dashboard widgets for real-time inventory tracking, sales trends, and chemical usage patterns.
                                        </p>
                                    </div>
                                </div>
                                <div class="flex gap-3 p-4 bg-emerald-50 rounded-xl border border-emerald-100">
                                    <div class="flex-shrink-0 w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                                        <i class="fa-solid fa-bell text-emerald-600 text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-semibold text-slate-900 mb-1">Low Stock Notifications</h4>
                                        <p class="text-xs text-slate-600 leading-relaxed">
                                            Automated email and in-app alerts when inventory levels fall below defined thresholds.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Improvements -->
                        <div class="mb-6">
                            <div class="flex items-center gap-2 mb-4">
                                <div class="p-2 bg-blue-100 rounded-lg">
                                    <i class="fa-solid fa-arrow-trend-up text-blue-600 text-sm"></i>
                                </div>
                                <h3 class="text-base font-bold text-slate-900 uppercase tracking-wide">Improvements</h3>
                            </div>
                            <div class="space-y-2">
                                <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-slate-50 transition-colors">
                                    <i class="fa-solid fa-check text-blue-500 mt-0.5 text-xs"></i>
                                    <span class="text-sm text-slate-700">20% faster inventory load times through database query optimization</span>
                                </div>
                                <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-slate-50 transition-colors">
                                    <i class="fa-solid fa-check text-blue-500 mt-0.5 text-xs"></i>
                                    <span class="text-sm text-slate-700">Enhanced search functionality with 40% better accuracy in product discovery</span>
                                </div>
                                <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-slate-50 transition-colors">
                                    <i class="fa-solid fa-check text-blue-500 mt-0.5 text-xs"></i>
                                    <span class="text-sm text-slate-700">Improved mobile responsiveness for all admin dashboard views</span>
                                </div>
                                <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-slate-50 transition-colors">
                                    <i class="fa-solid fa-check text-blue-500 mt-0.5 text-xs"></i>
                                    <span class="text-sm text-slate-700">Streamlined navigation menu with better icon visibility and hover states</span>
                                </div>
                            </div>
                        </div>

                        <!-- Bug Fixes -->
                        <div class="mb-6">
                            <div class="flex items-center gap-2 mb-4">
                                <div class="p-2 bg-amber-100 rounded-lg">
                                    <i class="fa-solid fa-bug text-amber-600 text-sm"></i>
                                </div>
                                <h3 class="text-base font-bold text-slate-900 uppercase tracking-wide">Bug Fixes</h3>
                            </div>
                            <div class="space-y-2">
                                <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-slate-50 transition-colors">
                                    <i class="fa-solid fa-wrench text-amber-500 mt-0.5 text-xs"></i>
                                    <span class="text-sm text-slate-700">Fixed authentication redirect issues on session timeout</span>
                                </div>
                                <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-slate-50 transition-colors">
                                    <i class="fa-solid fa-wrench text-amber-500 mt-0.5 text-xs"></i>
                                    <span class="text-sm text-slate-700">Resolved PDF export formatting problems in billing reports</span>
                                </div>
                                <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-slate-50 transition-colors">
                                    <i class="fa-solid fa-wrench text-amber-500 mt-0.5 text-xs"></i>
                                    <span class="text-sm text-slate-700">Corrected date display issues in Filipino locale settings</span>
                                </div>
                            </div>
                        </div>

                        <!-- Security Updates -->
                        <div>
                            <div class="flex items-center gap-2 mb-4">
                                <div class="p-2 bg-red-100 rounded-lg">
                                    <i class="fa-solid fa-shield-halved text-red-600 text-sm"></i>
                                </div>
                                <h3 class="text-base font-bold text-slate-900 uppercase tracking-wide">Security Updates</h3>
                            </div>
                            <div class="space-y-2">
                                <div class="flex items-start gap-3 p-3 rounded-lg bg-red-50 border border-red-100">
                                    <i class="fa-solid fa-lock text-red-500 mt-0.5 text-xs"></i>
                                    <span class="text-sm text-slate-700">Enhanced CSRF token validation across all forms</span>
                                </div>
                                <div class="flex items-start gap-3 p-3 rounded-lg bg-red-50 border border-red-100">
                                    <i class="fa-solid fa-lock text-red-500 mt-0.5 text-xs"></i>
                                    <span class="text-sm text-slate-700">Implemented rate limiting on login and password reset endpoints</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Version Footer -->
                    <div class="px-5 sm:px-6 py-4 bg-slate-50 border-t border-slate-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <span class="text-xs text-slate-500">
                                    <i class="fa-solid fa-code-commit mr-1"></i>
                                    47 changes
                                </span>
                                <span class="text-xs text-slate-500">
                                    <i class="fa-solid fa-user mr-1"></i>
                                    Deployed by DevOps Team
                                </span>
                            </div>
                            <a href="#" class="text-sm text-blue-600 hover:text-blue-700 font-medium hover:underline flex items-center gap-1">
                                View Details <i class="fa-solid fa-arrow-right text-xs"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Version 1.1.0 -->
            <div class="relative mb-12 pl-12 sm:pl-16">
                <!-- Timeline Dot -->
                <div class="absolute left-2 sm:left-4 top-6 w-6 h-6 bg-gradient-to-br from-orange-400 to-amber-500 rounded-full border-4 border-white shadow-lg flex items-center justify-center">
                    <div class="w-2 h-2 bg-white rounded-full"></div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-shadow duration-300">
                    <!-- Version Header -->
                    <div class="bg-gradient-to-r from-orange-50 to-amber-50 px-5 sm:px-6 py-4 border-b border-slate-200">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <span class="px-3 py-1 bg-orange-500 text-white text-xs font-bold rounded-full">v1.1.0</span>
                                <span class="flex items-center gap-2 text-slate-600 text-sm">
                                    <i class="fa-regular fa-calendar"></i>
                                    May 15, 2026
                                </span>
                            </div>
                            <span class="text-xs text-slate-500 font-medium">Build 2026.05.15</span>
                        </div>
                    </div>

                    <!-- Version Content -->
                    <div class="p-5 sm:p-6">
                        <!-- Overview -->
                        <div class="mb-6 pb-6 border-b border-slate-100">
                            <p class="text-slate-700 text-sm leading-relaxed">
                                Focused on export functionality improvements and critical authentication fixes. 
                                This release also includes performance optimizations for reporting modules.
                            </p>
                        </div>

                        <!-- Updates -->
                        <div class="mb-6">
                            <div class="flex items-center gap-2 mb-4">
                                <div class="p-2 bg-blue-100 rounded-lg">
                                    <i class="fa-solid fa-sync text-blue-600 text-sm"></i>
                                </div>
                                <h3 class="text-base font-bold text-slate-900 uppercase tracking-wide">Updates</h3>
                            </div>
                            <div class="space-y-2">
                                <div class="flex gap-3 p-4 bg-blue-50 rounded-xl border border-blue-100">
                                    <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <i class="fa-solid fa-file-export text-blue-600 text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-semibold text-slate-900 mb-1">Faster Billing Report Exports</h4>
                                        <p class="text-xs text-slate-600 leading-relaxed">
                                            Optimized PDF and Excel export generation with 3x faster processing for large datasets.
                                        </p>
                                    </div>
                                </div>
                                <div class="flex gap-3 p-4 bg-blue-50 rounded-xl border border-blue-100">
                                    <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <i class="fa-solid fa-wrench text-amber-600 text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-semibold text-slate-900 mb-1">Authentication System Fixes</h4>
                                        <p class="text-xs text-slate-600 leading-relaxed">
                                            Resolved critical redirect bugs affecting user sessions during login and password reset flows.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bug Fixes -->
                        <div>
                            <div class="flex items-center gap-2 mb-4">
                                <div class="p-2 bg-amber-100 rounded-lg">
                                    <i class="fa-solid fa-bug text-amber-600 text-sm"></i>
                                </div>
                                <h3 class="text-base font-bold text-slate-900 uppercase tracking-wide">Bug Fixes</h3>
                            </div>
                            <div class="space-y-2">
                                <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-slate-50 transition-colors">
                                    <i class="fa-solid fa-wrench text-amber-500 mt-0.5 text-xs"></i>
                                    <span class="text-sm text-slate-700">Fixed dashboard widget loading issues on slow connections</span>
                                </div>
                                <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-slate-50 transition-colors">
                                    <i class="fa-solid fa-wrench text-amber-500 mt-0.5 text-xs"></i>
                                    <span class="text-sm text-slate-700">Corrected calculation errors in chemical quantity reports</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Version Footer -->
                    <div class="px-5 sm:px-6 py-4 bg-slate-50 border-t border-slate-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <span class="text-xs text-slate-500">
                                    <i class="fa-solid fa-code-commit mr-1"></i>
                                    23 changes
                                </span>
                                <span class="text-xs text-slate-500">
                                    <i class="fa-solid fa-user mr-1"></i>
                                    Deployed by DevOps Team
                                </span>
                            </div>
                            <a href="#" class="text-sm text-blue-600 hover:text-blue-700 font-medium hover:underline flex items-center gap-1">
                                View Details <i class="fa-solid fa-arrow-right text-xs"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Version 1.0.0 -->
            <div class="relative pl-12 sm:pl-16">
                <!-- Timeline Dot -->
                <div class="absolute left-2 sm:left-4 top-6 w-6 h-6 bg-gradient-to-br from-slate-400 to-slate-500 rounded-full border-4 border-white shadow-lg flex items-center justify-center">
                    <div class="w-2 h-2 bg-white rounded-full"></div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-shadow duration-300">
                    <!-- Version Header -->
                    <div class="bg-gradient-to-r from-slate-50 to-slate-100 px-5 sm:px-6 py-4 border-b border-slate-200">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <span class="px-3 py-1 bg-slate-600 text-white text-xs font-bold rounded-full">v1.0.0</span>
                                <span class="flex items-center gap-2 text-slate-600 text-sm">
                                    <i class="fa-regular fa-calendar"></i>
                                    April 1, 2026
                                </span>
                            </div>
                            <span class="px-3 py-1 bg-slate-100 text-slate-600 text-xs font-bold uppercase tracking-wider rounded-full border border-slate-200">
                                Initial Release
                            </span>
                        </div>
                    </div>

                    <!-- Version Content -->
                    <div class="p-5 sm:p-6">
                        <!-- Overview -->
                        <div class="mb-6 pb-6 border-b border-slate-100">
                            <p class="text-slate-700 text-sm leading-relaxed">
                                The initial public release of Deurali Chemicals inventory management system. 
                                This foundational version includes core inventory tracking, user management, and basic reporting features.
                            </p>
                        </div>

                        <!-- Features -->
                        <div class="space-y-2">
                            <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-slate-50 transition-colors">
                                <i class="fa-solid fa-check text-slate-500 mt-0.5 text-xs"></i>
                                <span class="text-sm text-slate-700">Complete inventory management system with CRUD operations</span>
                            </div>
                            <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-slate-50 transition-colors">
                                <i class="fa-solid fa-check text-slate-500 mt-0.5 text-xs"></i>
                                <span class="text-sm text-slate-700">User authentication with role-based access control</span>
                            </div>
                            <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-slate-50 transition-colors">
                                <i class="fa-solid fa-check text-slate-500 mt-0.5 text-xs"></i>
                                <span class="text-sm text-slate-700">Basic dashboard with key metrics and statistics</span>
                            </div>
                            <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-slate-50 transition-colors">
                                <i class="fa-solid fa-check text-slate-500 mt-0.5 text-xs"></i>
                                <span class="text-sm text-slate-700">Chemical categorization and search functionality</span>
                            </div>
                            <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-slate-50 transition-colors">
                                <i class="fa-solid fa-check text-slate-500 mt-0.5 text-xs"></i>
                                <span class="text-sm text-slate-700">Simple reporting for inventory levels and usage history</span>
                            </div>
                        </div>
                    </div>

                    <!-- Version Footer -->
                    <div class="px-5 sm:px-6 py-4 bg-slate-50 border-t border-slate-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <span class="text-xs text-slate-500">
                                    <i class="fa-solid fa-code-commit mr-1"></i>
                                    156 changes
                                </span>
                                <span class="text-xs text-slate-500">
                                    <i class="fa-solid fa-user mr-1"></i>
                                    Deployed by DevOps Team
                                </span>
                            </div>
                            <span class="text-sm text-slate-400">Initial Release</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Load More -->
        <div class="mt-12 text-center">
            <button class="px-8 py-3 bg-white border border-slate-300 text-slate-700 font-medium rounded-xl hover:bg-slate-50 hover:border-slate-400 transition-all shadow-sm hover:shadow">
                Load Older Releases
                <i class="fa-solid fa-chevron-down ml-2 text-xs"></i>
            </button>
        </div>
    </div>

    <!-- Footer CTA -->
    <div class="bg-white border-t border-slate-200 mt-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <h3 class="text-base font-semibold text-slate-900">Found an issue or have a feature request?</h3>
                    <p class="text-sm text-slate-600 mt-1">We're always working to improve Deurali Chemicals.</p>
                </div>
                <div class="flex gap-3">
                    <a href="#" class="px-5 py-2.5 bg-slate-100 text-slate-700 font-medium rounded-lg hover:bg-slate-200 transition text-sm">
                        <i class="fa-solid fa-bug mr-2"></i>
                        Report Bug
                    </a>
                    <a href="#" class="px-5 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition text-sm">
                        <i class="fa-solid fa-lightbulb mr-2"></i>
                        Request Feature
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection