@extends('layouts.admin')

@section('title', 'Cheque Ledger')
@section('panel_title', 'Cheque Management')

@section('content')

<link href="https://nepalidatepicker.sajanmaharjan.com.np/v5/nepali.datepicker/css/nepali.datepicker.v5.0.6.min.css" rel="stylesheet" type="text/css"/>

<div class="max-w-4xl mx-auto">
    <div class="bg-white border border-slate-200 rounded-md shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 bg-slate-50/50">
            <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <span class="w-1 h-4 bg-blue-600 inline-block rounded-xs"></span>
                Register New Financial Instrument
            </h2>
        </div>
        
        <form action="{{ route('admin.cheques.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-600 uppercase">Cheque Number *</label>
                    <input type="text" name="cheque_no" required value="{{ old('cheque_no') }}" 
                        placeholder="e.g. CHQ-987654"
                        class="w-full px-3 py-1.5 border border-slate-200 text-xs rounded focus:outline-none focus:border-blue-500">
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-600 uppercase">Amount (NPR) *</label>
                    <input type="number" name="amount" step="0.01" required value="{{ old('amount') }}" 
                        placeholder="0.00"
                        class="w-full px-3 py-1.5 border border-slate-200 text-xs rounded focus:outline-none focus:border-blue-500 font-mono">
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-600 uppercase">Bank Name *</label>
                    <input type="text" name="bank_name" required value="{{ old('bank_name') }}" 
                        placeholder="e.g. Nabil Bank Ltd."
                        class="w-full px-3 py-1.5 border border-slate-200 text-xs rounded focus:outline-none focus:border-blue-500">
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-600 uppercase">Party Name *</label>
                    <input type="text" name="party_name" required value="{{ old('party_name') }}" 
                        placeholder="e.g. Kathmandu Pastry House"
                        class="w-full px-3 py-1.5 border border-slate-200 text-xs rounded focus:outline-none focus:border-blue-500">
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-600 uppercase">Issue Date (BS) *</label>
                    <input type="text" id="issue_date_bs" name="issue_date_bs" required readonly
                        placeholder="YYYY-MM-DD"
                        class="w-full px-3 py-1.5 border border-slate-200 text-xs rounded bg-white cursor-pointer focus:border-blue-500 font-mono">
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-600 uppercase">Maturity Date (BS) *</label>
                    <input type="text" id="maturity_date_bs" name="maturity_date_bs" required readonly
                        placeholder="YYYY-MM-DD"
                        class="w-full px-3 py-1.5 border border-slate-200 text-xs rounded bg-white cursor-pointer focus:border-blue-500 font-mono">
                </div>

                <div class="space-y-1 sm:col-span-2">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="send_reminder" value="1" checked class="rounded border-slate-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-xs text-slate-700">Send an email reminder on the maturity date</span>
                    </label>
                </div>

                <div class="space-y-1 sm:col-span-2">
                    <label class="text-[11px] font-bold text-slate-600 uppercase">Remarks</label>
                    <textarea name="remarks" rows="2" placeholder="Enter any additional notes regarding this cheque..."
                        class="w-full px-3 py-1.5 border border-slate-200 text-xs rounded focus:outline-none focus:border-blue-500">{{ old('remarks') }}</textarea>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.cheques.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs uppercase rounded transition-colors">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase rounded transition-colors shadow-xs">Save Cheque</button>
            </div>
        </form>
    </div>
</div>

<script src="https://nepalidatepicker.sajanmaharjan.com.np/v5/nepali.datepicker/js/nepali.datepicker.v5.0.6.min.js" type="text/javascript"></script>
<script type="text/javascript">
    window.onload = function() {
        var issueInput = document.getElementById("issue_date_bs");
        var maturityInput = document.getElementById("maturity_date_bs");
        
        issueInput.nepaliDatePicker({ dateFormat: "%Y-%m-%d", closeOnDateSelect: true });
        maturityInput.nepaliDatePicker({ dateFormat: "%Y-%m-%d", closeOnDateSelect: true });
    };
</script>
@endsection