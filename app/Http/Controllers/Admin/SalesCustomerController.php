<?php

namespace App\Http\Controllers\Admin;

use App\Models\SalesCustomer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SalesCustomerController extends Controller
{
    public function index()
    {
        $customers = SalesCustomer::with(['invoices'])->latest()->paginate(15);
        return view('admin.sales.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('admin.sales.customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_code' => 'required|unique:sales_customers,customer_code|max:50',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'billing_address' => 'nullable|string',
            'shipping_address' => 'nullable|string',
            'gstin' => 'nullable|string|max:20',
            'credit_limit' => 'nullable|string|max:20',
            'opening_balance' => 'required|numeric',
            'balance_type' => 'required|in:Debit,Credit',
            'status' => 'required|in:Active,Inactive',
            'remarks' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();

        SalesCustomer::create($validated);

        return redirect()->route('admin.sales.customers.index')
            ->with('success', 'Customer created successfully!');
    }

    public function edit(SalesCustomer $customer)
    {
        return view('admin.sales.customers.edit', compact('customer'));
    }

    public function update(Request $request, SalesCustomer $customer)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_code' => 'required|unique:sales_customers,customer_code,' . $customer->id . '|max:50',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'billing_address' => 'nullable|string',
            'shipping_address' => 'nullable|string',
            'gstin' => 'nullable|string|max:20',
            'credit_limit' => 'nullable|string|max:20',
            'opening_balance' => 'required|numeric',
            'balance_type' => 'required|in:Debit,Credit',
            'status' => 'required|in:Active,Inactive',
            'remarks' => 'nullable|string',
        ]);

        $validated['updated_by'] = auth()->id();

        $customer->update($validated);

        return redirect()->route('admin.sales.customers.index')
            ->with('success', 'Customer updated successfully!');
    }

    public function destroy(SalesCustomer $customer)
    {
        if ($customer->invoices()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete customer with associated invoices!');
        }

        $customer->delete();

        return redirect()->route('admin.sales.customers.index')
            ->with('success', 'Customer deleted successfully!');
    }

    public function show(SalesCustomer $customer)
    {
        $invoices = $customer->invoices()->latest()->paginate(10);
        return view('admin.sales.customers.show', compact('customer', 'invoices'));
    }
}
