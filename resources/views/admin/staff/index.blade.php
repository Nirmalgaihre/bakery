@extends('layouts.admin')

@section('content')
<div class="container mx-auto py-6 px-4 max-w-7xl font-sans antialiased text-slate-600">

    @if(session('success'))
    <div
        class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium rounded-xl flex items-center shadow-sm">
        <i class="fa-solid fa-circle-check mr-2 text-emerald-500 text-base"></i>
        {{ session('success') }}
    </div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-lg font-bold text-slate-900 tracking-tight">Staff Directory & User Controls</h1>
            <p class="text-xs text-slate-400 mt-0.5">Manage administrative accounts, operators, and assign baseline
                privileges.</p>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
            <div class="relative w-full sm:w-64">
                <form method="GET" action="{{ route('admin.staff.index') }}">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search by name, email..."
                        class="w-full pl-9 pr-4 py-1.5 bg-white border border-slate-200 rounded-md text-xs shadow-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                </form>
            </div>

            <a href="{{ route('admin.staff.create') }}"
                class="w-full sm:w-auto text-center px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-md transition-colors shadow-sm flex items-center justify-center gap-1.5">
                <i class="fa-solid fa-user-plus text-[11px]"></i> Add New Staff
            </a>
        </div>
    </div>

    <div class="bg-white border border-slate-200/60 rounded-lg shadow-sm overflow-hidden">
        <table class="w-full border-collapse text-left text-xs whitespace-nowrap">
            <thead>
                <tr
                    class="bg-slate-50 border-b border-slate-200/80 text-slate-500 font-semibold uppercase tracking-wider text-[10px]">
                    <th class="py-3 px-4">Staff Operator Details</th>
                    <th class="py-3 px-4">Email Address</th>
                    <th class="py-3 px-4">Assigned Roles</th>
                    <th class="py-3 px-4">Account Created At</th>
                    <th class="py-3 px-4 text-center">Action Matrix</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700">
                @forelse($staffs as $staff)
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="py-3 px-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="h-7 w-7 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-600 font-bold uppercase text-[11px]">
                                {{ substr($staff->name, 0, 2) }}
                            </div>
                            <span class="font-medium text-slate-900">{{ $staff->name }}</span>
                        </div>
                    </td>

                    <td class="py-3 px-4 font-mono text-slate-500 text-[11px]">{{ $staff->email }}</td>

                    <td class="py-3 px-4">
                        @if(method_exists($staff, 'getRoleNames') && $staff->getRoleNames()->count() > 0)
                        @foreach($staff->getRoleNames() as $role)
                        <span
                            class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wide border 
                {{ $role == 'admin' ? 'bg-rose-50 text-rose-700 border-rose-100' : 'bg-blue-50 text-blue-700 border-blue-100' }}">
                            {{ $role }}
                        </span>
                        @endforeach
                        @else
                        <span
                            class="inline-flex items-center px-2 py-0.5 rounded text-[10px] bg-slate-100 text-slate-600 font-medium">
                            Standard Operator
                        </span>
                        @endif
                    </td>

                    <td class="py-3 px-4 font-mono text-slate-500">
                        {{ $staff->created_at ? $staff->created_at->format('Y-m-d H:i') : 'N/A' }}</td>

                    <td class="py-3 px-4 text-center space-x-1.5">
                        <a href="{{ route('admin.staff.edit', $staff->id) }}"
                            class="inline-flex items-center px-2 py-1 bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-700 text-[11px] font-medium rounded transition-colors shadow-xs">
                            <i class="fa-solid fa-user-pen mr-1 text-[10px] text-slate-500"></i> Edit
                        </a>

                        <form action="{{ route('admin.staff.destroy', $staff->id) }}" method="POST" class="inline-block"
                            onsubmit="return confirm('Are you sure you want to completely terminate this operator access profile?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="inline-flex items-center px-2 py-1 bg-rose-50 hover:bg-rose-100 border border-rose-100 text-rose-700 text-[11px] font-medium rounded transition-colors shadow-xs">
                                <i class="fa-solid fa-trash-can mr-1 text-[10px] text-rose-400"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-12 text-slate-400">
                        <i class="fa-solid fa-users-slash text-2xl block mb-2 text-slate-200"></i>
                        No staff operator records registered inside this directory yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($staffs->hasPages())
        <div class="bg-white px-4 py-3 border-t border-slate-100 sm:px-6">
            {{ $staffs->appends(request()->query())->links() }}
        </div>
        @endif

    </div>
</div>
@endsection