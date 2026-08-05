<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\PurchasesExport;
use App\Imports\PurchasesImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseImportExportController extends Controller
{
    /**
     * Export purchases to Excel or CSV.
     */
    public function export(string $type = 'xlsx')
    {
        $filename = 'purchases_' . now()->format('Y-m-d_His');

        if (strtolower($type) === 'csv') {
            return Excel::download(new PurchasesExport, $filename . '.csv', \Maatwebsite\Excel\Excel::CSV);
        }

        return Excel::download(new PurchasesExport, $filename . '.xlsx');
    }

    /**
     * Display the purchase import page.
     */
    public function importForm()
    {
        return view('admin.purchases.import');
    }

    /**
     * Alias for importForm to match routes targeting importPage.
     */
    public function importPage()
    {
        return $this->importForm();
    }

    /**
     * Handle the purchases file upload and import logic.
     */
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

        return redirect()->back()
            ->with('success', 'Purchases imported successfully.');
    }

    /**
     * Alias for import to match routes targeting importExcel.
     */
    public function importExcel(Request $request)
    {
        return $this->import($request);
    }
}