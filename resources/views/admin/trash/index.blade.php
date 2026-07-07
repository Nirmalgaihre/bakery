@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-xl font-bold text-white uppercase tracking-wider">Recycle Bin</h1>
            <p class="text-[11px] text-slate-500 uppercase mt-1">Manage and recover deleted records</p>
        </div>
        <button class="px-4 py-2 bg-red-900/30 text-red-400 text-[12px] font-bold rounded-md hover:bg-red-900/50 transition-all">
            <i class="fa-solid fa-trash-can-arrow-up mr-2"></i>Empty Trash
        </button>
    </div>

    <div class="bg-brandDarkLight/50 border border-slate-800 rounded-lg overflow-hidden">
        
        <div class="flex border-b border-slate-800 bg-brandDark/20">
            <button class="px-6 py-3 text-[12px] font-bold text-blue-400 border-b-2 border-blue-500 uppercase">Products</button>
            <button class="px-6 py-3 text-[12px] font-bold text-slate-500 hover:text-slate-300 uppercase">Sales</button>
            <button class="px-6 py-3 text-[12px] font-bold text-slate-500 hover:text-slate-300 uppercase">Invoices</button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-[13px] text-slate-400">
                <thead class="bg-slate-900/50 text-[11px] uppercase text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Item Name</th>
                        <th class="px-6 py-4">Category</th>
                        <th class="px-6 py-4">Deleted At</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    <tr class="hover:bg-slate-800/30 transition-colors">
                        <td class="px-6 py-4 font-medium text-slate-200">Sample Product Name</td>
                        <td class="px-6 py-4">Electronics</td>
                        <td class="px-6 py-4 text-slate-500">2 mins ago</td>
                        <td class="px-6 py-4 text-right">
                            <button class="text-blue-400 hover:text-blue-300 mr-3 text-[11px] font-bold uppercase transition-all">Restore</button>
                            <button class="text-red-500 hover:text-red-400 text-[11px] font-bold uppercase transition-all">Delete</button>
                        </td>
                    </tr>
                    </tbody>
            </table>
        </div>

        <div class="p-12 text-center">
            <i class="fa-solid fa-box-open text-4xl text-slate-800 mb-4"></i>
            <p class="text-slate-500 text-[13px]">No deleted items found in the trash.</p>
        </div>
    </div>
</div>
@endsection