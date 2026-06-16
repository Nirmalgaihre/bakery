<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\LedgerEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerLedgerController extends Controller
{
    /**
     * Display the customer ledger details.
     */
    public function show($id)
    {
        $customer = Customer::findOrFail($id);
        
        // Fetch ledger entries ordered by date
        $ledgerLogs = LedgerEntry::where('customer_id', $id)
            ->orderBy('date', 'desc')
            ->get();

        return view('admin.ledger.show', compact('customer', 'ledgerLogs'));
    }

    /**
     * Store a new payment and update customer balance.
     */
    public function storePayment(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'date'   => 'required|date',
            'method' => 'required|string',
        ]);

        $customer = Customer::findOrFail($id);

        DB::transaction(function () use ($request, $customer) {
            // 1. Create the ledger entry
            LedgerEntry::create([
                'customer_id'    => $customer->id,
                'date'           => $request->date,
                'type'           => 'PAYMENT',
                'reference_no'   => 'REC-' . time(), // Generates a unique token ref
                'debit'          => 0,
                'credit'         => $request->amount,
                'remarks'        => $request->remarks ?? 'Payment received',
            ]);

            // 2. Update the customer's total due amount
            $customer->due_amount -= $request->amount;
            $customer->save();
        });

        return back()->with('success', 'Payment of Rs. ' . number_format($request->amount, 2) . ' recorded successfully.');
    }
}