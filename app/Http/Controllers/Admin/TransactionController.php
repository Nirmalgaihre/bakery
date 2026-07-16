<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    // List all transactions
    public function index()
    {
        $transactions = Transaction::latest()->paginate(20);
        return view('admin.transactions.index', compact('transactions'));
    }

    // Save transaction and update product stock
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'partner_name' => 'required|string',
            'transaction_type' => 'required|in:inward,outward',
            'quantity' => 'required|numeric|min:0',
            'rate' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            // Create the record
            Transaction::create([
                'product_id' => $request->product_id,
                'partner_name' => $request->partner_name,
                'transaction_type' => $request->transaction_type,
                'quantity' => $request->quantity,
                'rate' => $request->rate,
                'transaction_date' => now(),
            ]);

            // Update product stock
            $product = Product::find($request->product_id);
            if ($request->transaction_type == 'inward') {
                $product->stock += $request->quantity;
            } else {
                $product->stock -= $request->quantity;
            }
            $product->save();
        });

        return back()->with('success', 'Transaction recorded successfully!');
    }

    // Show the Tally-style analysis report
    public function showReport($productId)
    {
        $product = Product::findOrFail($productId);
        $transactions = Transaction::where('product_id', $productId)
                                    ->orderBy('transaction_date', 'asc')
                                    ->get();

        return view('admin.reports.movement', compact('product', 'transactions'));
    }
}