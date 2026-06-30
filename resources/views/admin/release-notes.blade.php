@extends('layouts.admin')

@section('title', 'Release Notes - Deurali Chemicals')

@section('content')
<div class="min-h-screen bg-slate-50">

    <!-- Header -->
    <div class="border-b bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            <h1 class="text-3xl font-bold text-slate-900">
                Release Notes
            </h1>

            <p class="mt-2 text-slate-600">
                Track new features, improvements, bug fixes, and important updates to the Deurali Chemicals Admin System.
            </p>

        </div>
    </div>

    <!-- Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <div class="relative border-l-2 border-slate-200 pl-8 space-y-10">

            <!-- ================= Version 1.2.0 ================= -->
            <div class="relative">

                <span class="absolute -left-[42px] top-2 h-5 w-5 rounded-full border-4 border-white bg-blue-600 shadow"></span>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm">

                    <!-- Header -->
                    <div class="p-6 border-b">

                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                            <div>

                                <div class="flex items-center gap-3">

                                    <h2 class="text-xl font-bold text-slate-900">
                                        Version 1.2.0
                                    </h2>

                                    <span class="px-2.5 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold">
                                        Latest
                                    </span>

                                </div>

                                <p class="mt-1 text-sm text-slate-500">
                                    June 30, 2026
                                </p>

                            </div>

                        </div>

                    </div>

                    <!-- Body -->
                    <div class="p-6 space-y-6">

                        <!-- New -->
                        <div>

                            <span class="inline-flex items-center rounded-md bg-green-100 px-2 py-1 text-xs font-semibold uppercase tracking-wide text-green-700">
                                New
                            </span>

                            <ul class="mt-3 space-y-2">

                                <li class="flex items-start gap-3">
                                    <i class="fa-solid fa-circle-check text-green-600 mt-1"></i>
                                    <span class="text-slate-700">
                                        Added a dedicated <strong>Release Notes</strong> page in the admin panel.
                                    </span>
                                </li>

                            </ul>

                        </div>

                        <!-- Improved -->
                        <div>

                            <span class="inline-flex items-center rounded-md bg-blue-100 px-2 py-1 text-xs font-semibold uppercase tracking-wide text-blue-700">
                                Improved
                            </span>

                            <ul class="mt-3 space-y-2">

                                <li class="flex items-start gap-3">
                                    <i class="fa-solid fa-gauge-high text-blue-600 mt-1"></i>
                                    <span class="text-slate-700">
                                        Optimized dashboard inventory queries for approximately 20% faster loading.
                                    </span>
                                </li>

                            </ul>

                        </div>

                    </div>

                </div>

            </div>

            <!-- ================= Version 1.1.0 ================= -->
            <div class="relative">

                <span class="absolute -left-[42px] top-2 h-5 w-5 rounded-full border-4 border-white bg-orange-500 shadow"></span>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm">

                    <div class="p-6 border-b">

                        <h2 class="text-xl font-bold text-slate-900">
                            Version 1.1.0
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            May 15, 2026
                        </p>

                    </div>

                    <div class="p-6 space-y-6">

                        <div>

                            <span class="inline-flex rounded-md bg-blue-100 px-2 py-1 text-xs font-semibold uppercase tracking-wide text-blue-700">
                                Improved
                            </span>

                            <ul class="mt-3 space-y-2">

                                <li class="flex gap-3">
                                    <i class="fa-solid fa-circle-check text-blue-600 mt-1"></i>
                                    <span class="text-slate-700">
                                        Improved billing report export performance.
                                    </span>
                                </li>

                            </ul>

                        </div>

                        <div>

                            <span class="inline-flex rounded-md bg-amber-100 px-2 py-1 text-xs font-semibold uppercase tracking-wide text-amber-700">
                                Fixed
                            </span>

                            <ul class="mt-3 space-y-2">

                                <li class="flex gap-3">
                                    <i class="fa-solid fa-wrench text-amber-600 mt-1"></i>
                                    <span class="text-slate-700">
                                        Fixed login redirect issue after authentication.
                                    </span>
                                </li>

                            </ul>

                        </div>

                    </div>

                </div>

            </div>

            <!-- ================= Version 1.0.0 ================= -->
            <div class="relative">

                <span class="absolute -left-[42px] top-2 h-5 w-5 rounded-full border-4 border-white bg-slate-500 shadow"></span>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm">

                    <div class="p-6 border-b">

                        <h2 class="text-xl font-bold text-slate-900">
                            Version 1.0.0
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            April 12, 2026
                        </p>

                    </div>

                    <div class="p-6">

                        <span class="inline-flex rounded-md bg-slate-100 px-2 py-1 text-xs font-semibold uppercase tracking-wide text-slate-700">
                            Initial Release
                        </span>

                        <ul class="mt-4 grid gap-3 sm:grid-cols-2">

                            <li class="flex gap-3">
                                <i class="fa-solid fa-check text-green-600 mt-1"></i>
                                <span class="text-slate-700">
                                    Inventory Management
                                </span>
                            </li>

                            <li class="flex gap-3">
                                <i class="fa-solid fa-check text-green-600 mt-1"></i>
                                <span class="text-slate-700">
                                    POS System
                                </span>
                            </li>

                            <li class="flex gap-3">
                                <i class="fa-solid fa-check text-green-600 mt-1"></i>
                                <span class="text-slate-700">
                                    Billing & Invoicing
                                </span>
                            </li>

                            <li class="flex gap-3">
                                <i class="fa-solid fa-check text-green-600 mt-1"></i>
                                <span class="text-slate-700">
                                    Customer Management
                                </span>
                            </li>

                            <li class="flex gap-3">
                                <i class="fa-solid fa-check text-green-600 mt-1"></i>
                                <span class="text-slate-700">
                                    Reports & Analytics
                                </span>
                            </li>

                            <li class="flex gap-3">
                                <i class="fa-solid fa-check text-green-600 mt-1"></i>
                                <span class="text-slate-700">
                                    User & Role Management
                                </span>
                            </li>

                        </ul>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
@endsection