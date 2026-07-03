@extends('layouts.admin')

@section('title', 'Sales Executive Dashboard')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    :root {
        --bg-card-blue: linear-gradient(135deg, #4f46e5, #7c3aed);
        --bg-card-green: linear-gradient(135deg, #10b981, #059669);
        --bg-card-orange: linear-gradient(135deg, #f97316, #ea580c);
        --text-primary: #1f2937;
        --border-color: #e5e7eb;
    }

    .main { padding: 24px; background-color: #f9fafb; font-family: 'Inter', sans-serif; }

    /* Stats Grid */
    .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 24px; }
    .card { position: relative; overflow: hidden; height: 140px; border-radius: 20px; padding: 20px; color: #fff; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
    .card h4 { font-size: 0.75rem; font-weight: 600; opacity: 0.9; text-transform: uppercase; margin: 0; }
    .card h2 { font-size: 1.5rem; font-weight: 800; margin: 0; }
    .icon-box { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 12px; background: rgba(255,255,255,0.2); margin-bottom: 10px; }

    /* Dashboard Layout */
    .dashboard-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px; }
    .widget { background: #fff; padding: 22px; border-radius: 20px; border: 1px solid var(--border-color); }
    .widget-title { font-size: 0.95rem; font-weight: 700; color: var(--text-primary); margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #f3f4f6; }
    
    /* Chart Container */
    .chart-container { position: relative; height: 250px; width: 100%; }

    /* Tables */
    .gov-table { width: 100%; border-collapse: collapse; }
    .gov-table th { color: #6b7280; font-size: 0.7rem; text-transform: uppercase; padding: 12px; text-align: left; }
    .gov-table td { padding: 12px; border-bottom: 1px solid #f3f4f6; font-size: 0.9rem; }
    .tag { padding: 4px 10px; border-radius: 8px; font-size: 0.7rem; font-weight: 800; background: #eef2ff; color: #4f46e5; }
    
    @media (max-width: 992px) { .dashboard-grid { grid-template-columns: 1fr; } }
</style>

<div class="main">
    <div class="filter-bar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h2 style="font-size: 1.1rem; font-weight: 800; color: var(--text-primary);">SALES EXECUTIVE OVERVIEW</h2>
        <form action="{{ route('admin.sales.dashboard') }}" method="GET">
            <select name="range" onchange="this.form.submit()" class="bg-gray-50 border border-gray-300 text-sm rounded-lg p-2">
                <option value="today" {{ request('range') == 'today' ? 'selected' : '' }}>Today</option>
                <option value="7days" {{ request('range') == '7days' ? 'selected' : '' }}>Last 7 Days</option>
                <option value="1month" {{ request('range') == '1month' ? 'selected' : '' }}>Last Month</option>
            </select>
        </form>
    </div>

    <div class="stats">
        <div class="card" style="background: var(--bg-card-blue);">
            <div class="icon-box"><i class="fas fa-wallet"></i></div>
            <h4>Total Revenue</h4>
            <h2>Rs. {{ number_format($totalRevenue, 2) }}</h2>
        </div>
        <div class="card" style="background: var(--bg-card-green);">
            <div class="icon-box"><i class="fas fa-file-invoice"></i></div>
            <h4>Invoices Cleared</h4>
            <h2>{{ number_format($invoiceCount) }}</h2>
        </div>
        <div class="card" style="background: var(--bg-card-orange);">
            <div class="icon-box"><i class="fas fa-calendar-day"></i></div>
            <h4>Revenue Today</h4>
            <h2>Rs. {{ number_format($salesToday, 2) }}</h2>
        </div>
        <div class="card" style="background: var(--bg-card-blue); opacity: 0.8;">
            <div class="icon-box"><i class="fas fa-tag"></i></div>
            <h4>Avg Ticket</h4>
            <h2>Rs. {{ number_format($averageInvoice, 2) }}</h2>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="widget">
            <div class="widget-title">Revenue Distribution</div>
            <div class="chart-container">
                <canvas id="salesBarChart"></canvas>
            </div>
        </div>
        <div class="widget">
            <div class="widget-title">High-Volume Clientele</div>
            <table class="gov-table">
                <thead><tr><th>Customer</th><th>Location</th><th>Orders</th></tr></thead>
                <tbody>
                    @foreach($customers as $customer)
                    <tr>
                        <td class="font-semibold">{{ $customer->name }}</td>
                        <td class="text-gray-500">{{ $customer->address ?? 'N/A' }}</td>
                        <td><span class="tag">{{ $customer->invoices_count }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="widget">
        <div class="widget-title">Real-Time Invoice Audit Log</div>
        <table class="gov-table">
            <thead><tr><th>Invoice #</th><th>Customer</th><th>Total</th><th>Status</th></tr></thead>
            <tbody>
                @foreach($recentInvoices as $invoice)
                <tr>
                    <td class="font-bold text-indigo-600">{{ $invoice->invoice_no }}</td>
                    <td>{{ $invoice->customer->name ?? 'Walk-in' }}</td>
                    <td>Rs. {{ number_format($invoice->grand_total, 2) }}</td>
                    <td><span class="tag" style="background: #f3f4f6; color: #374151;">{{ ucfirst($invoice->status) }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('salesBarChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! Js::from($chartData->pluck('date')) !!},
            datasets: [{
                data: {!! Js::from($chartData->pluck('total')) !!},
                backgroundColor: '#4f46e5',
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        }
    });
});
</script>
@endsection