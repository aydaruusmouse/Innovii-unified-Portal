@extends('admin.crbt.base')

@section('page_title', 'Billing & Charges')
@section('report_title', 'Billing & Charges Report')

@section('report_content')
<!-- Filter Section -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <input type="date" id="startDate" class="form-control" placeholder="Start date">
            </div>
    <div class="col-md-3">
        <input type="date" id="endDate" class="form-control" placeholder="End date">
                    </div>
    <div class="col-md-3">
        <select id="chargeTypeFilter" class="form-control">
            <option value="">All Charge Types</option>
            <option value="subscription">Subscription</option>
            <option value="renewal">Renewal</option>
            <option value="tone_download">Tone Download</option>
            <option value="activation">Activation</option>
        </select>
    </div>
    <div class="col-md-3 d-grid">
        <button id="applyFilter" class="btn btn-primary">Apply Filter</button>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted mb-3">Total Revenue</h6>
                <h3 class="f-w-300 d-flex align-items-center m-b-0" id="totalRevenue">
                    <i class="feather icon-dollar-sign text-success f-24 m-r-5"></i>
                    <span>Loading...</span>
                </h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted mb-3">Subscription Revenue</h6>
                <h3 class="f-w-300 d-flex align-items-center m-b-0" id="subscriptionRevenue">
                    <i class="feather icon-credit-card text-primary f-24 m-r-5"></i>
                    <span>Loading...</span>
                </h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted mb-3">Renewal Revenue</h6>
                <h3 class="f-w-300 d-flex align-items-center m-b-0" id="renewalRevenue">
                    <i class="feather icon-refresh-cw text-info f-24 m-r-5"></i>
                    <span>Loading...</span>
                </h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted mb-3">Average Charge</h6>
                <h3 class="f-w-300 d-flex align-items-center m-b-0" id="averageCharge">
                    <i class="feather icon-trending-up text-warning f-24 m-r-5"></i>
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
                <h5>Billing & Charges Data</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>MSISDN</th>
                                <th>Charge Type</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Transaction ID</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="billingChargesBody">
                            <tr><td colspan="7" class="text-center">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    <nav>
                        <ul class="pagination" id="billingChargesPagination">
                            <!-- Pagination links will be loaded here -->
                        </ul>
                    </nav>
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
                <h5>Revenue by Charge Type</h5>
            </div>
            <div class="card-body">
                <canvas id="revenueByTypeChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Daily Revenue Trends</h5>
                </div>
            <div class="card-body">
                <canvas id="dailyRevenueChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let revenueByTypeChart, dailyRevenueChart;
        let currentPage = 1;
        const perPage = 20;

        function initCharts(typeData = [], dailyData = []) {
            const typeCtx = document.getElementById('revenueByTypeChart').getContext('2d');
            const dailyCtx = document.getElementById('dailyRevenueChart').getContext('2d');

            if (revenueByTypeChart) revenueByTypeChart.destroy();
            if (dailyRevenueChart) dailyRevenueChart.destroy();

            // Revenue by Type (Doughnut Chart)
            revenueByTypeChart = new Chart(typeCtx, {
                type: 'doughnut',
                data: {
                    labels: typeData.labels || [],
                    datasets: [{
                        data: typeData.data || [],
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF']
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });

            // Daily Revenue (Line Chart)
            dailyRevenueChart = new Chart(dailyCtx, {
                type: 'line',
                data: {
                    labels: dailyData.labels || [],
                    datasets: [{
                        label: 'Daily Revenue',
                        data: dailyData.data || [],
                        borderColor: '#007bff',
                        tension: 0.1
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }

        function buildUrl() {
            const params = new URLSearchParams();
            const s = document.getElementById('startDate').value;
            const e = document.getElementById('endDate').value;
            const chargeType = document.getElementById('chargeTypeFilter').value;
            if (s) params.append('start_date', s);
            if (e) params.append('end_date', e);
            if (chargeType) params.append('charge_type', chargeType);
            params.append('page', currentPage);
            params.append('per_page', perPage);
            return `/api/crbt/billing-charges?${params.toString()}`;
        }

        async function fetchBillingChargesData() {
            const tbody = document.getElementById('billingChargesBody');
            tbody.innerHTML = '<tr><td colspan="7" class="text-center">Loading...</td></tr>';

            try {
                const res = await fetch(buildUrl());
                const json = await res.json();
                const rows = Array.isArray(json.data) ? json.data : [];
                const pagination = json.pagination || { current_page: 1, total: 0, per_page: perPage, last_page: 1 };

                if (rows.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="7" class="text-center">No data</td></tr>';
                    initCharts();
                    return;
                }

                let totalRevenue = 0, subscriptionRevenue = 0, renewalRevenue = 0;
                tbody.innerHTML = '';

                rows.forEach(r => {
                    const date = r.date || r.DATE || 'N/A';
                    const msisdn = r.msisdn || r.MSISDN || 'N/A';
                    const chargeType = r.charge_type || r.CHARGE_TYPE || 'N/A';
                    const amount = Number(r.amount || r.AMOUNT || 0);
                    const status = r.status || r.STATUS || 'Unknown';
                    const transactionId = r.transaction_id || r.TRANSACTION_ID || 'N/A';
                    
                    totalRevenue += amount;
                    if (chargeType.toLowerCase() === 'subscription') subscriptionRevenue += amount;
                    else if (chargeType.toLowerCase() === 'renewal') renewalRevenue += amount;
                    
                    tbody.insertAdjacentHTML('beforeend', `
                        <tr>
                            <td>${date}</td>
                            <td>${msisdn}</td>
                            <td>${chargeType}</td>
                            <td>${amount.toLocaleString()}</td>
                            <td><span class="badge ${getStatusBadgeClass(status)}">${status}</span></td>
                            <td>${transactionId}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="viewTransactionDetails('${transactionId}')">View</button>
                            </td>
                        </tr>
                    `);
                });

                // Update summary cards
                document.getElementById('totalRevenue').querySelector('span').textContent = totalRevenue.toLocaleString();
                document.getElementById('subscriptionRevenue').querySelector('span').textContent = subscriptionRevenue.toLocaleString();
                document.getElementById('renewalRevenue').querySelector('span').textContent = renewalRevenue.toLocaleString();
                document.getElementById('averageCharge').querySelector('span').textContent = rows.length > 0 ? Math.round(totalRevenue / rows.length).toLocaleString() : '0';

                // Update pagination
                updatePagination(pagination);

                initCharts(
                    { labels: ['Subscription', 'Renewal', 'Tone Download', 'Activation'], data: [subscriptionRevenue, renewalRevenue, 0, 0] },
                    { labels: [], data: [] }
                );
            } catch (e) {
                tbody.innerHTML = `<tr><td colspan="7" class="text-danger text-center">Error loading data</td></tr>`;
                initCharts();
            }
        }

        function getStatusBadgeClass(status) {
            switch (status.toLowerCase()) {
                case 'success': return 'badge-success';
                case 'pending': return 'badge-warning';
                case 'failed': return 'badge-danger';
                default: return 'badge-light';
            }
        }

        function updatePagination(pagination) {
            const paginationEl = document.getElementById('billingChargesPagination');
            paginationEl.innerHTML = `
                <li class="page-item ${pagination.current_page <= 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${pagination.current_page - 1}">Previous</a>
                </li>
                <li class="page-item ${pagination.current_page >= pagination.last_page ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${pagination.current_page + 1}">Next</a>
                </li>
            `;

            paginationEl.querySelectorAll('.page-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const page = parseInt(this.getAttribute('data-page'));
                    if (page >= 1 && page <= pagination.last_page) {
                        currentPage = page;
                        fetchBillingChargesData();
                    }
                });
            });
        }

        // Global function for transaction details
        window.viewTransactionDetails = function(transactionId) {
            alert(`View details for transaction: ${transactionId}`);
        };

        document.getElementById('applyFilter').addEventListener('click', () => {
            currentPage = 1;
            fetchBillingChargesData();
        });
        fetchBillingChargesData();
    });
</script>
@endpush
@endsection
