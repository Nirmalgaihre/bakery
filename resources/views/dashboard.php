@extends('layouts.admin')

@section('title', 'Admin Control Dashboard - Bakery Engine')

@section('content')
    <header class="filter-bar">
        <div class="date-filters">
            <div class="input-group">
                <label>From:</label>
                <input type="date" value="2026-05-08">
            </div>
            <div class="input-group">
                <label>To:</label>
                <input type="date" value="2026-06-07">
            </div>
            <div class="search-items">
                <input type="text" placeholder="Search items...">
            </div>
            <button class="win-btn-primary">Apply</button>
            <button class="win-btn-secondary"><i class="fa-solid fa-rotate-right"></i> Reset</button>
        </div>
        <div class="utility-btns">
            <button class="btn-text"><i class="fa-solid fa-rotate"></i> Refresh</button>
            <button class="btn-text"><i class="fa-solid fa-download"></i> Export</button>
        </div>
    </header>

    <nav class="nav-tabs">
        <a href="#" class="tab active"><i class="fa-solid fa-chart-pie"></i> Overview</a>
        <a href="#" class="tab"><i class="fa-solid fa-arrows-left-right"></i> Transactions</a>
        <a href="#" class="tab"><i class="fa-solid fa-boxes-stacked"></i> Items</a>
        <a href="#" class="tab"><i class="fa-solid fa-chart-line"></i> Analytics</a>
        <a href="#" class="tab"><i class="fa-solid fa-file-invoice"></i> Reports</a>
    </nav>

    <div class="dashboard-grid">
        
        <div class="card card-purple">
            <div class="card-icon"><i class="fa-solid fa-cart-shopping"></i></div>
            <div class="card-value">Rs. 0</div>
            <div class="card-label">Net Sales</div>
            <div class="card-subtext"><i class="fa-solid fa-file-lines"></i> 0 Invoices</div>
        </div>

        <div class="card card-green">
            <div class="card-icon"><i class="fa-solid fa-bag-shopping"></i></div>
            <div class="card-value">Rs. 0</div>
            <div class="card-label">Net Purchases</div>
            <div class="card-subtext"><i class="fa-solid fa-file-lines"></i> 0 Invoices</div>
        </div>

        <div class="card card-orange">
            <div class="card-icon-top"><i class="fa-solid fa-circle-plus"></i></div>
            <div class="card-value">0</div>
            <div class="card-label">Stock In (Qty)</div>
            <div class="card-subtext">+ Received</div>
        </div>

        <div class="card card-pink">
            <div class="card-icon-top"><i class="fa-solid fa-circle-plus"></i></div>
            <div class="card-value">0</div>
            <div class="card-label">Stock Out (Qty)</div>
            <div class="card-subtext">- Dispatched</div>
        </div>

        <div class="mini-card-grid grid-span-2">
            <div class="mini-card">
                <div class="mini-icon icon-blue"><i class="fa-solid fa-layer-group"></i></div>
                <div>
                    <div class="mini-value">15</div>
                    <div class="mini-label">Total Items</div>
                </div>
            </div>
            <div class="mini-card">
                <div class="mini-icon icon-green"><i class="fa-solid fa-circle-check"></i></div>
                <div>
                    <div class="mini-value">0</div>
                    <div class="mini-label">In Stock</div>
                </div>
            </div>
            <div class="mini-card">
                <div class="mini-icon icon-yellow"><i class="fa-solid fa-circle-exclamation"></i></div>
                <div>
                    <div class="mini-value">0</div>
                    <div class="mini-label">Low Stock</div>
                </div>
            </div>
            <div class="mini-card">
                <div class="mini-icon icon-red"><i class="fa-solid fa-circle-xmark"></i></div>
                <div>
                    <div class="mini-value">15</div>
                    <div class="mini-label">Out of Stock</div>
                </div>
            </div>
            <div class="mini-card grid-span-2-inner">
                <div>
                    <div class="mini-value">Rs. 0</div>
                    <div class="mini-label">Stock Value</div>
                </div>
            </div>
            <div class="mini-card">
                <div>
                    <div class="mini-value">0</div>
                    <div class="mini-label">Total Qty</div>
                </div>
            </div>
        </div>

        <div class="fluent-card">
            <div class="chart-header">
                <span class="chart-title">STOCK TURNOVER</span>
                <span class="badge badge-orange">Normal</span>
            </div>
            <div class="turnover-value">2.4x</div>
            <div class="bar-chart-mock">
                <div class="bar" style="height: 40%;"></div>
                <div class="bar" style="height: 60%;"></div>
                <div class="bar" style="height: 50%;"></div>
                <div class="bar" style="height: 75%;"></div>
                <div class="bar" style="height: 70%;"></div>
                <div class="bar" style="height: 85%;"></div>
            </div>
        </div>

        <div class="fluent-card">
            <div class="chart-header">
                <span class="chart-title">PROFIT MARGIN</span>
            </div>
            <div class="turnover-value">18%</div>
            <div class="line-chart-mock">
                <svg viewBox="0 0 100 30" class="wave-svg">
                    <path d="M0,25 Q25,25 50,15 T100,5" fill="none" stroke="#0078d4" stroke-width="2"/>
                    <path d="M0,25 Q25,25 50,15 T100,5 L100,30 L0,30 Z" fill="rgba(0, 120, 212, 0.08)"/>
                </svg>
            </div>
        </div>

        <div class="fluent-card grid-span-2">
            <div class="chart-header">
                <span class="chart-title">SALES TODAY</span>
                <span class="badge badge-green">+12%</span>
            </div>
            <div class="large-value">Rs. 0</div>
            <div class="horizontal-line h-line-green"></div>
        </div>

        <div class="fluent-card grid-span-2">
            <div class="chart-header">
                <span class="chart-title">PURCHASES TODAY</span>
                <span class="badge badge-blue">+8%</span>
            </div>
            <div class="large-value">Rs. 0</div>
            <div class="horizontal-line h-line-blue"></div>
        </div>

        <div class="fluent-card grid-span-4">
            <div class="trend-header">
                <span><i class="fa-solid fa-chart-line"></i> Daily Sales vs Purchase Trend</span>
                <div class="legend">
                    <span class="legend-item"><span class="dot dot-green"></span> Sales</span>
                    <span class="legend-item"><span class="dot dot-blue"></span> Purchases</span>
                </div>
            </div>
            <div class="trend-placeholder">
                <span>1.0</span>
                <div class="dotted-line"></div>
            </div>
        </div>

    </div>
@endsection