@extends('layouts.admin')

@section('title', 'Add New Item - Admin Console | Deurali Chemicals Pvt Ltd')
@section('panel_title', 'Admin Product Catalog Panel')

@section('content')
<div class="max-w-4xl w-full mx-auto">
    
    {{-- Form Validation Failure Warning Window --}}
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

    {{-- Interactive Product Entry Engine Form --}}
    <form action="{{ route('admin.products.store') }}" method="POST" class="bg-white border border-slate-200 rounded-lg shadow-xs overflow-hidden">
        @csrf
        
        <div class="p-4 px-5 border-b border-slate-100 bg-slate-50/70 text-xs font-bold text-slate-600 tracking-wider uppercase flex items-center gap-2">
            <i class="fa-solid fa-pen-to-square text-blue-600"></i> New Product Definition Form
        </div>

        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Product Title String --}}
                <div class="space-y-1">
                    <label for="name" class="block text-xs font-bold text-slate-700 tracking-wide uppercase">Product Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Enter chemical or product label..." 
                        class="w-full text-sm p-2 px-3 border rounded outline-none transition-all @error('name') border-red-500 focus:ring-1 focus:ring-red-500 @else border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @enderror" required>
                    @error('name') <p class="text-xs text-red-500 mt-0.5 font-medium">{{ $message }}</p> @enderror
                </div>

                {{-- Dynamic Sector Category Dropdown Block Selector --}}
                <div class="space-y-1">
                    <label for="category_id" class="block text-xs font-bold text-slate-700 tracking-wide uppercase">Category *</label>
                    <select name="category_id" id="category_id" class="w-full text-sm p-2 px-3 border bg-white outline-none transition-all @error('category_id') border-red-500 focus:ring-1 focus:ring-red-500 @else border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @enderror" required>
                        <option value="" disabled {{ old('category_id') == '' ? 'selected' : '' }}>Select system master category...</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="text-xs text-red-500 mt-0.5 font-medium">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                {{-- Purchase Cost Value --}}
                <div class="space-y-1">
                    <label for="purchase_cost" class="block text-xs font-bold text-slate-700 tracking-wide uppercase">Purchase Cost (NPR) *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rs.</span>
                        <input type="number" name="purchase_cost" id="purchase_cost" value="{{ old('purchase_cost') }}" step="0.01" min="0" placeholder="0.00" 
                            class="w-full text-sm p-2 pl-9 pr-3 border rounded outline-none transition-all @error('purchase_cost') border-red-500 focus:ring-1 focus:ring-red-500 @else border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @enderror" required>
                    </div>
                    @error('purchase_cost') <p class="text-xs text-red-500 mt-0.5 font-medium">{{ $message }}</p> @enderror
                </div>

                {{-- Selling Price Value --}}
                <div class="space-y-1">
                    <label for="selling_price" class="block text-xs font-bold text-slate-700 tracking-wide uppercase">Selling Price (NPR) *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rs.</span>
                        <input type="number" name="selling_price" id="selling_price" value="{{ old('selling_price') }}" step="0.01" min="0" placeholder="0.00" 
                            class="w-full text-sm p-2 pl-9 pr-3 border rounded outline-none transition-all @error('selling_price') border-red-500 focus:ring-1 focus:ring-red-500 @else border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @enderror" required>
                    </div>
                    @error('selling_price') <p class="text-xs text-red-500 mt-0.5 font-medium">{{ $message }}</p> @enderror
                </div>

                {{-- Custom Packaging Inventory Dropdown Unit Selection --}}
                <div class="space-y-1">
                    <label for="inventory_unit" class="block text-xs font-bold text-slate-700 tracking-wide uppercase">Default Inventory Unit *</label>
                    <select name="inventory_unit" id="inventory_unit" class="w-full text-sm p-2 px-3 border border-slate-200 rounded bg-white outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all" required>
                        <option value="" disabled {{ old('inventory_unit') == '' ? 'selected' : '' }}>Choose warehouse unit...</option>
                        <option value="kg" {{ old('inventory_unit') == 'kg' ? 'selected' : '' }}>Kilogram (kg)</option>
                        <option value="paau" {{ old('inventory_unit') == 'paau' ? 'selected' : '' }}>Paau</option>
                        <option value="bottle" {{ old('inventory_unit') == 'bottle' ? 'selected' : '' }}>Bottle</option>
                        <option value="cartoon" {{ old('inventory_unit') == 'cartoon' ? 'selected' : '' }}>Cartoon</option>
                        <option value="boxes" {{ old('inventory_unit') == 'boxes' ? 'selected' : '' }}>Boxes</option>
                    </select>
                    @error('inventory_unit') <p class="text-xs text-red-500 mt-0.5 font-medium">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 bg-slate-50 p-4 border border-slate-200/60 rounded-md">
                {{-- Stock On Hand Count --}}
                <div class="space-y-1">
                    <label for="initial_stock" class="block text-xs font-bold text-slate-700 tracking-wide uppercase mb-2">Initial Stock Level *</label>
                    <input type="number" name="initial_stock" id="initial_stock" value="{{ old('initial_stock', 0) }}" min="0" class="w-full text-sm p-2 px-3 border border-slate-200 rounded outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all" required>
                    @error('initial_stock') <p class="text-xs text-red-500 mt-0.5 font-medium">{{ $message }}</p> @enderror
                </div>

                {{-- Minimum Warning Threshold Level --}}
                <div class="space-y-1">
                    <label for="alert_stock_level" class="block text-xs font-bold text-slate-700 tracking-wide uppercase mb-2">Alert Stock Level Threshold *</label>
                    <input type="number" name="alert_stock_level" id="alert_stock_level" value="{{ old('alert_stock_level', 5) }}" min="0" class="w-full text-sm p-2 px-3 border border-slate-200 rounded outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all" required>
                    @error('alert_stock_level') <p class="text-xs text-red-500 mt-0.5 font-medium">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Form Process Control Action Panel Footer --}}
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
            <button type="reset" class="bg-white border border-slate-200 hover:bg-slate-100 text-slate-600 font-semibold text-xs px-4 py-2 rounded transition-colors uppercase tracking-wide">Clear</button>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-5 py-2 rounded shadow-xs transition-colors uppercase tracking-wide flex items-center gap-1.5">
                <i class="fa-solid fa-square-plus"></i> Save Item File
            </button>
        </div>
    </form>
</div>
@endsection