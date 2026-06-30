<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cheque;
use Illuminate\Http\Request;
use Anuzpandey\LaravelNepaliDate\LaravelNepaliDate;

class ChequeController extends Controller
{
    public function index(Request $request)
    {
        $cheques = Cheque::query()
            ->when($request->search, fn($q, $s) => $q->where('cheque_no', 'like', "%$s%"))
            ->latest()
            ->paginate(10);
            
        return view('admin.cheques.index', compact('cheques'));
    }

    public function markAsRead($id) 
    {
        Cheque::where('id', $id)->update(['is_read' => true]);
        return back()->with('success', 'Notification acknowledged.');
    }

    public function create()
    {
        return view('admin.cheques.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cheque_no'        => 'required|unique:cheques,cheque_no',
            'bank_name'        => 'required|string',
            'party_name'       => 'required|string',
            'amount'           => 'required|numeric',
            'issue_date_bs'    => 'required',
            'maturity_date_bs' => 'required',
            'status'           => 'nullable|string',
            'remarks'          => 'nullable|string',
            'send_reminder'    => 'nullable|boolean',
        ]);

        $validated['issue_date_ad'] = LaravelNepaliDate::from($request->issue_date_bs)->toEnglishDate();
        $validated['maturity_date_ad'] = LaravelNepaliDate::from($request->maturity_date_bs)->toEnglishDate();
        $validated['status'] = $request->status ?? 'pending';
        $validated['send_reminder'] = $request->has('send_reminder');

        Cheque::create($validated);
        
        return redirect()->route('admin.cheques.index')->with('success', 'Cheque recorded.');
    }
}