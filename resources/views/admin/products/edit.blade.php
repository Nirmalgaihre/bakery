@extends('layouts.admin')

@section('title', 'Edit Product - ' . $product->name . ' | Admin Console')
@section('panel_title', 'Edit Product Catalog Item')

@section('content')
<div class="max-w-4xl mx-auto py-6">
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
        
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
            <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wider">
                <i class="fa-solid fa-pen-to-square mr-2"></i>Editing: {{ $product->name }}
            </h2>
            <a href="{{ route('admin.products.index') }}" class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-600 px-3 py-1 rounded">Back to List</a>
        </div>

        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <!-- Global Validation Error Alert Box -->
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-600 rounded text-sm">
                    <p class="font-bold mb-1">Please correct the following validation errors:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Row 1 -->
                <div class="space-y-1">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase">Product Name *</label>
                    <input type="text" name="name" class="w-full p-2 border rounded text-sm focus:ring-1 focus:ring-blue-500 outline-none" value="{{ old('name', $product->name) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase">Item Code *</label>
                    <input type="text" name="item_code" class="w-full p-2 border rounded text-sm focus:ring-1 focus:ring-blue-500 outline-none" value="{{ old('item_code', $product->item_code) }}" required>
                </div>

                <!-- Row 2 -->
                <div class="space-y-1">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase">Category *</label>
                    <select name="category_id" class="w-full p-2 border rounded text-sm bg-white focus:ring-1 focus:ring-blue-500 outline-none" required>
                        <option value="">-- Select a Category --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase">Inventory Unit *</label>
                    <select name="inventory_unit" class="w-full p-2 border rounded text-sm bg-white focus:ring-1 focus:ring-blue-500 outline-none" required>
                        <option value="">-- Select Unit --</option>
                        @foreach(['kg' => 'KG', 'paau' => 'Paau', 'bottle' => 'Bottle', 'cartoon' => 'Cartoon', 'boxes' => 'Boxes'] as $value => $label)
                            <option value="{{ $value }}" {{ old('inventory_unit', $product->inventory_unit) == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Row 3: Specs -->
                <div class="space-y-1">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase">Color</label>
                    <input type="text" name="color" class="w-full p-2 border rounded text-sm focus:ring-1 focus:ring-blue-500 outline-none" value="{{ old('color', $product->color) }}">
                </div>
                <div class="space-y-1">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase">Size</label>
                    <input type="text" name="size" class="w-full p-2 border rounded text-sm focus:ring-1 focus:ring-blue-500 outline-none" value="{{ old('size', $product->size) }}">
                </div>

                <!-- Row 4: Pricing -->
                <div class="space-y-1">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase">Purchase Cost *</label>
                    <input type="number" step="0.01" name="purchase_cost" class="w-full p-2 border rounded text-sm focus:ring-1 focus:ring-blue-500 outline-none" value="{{ old('purchase_cost', $product->purchase_cost) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase">Selling Price *</label>
                    <input type="number" step="0.01" name="selling_price" class="w-full p-2 border rounded text-sm focus:ring-1 focus:ring-blue-500 outline-none" value="{{ old('selling_price', $product->selling_price) }}" required>
                </div>

                <!-- Row 5: Stock Levels -->
                <div class="space-y-1">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase">Initial Stock *</label>
                    <input type="number" step="any" name="initial_stock" class="w-full p-2 border rounded text-sm focus:ring-1 focus:ring-blue-500 outline-none" value="{{ old('initial_stock', $product->initial_stock) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase">Alert Stock Level *</label>
                    <input type="number" step="any" name="alert_stock_level" class="w-full p-2 border rounded text-sm focus:ring-1 focus:ring-blue-500 outline-none" value="{{ old('alert_stock_level', $product->alert_stock_level) }}" required>
                </div>
            </div>

            <div class="mt-8 pt-4 border-t border-slate-100 flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-8 py-3 rounded shadow-md transition-all">
                    UPDATE PRODUCT DETAILS
                </button>
            </div>
        </form>
    </div>
</div>
@endsection