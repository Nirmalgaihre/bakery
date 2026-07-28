@extends('layouts.admin')

@section('title', 'Import Purchases - Admin Console')
@section('panel_title', 'Purchase Import Tool')

@section('content')
<div class="max-w-2xl w-full mx-auto">

    @if($errors->any())
    <div class="mb-5 p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 rounded shadow-xs text-sm">
        <div class="font-semibold mb-1 flex items-center gap-2 text-rose-700">
            <i class="fa-solid fa-triangle-exclamation"></i> Import Errors:
        </div>
        <ul class="list-disc list-inside space-y-0.5 text-xs">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(session('import_warning'))
    <div class="mb-5 p-4 bg-amber-50 border-l-4 border-amber-500 text-amber-800 rounded shadow-xs text-sm">
        {{ session('import_warning') }}
    </div>
    @endif

    @if(session('success'))
    <div class="mb-5 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded shadow-xs text-sm">
        {{ session('success') }}
    </div>
    @endif