<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    /**
     * Display the unified Customer Ledger Workspace.
     */
    public function index()
    {
        // Using ->get() instead of ->paginate() to cleanly load the full datatable list 
        // on the left panel, matching your AJAX dashboard layout perfectly.
        $customers = Customer::latest()->get();
        
        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new customer.
     * Modified to load a dedicated creation page.
     */
    public function create()
    {
        // अब यो मेथडले छुट्टै create view लोड गर्छ
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
     * Display the dynamic detail HTML side panel via AJAX hook.
     */
    public function show($id)
    {
        // Fetch customer along with their related invoices/sales histories
        $customer = Customer::with(['invoices' => function($query) {
            $query->latest()->limit(5); // Pulls top 5 recent customer transactions
        }])->findOrFail($id);

        // Compute aggregate metrics
        $totalInwardOrders = $customer->invoices->count();
        $totalSpendings    = $customer->invoices->sum('grand_total'); // Matches your custom tracking column
        $outstandingDues   = $customer->previous_due ?? 0.00;

        // Return a partial view file slice instead of a full layout structure
        return view('admin.customers.partials.details-card', compact(
            'customer', 
            'totalInwardOrders', 
            'totalSpendings', 
            'outstandingDues'
        ));
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
    public function showLedger($id)
{
    // कस्टमर खोज्ने
    $customer = Customer::findOrFail($id);
    
    // ट्रान्जेक्सनहरू खोज्ने (तपाईंको डेटाबेसको 'transactions' टेबल अनुसार)
    $transactions = $customer->transactions()->orderBy('created_at', 'asc')->get();

    // अब यसले 'admin.customers.ledger' भ्यू खोल्छ
    // तपाईंको फाइलको लोकेसन अनुसार नाम सच्याउनुहोस् (जस्तै: 'admin.ledger.ledger')
    return view('admin.ledger.ledger', compact('customer', 'transactions'));
}
}