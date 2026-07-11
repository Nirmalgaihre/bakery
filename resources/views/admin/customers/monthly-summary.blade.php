@extends('layouts.admin')

@section('title', 'Monthly Summary')

@section('content')
<div class="bg-white p-6 rounded shadow border border-slate-200">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-bold text-slate-800">
            Monthly Summary: {{ $customer->name }} (FY {{ $fiscalYear }})
        </h2>
        <a href="{{ route('admin.customers.index', ['fiscal_year' => $fiscalYear]) }}" 
           class="text-xs bg-slate-100 px-3 py-1 rounded hover:bg-slate-200 text-slate-600">
           Back to Ledger
        </a>
    </div>
    
    <table class="w-full border-collapse text-sm">
        <thead>
            <tr class="bg-slate-50 text-left border-b">
                <th class="p-3">Month</th>
                <th class="p-3 text-right">Net Transactions</th>
                <th class="p-3 text-right">Running Balance</th>
            </tr>
        </thead>
        <tbody>
            {{-- Opening Balance Row --}}
            <tr class="bg-slate-50/50">
                <td class="p-3 font-bold text-slate-600">Opening Balance</td>
                <td></td>
                <td class="p-3 text-right font-mono font-bold text-slate-800">
                    NPR {{ number_format($openingBalance, 2) }}
                </td>
            </tr>

            @php 
                $runningBalance = $openingBalance; 
                $totalTransactions = 0;
            @endphp

            @foreach($nepaliMonths as $monthNum => $monthName)
                @php 
                    $monthlyTotal = $monthlyData->get($monthNum, 0);
                    $runningBalance += $monthlyTotal;
                    $totalTransactions += $monthlyTotal;
                @endphp
                
                <tr class="border-t hover:bg-indigo-50 cursor-pointer transition-colors" 
                    onclick="window.location.href='{{ route('admin.customers.month-invoices', [$customer->id, $monthNum]) }}?fiscal_year={{ $fiscalYear }}'">
                    
                    <td class="p-3 font-medium text-slate-700">{{ $monthName }}</td>
                    <td class="p-3 text-right font-mono text-slate-600">
                        {{ number_format($monthlyTotal, 2) }}
                    </td>
                    <td class="p-3 text-right font-mono font-bold text-slate-800">
                        {{ number_format($runningBalance, 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        
        {{-- Grand Total Footer --}}
        <tfoot class="bg-slate-50 border-t-2 border-slate-200">
            <tr>
                <td class="p-3 font-bold text-slate-800">GRAND TOTAL</td>
                <td class="p-3 text-right font-mono font-bold text-slate-900">
                    NPR {{ number_format($totalTransactions, 2) }}
                </td>
                <td class="p-3 text-right font-mono font-bold text-slate-900">
                    NPR {{ number_format($runningBalance, 2) }}
                </td>
            </tr>
        </tfoot>
    </table>
</div>
@endsection