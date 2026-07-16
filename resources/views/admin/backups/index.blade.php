@extends('layouts.admin')

@section('title', 'Backup & Restore - Deurali Chemicals')
@section('panel_title', 'Backup & Data Management')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- Alert Messages --}}
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-sm font-semibold text-emerald-800 flex items-center gap-2">
        <i class="fa-solid fa-circle-check text-emerald-600"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="bg-rose-50 border border-rose-200 rounded-xl p-4 text-sm font-semibold text-rose-800 flex items-center gap-2">
        <i class="fa-solid fa-circle-xmark text-rose-600"></i> {{ session('error') }}
    </div>
    @endif

    {{-- Quick Actions Header --}}
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-database"></i>
                    Backup & Restore Center
                </h2>
                <p class="text-blue-100 text-sm mt-1">Manage your system backups, import data, and monitor storage.</p>
            </div>
            <div class="flex items-center gap-2">
                <div class="text-right hidden sm:block">
                    <p class="text-white text-sm font-bold">v1.2.0</p>
                    <p class="text-blue-200 text-xs">System Version</p>
                </div>
                <div class="p-3 bg-white/20 rounded-xl">
                    <i class="fa-solid fa-server text-white"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- IMPORT SECTION - TOP PRIORITY --}}
    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border-2 border-emerald-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-emerald-500 to-teal-500 px-6 py-4">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-file-import"></i>
                Import / Restore Backup
            </h3>
            <p class="text-emerald-100 text-xs mt-1">Upload and restore from a backup file</p>
        </div>

        <div class="p-6">
            <form action="{{ route('admin.backups.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                
                <div>
                    <label for="backup_file" class="block text-sm font-bold text-slate-700 mb-2">
                        Select Backup File
                    </label>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input type="file" 
                               name="backup_file" 
                               id="backup_file" 
                               accept=".zip,.sql"
                               required
                               class="block w-full text-sm text-slate-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-emerald-100 file:text-emerald-700 hover:file:bg-emerald-200 transition-colors cursor-pointer">
                        
                        <button type="submit" 
                                class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold px-6 py-2.5 rounded-lg transition-all shadow-md flex items-center justify-center gap-2 whitespace-nowrap">
                            <i class="fa-solid fa-upload"></i>
                            Upload & Restore
                        </button>
                    </div>
                    <p class="text-xs text-slate-500 mt-2 flex items-center gap-1">
                        <i class="fa-solid fa-circle-info text-emerald-500"></i>
                        Supported: <strong>.zip</strong> or <strong>.sql</strong> files (Max 100MB)
                    </p>
                </div>

                @if($errors->has('backup_file'))
                <div class="bg-rose-50 border border-rose-200 rounded-lg p-3 text-sm text-rose-700 flex items-center gap-2">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    {{ $errors->first('backup_file') }}
                </div>
                @endif
            </form>
        </div>
    </div>

    {{-- CREATE BACKUP & STORAGE INFO --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Create Backup Form --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-slate-200">
                <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-cloud-arrow-up text-blue-600"></i>
                    Create New Backup
                </h3>
                <p class="text-slate-500 text-xs mt-1">Generate a snapshot of your system data</p>
            </div>

            <div class="p-6">
                <form action="{{ route('admin.backups.store') }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-3">Backup Type</label>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="relative flex items-start p-4 rounded-xl border-2 border-blue-200 bg-blue-50 cursor-pointer hover:bg-blue-100 transition-all">
                                <div class="flex items-center h-5">
                                    <input type="radio" name="backup_scope" value="all" checked
                                        class="h-4 w-4 text-blue-600 border-slate-300 focus:ring-blue-500">
                                </div>
                                <div class="ml-3 flex-1">
                                    <span class="block text-sm font-bold text-slate-800">Complete System Backup</span>
                                    <span class="block text-xs text-slate-600 mt-1">Database + Files + Media</span>
                                </div>
                                <i class="fa-solid fa-box-archive text-blue-500 text-lg ml-auto"></i>
                            </label>

                            <label class="relative flex items-start p-4 rounded-xl border border-slate-200 bg-white cursor-pointer hover:bg-slate-50 transition-all">
                                <div class="flex items-center h-5">
                                    <input type="radio" name="backup_scope" value="db_only"
                                        class="h-4 w-4 text-blue-600 border-slate-300 focus:ring-blue-500">
                                </div>
                                <div class="ml-3 flex-1">
                                    <span class="block text-sm font-bold text-slate-800">Database Only</span>
                                    <span class="block text-xs text-slate-600 mt-1">Quick & Lightweight</span>
                                </div>
                                <i class="fa-solid fa-database text-slate-400 text-lg ml-auto"></i>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="prefix" class="block text-sm font-bold text-slate-700 mb-2">
                            Custom Name (Optional)
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-mono">dc_pkg_</span>
                            <input type="text" 
                                   name="prefix" 
                                   id="prefix" 
                                   placeholder="manual_backup"
                                   class="block w-full pl-16 pr-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono">
                        </div>
                        <p class="text-xs text-slate-500 mt-1.5">
                            <i class="fa-solid fa-clock"></i>
                            Auto-timestamp added: <span class="font-mono">_YYYYMMDD_HHMMSS</span>
                        </p>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                        <p class="text-xs text-amber-600 flex items-center gap-1.5">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            May slow down queries temporarily
                        </p>
                        
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-5 py-2.5 rounded-lg transition-all shadow-md flex items-center gap-2">
                            <i class="fa-solid fa-play"></i>
                            Start Backup
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Storage Info Card --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-slate-50 to-slate-100 px-5 py-4 border-b border-slate-200">
                <h4 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                    <i class="fa-solid fa-hard-drive text-slate-600"></i>
                    Storage Status
                </h4>
            </div>

            <div class="p-5 space-y-4">
                <div>
                    <div class="flex justify-between text-sm mb-1.5">
                        <span class="text-slate-600">Disk Usage</span>
                        <span class="font-mono font-bold text-slate-700">{{ $diskDetails['percentage'] }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 h-3 rounded-full overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-500 to-indigo-500 h-full rounded-full transition-all" 
                             style="width: {{ $diskDetails['percentage'] }}%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-slate-500 mt-1.5 font-mono">
                        <span>Used: {{ $diskDetails['used'] }}</span>
                        <span>Free: {{ $diskDetails['free'] }}</span>
                    </div>
                </div>

                <div class="bg-slate-50 rounded-xl p-4 space-y-2 border border-slate-100">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-slate-700">Last Backup</span>
                        @if($diskDetails['last_backup'])
                        <span class="text-emerald-600 text-xs font-bold flex items-center gap-1">
                            <i class="fa-solid fa-circle-check"></i>
                            Success
                        </span>
                        @else
                        <span class="text-slate-400 text-xs">No records</span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 font-mono">
                        {{ $diskDetails['last_backup'] ? $diskDetails['last_backup']->created_at->format('M d, Y h:i A') : 'N/A' }}
                    </p>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <h5 class="text-xs font-bold text-slate-600 mb-2.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-clock-rotate-left text-slate-500"></i>
                        Auto Schedule
                    </h5>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2.5 text-xs">
                            <div class="p-1.5 bg-blue-100 rounded">
                                <i class="fa-solid fa-database text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-slate-700 font-medium">System Backup</p>
                                <p class="text-slate-500 text-[10px]">8:00 PM Daily</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2.5 text-xs">
                            <div class="p-1.5 bg-amber-100 rounded">
                                <i class="fa-solid fa-envelope text-amber-600"></i>
                            </div>
                            <div>
                                <p class="text-slate-700 font-medium">Cheque Reminder</p>
                                <p class="text-slate-500 text-[10px]">8:00 AM Daily</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- BACKUP ARCHIVES LIST --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-slate-50 to-slate-100 px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h4 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                    <i class="fa-solid fa-box-archive text-slate-600"></i>
                    Backup Archives
                </h4>
                <p class="text-slate-500 text-xs mt-0.5">Download or delete existing backups</p>
            </div>
            <span class="bg-slate-200 text-slate-700 text-xs font-bold px-3 py-1 rounded-full">
                {{ count($backups) }} Files
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold uppercase tracking-wider text-slate-600">
                        <th class="py-3.5 px-6">Filename</th>
                        <th class="py-3.5 px-6">Type</th>
                        <th class="py-3.5 px-6">Size</th>
                        <th class="py-3.5 px-6">Created</th>
                        <th class="py-3.5 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($backups as $backup)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-4 px-6 font-mono text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-file-zipper text-amber-500"></i>
                            {{ $backup->filename }}
                        </td>
                        <td class="py-4 px-6">
                            @if($backup->scope === 'Full Backup')
                            <span class="bg-blue-100 text-blue-700 text-[10px] px-2.5 py-1 rounded-full font-bold uppercase tracking-wide">Full System</span>
                            @else
                            <span class="bg-purple-100 text-purple-700 text-[10px] px-2.5 py-1 rounded-full font-bold uppercase tracking-wide">Database Only</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 font-mono text-slate-600">{{ $backup->size }}</td>
                        <td class="py-4 px-6 text-slate-600">{{ $backup->created_at->format('M d, Y h:i A') }}</td>
                        <td class="py-4 px-6 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.backups.download', $backup->filename) }}" 
                                   title="Download"
                                   class="bg-blue-50 hover:bg-blue-100 text-blue-600 p-2 rounded-lg transition-colors">
                                    <i class="fa-solid fa-download"></i>
                                </a>
                                <form action="{{ route('admin.backups.destroy', $backup->filename) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Delete this backup permanently?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            title="Delete"
                                            class="bg-rose-50 hover:bg-rose-100 text-rose-600 p-2 rounded-lg transition-colors">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <i class="fa-solid fa-inbox text-slate-300 text-4xl"></i>
                                <p class="text-slate-500 text-sm">No backups available yet</p>
                                <p class="text-slate-400 text-xs">Create your first backup using the form above</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- IMPORT HISTORY --}}
    @if(\App\Models\BackupImport::latest()->first())
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-emerald-50 to-teal-50 px-6 py-4 border-b border-emerald-200">
            <h4 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-emerald-600"></i>
                Import History
            </h4>
            <p class="text-slate-500 text-xs mt-0.5">Recent backup restoration records</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold uppercase tracking-wider text-slate-600">
                        <th class="py-3.5 px-6">File</th>
                        <th class="py-3.5 px-6">Original Name</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6">Uploaded By</th>
                        <th class="py-3.5 px-6">Completed</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse(\App\Models\BackupImport::latest()->limit(5)->get() as $import)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-4 px-6 font-mono text-slate-900">{{ $import->filename }}</td>
                        <td class="py-4 px-6 text-slate-600">{{ $import->original_name }}</td>
                        <td class="py-4 px-6">
                            @if($import->status === 'completed')
                            <span class="bg-emerald-100 text-emerald-700 text-[10px] px-2.5 py-1 rounded-full font-bold uppercase tracking-wide flex items-center gap-1 w-fit">
                                <i class="fa-solid fa-check"></i>
                                Success
                            </span>
                            @elseif($import->status === 'failed')
                            <span class="bg-rose-100 text-rose-700 text-[10px] px-2.5 py-1 rounded-full font-bold uppercase tracking-wide flex items-center gap-1 w-fit">
                                <i class="fa-solid fa-xmark"></i>
                                Failed
                            </span>
                            @else
                            <span class="bg-amber-100 text-amber-700 text-[10px] px-2.5 py-1 rounded-full font-bold uppercase tracking-wide flex items-center gap-1 w-fit">
                                <i class="fa-solid fa-clock"></i>
                                Pending
                            </span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-slate-600">{{ $import->uploaded_by }}</td>
                        <td class="py-4 px-6 text-slate-500 text-xs">
                            {{ $import->completed_at ? $import->completed_at->format('M d, Y h:i A') : '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-slate-400 text-xs">
                            No import history available
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection