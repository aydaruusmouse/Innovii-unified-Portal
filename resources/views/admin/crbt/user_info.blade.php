@extends('admin.crbt.base')

@section('page_title', 'User Information')
@section('report_title', 'User Information Report')

@section('report_content')
<!-- Filter Section -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <input type="text" id="searchMsisdn" class="form-control" placeholder="Search MSISDN">
    </div>
    <div class="col-md-4">
        <select id="statusFilter" class="form-control">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="grace">Grace</option>
            <option value="suspended">Suspended</option>
            <option value="churned">Churned</option>
        </select>
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
                <h6 class="text-muted mb-3">Total Users</h6>
                <h3 class="f-w-300 d-flex align-items-center m-b-0" id="totalUsers">
                    <i class="feather icon-users text-primary f-24 m-r-5"></i>
                    <span>Loading...</span>
                </h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted mb-3">Active Users</h6>
                <h3 class="f-w-300 d-flex align-items-center m-b-0" id="activeUsers">
                    <i class="feather icon-check-circle text-success f-24 m-r-5"></i>
                    <span>Loading...</span>
                </h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted mb-3">Suspended Users</h6>
                <h3 class="f-w-300 d-flex align-items-center m-b-0" id="suspendedUsers">
                    <i class="feather icon-pause-circle text-warning f-24 m-r-5"></i>
                    <span>Loading...</span>
                </h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted mb-3">Churned Users</h6>
                <h3 class="f-w-300 d-flex align-items-center m-b-0" id="churnedUsers">
                    <i class="feather icon-x-circle text-danger f-24 m-r-5"></i>
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
                <h5>User Information Data</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>MSISDN</th>
                                <th>Status</th>
                                <th>Registration Date</th>
                                <th>Last Activity</th>
                                <th>Service Type</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="userInfoBody">
                            <tr><td colspan="6" class="text-center">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    <nav>
                        <ul class="pagination" id="userInfoPagination">
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
                <h5>User Status Distribution</h5>
            </div>
            <div class="card-body">
                <canvas id="userStatusChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Registration Trends</h5>
            </div>
            <div class="card-body">
                <canvas id="registrationTrendsChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let userStatusChart, registrationTrendsChart;
        let currentPage = 1;
        const perPage = 20;

        function initCharts(statusData = [], trendData = []) {
            const statusCtx = document.getElementById('userStatusChart').getContext('2d');
            const trendCtx = document.getElementById('registrationTrendsChart').getContext('2d');

            if (userStatusChart) userStatusChart.destroy();
            if (registrationTrendsChart) registrationTrendsChart.destroy();

            // User Status (Doughnut Chart)
            userStatusChart = new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Active', 'Grace', 'Suspended', 'Churned'],
                    datasets: [{
                        data: statusData,
                        backgroundColor: ['#28a745', '#ffc107', '#dc3545', '#6c757d']
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });

            // Registration Trends (Line Chart)
            registrationTrendsChart = new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: trendData.labels || [],
                    datasets: [{
                        label: 'New Registrations',
                        data: trendData.data || [],
                        borderColor: '#007bff',
                        tension: 0.1
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }

        function buildUrl() {
            const params = new URLSearchParams();
            const msisdn = document.getElementById('searchMsisdn').value;
            const status = document.getElementById('statusFilter').value;
            if (msisdn) params.append('msisdn', msisdn);
            if (status) params.append('status', status);
            params.append('page', currentPage);
            params.append('per_page', perPage);
            return `/api/crbt/user-info?${params.toString()}`;
        }

        async function fetchUserInfoData() {
            const tbody = document.getElementById('userInfoBody');
            tbody.innerHTML = '<tr><td colspan="6" class="text-center">Loading...</td></tr>';

            try {
                const res = await fetch(buildUrl());
                const json = await res.json();
                const rows = Array.isArray(json.data) ? json.data : [];
                const pagination = json.pagination || { current_page: 1, total: 0, per_page: perPage, last_page: 1 };

                if (rows.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center">No data</td></tr>';
                    initCharts();
                    return;
                }

                let totalUsers = 0, activeUsers = 0, suspendedUsers = 0, churnedUsers = 0;
                tbody.innerHTML = '';

                rows.forEach(r => {
                    const msisdn = r.msisdn || r.MSISDN || 'N/A';
                    const status = r.status || r.STATUS || 'Unknown';
                    const regDate = r.registration_date || r.REGISTRATION_DATE || 'N/A';
                    const lastActivity = r.last_activity || r.LAST_ACTIVITY || 'N/A';
                    const serviceType = r.service_type || r.SERVICE_TYPE || 'N/A';
                    
                    totalUsers++;
                    if (status.toLowerCase() === 'active') activeUsers++;
                    else if (status.toLowerCase() === 'suspended') suspendedUsers++;
                    else if (status.toLowerCase() === 'churned') churnedUsers++;
                    
                    tbody.insertAdjacentHTML('beforeend', `
                        <tr>
                            <td>${msisdn}</td>
                            <td><span class="badge ${getStatusBadgeClass(status)}">${status}</span></td>
                            <td>${regDate}</td>
                            <td>${lastActivity}</td>
                            <td>${serviceType}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="viewUserDetails('${msisdn}')">View</button>
                            </td>
                        </tr>
                    `);
                });

                // Update summary cards
                document.getElementById('totalUsers').querySelector('span').textContent = totalUsers.toLocaleString();
                document.getElementById('activeUsers').querySelector('span').textContent = activeUsers.toLocaleString();
                document.getElementById('suspendedUsers').querySelector('span').textContent = suspendedUsers.toLocaleString();
                document.getElementById('churnedUsers').querySelector('span').textContent = churnedUsers.toLocaleString();

                // Update pagination
                updatePagination(pagination);

                initCharts([activeUsers, 0, suspendedUsers, churnedUsers], { labels: [], data: [] });
            } catch (e) {
                tbody.innerHTML = `<tr><td colspan="6" class="text-danger text-center">Error loading data</td></tr>`;
                initCharts();
            }
        }

        function getStatusBadgeClass(status) {
            switch (status.toLowerCase()) {
                case 'active': return 'badge-success';
                case 'grace': return 'badge-warning';
                case 'suspended': return 'badge-danger';
                case 'churned': return 'badge-secondary';
                default: return 'badge-light';
            }
        }

        function updatePagination(pagination) {
            const paginationEl = document.getElementById('userInfoPagination');
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
                        fetchUserInfoData();
                    }
                });
            });
        }

        // Global function for user details
        window.viewUserDetails = function(msisdn) {
            alert(`View details for user: ${msisdn}`);
        };

        document.getElementById('applyFilter').addEventListener('click', () => {
            currentPage = 1;
            fetchUserInfoData();
        });
        fetchUserInfoData();
    });
</script>
@endpush
@endsection