@extends('layouts.admin')

@section('title', 'Add New Item - Admin Console | Deurali Chemicals Pvt Ltd')
@section('panel_title', 'Admin Product Catalog Panel')

@section('content')
<div class="max-w-4xl w-full mx-auto">

    @if($errors->any())
    <div class="mb-5 p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 rounded shadow-xs text-sm">
        <div class="font-semibold mb-1 flex items-center gap-2 text-rose-700">
            <i class="fa-solid fa-triangle-exclamation"></i> Form Validation Failed:
        </div>
        <ul class="list-disc list-inside space-y-0.5 text-xs">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form id="product-form" action="{{ route('admin.products.store') }}" method="POST"
        class="bg-white border border-slate-200 rounded-lg shadow-xs overflow-hidden">
        @csrf

        <div
            class="p-4 px-5 border-b border-slate-100 bg-slate-50/70 text-xs font-bold text-slate-600 tracking-wider uppercase flex items-center gap-2">
            <i class="fa-solid fa-pen-to-square text-blue-600"></i> New Product Definition Form
        </div>

        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="space-y-1">
                    <label for="item_code" class="block text-xs font-bold text-slate-700 tracking-wide uppercase">Item
                        Code *</label>
                    <input type="text" name="item_code" id="item_code" value="{{ old('item_code') }}"
                        placeholder="e.g. DC-001"
                        class="w-full text-sm p-2 px-3 border rounded outline-none @error('item_code') border-red-500 @else border-slate-200 @enderror"
                        required>
                </div>

                <div class="space-y-1 md:col-span-2">
                    <label for="name" class="block text-xs font-bold text-slate-700 tracking-wide uppercase">Product
                        Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                        placeholder="Enter chemical name..."
                        class="w-full text-sm p-2 px-3 border rounded outline-none @error('name') border-red-500 @else border-slate-200 @enderror"
                        required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="space-y-1">
                    <label for="category_id"
                        class="block text-xs font-bold text-slate-700 tracking-wide uppercase">Category *</label>
                    <select name="category_id" id="category_id"
                        class="w-full text-sm p-2 px-3 border border-slate-200 bg-white rounded outline-none" required>
                        <option value="">Select category...</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1">
                    <label for="supplier_id"
                        class="block text-xs font-bold text-slate-700 tracking-wide uppercase">Supplier <span
                            class="text-slate-400 normal-case">(optional)</span></label>
                    <select name="supplier_id" id="supplier_id"
                        class="w-full text-sm p-2 px-3 border border-slate-200 bg-white rounded outline-none">
                        <option value="">Select supplier...</option>
                        @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1">
                    <label for="inventory_unit"
                        class="block text-xs font-bold text-slate-700 tracking-wide uppercase">Inventory Unit *</label>
                    <select name="inventory_unit" id="inventory_unit"
                        class="w-full text-sm p-2 px-3 border border-slate-200 bg-white rounded outline-none" required>
                        <option value="kg" {{ old('inventory_unit') == 'kg' ? 'selected' : '' }}>Kilogram (kg)</option>
                        <option value="bottle" {{ old('inventory_unit') == 'bottle' ? 'selected' : '' }}>Bottle</option>
                        <option value="boxes" {{ old('inventory_unit') == 'boxes' ? 'selected' : '' }}>Boxes</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="space-y-1">
                    <label for="purchase_cost"
                        class="block text-xs font-bold text-slate-700 tracking-wide uppercase">Purchase Cost (Rs.)
                        *</label>
                    <input type="number" name="purchase_cost" id="purchase_cost" value="{{ old('purchase_cost') }}"
                        step="0.01" class="w-full text-sm p-2 px-3 border border-slate-200 rounded outline-none"
                        required>
                </div>
                <div class="space-y-1">
                    <label for="selling_price"
                        class="block text-xs font-bold text-slate-700 tracking-wide uppercase">Selling Price (Rs.)
                        *</label>
                    <input type="number" name="selling_price" id="selling_price" value="{{ old('selling_price') }}"
                        step="0.01" class="w-full text-sm p-2 px-3 border border-slate-200 rounded outline-none"
                        required>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div class="space-y-1">
                        <label for="color"
                            class="block text-xs font-bold text-slate-700 tracking-wide uppercase">Color</label>
                        <input type="text" name="color" id="color" value="{{ old('color') }}"
                            class="w-full text-sm p-2 px-3 border border-slate-200 rounded outline-none">
                    </div>
                    <div class="space-y-1">
                        <label for="size"
                            class="block text-xs font-bold text-slate-700 tracking-wide uppercase">Size</label>
                        <input type="text" name="size" id="size" value="{{ old('size') }}"
                            class="w-full text-sm p-2 px-3 border border-slate-200 rounded outline-none">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 bg-slate-50 p-4 border rounded-md">
                <div class="space-y-1">
                    <label for="initial_stock"
                        class="block text-xs font-bold text-slate-700 tracking-wide uppercase">Initial Stock *</label>
                    <input type="number" name="initial_stock" id="initial_stock" value="{{ old('initial_stock', 0) }}"
                        class="w-full text-sm p-2 px-3 border border-slate-200 rounded outline-none" required>
                </div>
                <div class="space-y-1">
                    <label for="alert_stock_level"
                        class="block text-xs font-bold text-slate-700 tracking-wide uppercase">Alert Threshold *</label>
                    <input type="number" name="alert_stock_level" id="alert_stock_level"
                        value="{{ old('alert_stock_level', 5) }}"
                        class="w-full text-sm p-2 px-3 border border-slate-200 rounded outline-none" required>
                </div>
            </div>
        </div>

        <div class="p-4 bg-slate-50 border-t flex justify-end gap-3">
            <button type="reset"
                class="bg-white border px-4 py-2 rounded text-xs font-bold text-slate-600 uppercase">Clear</button>
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-5 py-2 rounded uppercase flex items-center gap-1.5">
                <i class="fa-solid fa-square-plus"></i> Save Item File
            </button>
        </div>
    </form>
</div>
@endsection