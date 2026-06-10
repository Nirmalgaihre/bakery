<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    /**
     * Display a listing of the customers.
     */
    public function index()
    {
        // Fetch customers sorted by the latest registration
        $customers = Customer::latest()->paginate(10);
        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create()
    {
        return view('admin.customers.create');
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name'         => 'required|string|max:255',
            'pan_number'   => 'nullable|string|max:50',
            'phone_number' => 'required|string|max:20',
            'previous_due' => 'nullable|numeric|min:0',
            'address'      => 'required|string',
        ]);

        try {
            if (empty($validatedData['previous_due'])) {
                $validatedData['previous_due'] = 0.00;
            }

            Customer::create($validatedData);

            return redirect()
                ->route('admin.customers.index')
                ->with('success', 'Customer account registered successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to register customer: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('admin.customers.edit', compact('customer'));
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $validatedData = $request->validate([
            'name'         => 'required|string|max:255',
            'pan_number'   => 'nullable|string|max:50',
            'phone_number' => 'required|string|max:20',
            'previous_due' => 'nullable|numeric|min:0',
            'address'      => 'required|string',
        ]);

        try {
            if (empty($validatedData['previous_due'])) {
                $validatedData['previous_due'] = 0.00;
            }

            $customer->update($validatedData);

            return redirect()
                ->route('admin.customers.index')
                ->with('success', 'Customer record updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update customer: ' . $e->getMessage());
        }
    }
}