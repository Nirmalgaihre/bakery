@extends('layouts.admin')

@section('title', 'Procurement Dashboard')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
:root {
    --bg-card-blue: linear-gradient(135deg, #4f46e5, #7c3aed);
    --bg-card-green: linear-gradient(135deg, #10b981, #059669);
    --bg-card-orange: linear-gradient(135deg, #f97316, #ea580c);
    --bg-card-purple: linear-gradient(135deg, #ec4899, #d946ef);
    --text-primary: #1f2937;
    --text-muted: #6b7280;
    --border-color: #e5e7eb;
    --glass-glow: 0 8px 32px 0 rgba(31, 38, 135, 0.05);
}

.main { padding: 16px; background-color: #f9fafb; font-family: 'Inter', sans-serif; min-height: 100vh; }
@media (min-width: 768px) { .main { padding: 24px; } }

/* Filter Container */
.filter-container { display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: 16px; margin-bottom: 24px; background: #fff; padding: 16px 20px; border-radius: 16px; border: 1px solid var(--border-color); box-shadow: var(--glass-glow); }
.section-title { font-size: 1.15rem; font-weight: 800; color: var(--text-primary); display: flex; align-items: center; gap: 10px; }
.section-title i { background: #eff6ff; padding: 8px; border-radius: 10px; color: #4f46e5; }

/* Stats Grid & Widgets */
.stats { display: grid; grid-template-columns: 1fr; gap: 20px; margin-bottom: 20px; }
@media (min-width: 992px) { .stats { grid-template-columns: repeat(4, 1fr); } }
.card { position: relative; overflow: hidden; height: 140px; border-radius: 20px; padding: 20px; color: #fff; transition: 0.3s; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.06); display: flex; flex-direction: column; justify-content: space-between; }
.card:hover { transform: translateY(-5px); }
.card::before { content: ''; position: absolute; width: 110px; height: 110px; top: -30px; right: -30px; border-radius: 50%; background: rgba(255, 255, 255, 0.1); }
.icon-box { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 12px; background: rgba(255, 255, 255, 0.2); }
.card h4 { font-size: 0.8rem; font-weight: 500; opacity: 0.9; margin: 0; text-transform: uppercase; }
.card h2 { font-size: 1.45rem; font-weight: 800; margin: 0; }
.mini-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px; }
.mini { background: #fff; padding: 16px; border-radius: 18px; border: 1px solid var(--border-color); display: flex; align-items: center; gap: 14px; }
.mini-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: #f3f4f6; }
.dashboard-grid { display: grid; grid-template-columns: 1fr; gap: 20px; margin-bottom: 24px; }
@media (min-width: 992px) { .dashboard-grid { grid-template-columns: 1fr 1.3fr; } }
.widget { background: #fff; padding: 22px; border-radius: 20px; border: 1px solid var(--border-color); }
.widget-title { font-weight: 700; color: var(--text-primary); margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #f3f4f6; display: flex; align-items: center; gap: 10px; }
.custom-table { width: 100%; border-collapse: separate; border-spacing: 0 6px; }
.custom-table td { padding: 14px; border-top: 1px solid #f3f4f6; border-bottom: 1px solid #f3f4f6; }
.badge { padding: 4px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 600; background: #eff6ff; color: #1d4ed8; }
</style>

<div class="main">
    <div class="filter-container">
        <div class="section-title"><i class="fas fa-shopping-cart"></i> Procurement Overview</div>
        <form action="{{ route('admin.purchases.dashboard') }}" method="GET" class="flex items-center gap-3">
            <label class="text-xs font-bold text-slate-500">FISCAL YEAR:</label>
            <select name="fiscal_year" onchange="this.form.submit()" class="px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500 outline-none">
                @foreach(\App\Helpers\FiscalYearHelper::getFiscalYearList() as $fyOption)
                    <option value="{{ $fyOption }}" {{ request('fiscal_year', \App\Helpers\FiscalYearHelper::getCurrentFiscalYear()) == $fyOption ? 'selected' : '' }}>
                        {{ $fyOption }}
                    </option>
                @endforeach
            </select>
            <a href="{{ route('admin.purchases.dashboard') }}" class="text-xs text-slate-400 hover:text-blue-600 underline">Reset</a>
        </form>
    </div>

    <div class="stats">
        <div class="card" style="background: var(--bg-card-blue);"><div class="icon-box"><i class="fas fa-wallet"></i></div><div><h4>Total Expenditure</h4><h2>Rs. {{ number_format($totalPurchased, 2) }}</h2></div></div>
        <div class="card" style="background: var(--bg-card-green);"><div class="icon-box"><i class="fas fa-file-invoice"></i></div><div><h4>Total Purchases</h4><h2>{{ number_format($purchaseCount) }}</h2></div></div>
        <div class="card" style="background: var(--bg-card-purple);"><div class="icon-box"><i class="fas fa-bolt"></i></div><div><h4>Purchased Today</h4><h2>Rs. {{ number_format($purchasesToday, 2) }}</h2></div></div>
        <div class="card" style="background: var(--bg-card-orange);"><div class="icon-box"><i class="fas fa-calculator"></i></div><div><h4>Avg Purchase</h4><h2>Rs. {{ number_format($averagePurchase, 2) }}</h2></div></div>
    </div>

    <div class="mini-cards">
        <div class="mini"><div class="mini-icon"><i class="fas fa-cubes"></i></div><div><p style="font-size:0.7rem; color:var(--text-muted); margin:0;">Catalog Items</p><h3>{{ $totalProducts }}</h3></div></div>
        <div class="mini"><div class="mini-icon"><i class="fas fa-check-circle"></i></div><div><p style="font-size:0.7rem; color:var(--text-muted); margin:0;">In Stock</p><h3>{{ $inStock }}</h3></div></div>
        <div class="mini"><div class="mini-icon"><i class="fas fa-times-circle"></i></div><div><p style="font-size:0.7rem; color:var(--text-muted); margin:0;">Out of Stock</p><h3>{{ $outOfStock }}</h3></div></div>
    </div>

    <div class="dashboard-grid">
        <div class="widget">
            <div class="widget-title"><i class="fas fa-chart-pie"></i> Expenditure Trend</div>
            <div style="height: 240px;"><canvas id="salesPieChart"></canvas></div>
        </div>
        <div class="widget">
            <div class="widget-title"><i class="fas fa-crown"></i> Top Suppliers</div>
            <table class="custom-table">
                @foreach($suppliers as $s)
                <tr>
                    <td>{{ $s->supplier_name }}</td>
                    <td style="text-align: right;"><span class="badge">{{ $s->purchase_count }} Orders</span></td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>

    <div class="widget">
        <div class="widget-title"><i class="fas fa-history"></i> Recent Procurement Log</div>
        <table class="custom-table">
            @foreach($recentPurchases as $p)
            <tr>
                <td>{{ $p->created_at->format('M d, Y') }}</td>
                <td>{{ $p->supplier_name }}</td>
                <td style="text-align: right; font-weight: 700;">Rs. {{ number_format($p->total_amount, 2) }}</td>
            </tr>
            @endforeach
        </table>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const chartData = @json($chartData);
    new Chart(document.getElementById('salesPieChart').getContext('2d'), {
        type: 'pie',
        data: {
            labels: chartData.map(i => i.date),
            datasets: [{
                data: chartData.map(i => i.total),
                backgroundColor: ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#ec4899']
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
    });
});
</script>
@endsection