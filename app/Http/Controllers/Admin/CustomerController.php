<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\InvoiceItem;
use App\Helpers\FiscalYearHelper;
use Anuzpandey\LaravelNepaliDate\LaravelNepaliDate;

class CustomerController extends Controller
{
    /**
     * Display the unified Customer Ledger Workspace.
     */
    public function index(Request $request)
    {
        // Default to ongoing fiscal year if none selected
        $fiscalYear = $request->get('fiscal_year', FiscalYearHelper::getCurrentFiscalYear());
        $search = $request->get('search');
        
        $range = FiscalYearHelper::getFiscalYearDateRange($fiscalYear);
        $adStart = $range['ad_start'];
        $adEnd = $range['ad_end'];

        $customers = Customer::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('pan_number', 'like', "%{$search}%");
                });
            })
            ->withSum(['invoices as opening_balance' => function ($query) use ($adStart) {
                $query->where('invoice_date', '<', $adStart);
            }], 'grand_total')
            ->withSum(['invoices as net_transactions' => function ($query) use ($adStart, $adEnd) {
                $query->whereBetween('invoice_date', [$adStart, $adEnd]);
            }], 'grand_total')
            ->get();

        $fiscalYears = FiscalYearHelper::getFiscalYearList();

        return view('admin.customers.index', compact('customers', 'fiscalYear', 'fiscalYears', 'search'));
    }

    /**
     * Display the customer management list.
     * Passes $fiscalYear and $fiscalYears alongside $customers to prevent Blade undefined variable errors.
     */
    public function manage(Request $request)
    {
        $fiscalYear = $request->get('fiscal_year', FiscalYearHelper::getCurrentFiscalYear());
        $fiscalYears = FiscalYearHelper::getFiscalYearList();
        $search = $request->get('search');

        $customers = Customer::when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('pan_number', 'like', "%{$search}%")
                      ->orWhere('phone_number', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15);

        return view('admin.customers.manage', compact('customers', 'fiscalYear', 'fiscalYears', 'search'));
    }

    /**
     * Display monthly summary of customer transactions.
     */
    public function monthlySummary(Request $request, Customer $customer)
    {
        $fiscalYear = $request->get('fiscal_year', FiscalYearHelper::getCurrentFiscalYear());
        $range = FiscalYearHelper::getFiscalYearDateRange($fiscalYear);
        
        // Fetch transactions
        $invoices = $customer->invoices()
            ->whereBetween('invoice_date', [$range['ad_start'], $range['ad_end']])
            ->get();

        $monthlyData = $invoices->groupBy(function($invoice) {
            $nepaliDate = LaravelNepaliDate::from($invoice->invoice_date)->toNepaliDateArray();
            return (int) $nepaliDate->month;
        })->map(fn($group) => $group->sum('grand_total'));

        // Nepali Fiscal Year order: Shrawan (4) to Ashadh (3)
        $nepaliMonths = [
            4 => 'Shrawan', 5 => 'Bhadra', 6 => 'Ashwin', 7 => 'Kartik', 
            8 => 'Mangsir', 9 => 'Poush', 10 => 'Magh', 11 => 'Falgun', 
            12 => 'Chaitra', 1 => 'Baishakh', 2 => 'Jestha', 3 => 'Ashadh'
        ];

        $openingBalance = $customer->invoices()
            ->where('invoice_date', '<', $range['ad_start'])
            ->sum('grand_total');

        return view('admin.customers.monthly-summary', compact('customer', 'monthlyData', 'openingBalance', 'fiscalYear', 'nepaliMonths'));
    }

    /**
     * Display detailed invoices for a specific month.
     */
    public function monthInvoices(Request $request, Customer $customer, $month)
    {
        $fiscalYear = $request->get('fiscal_year', FiscalYearHelper::getCurrentFiscalYear());
        $range = FiscalYearHelper::getFiscalYearDateRange($fiscalYear);

        $customerInvoices = $customer->invoices()
            ->with('items')
            ->whereBetween('invoice_date', [$range['ad_start'], $range['ad_end']])
            ->get()
            ->filter(function($invoice) use ($month) {
                return (int) LaravelNepaliDate::from($invoice->invoice_date)->toNepaliDateArray()->month == (int)$month;
            });

        $nepaliMonths = [
            4 => 'Shrawan', 5 => 'Bhadra', 6 => 'Ashwin', 7 => 'Kartik', 
            8 => 'Mangsir', 9 => 'Poush', 10 => 'Magh', 11 => 'Falgun', 
            12 => 'Chaitra', 1 => 'Baishakh', 2 => 'Jestha', 3 => 'Ashadh'
        ];

        $monthName = $nepaliMonths[$month] ?? 'Unknown';
        $customerName = $customer->name;

        return view('admin.customers.month-invoices', compact(
            'customer', 'customerInvoices', 'monthName', 'fiscalYear', 'customerName'
        ));
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
     * Display the dynamic detail HTML side panel via AJAX hook.
     */
    public function show($id)
    {
        $customer = Customer::with(['invoices' => function($query) {
            $query->latest()->limit(5);
        }])->findOrFail($id);

        $totalInwardOrders = $customer->invoices->count();
        $totalSpendings    = $customer->invoices->sum('grand_total');
        $outstandingDues   = $customer->previous_due ?? 0.00;

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

    /**
     * Display customer ledger details.
     */
    public function showLedger($id)
    {
        $customer = Customer::findOrFail($id);
        $transactions = $customer->transactions()->orderBy('created_at', 'asc')->get();

        return view('admin.ledger.ledger', compact('customer', 'transactions'));
    }

   /**
 * Display all products purchased by a specific customer with itemized invoice due details.
 */
public function purchasedProducts(Request $request, Customer $customer)
{
    $fiscalYear = $request->get('fiscal_year', FiscalYearHelper::getCurrentFiscalYear());
    $search = $request->get('search');
    $statusFilter = $request->get('status'); // 'all', 'due', 'paid'
    
    $range = FiscalYearHelper::getFiscalYearDateRange($fiscalYear);

    // Fetch items with invoice relations
    $purchasedItems = InvoiceItem::whereHas('invoice', function ($query) use ($customer, $range, $statusFilter) {
            $query->where('customer_id', $customer->id)
                  ->whereBetween('invoice_date', [$range['ad_start'], $range['ad_end']]);
            
            // Filter by Payment Status if requested
            if ($statusFilter === 'due') {
                $query->whereRaw('grand_total > paid_amount');
            } elseif ($statusFilter === 'paid') {
                $query->whereRaw('grand_total <= paid_amount');
            }
        })
        ->when($search, function ($query) use ($search) {
            $query->where(function($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                  ->orWhereHas('product', function ($pQuery) use ($search) {
                      $pQuery->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('invoice', function ($iQuery) use ($search) {
                      $iQuery->where('invoice_number', 'like', "%{$search}%")
                             ->orWhere('invoice_no', 'like', "%{$search}%");
                  });
            });
        })
        ->with(['product', 'invoice'])
        ->latest()
        ->paginate(20);

    // Fiscal year customer summary stats
    $fiscalInvoices = $customer->invoices()
        ->whereBetween('invoice_date', [$range['ad_start'], $range['ad_end']])
        ->get();

    $totalQuantity     = InvoiceItem::whereHas('invoice', function ($q) use ($customer, $range) {
                             $q->where('customer_id', $customer->id)
                               ->whereBetween('invoice_date', [$range['ad_start'], $range['ad_end']]);
                         })->sum('qty');

    $totalAmountSpent  = $fiscalInvoices->sum('grand_total');
    $totalPaidAmount   = $fiscalInvoices->sum('paid_amount');
    $totalRemainingDue = max(0, $totalAmountSpent - $totalPaidAmount);

    $fiscalYears = FiscalYearHelper::getFiscalYearList();

    return view('admin.customers.purchased-products', compact(
        'customer',
        'purchasedItems',
        'fiscalYear',
        'fiscalYears',
        'search',
        'statusFilter',
        'totalQuantity',
        'totalAmountSpent',
        'totalPaidAmount',
        'totalRemainingDue'
    ));
}

}