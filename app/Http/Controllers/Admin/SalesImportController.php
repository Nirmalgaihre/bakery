<?php

namespace App\Http\Controllers\Admin;

use App\Models\SalesImport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SalesImportController extends Controller
{
    public function index()
    {
        $imports = SalesImport::with('creator')->latest()->paginate(15);
        return view('admin.sales.imports.index', compact('imports'));
    }

    public function show(SalesImport $import)
    {
        return view('admin.sales.imports.show', compact('import'));
    }

    public function destroy(SalesImport $import)
    {
        $import->delete();

        return redirect()->route('admin.sales.imports.index')
            ->with('success', 'Import record deleted successfully!');
    }
}
