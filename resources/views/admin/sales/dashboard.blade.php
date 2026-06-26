@extends('layouts.admin')

@section('title', 'Sales Executive Dashboard')

@section('content')
<!-- Google Fonts & Chart.js Integration -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
:root {
    --bg-card-blue: linear-gradient(135deg, #4f46e5, #7c3aed);
    --bg-card-green: linear-gradient(135deg, #10b981, #059669);
    --bg-card-orange: linear-gradient(135deg, #f97316, #ea580c);
    --bg-card-purple: linear-gradient(135deg, #ec4899, #d946ef);
    --bg-card-red: linear-gradient(135deg, #ef4444, #b91c1c);
    --text-primary: #1f2937;
    --text-muted: #6b7280;
    --border-color: #e5e7eb;
    --glass-glow: 0 8px 32px 0 rgba(31, 38, 135, 0.05);
}

/* Base Layout Framework */
.main {
    padding: 16px;
    background-color: #f9fafb;
    font-family: 'Inter', sans-serif;
    box-sizing: border-box;
    min-height: 100vh;
}
@media (min-width: 768px) { .main { padding: 24px; } }

/* Shared Control Filter Header */
.filter-container {
    display: flex;
    flex-direction: column;
    gap: 16px;
    margin-bottom: 24px;
    background: rgba(255, 255, 255, 0.8);
    padding: 16px 20px;
    border-radius: 16px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.5);
    box-shadow: var(--glass-glow);
}
@media (min-width: 576px) {
    .filter-container { flex-direction: row; justify-content: space-between; align-items: center; }
}

.section-title {
    font-size: 1.15rem;
    font-weight: 800;
    color: var(--text-primary);
    margin: 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.section-title i {
    background: #fff;
    padding: 8px;
    border-radius: 10px;
    color: #4f46e5;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.range-select {
    width: 100%;
    padding: 10px 16px;
    border-radius: 12px;
    border: 1px solid var(--border-color);
    background-color: #fff;
    font-weight: 600;
    color: var(--text-primary);
    outline: none;
    cursor: pointer;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
}
@media (min-width: 576px) { .range-select { width: auto; min-width: 200px; } }

/* 3D Premium Financial Stats Cards Grid */
.stats {
    display: grid;
    grid-template-columns: 1fr;
    gap: 20px;
    margin-bottom: 20px;
}
@media (min-width: 576px) { .stats { grid-template-columns: repeat(2, 1fr); } }
@media (min-width: 992px) { .stats { grid-template-columns: repeat(4, 1fr); } }

.card {
    position: relative;
    overflow: hidden;
    height: 140px;
    border-radius: 20px;
    padding: 20px;
    color: #fff;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.15);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.card:hover { transform: translateY(-5px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.12); }
.card::before, .card::after { content: ''; position: absolute; border-radius: 50%; background: rgba(255, 255, 255, 0.1); pointer-events: none; z-index: 0; }
.card::before { width: 110px; height: 110px; top: -30px; right: -30px; }
.card::after { width: 55px; height: 55px; bottom: -25px; left: -25px; }

.card-header-row, .card-body-row { position: relative; z-index: 1; }
.card-header-row { display: flex; justify-content: space-between; align-items: center; }

.icon-box {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    font-size: 1.1rem;
}
.card-meta { font-size: 0.72rem; background: rgba(255, 255, 255, 0.22); padding: 4px 10px; border-radius: 20px; font-weight: 600; }
.card-body-row h4 { font-size: 0.8rem; font-weight: 500; opacity: 0.9; margin: 0 0 4px 0; text-transform: uppercase; }
.card-body-row h2 { font-size: 1.45rem; font-weight: 800; margin: 0; letter-spacing: -0.5px; }

.blue { background: var(--bg-card-blue); }
.green { background: var(--bg-card-green); }
.purple { background: var(--bg-card-purple); }
.orange { background: var(--bg-card-orange); }

/* Mini Inventory Counter Badges Group */
.mini-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}
.mini {
    background: #fff;
    padding: 16px;
    border-radius: 18px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.01);
    border: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    gap: 14px;
    transition: all 0.2s ease;
}
.mini:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); }
.mini-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; }
.m-total { background: #eff6ff; color: #2563eb; }
.m-instock { background: #ecfdf5; color: #059669; }
.m-out { background: #fef2f2; color: #dc2626; }

.mini-content p { margin: 0; font-size: 0.72rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; }
.mini-content h3 { margin: 2px 0 0 0; font-size: 1.3rem; font-weight: 700; color: var(--text-primary); }

/* Double Column Grid Base */
.dashboard-grid { display: grid; grid-template-columns: 1fr; gap: 20px; margin-bottom: 24px; }
@media (min-width: 992px) { .dashboard-grid { grid-template-columns: 1fr 1.3fr; } }

.widget { background: #fff; padding: 22px; border-radius: 20px; border: 1px solid var(--border-color); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.01); display: flex; flex-direction: column; }
.widget-title { font-size: 0.95rem; font-weight: 700; color: var(--text-primary); margin: 0 0 20px 0; padding-bottom: 12px; border-bottom: 2px solid #f3f4f6; display: flex; align-items: center; gap: 10px; }

.chart-container { position: relative; margin: auto; height: 240px; width: 100%; max-width: 240px; }
.table-responsive { width: 100%; overflow-x: auto; }

/* Enhanced Modern UI Tables */
.custom-table { width: 100%; border-collapse: separate; border-spacing: 0 6px; }
.custom-table th { padding: 10px 14px; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); text-align: left; }
.custom-table tbody tr { background-color: #f9fafb; transition: background 0.2s; }
.custom-table tbody tr:hover { background-color: #f3f4f6; }
.custom-table td { padding: 14px; font-size: 0.85rem; border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color); }
.custom-table td:first-child { border-left: 1px solid var(--border-color); border-top-left-radius: 12px; border-bottom-left-radius: 12px; font-weight: 600; }
.custom-table td:last-child { border-right: 1px solid var(--border-color); border-top-right-radius: 12px; border-bottom-right-radius: 12px; }

/* Badges Stylings */
.badge { padding: 5px 10px; border-radius: 8px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px; }
.badge-paid { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
.badge-credit { background: #fef2f2; color: #b91c1c; border: 1px solid #fca5a5; }
.badge-volume { background: #eff6ff; color: #1d4ed8; border: 1px solid #bae6fd; }
</style>

<div class="main">
    
    <!-- Top Configuration Header Component -->
    <div class="filter-container">
        <div class="section-title"><i class="fas fa-chart-line"></i> Sales Matrix Dashboard</div>
        <form action="{{ route('admin.sales.dashboard') }}" method="GET" id="rangeForm">
            <select name="range" class="range-select" onchange="document.getElementById('rangeForm').submit();">
                <option value="today" {{ $range == 'today' ? 'selected' : '' }}>Today</option>
                <option value="3days" {{ $range == '3days' ? 'selected' : '' }}>Last 3 Days</option>
                <option value="7days" {{ $range == '7days' ? 'selected' : '' }}>Last 7 Days</option>
                <option value="1month" {{ $range == '1month' ? 'selected' : '' }}>Last 1 Month</option>
                <option value="6months" {{ $range == '6months' ? 'selected' : '' }}>Last 6 Months</option>
                <option value="12months" {{ $range == '12months' ? 'selected' : '' }}>Last 12 Months</option>
                <option value="yearwise" {{ $range == 'yearwise' ? 'selected' : '' }}>Year Wise ({{ date('Y') }})</option>
            </select>
        </form>
    </div>
    
    <!-- Premium 3D Financial Grid Cards -->
    <div class="stats">
        <div class="card blue">
            <div class="card-header-row"><div class="icon-box"><i class="fas fa-wallet"></i></div></div>
            <div class="card-body-row"><h4>Total Revenue</h4><h2>Rs. {{ number_format($totalRevenue, 2) }}</h2></div>
        </div>
        <div class="card green">
            <div class="card-header-row"><div class="icon-box"><i class="fas fa-file-invoice-dollar"></i></div></div>
            <div class="card-body-row"><h4>Invoices Cleared</h4><h2>{{ number_format($invoiceCount) }} bills</h2></div>
        </div>
        <div class="card purple">
            <div class="card-header-row"><div class="icon-box"><i class="fas fa-bolt"></i></div></div>
            <div class="card-body-row"><h4>Revenue Today</h4><h2>Rs. {{ number_format($salesToday, 2) }}</h2></div>
        </div>
        <div class="card orange">
            <div class="card-header-row"><div class="icon-box"><i class="fas fa-calculator"></i></div></div>
            <div class="card-body-row"><h4>Average Invoice Ticket</h4><h2>Rs. {{ number_format($averageInvoice, 2) }}</h2></div>
        </div>
    </div>

    <!-- Inventory Live Badges -->
    <div class="mini-cards">
        <div class="mini">
            <div class="mini-icon m-total"><i class="fas fa-cubes"></i></div>
            <div class="mini-content"><p>Total Catalog Items</p><h3>{{ $totalProducts }}</h3></div>
        </div>
        <div class="mini">
            <div class="mini-icon m-instock"><i class="fas fa-check-circle"></i></div>
            <div class="mini-content"><p>Available In Stock</p><h3>{{ $inStock }} items</h3></div>
        </div>
        <div class="mini">
            <div class="mini-icon m-out"><i class="fas fa-times-circle"></i></div>
            <div class="mini-content"><p>Out of Stock Alert</p><h3>{{ $outOfStock }} items</h3></div>
        </div>
    </div>

    <!-- Analytics Architecture Breakdown Grid -->
    <div class="dashboard-grid">
        <!-- Revenue Calendar Distribution Share Chart -->
        <div class="widget">
            <div class="widget-title"><i class="fas fa-chart-pie"></i> Revenue Share Matrix</div>
            <div class="chart-container"><canvas id="salesPieChart"></canvas></div>
        </div>

        <!-- High Volume Clientele Tracker Component -->
        <div class="widget">
            <div class="widget-title"><i class="fas fa-crown"></i> High-Volume Clientele</div>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Location</th>
                            <th style="text-align: right;">Bills Ordered</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                            <tr>
                                <td>{{ $customer->name }}</td>
                                <td>{{ $customer->address ?? 'N/A' }}</td>
                                <td style="text-align: right;"><span class="badge badge-volume">{{ $customer->invoices_count }} Purchases</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="3" style="text-align: center; color: var(--text-muted);">No client records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Invoice Audit Log (Full-Width Component) -->
    <div class="dashboard-grid" style="grid-template-columns: 1fr;">
        <div class="widget">
            <div class="widget-title"><i class="fas fa-history"></i> Real-Time Invoice Audit Log</div>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Invoice Number</th>
                            <th>Customer Name</th>
                            <th>Grand Total</th>
                            <th>Status Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentInvoices as $invoice)
                            <tr>
                                <td><strong>{{ $invoice->invoice_no }}</strong></td>
                                <td>{{ $invoice->customer->name ?? 'Walk-in Customer' }}</td>
                                <td>Rs. {{ number_format($invoice->grand_total, 2) }}</td>
                                <td><span class="badge {{ $invoice->status === 'Paid' ? 'badge-paid' : 'badge-credit' }}">{{ $invoice->status }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" style="text-align: center; color: var(--text-muted);">No transactional events recorded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const chartRawData = @json($chartData);
    const labels = chartRawData.map(item => item.date);
    const dataValues = chartRawData.map(item => parseFloat(item.total));
    const poolColors = ['#4f46e5', '#10b981', '#8b5cf6', '#f97316', '#ef4444', '#06b6d4', '#ec4899'];

    const ctx = document.getElementById('salesPieChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie', 
        data: {
            labels: labels.length > 0 ? labels : ['No Sales Tracked'],
            datasets: [{
                data: dataValues.length > 0 ? dataValues : [1],
                backgroundColor: labels.length > 0 ? poolColors.slice(0, labels.length) : ['#e5e7eb'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    position: 'bottom', 
                    labels: { 
                        boxWidth: 10, 
                        font: { size: 11, family: "'Inter', sans-serif" }, 
                        color: '#4b5563' 
                    } 
                }
            }
        }
    });
});
</script>
@endsection