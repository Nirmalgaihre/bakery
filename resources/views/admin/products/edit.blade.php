@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto py-6">
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
        
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
            <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wider">
                <i class="fa-solid fa-pen-to-square mr-2"></i>Edit Product: {{ $product->name }}
            </h2>
            <a href="{{ route('admin.products.index') }}" class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-600 px-3 py-1 rounded">Back to List</a>
        </div>

        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-500 uppercase">Product Name *</label>
                    <input type="text" name="name" class="w-full p-2.5 border rounded-md focus:ring-2 focus:ring-blue-500 outline-none" value="{{ old('name', $product->name) }}" required>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-500 uppercase">Selling Price</label>
                    <input type="number" name="selling_price" class="w-full p-2.5 border rounded-md focus:ring-2 focus:ring-blue-500 outline-none" value="{{ old('selling_price', $product->selling_price) }}">
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100 flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-6 py-2.5 rounded-md transition-all">
                    SAVE CHANGES
                </button>
            </div>
        </form>
    </div>
</div>
@endsection