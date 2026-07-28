<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\PurchasesExport;
use App\Imports\PurchasesImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseImportExportController extends Controller
{
    public function export(string $type)
    {
        $filename = 'purchases_' . now()->format('Y-m-d_His');

        if ($type === 'csv') {
            return Excel::download(new PurchasesExport, $filename . '.csv', \Maatwebsite\Excel\Excel::CSV);
        }

        return Excel::download(new PurchasesExport, $filename . '.xlsx');
    }

    public function importForm()
    {
        return view('admin.purchases.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        $import = new PurchasesImport;

        try {
            Excel::import($import, $request->file('file'));
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            return back()->withErrors(
                collect($failures)->map(fn($f) => "Row {$f->row()}: " . implode(', ', $f->errors()))->toArray()
            );
        }

        if ($import->failures()->isNotEmpty()) {
            return back()->with('import_warning', $import->failures()->count() . ' row(s) skipped due to validation errors.');
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Purchases imported successfully.');
    }
}