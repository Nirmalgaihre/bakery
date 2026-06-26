@extends('layouts.admin')

@section('title', isset($editingCategory) ? 'Edit Category' : 'Manage Categories')
@section('panel_title', 'Sector Category Registry')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Form Section -->
        <div class="md:col-span-1">
            <div class="bg-white border border-slate-200 rounded-lg shadow-xs p-6">
                <h3 class="text-xs font-bold text-slate-700 uppercase mb-4">
                    {{ isset($editingCategory) ? 'Edit Category' : 'Add New Category' }}
                </h3>
                
                <form action="{{ isset($editingCategory) ? route('admin.categories.update', $editingCategory->id) : route('admin.categories.store') }}" method="POST">
                    @csrf
                    @if(isset($editingCategory)) @method('PUT') @endif

                    <div class="space-y-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Category Name *</label>
                            <input type="text" name="name" value="{{ old('name', $editingCategory->name ?? '') }}" 
                                class="w-full text-sm p-2 border rounded border-slate-200 focus:border-blue-500 outline-none" required>
                            @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs py-2 rounded transition-colors">
                                {{ isset($editingCategory) ? 'Update' : 'Save' }}
                            </button>
                            @if(isset($editingCategory))
                                <a href="{{ route('admin.categories.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded">Cancel</a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table Section -->
        <div class="md:col-span-2">
            <div class="bg-white border border-slate-200 rounded-lg shadow-xs overflow-hidden">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-[10px] font-bold text-slate-500 uppercase">
                        <tr>
                            <th class="px-6 py-3">ID</th>
                            <th class="px-6 py-3">Category Name</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($categories as $category)
                        <tr class="{{ (isset($editingCategory) && $editingCategory->id == $category->id) ? 'bg-blue-50' : '' }}">
                            <td class="px-6 py-4 font-mono text-xs">{{ $category->id }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-800">{{ $category->name }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.categories.edit', $category->id) }}" class="text-blue-600 hover:text-blue-800 text-xs font-bold uppercase">Edit</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-slate-400 italic">No categories found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection