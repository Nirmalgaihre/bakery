@extends('layouts.admin')

@section('title', 'Bakery & Cheque Management Dashboard')

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

/* Hero KPI Cards */
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
.kpi-card.red { background: linear-gradient(135deg, #ef4444, #dc2626); }

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

.kpi-meta {
    font-size: 0.75rem;
    font-weight: 700;
    background: rgba(255, 255, 255, 0.25);
    padding: 4px 12px;
    border-radius: 20px;
    backdrop-filter: blur(4px);
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

/* Secondary Metric Cards Grid */
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

/* Solid Color Accent Cards */
.solid-accent-card {
    border-radius: 16px;
    padding: 20px;
    color: #ffffff;
    box-shadow: 0 8px 20px -4px rgba(0,0,0,0.08);
    display: flex;
    flex-direction: column;
    justify-content: center;
    transition: transform 0.2s ease;
}

.solid-accent-card:hover {
    transform: translateY(-2px);
}

.solid-accent-card h3 {
    font-size: 1.85rem;
    font-weight: 800;
    margin: 0;
    line-height: 1.1;
}

.solid-accent-card p {
    font-size: 0.8rem;
    font-weight: 600;
    opacity: 0.9;
    margin: 6px 0 0 0;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}

/* Dashboard Grid Layout */
.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
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
    flex-grow: 1;
}

.chart-h-36 { height: 300px; }
.chart-h-32 { height: 280px; }
.chart-h-30 { height: 260px; }

/* Watchlists & Tables Styling */
.watchlist-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(420px, 1fr));
    gap: 20px;
    margin-bottom: 24px;
}

@media (max-width: 640px) {
    .watchlist-grid {
        grid-template-columns: 1fr;
    }
}

.watchlist-card, .table-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 4px 15px -3px rgba(0, 0, 0, 0.03);
    display: flex;
    flex-direction: column;
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

.watchlist-content {
    flex-grow: 1;
}

.watch-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 20px;
    border-bottom: 1px solid #f1f5f9;
    font-size: 0.875rem;
    transition: background 0.15s ease;
}

.watch-item:last-child {
    border-bottom: none;
}

.watch-item:hover {
    background-color: #f8fafc;
}

.watch-left {
    font-weight: 600;
    color: #1e293b;
}

.watch-sub {
    font-size: 0.75rem;
    color: var(--text-muted);
    margin-top: 2px;
}

.watch-right {
    text-align: right;
    font-weight: 600;
    color: #334155;
    line-height: 1.3;
}

.badge-tag {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
}

.badge-tag.danger { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
.badge-tag.warning { background: rgba(249, 115, 22, 0.1); color: #f97316; }
.badge-tag.info { background: rgba(79, 70, 229, 0.1); color: #4f46e5; }
.badge-tag.success { background: rgba(16, 185, 129, 0.1); color: #10b981; }

/* Modern Table Styling */
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

.text-right { text-align: right; }
.text-center { text-align: center; }

/* See More Button Footer */
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
            <i class="fas fa-chart-line text-indigo-600"></i>
            <span>Dashboard Reporting</span>
        </div>

        <form action="{{ route('admin.dashboard') }}" method="GET" class="filter-form">
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
                    @php $currentMonth = date('n'); @endphp
                    @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" {{ request('month', $currentMonth) == $m ? 'selected' : '' }}>
                        {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Daily Range</label>
                <select name="daily_range" onchange="this.form.submit()" class="filter-select" style="width: 150px;">
                    <option value="">-- Select --</option>
                    <option value="today" {{ request('daily_range', 'today') == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="yesterday" {{ request('daily_range') == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                    <option value="3days" {{ request('daily_range') == '3days' ? 'selected' : '' }}>Last 3 Days</option>
                    <option value="7days" {{ request('daily_range') == '7days' ? 'selected' : '' }}>Last 7 Days</option>
                    <option value="14days" {{ request('daily_range') == '14days' ? 'selected' : '' }}>Last 14 Days</option>
                    <option value="28days" {{ request('daily_range') == '28days' ? 'selected' : '' }}>Last 28 Days</option>
                </select>
            </div>

            <div class="date-range-badge">
                Range: <strong>{{ $dateRange }}</strong>
            </div>
        </form>
    </div>

    <!-- Top KPI Cards -->
    <div class="hero-stats-grid">
        <div class="kpi-card blue">
            <div class="kpi-header">
                <div class="kpi-icon-box"><i class="fas fa-shopping-cart"></i></div>
                <div class="kpi-meta">{{ $data['invoiceCount'] }} Bills</div>
            </div>
            <div class="kpi-body">
                <h4>Net Sales</h4>
                <h2>Rs. {{ number_format($data['totalSales'], 2) }}</h2>
            </div>
        </div>

        <div class="kpi-card green">
            <div class="kpi-header">
                <div class="kpi-icon-box"><i class="fas fa-bread-slice"></i></div>
                <div class="kpi-meta">{{ $data['purchaseCount'] }} Purchases</div>
            </div>
            <div class="kpi-body">
                <h4>Supply Purchases</h4>
                <h2>Rs. {{ number_format($data['totalSpent'], 2) }}</h2>
            </div>
        </div>

        <div class="kpi-card purple">
            <div class="kpi-header">
                <div class="kpi-icon-box"><i class="fas fa-boxes-stacked"></i></div>
                <div class="kpi-meta">{{ number_format($data['stockOutQty']) }} Units</div>
            </div>
            <div class="kpi-body">
                <h4>Cost of Goods Sold</h4>
                <h2>Rs. {{ number_format($data['costOfGoodsSold'], 2) }}</h2>
            </div>
        </div>

        <div class="kpi-card {{ $data['netProfit'] >= 0 ? 'green' : 'red' }}">
            <div class="kpi-header">
                <div class="kpi-icon-box"><i class="fas fa-chart-pie"></i></div>
                <div class="kpi-meta">Net Profit</div>
            </div>
            <div class="kpi-body">
                <h4>Net Profit</h4>
                <h2>Rs. {{ number_format($data['netProfit'], 2) }}</h2>
            </div>
        </div>
    </div>

    <!-- Secondary Metric White Cards -->
    <div class="metrics-row">
        <div class="metric-card-white">
            <h3 class="metric-val">{{ $data['totalProducts'] }}</h3>
            <p class="metric-lbl">Total Products</p>
            <div class="progress-track">
                <div class="progress-fill" style="width: 60%; background: var(--primary-green);"></div>
            </div>
        </div>

        <div class="metric-card-white">
            <h3 class="metric-val">{{ $data['totalCustomers'] }}</h3>
            <p class="metric-lbl">Customers</p>
            <div class="progress-track">
                <div class="progress-fill" style="width: 80%; background: var(--primary-blue);"></div>
            </div>
        </div>

        <div class="metric-card-white">
            <h3 class="metric-val">{{ $data['inStock'] }}</h3>
            <p class="metric-lbl">Products In Stock</p>
            <div class="progress-track">
                <div class="progress-fill" style="width: 45%; background: var(--primary-orange);"></div>
            </div>
        </div>

        <div class="metric-card-white">
            <h3 class="metric-val">{{ $data['outOfStock'] }}</h3>
            <p class="metric-lbl">Products Out Of Stock</p>
            <div class="progress-track">
                <div class="progress-fill" style="width: 90%; background: var(--primary-red);"></div>
            </div>
        </div>
    </div>

    <!-- Solid Accent Status Cards -->
    <div class="metrics-row">
        <div class="solid-accent-card" style="background: linear-gradient(135deg, #6366f1, #4f46e5);">
            <h3>{{ $data['nearExpiry'] }}</h3>
            <p>Near Expiry Products</p>
        </div>
        <div class="solid-accent-card" style="background: linear-gradient(135deg, #fb923c, #f97316);">
            <h3>{{ $data['chequesPending'] }}</h3>
            <p>Pending Cheques</p>
        </div>
        <div class="solid-accent-card" style="background: linear-gradient(135deg, #f43f5e, #e11d48);">
            <h3>{{ $data['chequesBounced'] }}</h3>
            <p>Bounced Cheques</p>
        </div>
        <div class="solid-accent-card" style="background: linear-gradient(135deg, #0ea5e9, #0284c7);">
            <h3>{{ $data['chequesCleared'] }}</h3>
            <p>Cleared Cheques</p>
        </div>
    </div>

    <!-- Main Charts Section -->
    <div class="dashboard-grid">
        <div class="widget">
            <div class="widget-title"><i class="fas fa-chart-line text-indigo-500"></i> Monthly Sales vs Purchases</div>
            <div class="chart-container chart-h-36">
                <canvas id="monthlyAreaChart"></canvas>
            </div>
        </div>

        <div class="widget">
            <div class="widget-title"><i class="fas fa-chart-bar text-emerald-500"></i> Monthly Sales Comparison</div>
            <div class="chart-container chart-h-32">
                <canvas id="monthlyBarChart"></canvas>
            </div>
        </div>

        <div class="widget">
            <div class="widget-title"><i class="fas fa-radar text-purple-500"></i> Cheque Overview</div>
            <div class="chart-container chart-h-30">
                <canvas id="chequeRadarChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Inventory & Wastage Pie Charts -->
    <div class="dashboard-grid">
        <div class="widget">
            <div class="widget-title"><i class="fas fa-chart-pie text-emerald-500"></i> Stock Status</div>
            <div class="chart-container chart-h-36">
                <canvas id="bakeryStockPieChart"></canvas>
            </div>
        </div>

        <div class="widget">
            <div class="widget-title" style="color: var(--primary-red);"><i class="fas fa-dumpster text-rose-500"></i> Wastage & Adjustments Breakdown</div>
            <div class="chart-container chart-h-36">
                <canvas id="wastagePieChart"></canvas>
                @if($data['totalWastage'] == 0)
                <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; color: #94a3b8; font-weight: 500;">
                    No wastage data for this period.
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Watchlists Grid (10 items max each with See More option) -->
    <div class="watchlist-grid">
        <!-- Low Stock Watchlist -->
        <div class="watchlist-card">
            <div class="card-title-bar">
                <h3><i class="fas fa-triangle-exclamation text-amber-500"></i> Low Stock Watchlist</h3>
                <span class="badge-tag warning">{{ count($data['lowStockProducts']) }} Items</span>
            </div>
            <div class="watchlist-content">
                @forelse($data['lowStockProducts'] as $index => $product)
                <div class="watch-item {{ $index >= 10 ? 'expandable-item hidden' : '' }}">
                    <div>
                        <div class="watch-left">{{ $product->name }}</div>
                        <div class="watch-sub">Alert stock level: {{ $product->alert_stock_level }}</div>
                    </div>
                    <div class="watch-right">
                        <span class="badge-tag danger">Stock: {{ $product->stock }}</span>
                    </div>
                </div>
                @empty
                <div class="watch-item text-center" style="color: var(--text-muted); justify-content: center;">No low stock products</div>
                @endforelse
            </div>
            @if(count($data['lowStockProducts']) > 10)
            <div class="see-more-footer">
                <button type="button" onclick="toggleSeeMore(this)" class="see-more-btn">
                    <span>See More ({{ count($data['lowStockProducts']) - 10 }} more)</span>
                    <i class="fas fa-chevron-down text-xs"></i>
                </button>
            </div>
            @endif
        </div>

        <!-- Pending Cheque Watchlist -->
        <div class="watchlist-card">
            <div class="card-title-bar">
                <h3><i class="fas fa-clock text-orange-500"></i> Pending Cheque Watchlist</h3>
                <span class="badge-tag warning">{{ count($data['watchlistCheques']) }} Pending</span>
            </div>
            <div class="watchlist-content">
                @forelse($data['watchlistCheques'] as $index => $cheque)
                <div class="watch-item {{ $index >= 10 ? 'expandable-item hidden' : '' }}">
                    <div>
                        <div class="watch-left">{{ $cheque->party_name }}</div>
                        <div class="watch-sub">Cheque #: {{ $cheque->cheque_no }}</div>
                    </div>
                    <div class="watch-right">
                        <span style="color: var(--primary-blue);">Rs. {{ number_format($cheque->amount, 2) }}</span>
                    </div>
                </div>
                @empty
                <div class="watch-item text-center" style="color: var(--text-muted); justify-content: center;">No pending cheques</div>
                @endforelse
            </div>
            @if(count($data['watchlistCheques']) > 10)
            <div class="see-more-footer">
                <button type="button" onclick="toggleSeeMore(this)" class="see-more-btn">
                    <span>See More ({{ count($data['watchlistCheques']) - 10 }} more)</span>
                    <i class="fas fa-chevron-down text-xs"></i>
                </button>
            </div>
            @endif
        </div>

        <!-- Due Customers Watchlist -->
        <div class="watchlist-card">
            <div class="card-title-bar">
                <h3><i class="fas fa-users text-indigo-500"></i> Due Customers</h3>
                <span class="badge-tag info">{{ count($data['dueCustomers']) }} Customers</span>
            </div>
            <div class="watchlist-content">
                @forelse($data['dueCustomers'] as $index => $customer)
                <div class="watch-item {{ $index >= 10 ? 'expandable-item hidden' : '' }}">
                    <div>
                        <div class="watch-left">{{ $customer->name }}</div>
                        <div class="watch-sub"><i class="fas fa-phone text-xs mr-1"></i>{{ $customer->phone_number }}</div>
                    </div>
                    <div class="watch-right">
                        <span style="color: var(--primary-red);">Due: Rs. {{ number_format($customer->previous_due, 2) }}</span>
                    </div>
                </div>
                @empty
                <div class="watch-item text-center" style="color: var(--text-muted); justify-content: center;">No due customers</div>
                @endforelse
            </div>
            @if(count($data['dueCustomers']) > 10)
            <div class="see-more-footer">
                <button type="button" onclick="toggleSeeMore(this)" class="see-more-btn">
                    <span>See More ({{ count($data['dueCustomers']) - 10 }} more)</span>
                    <i class="fas fa-chevron-down text-xs"></i>
                </button>
            </div>
            @endif
        </div>

        <!-- Recent Adjustments Watchlist -->
        <div class="watchlist-card">
            <div class="card-title-bar">
                <h3><i class="fas fa-boxes-packing text-emerald-500"></i> Recent Adjustments</h3>
                <span class="badge-tag success">{{ count($data['recentAdjustments']) }} Records</span>
            </div>
            <div class="watchlist-content">
                @forelse($data['recentAdjustments'] as $index => $adjustment)
                <div class="watch-item {{ $index >= 10 ? 'expandable-item hidden' : '' }}">
                    <div>
                        <div class="watch-left">{{ $adjustment->product->name ?? 'Deleted Product' }}</div>
                        <div class="watch-sub">Qty: {{ number_format($adjustment->quantity, 2) }}</div>
                    </div>
                    <div class="watch-right">
                        <span class="badge-tag {{ strtolower($adjustment->type) == 'wastage' ? 'danger' : 'info' }}">
                            {{ strtoupper($adjustment->type) }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="watch-item text-center" style="color: var(--text-muted); justify-content: center;">No adjustments found</div>
                @endforelse
            </div>
            @if(count($data['recentAdjustments']) > 10)
            <div class="see-more-footer">
                <button type="button" onclick="toggleSeeMore(this)" class="see-more-btn">
                    <span>See More ({{ count($data['recentAdjustments']) - 10 }} more)</span>
                    <i class="fas fa-chevron-down text-xs"></i>
                </button>
            </div>
            @endif
        </div>
    </div>

    <!-- Data Tables Grid (10 items max each with See More option) -->
    <div class="watchlist-grid">
        <!-- Cheques Due Today Table -->
        <div class="table-card">
            <div class="card-title-bar">
                <h3><i class="fas fa-calendar-day text-rose-500"></i> Cheques Due Today</h3>
                <span class="badge-tag danger">{{ count($data['dueTodayCheques']) }} Today</span>
            </div>
            <div style="overflow-x: auto;">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Party Name</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['dueTodayCheques'] as $index => $cheque)
                        <tr class="{{ $index >= 10 ? 'expandable-item hidden' : '' }}">
                            <td class="font-medium">{{ $cheque->party_name }}</td>
                            <td class="text-right font-bold text-slate-800">Rs. {{ number_format($cheque->amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center" style="color: var(--text-muted); padding: 20px;">No cheques due today</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(count($data['dueTodayCheques']) > 10)
            <div class="see-more-footer">
                <button type="button" onclick="toggleSeeMore(this)" class="see-more-btn">
                    <span>See More ({{ count($data['dueTodayCheques']) - 10 }} more)</span>
                    <i class="fas fa-chevron-down text-xs"></i>
                </button>
            </div>
            @endif
        </div>

        <!-- Pending Cheques (Upcoming) Table -->
        <div class="table-card">
            <div class="card-title-bar">
                <h3><i class="fas fa-calendar-week text-indigo-500"></i> Pending Cheques (Upcoming)</h3>
                <span class="badge-tag info">{{ count($data['watchlistCheques']) }} Total</span>
            </div>
            <div style="overflow-x: auto;">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Party Name</th>
                            <th class="text-right">Maturity Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['watchlistCheques'] as $index => $cheque)
                        <tr class="{{ $index >= 10 ? 'expandable-item hidden' : '' }}">
                            <td class="font-medium">{{ $cheque->party_name }}</td>
                            <td class="text-right font-semibold text-slate-700">
                                {{ \Carbon\Carbon::parse($cheque->maturity_date_ad)->format('d M, Y') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center" style="color: var(--text-muted); padding: 20px;">No pending cheques</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(count($data['watchlistCheques']) > 10)
            <div class="see-more-footer">
                <button type="button" onclick="toggleSeeMore(this)" class="see-more-btn">
                    <span>See More ({{ count($data['watchlistCheques']) - 10 }} more)</span>
                    <i class="fas fa-chevron-down text-xs"></i>
                </button>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- JavaScript for Charts & See More Toggle -->
<script>
function toggleSeeMore(btn) {
    const card = btn.closest('.watchlist-card, .table-card');
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
    // Stock Status Pie Chart
    new Chart(document.getElementById('bakeryStockPieChart'), {
        type: 'doughnut',
        data: {
            labels: ['In Stock', 'Out of Stock'],
            datasets: [{
                data: [
                    @json($data['inStock'] ?? 0),
                    @json($data['outOfStock'] ?? 0)
                ],
                backgroundColor: ['#10b981', '#ef4444'],
                borderColor: '#ffffff',
                borderWidth: 3,
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { padding: 15, font: { family: 'Inter', weight: '600' } } }
            }
        }
    });

    // Monthly Sales vs Purchases Area Chart
    new Chart(document.getElementById('monthlyAreaChart'), {
        type: 'line',
        data: {
            labels: @json($data['monthlyLabels']),
            datasets: [
                {
                    label: 'Sales',
                    data: @json($data['monthlySales']),
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    tension: 0.35,
                    fill: true,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    borderWidth: 2
                },
                {
                    label: 'Purchases',
                    data: @json($data['monthlyPurchases']),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.35,
                    fill: true,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    borderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { font: { family: 'Inter', weight: '600' } } }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                x: { grid: { display: false } }
            }
        }
    });

    // Monthly Sales Comparison Bar Chart
    new Chart(document.getElementById('monthlyBarChart'), {
        type: 'bar',
        data: {
            labels: @json($data['monthlyLabels']),
            datasets: [{
                label: 'Sales',
                data: @json($data['monthlySales']),
                backgroundColor: '#10b981',
                borderRadius: 8,
                barPercentage: 0.65
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                x: { grid: { display: false } }
            }
        }
    });

    // Cheque Overview Radar Chart
    new Chart(document.getElementById('chequeRadarChart'), {
        type: 'radar',
        data: {
            labels: ['Due Today', 'Pending', 'Watchlist', 'Recent'],
            datasets: [{
                label: 'Cheque Metrics',
                data: [
                    @json(count($data['dueTodayCheques'] ?? [])),
                    @json(count($data['watchlistCheques'] ?? [])),
                    @json(count($data['watchlistCheques'] ?? [])),
                    @json(count($data['recentInvoices'] ?? []))
                ],
                backgroundColor: 'rgba(139, 92, 246, 0.18)',
                borderColor: '#8b5cf6',
                pointBackgroundColor: '#8b5cf6',
                pointBorderColor: '#ffffff',
                pointHoverRadius: 6,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { font: { family: 'Inter', weight: '600' } } }
            },
            scales: {
                r: { beginAtZero: true, grid: { color: '#f1f5f9' } }
            }
        }
    });

    // Wastage Pie Chart
    new Chart(document.getElementById('wastagePieChart'), {
        type: 'doughnut',
        data: {
            labels: @json($data['adjustmentLabels']),
            datasets: [{
                label: 'Wastage Quantity',
                data: @json($data['adjustmentValues']),
                backgroundColor: @json($data['adjustmentColors']),
                borderColor: '#ffffff',
                borderWidth: 3,
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { padding: 15, font: { family: 'Inter', weight: '600' } } }
            }
        }
    });
});
</script>
@endsection