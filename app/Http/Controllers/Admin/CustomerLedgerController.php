<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\LedgerEntry;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerLedgerController extends Controller
{
    /**
     * Display the customer ledger details.
     * भ्यू: resources/views/admin/ledger/show.blade.php
     */
    public function show($id)
    {
        $customer = Customer::findOrFail($id);
        
        // Ledger entries fetch गर्ने
        $ledgerLogs = LedgerEntry::where('customer_id', $id)
            ->orderBy('date', 'desc')
            ->get();

        return view('admin.ledger.show', compact('customer', 'ledgerLogs'));
    }

    /**
     * Payment भण्डारण गर्ने
     */
    public function storePayment(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'date'   => 'required|date',
        ]);

        $customer = Customer::findOrFail($id);

        DB::transaction(function () use ($request, $customer) {
            LedgerEntry::create([
                'customer_id'    => $customer->id,
                'date'           => $request->date,
                'type'           => 'PAYMENT',
                'reference_no'   => 'REC-' . time(),
                'debit'          => 0,
                'credit'         => $request->amount,
                'remarks'        => $request->remarks ?? 'Payment received',
            ]);

            // यहाँ तपाईंको customer table मा कुन कोलम छ ध्यान दिनुहोस् (due_amount वा balance)
            $customer->decrement('due_amount', $request->amount);
        });

        return back()->with('success', 'Payment recorded successfully.');
    }

    /**
     * यो 'admin.ledger.show' राउटमा प्रयोग हुन्छ
     */
    public function showCustomerLedger($id)
    {
        return $this->show($id); // माथिको show मेथड नै कल गर्छ
    }

    /**
     * फोन नम्बरबाट लेजर हेर्ने
     */
   // In App\Http\Controllers\Admin\CustomerLedgerController.php

public function showByPhone($phone)
    {
        // 1. Find the customer using the correct column name found in your DB
        $customer = Customer::where('phone_number', $phone)->firstOrFail();

        // 2. Fetch invoices for this customer
        $customerInvoices = Invoice::with('items')
            ->where('customer_id', $customer->id)
            ->orderBy('invoice_date', 'desc')
            ->get();

        $customerName = $customer->name;

        // 3. Return the view
return view('admin.sales.customer-ledger', compact('customerInvoices', 'customerName'));    }
}