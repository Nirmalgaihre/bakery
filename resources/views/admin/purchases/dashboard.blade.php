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

.main {
    padding: 16px;
    background-color: #f9fafb;
    font-family: 'Inter', sans-serif;
    min-height: 100vh;
}

@media (min-width: 768px) {
    .main {
        padding: 24px;
    }
}

/* Filter Container */
.filter-container {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 24px;
    background: #fff;
    padding: 16px 20px;
    border-radius: 16px;
    border: 1px solid var(--border-color);
    box-shadow: var(--glass-glow);
}

.section-title {
    font-size: 1.15rem;
    font-weight: 800;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-title i {
    background: #eff6ff;
    padding: 8px;
    border-radius: 10px;
    color: #4f46e5;
}

/* Stats Grid */
.stats {
    display: grid;
    grid-template-columns: 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

@media (min-width: 992px) {
    .stats {
        grid-template-columns: repeat(4, 1fr);
    }
}

.card {
    position: relative;
    overflow: hidden;
    height: 140px;
    border-radius: 20px;
    padding: 20px;
    color: #fff;
    transition: 0.3s;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.06);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.icon-box {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.2);
}

.card h4 {
    font-size: 0.8rem;
    font-weight: 500;
    opacity: 0.9;
    margin: 0;
    text-transform: uppercase;
}

.card h2 {
    font-size: 1.45rem;
    font-weight: 800;
    margin: 0;
}

/* Layout Components */
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
    border: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    gap: 14px;
}

.mini-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f3f4f6;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 20px;
    margin-bottom: 24px;
}

@media (min-width: 992px) {
    .dashboard-grid {
        grid-template-columns: 1fr 1.3fr;
    }
}

.widget {
    background: #fff;
    padding: 22px;
    border-radius: 20px;
    border: 1px solid var(--border-color);
}

.widget-title {
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 2px solid #f3f4f6;
    display: flex;
    align-items: center;
    gap: 10px;
}

/* Table & Chart Styles */
.chart-container {
    position: relative;
    height: 240px;
    width: 100%;
}

.custom-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 6px;
}

.custom-table td {
    padding: 14px;
    border-top: 1px solid #f3f4f6;
    border-bottom: 1px solid #f3f4f6;
}

.badge {
    padding: 4px 10px;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 600;
    background: #eff6ff;
    color: #1d4ed8;
}
</style>

<div class="main">
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4 mb-6">
        <div class="flex items-center gap-2 mb-4 text-gray-700 font-semibold">
            <i class="fas fa-database text-blue-600"></i>
            <span>Dashboard Reporting</span>
        </div>

        <form action="{{ route('admin.purchases.dashboard') }}" method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex flex-col gap-1">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Year</label>
                <select name="year" onchange="this.form.submit()"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-blue-500 focus:border-blue-500 block p-2 w-40">
                    @php $currentYear = date('Y'); @endphp
                    @for($y = $currentYear; $y >= $currentYear - 5; $y--)
                    <option value="{{ $y }}" {{ request('year', $currentYear) == $y ? 'selected' : '' }}>{{ $y }}
                    </option>
                    @endfor
                </select>
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Period</label>
                <select name="month" onchange="this.form.submit()"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-blue-500 focus:border-blue-500 block p-2 w-48">
                    <option value="">Full Year Summary</option>
                    @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                        {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="ml-auto text-sm text-gray-600 bg-gray-100 px-3 py-2 rounded-md border border-gray-200">
                Showing: <strong class="text-blue-700">{{ $dateRange }}</strong>
            </div>
        </form>
    </div>

    <div class="stats">
        <div class="card" style="background: var(--bg-card-blue);">
            <div class="icon-box"><i class="fas fa-wallet"></i></div>
            <div>
                <h4>Total Expenditure</h4>
                <h2>Rs. {{ number_format($totalPurchased, 2) }}</h2>
            </div>
        </div>
        <div class="card" style="background: var(--bg-card-green);">
            <div class="icon-box"><i class="fas fa-file-invoice"></i></div>
            <div>
                <h4>Total Purchases</h4>
                <h2>{{ number_format($purchaseCount) }}</h2>
            </div>
        </div>
        <div class="card" style="background: var(--bg-card-purple);">
            <div class="icon-box"><i class="fas fa-bolt"></i></div>
            <div>
                <h4>Purchased Today</h4>
                <h2>Rs. {{ number_format($purchasesToday, 2) }}</h2>
            </div>
        </div>
        <div class="card" style="background: var(--bg-card-orange);">
            <div class="icon-box"><i class="fas fa-calculator"></i></div>
            <div>
                <h4>Avg Purchase</h4>
                <h2>Rs. {{ number_format($averagePurchase, 2) }}</h2>
            </div>
        </div>
    </div>
    <!-- Updated Mini Cards Section -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <!-- Catalog Items -->
        <div class="bg-white p-5 border border-gray-200">
            <h3 class="text-2xl font-bold text-gray-800">{{ $totalProducts }}</h3>
            <p class="text-[11px] font-bold text-gray-500 uppercase tracking-widest mt-1">Catalog Items</p>
            <div class="w-full bg-gray-100 h-1 mt-4 rounded-full">
                <div class="bg-indigo-500 h-1" style="width: 100%"></div>
            </div>
        </div>
        <!-- In Stock -->
        <div class="bg-white p-5 border border-gray-200">
            <h3 class="text-2xl font-bold text-gray-800">{{ $inStock }}</h3>
            <p class="text-[11px] font-bold text-gray-500 uppercase tracking-widest mt-1">In Stock</p>
            <div class="w-full bg-gray-100 h-1 mt-4 rounded-full">
                <div class="bg-emerald-500 h-1" style="width: 70%"></div>
            </div>
        </div>
        <!-- Out of Stock -->
        <div class="bg-white p-5 border border-gray-200">
            <h3 class="text-2xl font-bold text-gray-800">{{ $outOfStock }}</h3>
            <p class="text-[11px] font-bold text-gray-500 uppercase tracking-widest mt-1">Out of Stock</p>
            <div class="w-full bg-gray-100 h-1 mt-4 rounded-full">
                <div class="bg-red-500 h-1" style="width: 30%"></div>
            </div>
        </div>
        <!-- Placeholder for extra metric -->
        <div class="bg-white p-5 border border-gray-200">
            <h3 class="text-2xl font-bold text-gray-800">{{ count($suppliers) }}</h3>
            <p class="text-[11px] font-bold text-gray-500 uppercase tracking-widest mt-1">Active Suppliers</p>
            <div class="w-full bg-gray-100 h-1 mt-4 rounded-full">
                <div class="bg-blue-500 h-1" style="width: 50%"></div>
            </div>
        </div>
    </div>

    <!-- Main Grid Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Expenditure Trend -->
        <div class="bg-white p-6 border border-gray-200">
            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-4 flex items-center gap-2">
                <i class="fas fa-chart-pie"></i> Expenditure Trend
            </h3>
            <div class="h-60 w-full">
                <canvas id="salesPieChart"></canvas>
            </div>
        </div>

        <!-- Top Suppliers -->
        <div class="bg-white p-6 border border-gray-200">
            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-4 flex items-center gap-2">
                <i class="fas fa-crown"></i> Top Suppliers
            </h3>
            <table class="w-full">
                @foreach($suppliers as $s)
                <tr class="border-b border-gray-50 last:border-0">
                    <td class="py-3 text-sm text-gray-600">{{ $s->supplier_name }}</td>
                    <td class="py-3 text-right">
                        <span
                            class="px-2 py-1 bg-gray-100 text-[10px] font-bold uppercase rounded">{{ $s->purchase_count }}
                            Orders</span>
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>

    <!-- Recent Procurement Log -->
    <div class="bg-white p-6 border border-gray-200">
        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-4 flex items-center gap-2">
            <i class="fas fa-history"></i> Recent Procurement Log
        </h3>
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-400 border-b border-gray-200">
                    <th class="pb-3">Date</th>
                    <th class="pb-3">Supplier</th>
                    <th class="pb-3 text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentPurchases as $p)
                <tr class="border-b border-gray-50 last:border-0">
                    <td class="py-4 text-gray-500">{{ $p->created_at->format('M d, Y') }}</td>
                    <td class="py-4 font-medium text-gray-800">{{ $p->supplier_name }}</td>
                    <td class="py-4 text-right font-bold text-gray-900">Rs. {{ number_format($p->total_amount, 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const chartData = @json($chartData);
    new Chart(document.getElementById('salesPieChart'), {
        type: 'pie',
        data: {
            labels: chartData.map(i => i.date),
            datasets: [{
                data: chartData.map(i => i.total),
                backgroundColor: ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6',
                    '#06b6d4', '#ec4899'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
@endsection