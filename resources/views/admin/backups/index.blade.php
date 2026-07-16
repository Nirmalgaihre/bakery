@extends('layouts.admin')

@section('title', 'Create System Backup - Deurali Chemicals')
@section('panel_title', 'Backup & Data Management')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    {{-- Alert Messages --}}
    @if(session('success'))
    <div
        class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-xs font-semibold text-emerald-800 flex items-center gap-2">
        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div
        class="bg-rose-50 border border-rose-200 rounded-xl p-4 text-xs font-semibold text-rose-800 flex items-center gap-2">
        <i class="fa-solid fa-circle-xmark text-rose-600 text-sm"></i> {{ session('error') }}
    </div>
    @endif

    <div
        class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-base font-bold text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-cloud-arrow-up text-blue-600"></i> Create System Backup
            </h3>
            <p class="text-xs text-slate-500 mt-1">Generate snapshot archives of your database files and warehouse
                inventories.</p>
        </div>
        <div class="flex items-center gap-2 text-xs font-mono bg-slate-50 border border-slate-200 rounded-lg p-2 px-3">
            <span class="h-2 w-2 bg-emerald-500 rounded-full animate-pulse"></span>
            <span class="text-slate-600">Storage Node:</span>
            <span class="font-bold text-slate-800">LOCAL_DISK (Encrypted)</span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <div class="md:col-span-2 bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-6">
            <h4 class="text-xs font-bold text-slate-400 tracking-wider uppercase border-b border-slate-100 pb-3">
                Backup Options
            </h4>

            <form action="{{ route('admin.backups.store') }}" method="POST" class="space-y-5">
                @csrf

                <div class="space-y-3">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide">Backup Target
                        Scope</label>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label
                            class="relative flex items-start p-4 rounded-xl border border-blue-100 bg-blue-50/30 cursor-pointer hover:bg-blue-50/50 transition-colors">
                            <div class="flex items-center h-5">
                                <input type="radio" name="backup_scope" value="all" checked
                                    class="h-4 w-4 text-blue-600 border-slate-300 focus:ring-blue-50">
                            </div>
                            <div class="ml-3">
                                <span class="block text-xs font-bold text-slate-800">Complete System Backup</span>
                                <span class="block text-[11px] text-slate-500 mt-0.5">Database tables, ledger logs, and
                                    media assets.</span>
                            </div>
                        </label>

                        <label
                            class="relative flex items-start p-4 rounded-xl border border-slate-200 bg-white cursor-pointer hover:bg-slate-50/50 transition-colors">
                            <div class="flex items-center h-5">
                                <input type="radio" name="backup_scope" value="db_only"
                                    class="h-4 w-4 text-blue-600 border-slate-300 focus:ring-blue-50">
                            </div>
                            <div class="ml-3">
                                <span class="block text-xs font-bold text-slate-800">Database SQL Only</span>
                                <span class="block text-[11px] text-slate-500 mt-0.5">Lightweight dump of inventories
                                    and sales ledgers.</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="prefix" class="block text-xs font-bold text-slate-700 uppercase tracking-wide">Custom
                        Filename Prefix (Optional)</label>
                    <div class="relative rounded-lg shadow-sm max-w-md">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-slate-400 text-xs font-mono">dc_pkg_</span>
                        </div>
                        <input type="text" name="prefix" id="prefix" placeholder="manual_dump"
                            class="block w-full pl-16 pr-3 py-2 text-xs border border-slate-200 bg-slate-50/50 rounded-lg focus:outline-none focus:border-blue-500 focus:bg-white font-mono placeholder-slate-300">
                    </div>
                    <p class="text-[10px] text-slate-400">System appends current timestamp automatically: <span
                            class="font-mono">dc_pkg_manual_dump_all_{{ date('Ymd') }}.zip</span></p>
                </div>

                <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                    <div
                        class="flex items-center gap-2 text-amber-600 bg-amber-50 rounded-md p-1.5 px-2.5 text-[10px] font-semibold">
                        <i class="fa-solid fa-triangle-exclamation"></i> May temporarily slow intensive background
                        queries.
                    </div>

                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2.5 rounded-lg transition-all shadow-sm flex items-center gap-2">
                        <i class="fa-solid fa-play"></i> Initialize Backup Process
                    </button>
                </div>
            </form>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm space-y-4">
                <h4 class="text-xs font-bold text-slate-400 tracking-wider uppercase border-b border-slate-100 pb-2">
                    Storage Target
                </h4>

                <div class="space-y-3">
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500">Local System Disk</span>
                        <span class="font-mono font-bold text-slate-700">storage/app/backups</span>
                    </div>

                    <div class="space-y-1">
                        <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                            <div class="bg-blue-600 h-full rounded-full"
                                style="width: {{ $diskDetails['percentage'] }}%"></div>
                        </div>
                        <div class="flex justify-between text-[10px] text-slate-400 font-mono">
                            <span>Used: {{ $diskDetails['used'] }} ({{ $diskDetails['percentage'] }}%)</span>
                            <span>Free: {{ $diskDetails['free'] }}</span>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-slate-50/80 border border-slate-100 rounded-xl p-3 text-[11px] space-y-1.5 text-slate-600">
                    <div class="flex items-center justify-between text-slate-800 font-medium">
                        <span>Last Backup Status</span>
                        @if($diskDetails['last_backup'])
                        <span class="text-emerald-600 font-bold"><i class="fa-solid fa-circle-check"></i> Success</span>
                        @else
                        <span class="text-slate-400 font-bold">No Records</span>
                        @endif
                    </div>
                    <p class="font-mono text-[10px] text-slate-400">Timestamp:
                        {{ $diskDetails['last_backup'] ? $diskDetails['last_backup']->created_at->format('Y-m-d h:i A') : 'N/A' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h4 class="text-xs font-bold text-slate-400 tracking-wider uppercase">
                Available Snapshot Archives
            </h4>
            <span class="bg-slate-100 text-slate-600 text-[10px] font-mono px-2 py-0.5 rounded-md font-semibold">Total:
                {{ count($backups) }} Local Files</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="bg-slate-50/70 border-b border-slate-100 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                        <th class="py-3 px-6">Archive Package Identifier</th>
                        <th class="py-3 px-6">Scope</th>
                        <th class="py-3 px-6">Size</th>
                        <th class="py-3 px-6">Generated At</th>
                        <th class="py-3 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700 font-medium">
                    @forelse($backups as $backup)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-3.5 px-6 font-mono text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-file-zipper text-amber-500"></i> {{ $backup->filename }}
                        </td>
                        <td class="py-3.5 px-6">
                            @if($backup->scope === 'Full Backup')
                            <span
                                class="bg-blue-50 text-blue-700 text-[10px] px-2 py-0.5 rounded font-bold uppercase tracking-wide">Full
                                Backup</span>
                            @else
                            <span
                                class="bg-purple-50 text-purple-700 text-[10px] px-2 py-0.5 rounded font-bold uppercase tracking-wide">DB
                                Only</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-6 font-mono text-slate-500">{{ $backup->size }}</td>
                        <td class="py-3.5 px-6 text-slate-500">{{ $backup->created_at->format('Y-m-d h:i A') }}</td>
                        <td class="py-3.5 px-6 text-right">
                            {{-- Delete form removed to prevent deletion --}}
                            <a href="{{ route('admin.backups.download', $backup->filename) }}" title="Download Archive"
                                class="text-slate-500 hover:text-blue-600 transition-colors p-1 inline-block">
                                <i class="fa-solid fa-download"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-slate-400 text-xs">
                            No backup archives found on this storage node infrastructure.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection