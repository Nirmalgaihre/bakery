@extends('layouts.admin')

@section('title', 'Bakery & Cheque Management Dashboard')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght=400;500;600;700;800&display=swap" rel="stylesheet">
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

.main {
    padding: 16px;
    background-color: #f9fafb;
    font-family: 'Inter', sans-serif;
    box-sizing: border-box;
}
@media (min-width: 768px) { .main { padding: 24px; } }

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

.stats {
    display: grid;
    grid-template-columns: 1fr;
    gap: 20px;
    margin-bottom: 20px;
}
@media (min-width: 576px) { .stats { grid-template-columns: repeat(2, 1fr); } }
@media (min-width: 992px) { .stats { grid-template-columns: repeat(3, 1fr); } }
@media (min-width: 1200px) { .stats { grid-template-columns: repeat(5, 1fr); } }

.card {
    position: relative;
    overflow: hidden;
    height: 150px;
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
.card-body-row h2 { font-size: 1.4rem; font-weight: 800; margin: 0; letter-spacing: -0.5px; }

.blue { background: var(--bg-card-blue); }
.green { background: var(--bg-card-green); }
.orange { background: var(--bg-card-orange); }
.purple { background: var(--bg-card-purple); }
.red { background: var(--bg-card-red); }

.mini-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
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
}
.mini-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; }
.m-total { background: #eff6ff; color: #2563eb; }
.m-raw { background: #f5f3ff; color: #7c3aed; }
.m-instock { background: #ecfdf5; color: #059669; }
.m-expiry { background: #fffbeb; color: #d97706; }
.m-out { background: #fef2f2; color: #dc2626; }
.m-user { background: #f0fdfa; color: #0d9488; }

.mini-content p { margin: 0; font-size: 0.72rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; }
.mini-content h3 { margin: 2px 0 0 0; font-size: 1.3rem; font-weight: 700; color: var(--text-primary); }

.dashboard-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 20px;
    margin-bottom: 24px;
}
@media (min-width: 992px) { .dashboard-grid { grid-template-columns: 1fr 1.5fr; } }

.widget {
    background: #fff;
    padding: 22px;
    border-radius: 20px;
    border: 1px solid var(--border-color);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.01);
    display: flex;
    flex-direction: column;
}
.widget-title {
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 20px 0;
    padding-bottom: 12px;
    border-bottom: 2px solid #f3f4f6;
    display: flex;
    align-items: center;
    gap: 10px;
}

.chart-container { position: relative; margin: auto; height: 240px; width: 100%; max-width: 240px; }

.analytics { display: grid; grid-template-columns: 1fr; gap: 20px; margin-bottom: 32px; }
@media (min-width: 992px) { .analytics { grid-template-columns: 1fr 1fr 1.2fr; } }

.fin-widget { background: #fff; padding: 22px; border-radius: 20px; border: 1px solid var(--border-color); display: flex; flex-direction: column; justify-content: space-between; min-height: 165px; }
.fin-widget h4 { margin: 0; font-size: 0.88rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; }
.widget-amount { margin: 12px 0; font-size: 1.85rem; font-weight: 800; color: var(--text-primary); }
.trend-live { font-size: 0.7rem; font-weight: 700; padding: 4px 10px; border-radius: 20px; background: #fef2f2; color: #ef4444; text-transform: uppercase; animation: pulse 2s infinite; }
@keyframes pulse { 0% { opacity: 0.6; } 50% { opacity: 1; } 100% { opacity: 0.6; } }

.cheque-status-list { display: flex; flex-direction: column; gap: 10px; width: 100%; }
.cheque-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; border-radius: 12px; background: #f9fafb; border: 1px solid #f3f4f6; }
.cheque-info { display: flex; align-items: center; gap: 12px; }
.cheque-icon { width: 34px; height: 34px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.95rem; }
.c-pending { background: #fffbeb; color: #b45309; }
.c-cleared { background: #ecfdf5; color: #047857; }
.c-bounced { background: #fef2f2; color: #b91c1c; }
.cheque-info h5 { margin: 0; font-size: 0.85rem; font-weight: 600; }
.cheque-badge { font-size: 0.75rem; font-weight: 700; padding: 4px 10px; border-radius: 8px; }
</style>

<div class="main">
    
    <div class="filter-container">
        <div class="section-title"><i class="fas fa-cookie-bite"></i> Bakery Live Infrastructure</div>
        <form action="{{ route('admin.dashboard') }}" method="GET" id="dashboardFilterForm">
            <select name="range" class="range-select" onchange="document.getElementById('dashboardFilterForm').submit();">
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
    
    <div class="stats">
        <div class="card blue">
            <div class="card-header-row">
                <div class="icon-box"><i class="fas fa-shopping-cart"></i></div>
                <div class="card-meta">{{ $data['invoiceCount'] }} Bills</div>
            </div>
            <div class="card-body-row">
                <h4>Net Sales</h4>
                <h2>Rs. {{ number_format($data['totalSales'], 2) }}</h2>
            </div>
        </div>
        
        <div class="card green">
            <div class="card-header-row">
                <div class="icon-box"><i class="fas fa-bread-slice"></i></div>
                <div class="card-meta">{{ $data['purchaseCount'] }} Invoices</div>
            </div>
            <div class="card-body-row">
                <h4>Supply Purchases</h4>
                <h2>Rs. {{ number_format($data['totalSpent'], 2) }}</h2>
            </div>
        </div>
        
        <div class="card orange">
            <div class="card-header-row">
                <div class="icon-box"><i class="fas fa-arrow-alt-circle-down"></i></div>
                <div class="card-meta">Inflow</div>
            </div>
            <div class="card-body-row">
                <h4>Baked / Stock In</h4>
                <h2>{{ number_format($data['stockInQty']) }} Units</h2>
            </div>
        </div>
        
        <div class="card purple">
            <div class="card-header-row">
                <div class="icon-box"><i class="fas fa-arrow-alt-circle-up"></i></div>
                <div class="card-meta">Outflow</div>
            </div>
            <div class="card-body-row">
                <h4>Sold / Stock Out</h4>
                <h2>{{ number_format($data['stockOutQty']) }} Units</h2>
            </div>
        </div>

        <div class="card red">
            <div class="card-header-row">
                <div class="icon-box"><i class="fas fa-trash-alt"></i></div>
                <div class="card-meta">Adjustments</div>
            </div>
            <div class="card-body-row">
                <h4>Wastage & Expired</h4>
                <h2>{{ number_format($data['totalWastage'], 2) }} Units</h2>
            </div>
        </div>
    </div>

    <div class="mini-cards">
        <div class="mini"><div class="mini-icon m-total"><i class="fas fa-cookie"></i></div><div class="mini-content"><p>Products</p><h3>{{ $data['totalProducts'] }}</h3></div></div>
        <div class="mini"><div class="mini-icon m-user"><i class="fas fa-users"></i></div><div class="mini-content"><p>Customers</p><h3>{{ $data['totalCustomers'] }}</h3></div></div>
        <div class="mini"><div class="mini-icon m-raw"><i class="fas fa-seedling"></i></div><div class="mini-content"><p>Raw Materials</p><h3>{{ $data['rawMaterialsCount'] }}</h3></div></div>
        <div class="mini"><div class="mini-icon m-instock"><i class="fas fa-check-circle"></i></div><div class="mini-content"><p>Available Stock</p><h3>{{ $data['inStock'] }}</h3></div></div>
        <div class="mini"><div class="mini-icon m-expiry"><i class="fas fa-hourglass-half"></i></div><div class="mini-content"><p>Near Expiry</p><h3>{{ $data['nearExpiry'] }}</h3></div></div>
        <div class="mini"><div class="mini-icon m-out"><i class="fas fa-times-circle"></i></div><div class="mini-content"><p>Out Of Stock</p><h3>{{ $data['outOfStock'] }}</h3></div></div>
    </div>

    <div class="dashboard-grid">
        {{-- Stock Pie Chart Left Block --}}
        <div class="widget">
            <div class="widget-title"><i class="fas fa-chart-pie"></i> Real-Time Stock Allocation Share</div>
            <div class="chart-container">
                <canvas id="bakeryStockPieChart"></canvas>
            </div>
        </div>

        {{-- 💡 नयाँ थपिएको ग्रिड: वेस्टेज विवरण ब्रेकडाउन म्याट्रिक्स तालिका (Right Block) --}}
        <div class="widget">
            <div class="widget-title text-red-600"><i class="fas fa-dumpster text-red-500"></i> Wastage & Adjustments Breakdown</div>
            <div class="w-full overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-100 text-slate-700 font-bold uppercase tracking-wider border-b border-slate-200">
                            <th class="p-3">Product Item</th>
                            <th class="p-3">Waste Cause</th>
                            <th class="p-3 text-right">Quantity</th>
                            <th class="p-3 text-right">Estimated Cost Loss</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($data['wastageBreakdown'] ?? [] as $breakdown)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="p-2.5 font-semibold text-slate-800">
                                    {{ $breakdown->product->name ?? 'Deleted Product' }}
                                </td>
                                <td class="p-2.5">
                                    @if($breakdown->type === 'expired')
                                        <span class="px-2 py-0.5 text-[10px] rounded bg-red-100 text-red-800 font-bold uppercase">Expired</span>
                                    @elseif($breakdown->type === 'damaged')
                                        <span class="px-2 py-0.5 text-[10px] rounded bg-orange-100 text-orange-800 font-bold uppercase">Damaged</span>
                                    @elseif($breakdown->type === 'internal_use')
                                        <span class="px-2 py-0.5 text-[10px] rounded bg-blue-100 text-blue-800 font-bold uppercase">Internal</span>
                                    @else
                                        <span class="px-2 py-0.5 text-[10px] rounded bg-amber-100 text-amber-800 font-bold uppercase">{{ $breakdown->type }}</span>
                                    @endif
                                </td>
                                <td class="p-2.5 text-right font-mono font-bold text-slate-700">
                                    {{ number_format($breakdown->total_qty, 2) }} {{ strtoupper($breakdown->product->inventory_unit ?? 'units') }}
                                </td>
                                <td class="p-2.5 text-right font-mono text-red-600 font-semibold">
                                    Rs. {{ number_format($breakdown->total_loss_amt, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-6 text-center text-slate-400 font-medium">
                                    No wastage or adjustments recorded within this selected timeframe.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="section-title"><i class="fas fa-money-check-alt"></i> Financials & Cheque Lifecycle</div>

    <div class="analytics">
        <div class="fin-widget">
            <div class="widget-header"><h4>Bakery Sales (Today)</h4><span class="trend-live">Live</span></div>
            <h2 class="widget-amount">Rs. {{ number_format($data['salesToday'], 2) }}</h2>
            <div class="widget-footer">Today's direct counter income calculation</div>
        </div>
        
        <div class="fin-widget">
            <div class="widget-header"><h4>Baking Cost (Today)</h4><span class="trend-live" style="background:#fff7ed; color:#c2410c;">Expenses</span></div>
            <h2 class="widget-amount">Rs. {{ number_format($data['costToday'], 2) }}</h2>
            <div class="widget-footer">Procurement distribution records today</div>
        </div>
        
        <div class="fin-widget" style="justify-content: center; min-height: 180px;">
            <div class="cheque-status-list">
                <div class="cheque-row">
                    <div class="cheque-info"><div class="cheque-icon c-pending"><i class="fas fa-clock"></i></div><h5>Pending Cheques</h5></div>
                    <span class="cheque-badge badge-pending">{{ $data['chequesPending'] }} pcs</span>
                </div>
                <div class="cheque-row">
                    <div class="cheque-info"><div class="cheque-icon c-cleared"><i class="fas fa-check-double"></i></div><h5>Cleared Assets</h5></div>
                    <span class="cheque-badge badge-cleared">{{ $data['chequesCleared'] }} pcs</span>
                </div>
                <div class="cheque-row">
                    <div class="cheque-info"><div class="cheque-icon c-bounced"><i class="fas fa-exclamation-triangle"></i></div><h5>Bounced Faults</h5></div>
                    <span class="cheque-badge badge-bounced">{{ $data['chequesBounced'] }} pcs</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const availableStock = {{ $data['inStock'] }};
    const outOfStock = {{ $data['outOfStock'] }};

    const ctx = document.getElementById('bakeryStockPieChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Available Stock', 'Out Of Stock'],
            datasets: [{
                data: [availableStock, outOfStock],
                backgroundColor: ['#10b981', '#ef4444'],
                borderWidth: 3,
                borderColor: '#ffffff',
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        font: { size: 12, family: "'Inter', sans-serif", weight: '500' },
                        color: '#4b5563'
                    }
                }
            }
        }
    });
});
</script>
@endsection