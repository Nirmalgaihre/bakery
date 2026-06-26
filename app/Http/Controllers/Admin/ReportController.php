<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
{
    // कतिवटा डेटा देखाउने (डिफल्ट १५, नत्र प्रयोगकर्ताले रोजेको)
    $perPage = $request->input('per_page', 15);

    $customers = Customer::query();

    if ($request->filled('search')) {
        $searchTerm = $request->search;
        $customers->where(function($query) use ($searchTerm) {
            $query->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('phone_number', 'like', '%' . $searchTerm . '%');
        });
    }

    $customers = $customers->orderBy('name')->paginate($perPage);

    return view('admin.reports.report', compact('customers'));
}
}