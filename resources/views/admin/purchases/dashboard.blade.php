@extends('layouts.admin')

@section('title', 'Procurement Dashboard')

@section('content')
<!-- Fonts & FontAwesome Icons -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
:root {
    --primary-blue: #4f46e5;
    --primary-green: #10b981;
    --primary-orange: #f97316;
    --primary-purple: #8b5cf6;
    --primary-red: #ef4444;
    --bg-main: #f8fafc;
    --card-bg: #ffffff;
    --text-dark: #0f172a;
    --text-muted: #64748b;
    --border-color: #e2e8f0;
}

.dashboard-container {
    padding: 24px;
    background-color: var(--bg-main);
    font-family: 'Inter', sans-serif;
    color: var(--text-dark);
    box-sizing: border-box;
    min-height: 100vh;
}

/* Filter Card */
.filter-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 20px 24px;
    margin-bottom: 24px;
    box-shadow: 0 4px 15px -3px rgba(0, 0, 0, 0.03);
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
}

.filter-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 700;
    font-size: 1.1rem;
    color: #1e293b;
}

.filter-title i {
    background: #eff6ff;
    padding: 8px;
    border-radius: 10px;
    color: var(--primary-blue);
}

.filter-form {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    gap: 16px;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.filter-label {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--text-muted);
}

.filter-select {
    background-color: #f1f5f9;
    border: 1px solid #cbd5e1;
    color: #1e293b;
    font-size: 0.875rem;
    font-weight: 500;
    border-radius: 10px;
    padding: 8px 12px;
    outline: none;
    transition: all 0.2s ease;
}

.filter-select:focus {
    border-color: var(--primary-blue);
    background-color: #fff;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
}

.date-range-badge {
    background: rgba(79, 70, 229, 0.08);
    color: var(--primary-blue);
    padding: 8px 16px;
    border-radius: 10px;
    font-size: 0.85rem;
    font-weight: 600;
    border: 1px solid rgba(79, 70, 229, 0.15);
}

/* KPI Hero Cards */
.hero-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
    margin-bottom: 24px;
}

.kpi-card {
    position: relative;
    border-radius: 18px;
    padding: 22px;
    color: #ffffff;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-height: 140px;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.kpi-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.12);
}

.kpi-card.blue { background: linear-gradient(135deg, #4f46e5, #6366f1); }
.kpi-card.green { background: linear-gradient(135deg, #10b981, #059669); }
.kpi-card.purple { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
.kpi-card.orange { background: linear-gradient(135deg, #f97316, #ea580c); }

.kpi-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.kpi-icon-box {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.22);
    backdrop-filter: blur(8px);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
}

.kpi-body h4 {
    font-size: 0.78rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    opacity: 0.9;
    margin: 12px 0 4px 0;
}

.kpi-body h2 {
    font-size: 1.6rem;
    font-weight: 800;
    margin: 0;
    letter-spacing: -0.02em;
}

/* Mini Metrics Grid */
.metrics-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}

.metric-card-white {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 4px 12px -2px rgba(0,0,0,0.03);
    transition: transform 0.2s ease;
}

.metric-card-white:hover {
    transform: translateY(-2px);
}

.metric-val {
    font-size: 1.75rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
    line-height: 1;
}

.metric-lbl {
    font-size: 0.72rem;
    font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-top: 6px;
}

.progress-track {
    width: 100%;
    background: #f1f5f9;
    height: 6px;
    margin-top: 14px;
    border-radius: 999px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    border-radius: 999px;
}

/* Main Grid Layout */
.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(420px, 1fr));
    gap: 20px;
    margin-bottom: 24px;
}

@media (max-width: 768px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
}

.widget {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 18px;
    padding: 20px 24px;
    box-shadow: 0 4px 15px -3px rgba(0, 0, 0, 0.03);
    display: flex;
    flex-direction: column;
}

.widget-title {
    font-size: 0.95rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 18px;
    padding-bottom: 12px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    gap: 10px;
}

.chart-container {
    position: relative;
    width: 100%;
    height: 280px;
}

/* Card & Table Styling */
.table-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 4px 15px -3px rgba(0, 0, 0, 0.03);
    display: flex;
    flex-direction: column;
    margin-bottom: 24px;
}

.card-title-bar {
    background: #f8fafc;
    border-bottom: 1px solid var(--border-color);
    padding: 14px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.card-title-bar h3 {
    margin: 0;
    font-size: 0.9rem;
    font-weight: 700;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 8px;
}

.modern-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.875rem;
}

.modern-table th {
    background: #f8fafc;
    color: var(--text-muted);
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 12px 20px;
    text-align: left;
    border-bottom: 1px solid var(--border-color);
}

.modern-table td {
    padding: 14px 20px;
    border-bottom: 1px solid #f1f5f9;
    color: #334155;
}

.modern-table tr:hover {
    background-color: #f8fafc;
}

.badge-tag {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
}

.badge-tag.info { background: rgba(79, 70, 229, 0.1); color: #4f46e5; }
.badge-tag.success { background: rgba(16, 185, 129, 0.1); color: #10b981; }

.text-right { text-align: right; }
.text-center { text-align: center; }

/* See More Footer Button */
.see-more-footer {
    padding: 12px 20px;
    border-top: 1px solid #f1f5f9;
    background: #ffffff;
    text-align: center;
}

.see-more-btn {
    background: #f1f5f9;
    color: #475569;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
}

.see-more-btn:hover {
    background: #e2e8f0;
    color: #1e293b;
}

.expandable-item.hidden {
    display: none !important;
}
</style>

<div class="dashboard-container">

    <!-- Top Filter Header -->
    <div class="filter-card">
        <div class="filter-title">
            <i class="fas fa-truck-ramp-box"></i>
            <span>Procurement Dashboard</span>
        </div>

        <form action="{{ route('admin.purchases.dashboard') }}" method="GET" class="filter-form">
            <div class="filter-group">
                <label class="filter-label">Year</label>
                <select name="year" onchange="this.form.submit()" class="filter-select" style="width: 120px;">
                    @php $currentYear = date('Y'); @endphp
                    @for($y = $currentYear; $y >= $currentYear - 5; $y--)
                    <option value="{{ $y }}" {{ request('year', $currentYear) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Period</label>
                <select name="month" onchange="this.form.submit()" class="filter-select" style="width: 180px;">
                    <option value="">Full Year Summary</option>
                    @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                        {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="date-range-badge">
                Showing: <strong>{{ $dateRange }}</strong>
            </div>
        </form>
    </div>

    <!-- KPI Hero Cards -->
    <div class="hero-stats-grid">
        <div class="kpi-card blue">
            <div class="kpi-header">
                <div class="kpi-icon-box"><i class="fas fa-wallet"></i></div>
            </div>
            <div class="kpi-body">
                <h4>Total Expenditure</h4>
                <h2>Rs. {{ number_format($totalPurchased, 2) }}</h2>
            </div>
        </div>

        <div class="kpi-card green">
            <div class="kpi-header">
                <div class="kpi-icon-box"><i class="fas fa-file-invoice-dollar"></i></div>
            </div>
            <div class="kpi-body">
                <h4>Total Purchases</h4>
                <h2>{{ number_format($purchaseCount) }}</h2>
            </div>
        </div>

        <div class="kpi-card purple">
            <div class="kpi-header">
                <div class="kpi-icon-box"><i class="fas fa-bolt"></i></div>
            </div>
            <div class="kpi-body">
                <h4>Purchased Today</h4>
                <h2>Rs. {{ number_format($purchasesToday, 2) }}</h2>
            </div>
        </div>

        <div class="kpi-card orange">
            <div class="kpi-header">
                <div class="kpi-icon-box"><i class="fas fa-calculator"></i></div>
            </div>
            <div class="kpi-body">
                <h4>Avg Purchase</h4>
                <h2>Rs. {{ number_format($averagePurchase, 2) }}</h2>
            </div>
        </div>
    </div>

    <!-- Secondary Mini Cards -->
    <div class="metrics-row">
        <div class="metric-card-white">
            <h3 class="metric-val">{{ $totalProducts }}</h3>
            <p class="metric-lbl">Catalog Items</p>
            <div class="progress-track">
                <div class="progress-fill" style="width: 100%; background: var(--primary-blue);"></div>
            </div>
        </div>

        <div class="metric-card-white">
            <h3 class="metric-val">{{ $inStock }}</h3>
            <p class="metric-lbl">In Stock</p>
            <div class="progress-track">
                <div class="progress-fill" style="width: 70%; background: var(--primary-green);"></div>
            </div>
        </div>

        <div class="metric-card-white">
            <h3 class="metric-val">{{ $outOfStock }}</h3>
            <p class="metric-lbl">Out of Stock</p>
            <div class="progress-track">
                <div class="progress-fill" style="width: 30%; background: var(--primary-red);"></div>
            </div>
        </div>

        <div class="metric-card-white">
            <h3 class="metric-val">{{ count($suppliers) }}</h3>
            <p class="metric-lbl">Active Suppliers</p>
            <div class="progress-track">
                <div class="progress-fill" style="width: 50%; background: var(--primary-purple);"></div>
            </div>
        </div>
    </div>

    <!-- Expenditure Chart & Top Suppliers Grid -->
    <div class="dashboard-grid">
        <!-- Expenditure Trend Chart -->
        <div class="widget">
            <div class="widget-title"><i class="fas fa-chart-pie text-indigo-500"></i> Expenditure Trend</div>
            <div class="chart-container">
                <canvas id="salesPieChart"></canvas>
            </div>
        </div>

        <!-- Top Suppliers Table (10 items max initially + See More) -->
        <div class="table-card" style="margin-bottom: 0;">
            <div class="card-title-bar">
                <h3><i class="fas fa-crown text-amber-500"></i> Top Suppliers</h3>
                <span class="badge-tag info">{{ count($suppliers) }} Suppliers</span>
            </div>
            <div style="overflow-x: auto;">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Supplier Name</th>
                            <th class="text-right">Orders Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $index => $s)
                        <tr class="{{ $index >= 10 ? 'expandable-item hidden' : '' }}">
                            <td class="font-semibold text-slate-800">{{ $s->supplier_name }}</td>
                            <td class="text-right">
                                <span class="badge-tag success">{{ $s->purchase_count }} Orders</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center" style="color: var(--text-muted); padding: 20px;">No suppliers found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(count($suppliers) > 10)
            <div class="see-more-footer">
                <button type="button" onclick="toggleSeeMore(this)" class="see-more-btn">
                    <span>See More ({{ count($suppliers) - 10 }} more)</span>
                    <i class="fas fa-chevron-down text-xs"></i>
                </button>
            </div>
            @endif
        </div>
    </div>

    <!-- Recent Procurement Log Table (10 items max initially + See More) -->
    <div class="table-card">
        <div class="card-title-bar">
            <h3><i class="fas fa-history text-indigo-500"></i> Recent Procurement Log</h3>
            <span class="badge-tag info">{{ count($recentPurchases) }} Total Logs</span>
        </div>
        <div style="overflow-x: auto;">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Supplier</th>
                        <th class="text-right">Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPurchases as $index => $p)
                    <tr class="{{ $index >= 10 ? 'expandable-item hidden' : '' }}">
                        <td class="font-medium text-slate-500">{{ $p->created_at->format('M d, Y') }}</td>
                        <td class="font-semibold text-slate-800">{{ $p->supplier_name }}</td>
                        <td class="text-right font-bold text-slate-900">Rs. {{ number_format($p->total_amount, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center" style="color: var(--text-muted); padding: 20px;">No procurement logs recorded</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(count($recentPurchases) > 10)
        <div class="see-more-footer">
            <button type="button" onclick="toggleSeeMore(this)" class="see-more-btn">
                <span>See More ({{ count($recentPurchases) - 10 }} more)</span>
                <i class="fas fa-chevron-down text-xs"></i>
            </button>
        </div>
        @endif
    </div>

</div>

<!-- JavaScript for Charts & See More Toggle -->
<script>
function toggleSeeMore(btn) {
    const card = btn.closest('.table-card');
    if (!card) return;
    
    const hiddenItems = card.querySelectorAll('.expandable-item');
    const isExpanded = btn.classList.contains('expanded');

    if (isExpanded) {
        hiddenItems.forEach(el => el.classList.add('hidden'));
        btn.classList.remove('expanded');
        btn.innerHTML = `<span>See More (${hiddenItems.length} more)</span><i class="fas fa-chevron-down text-xs"></i>`;
    } else {
        hiddenItems.forEach(el => el.classList.remove('hidden'));
        btn.classList.add('expanded');
        btn.innerHTML = `<span>Show Less</span><i class="fas fa-chevron-up text-xs"></i>`;
    }
}

document.addEventListener("DOMContentLoaded", function() {
    const chartData = @json($chartData);
    new Chart(document.getElementById('salesPieChart'), {
        type: 'doughnut',
        data: {
            labels: chartData.map(i => i.date),
            datasets: [{
                data: chartData.map(i => i.total),
                backgroundColor: [
                    '#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6',
                    '#06b6d4', '#ec4899'
                ],
                borderColor: '#ffffff',
                borderWidth: 3,
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 15, font: { family: 'Inter', weight: '600' } }
                }
            }
        }
    });
});
</script>
@endsection