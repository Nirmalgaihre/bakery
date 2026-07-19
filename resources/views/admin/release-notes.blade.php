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
                        Welcome to our very first version!
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="text-right hidden sm:block">
                        <p class="text-white text-2xl font-bold">v1.0.0</p>
                        <p class="text-blue-200 text-sm">Initial Release</p>
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
                    <p class="text-white text-2xl font-bold mt-1">1</p>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                    <p class="text-blue-200 text-xs font-medium uppercase tracking-wider">Core Features</p>
                    <p class="text-white text-2xl font-bold mt-1">5</p>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                    <p class="text-blue-200 text-xs font-medium uppercase tracking-wider">Total Commits</p>
                    <p class="text-white text-2xl font-bold mt-1">6</p>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                    <p class="text-blue-200 text-xs font-medium uppercase tracking-wider">Launch Date</p>
                    <p class="text-white text-2xl font-bold mt-1">July 18</p>
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
                        <option>v1.0.0 (Current)</option>
                    </select>
                    <select class="px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white">
                        <option>All Modules</option>
                        <option>Inventory</option>
                        <option>Authentication</option>
                        <option>Reports</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Timeline Container -->
        <div class="relative">
            <!-- Timeline Line -->
            <div class="absolute left-4 sm:left-6 top-0 bottom-0 w-0.5 bg-gradient-to-b from-blue-600 to-slate-200"></div>

            <!-- Version 1.0.0 -->
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
                                <span class="px-3 py-1 bg-blue-600 text-white text-xs font-bold rounded-full">v1.0.0</span>
                                <span class="flex items-center gap-2 text-slate-600 text-sm">
                                    <i class="fa-regular fa-calendar"></i>
                                    April 1, 2026
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold uppercase tracking-wider rounded-full border border-emerald-200 flex items-center gap-1">
                                    <i class="fa-solid fa-star text-[10px]"></i>
                                    Latest
                                </span>
                                <span class="px-3 py-1 bg-slate-100 text-slate-600 text-xs font-bold uppercase tracking-wider rounded-full border border-slate-200">
                                    Initial Release
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Version Content -->
                    <div class="p-5 sm:p-6">
                        <!-- Overview -->
                        <div class="mb-6 pb-6 border-b border-slate-100">
                            <p class="text-slate-700 text-sm leading-relaxed">
                                Welcome to the initial public deployment of the **Deurali Chemicals Inventory Management System**. 
                                This foundational version establishes stable, production-ready systems for day-to-day operations, complete with secure access logs, role privileges, and inventory structures.
                            </p>
                        </div>

                        <!-- Core Core Features -->
                        <div>
                            <div class="flex items-center gap-2 mb-4">
                                <div class="p-2 bg-blue-100 rounded-lg">
                                    <i class="fa-solid fa-sparkles text-blue-600 text-sm"></i>
                                </div>
                                <h3 class="text-base font-bold text-slate-900 uppercase tracking-wide">Core Modules Included</h3>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex gap-3 p-4 bg-slate-50 rounded-xl border border-slate-100">
                                    <div class="flex-shrink-0 w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white">
                                        <i class="fa-solid fa-boxes-stacked text-sm"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-semibold text-slate-900 mb-1">Inventory Control</h4>
                                        <p class="text-xs text-slate-600 leading-relaxed">Complete management system supporting comprehensive CRUD operations for chemical products.</p>
                                    </div>
                                </div>

                                <div class="flex gap-3 p-4 bg-slate-50 rounded-xl border border-slate-100">
                                    <div class="flex-shrink-0 w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white">
                                        <i class="fa-solid fa-user-shield text-sm"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-semibold text-slate-900 mb-1">Role-Based Security</h4>
                                        <p class="text-xs text-slate-600 leading-relaxed">Granular user authentication architecture governing access controls across system states.</p>
                                    </div>
                                </div>

                                <div class="flex gap-3 p-4 bg-slate-50 rounded-xl border border-slate-100">
                                    <div class="flex-shrink-0 w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white">
                                        <i class="fa-solid fa-chart-pie text-sm"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-semibold text-slate-900 mb-1">Metrics Dashboard</h4>
                                        <p class="text-xs text-slate-600 leading-relaxed">Central stats portal displaying real-time tracking points and core logistical operations indicators.</p>
                                    </div>
                                </div>

                                <div class="flex gap-3 p-4 bg-slate-50 rounded-xl border border-slate-100">
                                    <div class="flex-shrink-0 w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white">
                                        <i class="fa-solid fa-tags text-sm"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-semibold text-slate-900 mb-1">Categorization & Search</h4>
                                        <p class="text-xs text-slate-600 leading-relaxed">Normalized global indices enabling direct query sorting across multiple asset types.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 flex gap-3 p-4 bg-slate-50 rounded-xl border border-slate-100 w-full">
                                <div class="flex-shrink-0 w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white">
                                    <i class="fa-solid fa-file-invoice text-sm"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-semibold text-slate-900 mb-1">Historical Reporting</h4>
                                    <p class="text-xs text-slate-600 leading-relaxed">Structured reporting layout providing detailed records of asset counts and tracking history logs.</p>
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
                                    156 changes
                                </span>
                                <a href="https://instagram.com/gaihre_nirmal" target="_blank" class="text-xs text-slate-500 hover:text-blue-600 transition-colors flex items-center gap-1">
                                    <i class="fa-brands fa-instagram text-sm"></i>
                                    Deployed by Nirmal Gaihre
                                </a>
                            </div>
                            <span class="text-sm text-slate-400 font-medium">Production Build Stable</span>
                        </div>
                    </div>
                </div>
            </div>

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