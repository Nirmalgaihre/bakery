@extends('layouts.admin')

@section('title', 'Bakery & Cheque Management Dashboard')

@section('content')
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
}

.main {
    padding: 16px;
    background-color: #f9fafb;
    font-family: 'Inter', sans-serif;
    box-sizing: border-box;
}

@media (min-width: 768px) {
    .main {
        padding: 24px;
    }
}

.stats {
    display: grid;
    grid-template-columns: 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

@media (min-width: 576px) {
    .stats {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 992px) {
    .stats {
        grid-template-columns: repeat(4, 1fr);
    }
}

.card {
    position: relative;
    overflow: hidden;
    height: 150px;
    border-radius: 20px;
    padding: 20px;
    color: #fff;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.06);
}

.card-header-row,
.card-body-row {
    position: relative;
    z-index: 1;
}

.card-header-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
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

.card-meta {
    font-size: 0.72rem;
    background: rgba(255, 255, 255, 0.22);
    padding: 4px 10px;
    border-radius: 20px;
    font-weight: 600;
}

.card-body-row h4 {
    font-size: 0.8rem;
    font-weight: 500;
    opacity: 0.9;
    margin: 0 0 4px 0;
    text-transform: uppercase;
}

.card-body-row h2 {
    font-size: 1.4rem;
    font-weight: 800;
    margin: 0;
}

.blue { background: var(--bg-card-blue); }
.green { background: var(--bg-card-green); }
.orange { background: var(--bg-card-orange); }
.purple { background: var(--bg-card-purple); }
.red { background: var(--bg-card-red); }

.dashboard-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 20px;
    margin-bottom: 24px;
}

@media (min-width: 992px) {
    .dashboard-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

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

.chart-container {
    position: relative;
    width: 100%;
    overflow: hidden;
}

.chart-h-36 { height: 36vh; min-height: 260px; }
.chart-h-32 { height: 32vh; min-height: 240px; }
.chart-h-28 { height: 28vh; min-height: 220px; }
.chart-h-30 { height: 30vh; min-height: 230px; }

.watchlist-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.watch-card {
    background: #ffffff;
    border: 1px solid #334e6f;
    border-radius: 4px;
    overflow: hidden;
}

.watch-card h3 {
    background: #334e6f;
    color: #ffffff;
    margin: 0;
    padding: 10px 15px;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.watch-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 15px;
    border-bottom: 1px solid #dee2e6;
    font-size: 0.9rem;
}

.watch-item:last-child {
    border-bottom: none;
}

.watch-right {
    text-align: right;
    color: #444;
    line-height: 1.2;
}

.gov-table-container {
    background: #ffffff;
    border: 1px solid #334e6f;
}

.gov-table-header {
    background: #334e6f;
    color: #ffffff;
    padding: 10px 15px;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
}

.gov-table {
    width: 100%;
    border-collapse: collapse;
    font-family: 'Segoe UI', Tahoma, sans-serif;
}

.gov-table th {
    background: #f4f7f9;
    color: #444;
    font-size: 0.75rem;
    padding: 8px 15px;
    text-align: left;
    border-bottom: 2px solid #334e6f;
}

.gov-table td {
    padding: 10px 15px;
    border-bottom: 1px solid #dee2e6;
    font-size: 0.9rem;
    color: #2c3e50;
}

.gov-table tr:nth-child(even) {
    background-color: #f9f9f9;
}

.text-right { text-align: right; }
.text-center { text-align: center; color: #7f8c8d; }
.data-value { font-weight: 600; color: #000; }

@media (max-width: 640px) {
    .watchlist-grid {
        grid-template-columns: 1fr;
    }

    .chart-h-36,
    .chart-h-32,
    .chart-h-28,
    .chart-h-30 {
        height: 280px;
        min-height: 280px;
    }
}
/* Card Container */

.stat-card {

    background: #ffffff;

    padding: 20px;

    border: 1px solid #e2e8f0; /* Matches your border-gray-200 */

    border-radius: 12px;

}



.stat-value {

    font-size: 1.5rem;

    font-weight: 800;

    color: #1f2937;

    margin: 0;

}



.stat-label {

    font-size: 0.68rem;

    font-weight: 800;

    color: #64748b;

    text-transform: uppercase;

    letter-spacing: 0.1em;

    margin-top: 4px;

}



/* Progress Bar Container */

.progress-bg {

    width: 100%;

    background: #f1f5f9;

    height: 4px;

    margin-top: 16px;

    border-radius: 2px;

}



.progress-fill {

    height: 100%;

    border-radius: 2px;

}



/* Solid Color Bottom Cards */

.solid-card {

    padding: 24px;

    color: #ffffff;

    border-radius: 16px;

    display: flex;

    flex-direction: column;

}



.solid-card h3 {

    font-size: 1.85rem;

    font-weight: 600;

    margin: 0;

}



.solid-card p {

    font-size: 0.875rem;

    opacity: 0.8;

    margin: 4px 0 0 0;

}



/* Table Enhancements */

.wastage-table th {

    background: #f8fafc;

    color: #475569;

    font-size: 0.7rem;

}



.tag {

    padding: 2px 8px;

    border-radius: 6px;

    font-size: 0.6rem;

    font-weight: 800;

    text-transform: uppercase;

}
</style>

<div class="main">
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4 mb-6">
        <div class="flex items-center gap-2 mb-4 text-gray-700 font-semibold">
            <i class="fas fa-database text-blue-600"></i>
            <span>Dashboard Reporting</span>
        </div>

        <form action="{{ route('admin.dashboard') }}" method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex flex-col gap-1">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Year</label>
                <select name="year" onchange="this.form.submit()"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-blue-500 focus:border-blue-500 block p-2 w-40">
                    @php $currentYear = date('Y'); @endphp
                    @for($y = $currentYear; $y >= $currentYear - 5; $y--)
                    <option value="{{ $y }}" {{ request('year', $currentYear) == $y ? 'selected' : '' }}>{{ $y }}</option>
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
                <div class="card-meta">{{ $data['purchaseCount'] }} Purchases</div>
            </div>
            <div class="card-body-row">
                <h4>Supply Purchases</h4>
                <h2>Rs. {{ number_format($data['totalSpent'], 2) }}</h2>
            </div>
        </div>

        <div class="card purple">
            <div class="card-header-row">
                <div class="icon-box"><i class="fas fa-dolly"></i></div>
                <div class="card-meta">{{ number_format($data['stockOutQty']) }} Units</div>
            </div>
            <div class="card-body-row">
                <h4>Cost of Goods Sold</h4>
                <h2>Rs. {{ number_format($data['costOfGoodsSold'], 2) }}</h2>
            </div>
        </div>

        <div class="card {{ $data['netProfit'] >= 0 ? 'green' : 'red' }}">
            <div class="card-header-row">
                <div class="icon-box"><i class="fas fa-money-bill-wave"></i></div>
                <div class="card-meta">Net Profit</div>
            </div>
            <div class="card-body-row">
                <h4>Net Profit</h4>
                <h2>Rs. {{ number_format($data['netProfit'], 2) }}</h2>
            </div>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="widget">
            <div class="widget-title"><i class="fas fa-chart-line"></i> Monthly Sales vs Purchases</div>
            <div class="chart-container chart-h-36">
                <canvas id="monthlyAreaChart"></canvas>
            </div>
        </div>

        <div class="widget">
            <div class="widget-title"><i class="fas fa-chart-bar"></i> Monthly Sales Comparison</div>
            <div class="chart-container chart-h-32">
                <canvas id="monthlyBarChart"></canvas>
            </div>
        </div>

        <div class="widget">
            <div class="widget-title"><i class="fas fa-chart-area"></i> Cheque Overview</div>
            <div class="chart-container chart-h-30">
                <canvas id="chequeRadarChart"></canvas>
            </div>
        </div>
    </div>
    <!-- Top section: White Cards with Progress Bars -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <!-- Products Card -->
        <div class="bg-white p-5 border border-gray-200">
            <h3 class="text-2xl font-bold text-gray-800">{{ $data['totalProducts'] }}</h3>
            <p class="text-[11px] font-bold text-gray-500 uppercase tracking-widest mt-1">Total Products</p>
            <div class="w-full bg-gray-100 h-1 mt-4 rounded-full">
                <div class="bg-emerald-500 h-1" style="width: 60%"></div>
            </div>
        </div>
        <!-- Customers Card -->
        <div class="bg-white p-5 border border-gray-200">
            <h3 class="text-2xl font-bold text-gray-800">{{ $data['totalCustomers'] }}</h3>
            <p class="text-[11px] font-bold text-gray-500 uppercase tracking-widest mt-1">Customers</p>
            <div class="w-full bg-gray-100 h-1 mt-4 rounded-full">
                <div class="bg-blue-600 h-1" style="width: 80%"></div>
            </div>
        </div>
        <!-- In Stock Card -->
        <div class="bg-white p-5 border border-gray-200">
            <h3 class="text-2xl font-bold text-gray-800">{{ $data['inStock'] }}</h3>
            <p class="text-[11px] font-bold text-gray-500 uppercase tracking-widest mt-1">Products In Stock</p>
            <div class="w-full bg-gray-100 h-1 mt-4 rounded-full">
                <div class="bg-amber-400 h-1" style="width: 45%"></div>
            </div>
        </div>
        <!-- Out of Stock Card -->
        <div class="bg-white p-5 border border-gray-200">
            <h3 class="text-2xl font-bold text-gray-800">{{ $data['outOfStock'] }}</h3>
            <p class="text-[11px] font-bold text-gray-500 uppercase tracking-widest mt-1">Products Out Of Stock</p>
            <div class="w-full bg-gray-100 h-1 mt-4">
                <div class="bg-red-600 h-1" style="width: 90%"></div>
            </div>
        </div>
    </div>
    <!-- Bottom: Solid Color Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-indigo-600 p-6 text-white">
            <h3 class="text-3xl font-semibold">{{ $data['nearExpiry'] }}</h3>
            <p class="text-sm opacity-80 mt-1">Near Expiry</p>
        </div>
        <div class="bg-orange-400 p-6 text-white">
            <h3 class="text-3xl font-semibold">{{ $data['chequesPending'] }}</h3>
            <p class="text-sm opacity-80 mt-1">Pending Cheques</p>
        </div>
        <div class="bg-rose-500 p-6 text-white">
            <h3 class="text-3xl font-semibold">{{ $data['chequesBounced'] }}</h3>
            <p class="text-sm opacity-80 mt-1">Bounced Cheques</p>
        </div>
        <div class="bg-sky-500 p-6 text-white">
            <h3 class="text-3xl font-semibold">{{ $data['chequesCleared'] }}</h3>
            <p class="text-sm opacity-80 mt-1">Cleared Cheques</p>
        </div>
    </div>

    <div class="dashboard-grid">
        {{-- Stock Pie Chart Left Block --}}
        <div class="widget">
            <div class="widget-title"><i class="fas fa-chart-pie"></i> Stock Status</div>
            <div class="chart-container chart-h-36">
                <canvas id="bakeryStockPieChart"></canvas>
            </div>
        </div>

        <div class="widget">
            <div class="widget-title text-red-600"><i class="fas fa-dumpster text-red-500"></i> Wastage & Adjustments
                Breakdown</div>
            <div class="chart-container chart-h-36">
                <canvas id="wastagePieChart"></canvas>
                @if($data['totalWastage'] == 0)
                <div class="absolute inset-0 flex items-center justify-center text-slate-400 font-medium">No wastage data for this period.</div>
                @endif
            </div>
        </div>
    </div>

    <div class="watchlist-grid">
        <div class="watch-card">
            <h3>Low Stock Watchlist</h3>
            @forelse($data['lowStockProducts'] as $product)
            <div class="watch-item">
                <div class="watch-left">{{ $product->name }}</div>
                <div class="watch-right">
                    Stock: {{ $product->stock }}<br>
                    Alert: {{ $product->alert_stock_level }}
                </div>
            </div>
            @empty
            <div class="watch-item">No low stock products</div>
            @endforelse
        </div>

        <div class="watch-card">
            <h3>Pending Cheque Watchlist</h3>
            @forelse($data['watchlistCheques'] as $cheque)
            <div class="watch-item">
                <div class="watch-left">{{ $cheque->party_name }}</div>
                <div class="watch-right">
                    Cheque: {{ $cheque->cheque_no }}<br>
                    Rs. {{ number_format($cheque->amount, 2) }}
                </div>
            </div>
            @empty
            <div class="watch-item">No pending cheques</div>
            @endforelse
        </div>

        <div class="watch-card">
            <h3>Due Customers</h3>
            @forelse($data['dueCustomers'] as $customer)
            <div class="watch-item">
                <div class="watch-left">{{ $customer->name }}</div>
                <div class="watch-right">
                    Phone: {{ $customer->phone_number }}<br>
                    Due: Rs. {{ number_format($customer->previous_due, 2) }}
                </div>
            </div>
            @empty
            <div class="watch-item">No due customers</div>
            @endforelse
        </div>

        <div class="watch-card">
            <h3>Recent Adjustments</h3>
            @forelse($data['recentAdjustments'] as $adjustment)
            <div class="watch-item">
                <div class="watch-left">{{ $adjustment->product->name ?? 'Deleted Product' }}</div>
                <div class="watch-right">
                    {{ strtoupper($adjustment->type) }}<br>
                    Qty: {{ number_format($adjustment->quantity, 2) }}
                </div>
            </div>
            @empty
            <div class="watch-item">No adjustments found</div>
            @endforelse
        </div>

        
    </div>

    <div class="watchlist-grid">
        <div class="gov-table-container">
            <div class="gov-table-header">Cheques Due Today</div>
            <table class="gov-table">
                <thead>
                    <tr>
                        <th>Party</th>
                        <th class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data['dueTodayCheques'] as $cheque)
                    <tr>
                        <td>{{ $cheque->party_name }}</td>
                        <td class="text-right">Rs. {{ number_format($cheque->amount, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="text-center">No cheques due today</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="gov-table-container">
            <div class="gov-table-header">Pending Cheques (Upcoming)</div>
            <table class="gov-table">
                <thead>
                    <tr>
                        <th>Party</th>
                        <th class="text-right">Maturity</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data['watchlistCheques'] as $cheque)
                    <tr>
                        <td>{{ $cheque->party_name }}</td>
                        <td class="text-right">{{ \Carbon\Carbon::parse($cheque->maturity_date_ad)->format('d M, Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="text-center">No pending cheques</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    new Chart(document.getElementById('bakeryStockPieChart'), {
        type: 'pie',
        data: {
            labels: ['In Stock', 'Out of Stock'],
            datasets: [{
                data: [
                    @json($data['inStock'] ?? 0),
                    @json($data['outOfStock'] ?? 0)
                ],
                backgroundColor: ['#10b981', '#ef4444'],
                borderColor: '#ffffff',
                borderWidth: 2,
                hoverOffset: 8
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

    new Chart(document.getElementById('monthlyAreaChart'), {
        type: 'line',
        data: {
            labels: @json($data['monthlyLabels']),
            datasets: [
                {
                    label: 'Sales',
                    data: @json($data['monthlySales']),
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.12)',
                    tension: 0.35,
                    fill: true,
                    pointRadius: 3,
                    borderWidth: 2
                },
                {
                    label: 'Purchases',
                    data: @json($data['monthlyPurchases']),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.12)',
                    tension: 0.35,
                    fill: true,
                    pointRadius: 3,
                    borderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    new Chart(document.getElementById('monthlyBarChart'), {
        type: 'bar',
        data: {
            labels: @json($data['monthlyLabels']),
            datasets: [{
                label: 'Sales',
                data: @json($data['monthlySales']),
                backgroundColor: '#4f46e5',
                borderRadius: 8,
                barPercentage: 0.7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

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
                backgroundColor: 'rgba(124, 58, 237, 0.18)',
                borderColor: '#7c3aed',
                pointBackgroundColor: '#7c3aed',
                pointBorderColor: '#ffffff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            },
            scales: {
                r: { beginAtZero: true }
            }
        }
    });

    new Chart(document.getElementById('wastagePieChart'), {
        type: 'doughnut',
        data: {
            labels: @json($data['adjustmentLabels']),
            datasets: [{
                label: 'Wastage Quantity',
                data: @json($data['adjustmentValues']),
                backgroundColor: @json($data['adjustmentColors']),
                borderColor: '#ffffff',
                borderWidth: 2,
                hoverOffset: 8
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