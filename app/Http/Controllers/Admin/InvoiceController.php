<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Browsershot\Browsershot;
use App\Models\Invoice;
use App\Models\InventoryAdjustment; 
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage; // Ensured Storage facade is imported for your image generation

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices.
     */
    public function index()
    {
        $invoices = Invoice::orderBy('invoice_date', 'desc')->paginate(15);
        return view('admin.invoices.index', compact('invoices'));
    }

    /**
     * Show the form for creating a new invoice.
     */
    public function create()
    {
        // 1. Fetch Customers
        $customers = Customer::all();

        // 2. Fetch Products with stock
        $products = Product::where('stock', '>', 0)->get();

        // 3. Generate Invoice Number
        $invoiceNo = 'INV-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

        // 4. Set Nepali Date
        $nepaliDate = "2083-02-29"; 

        return view('admin.invoices.create', compact('customers', 'products', 'invoiceNo', 'nepaliDate'));
    }

    /**
     * Store a newly created invoice in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $invoice = Invoice::create([
                    'invoice_no'      => $request->invoice_no,
                    'invoice_date'    => $request->invoice_date,
                    'customer_id'     => $request->customer_id,
                    'patient_name'    => $request->patient_name ?? 'Walk-in',
                    'patient_address' => $request->patient_address ?? 'N/A',
                    'patient_city'    => $request->patient_city ?? 'N/A',
                    'grand_total'     => $request->grand_total,
                ]);

                foreach ($request->items as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    $qty = ($item['unit'] === 'g') ? ($item['qty'] / 1000) : $item['qty'];
                    
                    if ($product->stock < $qty) throw new \Exception("Insufficient stock for {$product->name}");

                    $product->decrement('stock', $qty);
                    $invoice->items()->create([
                        'product_id' => $item['product_id'],
                        'qty'        => $item['qty'],
                        'unit'       => $item['unit'],
                        'price'      => $product->selling_price
                    ]);
                }
            });
            return response()->json(['success' => true, 'redirect' => route('admin.invoices.index')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function generateShareableImage($id)
    {
        $invoice = \App\Models\Invoice::with('customer')->findOrFail($id);
        
        // 1. Render the HTML view as a string
        $html = view('admin.pdf.statement', compact('invoice'))->render();

        // 2. Define the path
        $fileName = 'Statement_' . $invoice->invoice_no . '_' . time() . '.png';
        $directory = 'public/shares';
        
        // Ensure directory exists
        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }
        
        $path = storage_path('app/' . $directory . '/' . $fileName);

        // 3. Generate the PNG Image
        Browsershot::html($html)
            ->setScreenshotType('png')
            ->windowSize(1080, 1350)
            ->deviceScaleFactor(2)
            ->save($path);

        // 4. Return the public URL for the share link
        return asset('storage/shares/' . $fileName);
    }

    /**
     * Generate a secure single-use share token link for an invoice.
     */
    public function generateShareLink(Invoice $invoice)
    {
        try {
            // Reset visibility parameter whenever admin creates a fresh communication share link
            $invoice->update(['is_shared_viewed' => false]);

            // Construct secure single-use token mapping strings
            $payload = $invoice->id . '-' . time() . '-' . uniqid();
            $token = Crypt::encryptString($payload);

            return response()->json([
                'success' => true,
                'share_url' => route('invoice.public_share', ['token' => $token])
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Token encryption error.'], 500);
        }
    }

    /**
     * Display the public shared view of the invoice using a secure token.
     */
    public function showWebInvoice(Invoice $invoice)
    {
        $relatedAdjustments = \App\Models\InventoryAdjustment::where('reference_note', 'LIKE', '%' . $invoice->invoice_no . '%')
            ->with('product')
            ->get();

        $calculatedSubtotal = $relatedAdjustments->sum(function($item) {
            return $item->quantity * $item->unit_cost;
        });

        return view('admin.invoices.web_share', compact('invoice', 'relatedAdjustments', 'calculatedSubtotal'));
    }

    /**
     * ==========================================
     * ADDED: Customer Financial & Invoice Ledger
     * ==========================================
     */
    public function customerLedger($customer_id)
    {
        // 1. Fetch target customer data details safely
        $customer = Customer::findOrFail($customer_id);
        $customerName = $customer->name;

        // 2. Fetch customer invoices and EAGER LOAD internal row line items and nested product relations
        $customerInvoices = Invoice::where('customer_id', $customer_id)
            ->with('items.product') 
            ->latest()
            ->get();

        // 3. Fallback ledger tracking queries if applicable for transaction grids
        $ledgerLogs = DB::table('ledger_logs')->where('customer_id', $customer_id)->get(); 

        return view('admin.sales.customer-ledger', compact('customerInvoices', 'customerName', 'customer', 'ledgerLogs'));
    }
} // <-- This is the last curly brace closing the entire Controller class