@extends('admin.crbt.base')

@section('page_title', 'Daily CRBT MIS Report')
@section('report_title', 'Daily CRBT Activities Summary')

@section('report_content')
<!-- Filter Section -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <input type="date" id="startDate" class="form-control" placeholder="Start date">
    </div>
    <div class="col-md-4">
        <input type="date" id="endDate" class="form-control" placeholder="End date">
    </div>
    <div class="col-md-4 d-grid">
        <button id="applyFilter" class="btn btn-primary">Apply Filter</button>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted mb-3">Total Subscriptions</h6>
                <h3 class="f-w-300 d-flex align-items-center m-b-0" id="totalSubscriptions">
                    <i class="feather icon-user-plus text-primary f-24 m-r-5"></i>
                    <span>Loading...</span>
                </h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted mb-3">Total Unsubscriptions</h6>
                <h3 class="f-w-300 d-flex align-items-center m-b-0" id="totalUnsubscriptions">
                    <i class="feather icon-user-minus text-danger f-24 m-r-5"></i>
                    <span>Loading...</span>
                </h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted mb-3">Active Subscribers</h6>
                <h3 class="f-w-300 d-flex align-items-center m-b-0" id="activeSubscribers">
                    <i class="feather icon-users text-success f-24 m-r-5"></i>
                    <span>Loading...</span>
                </h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted mb-3">Revenue</h6>
                <h3 class="f-w-300 d-flex align-items-center m-b-0" id="totalRevenue">
                    <i class="feather icon-dollar-sign text-warning f-24 m-r-5"></i>
                    <span>Loading...</span>
                </h3>
            </div>
        </div>
    </div>
</div>

<!-- Data Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>Daily CRBT MIS Data</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Total Subscriptions</th>
                                <th>Total Unsubscriptions</th>
                                <th>Active Subscribers</th>
                                <th>Tone Downloads</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody id="dailyMisBody">
                            <tr><td colspan="6" class="text-center">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Subscription Trend</h5>
            </div>
            <div class="card-body">
                <canvas id="subscriptionChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Revenue Trend</h5>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let subscriptionChart, revenueChart;

        function initCharts(labels = [], subs = [], revenue = []) {
            const subscriptionCtx = document.getElementById('subscriptionChart').getContext('2d');
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');

            if (subscriptionChart) subscriptionChart.destroy();
            if (revenueChart) revenueChart.destroy();

            subscriptionChart = new Chart(subscriptionCtx, {
                type: 'line',
                data: { labels, datasets: [{ label: 'Subscriptions', data: subs, borderColor: 'rgb(75, 192, 192)', tension: 0.1 }] },
                options: { responsive: true, maintainAspectRatio: false }
            });

            revenueChart = new Chart(revenueCtx, {
                type: 'bar',
                data: { labels, datasets: [{ label: 'Revenue', data: revenue, backgroundColor: 'rgba(54, 162, 235, 0.5)', borderColor: 'rgb(54, 162, 235)', borderWidth: 1 }] },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }

        async function fetchDailyMIS() {
            const params = new URLSearchParams();
            const s = document.getElementById('startDate').value;
            const e = document.getElementById('endDate').value;
            if (s) params.append('start_date', s);
            if (e) params.append('end_date', e);

            const url = `/api/crbt/daily-mis?${params.toString()}`;
            const tbody = document.getElementById('dailyMisBody');
            tbody.innerHTML = '<tr><td colspan="6" class="text-center">Loading...</td></tr>';

            try {
                const res = await fetch(url);
                const json = await res.json();
                const rows = Array.isArray(json.data) ? json.data : [];

                if (rows.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center">No data</td></tr>';
                    initCharts();
                    return;
                }

                const labels = [];
                const subs = [];
                const rev = [];
                let totalSubs = 0, totalUnsubs = 0, totalActive = 0, totalRev = 0;
                tbody.innerHTML = '';

                rows.forEach(r => {
                    const date = r.date || r.DATE || '';
                    const subsCount = Number(r.total_subscriptions || r.SUBS || r.subscriptions || 0);
                    const unsubsCount = Number(r.total_unsubscriptions || r.UNSUBS || r.unsubscriptions || 0);
                    const activeCount = Number(r.active_subscribers || r.ACTIVE || 0);
                    const revenue = Number(r.revenue || r.REVENUE || 0);
                    
                    labels.push(date);
                    subs.push(subsCount);
                    rev.push(revenue);
                    
                    totalSubs += subsCount;
                    totalUnsubs += unsubsCount;
                    totalActive += activeCount;
                    totalRev += revenue;
                    
                    tbody.insertAdjacentHTML('beforeend', `
                        <tr>
                            <td>${date}</td>
                            <td>${subsCount.toLocaleString()}</td>
                            <td>${unsubsCount.toLocaleString()}</td>
                            <td>${activeCount.toLocaleString()}</td>
                            <td>${(r.tone_downloads ?? r.TONES ?? 0).toLocaleString()}</td>
                            <td>${revenue.toLocaleString()}</td>
                        </tr>
                    `);
                });

                // Update summary cards
                document.getElementById('totalSubscriptions').querySelector('span').textContent = totalSubs.toLocaleString();
                document.getElementById('totalUnsubscriptions').querySelector('span').textContent = totalUnsubs.toLocaleString();
                document.getElementById('activeSubscribers').querySelector('span').textContent = totalActive.toLocaleString();
                document.getElementById('totalRevenue').querySelector('span').textContent = totalRev.toLocaleString();

                initCharts(labels, subs, rev);
            } catch (e) {
                tbody.innerHTML = `<tr><td colspan="6" class="text-danger text-center">Error loading data</td></tr>`;
                initCharts();
            }
        }

        document.getElementById('applyFilter').addEventListener('click', fetchDailyMIS);
        fetchDailyMIS();
    });
</script>
@endpush 
@endsection