@extends('admin.crbt.base')

@section('page_title', 'User Tone Information')
@section('report_title', 'User Tone Information Report')

@section('report_content')
<!-- Filter Section -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <input type="text" id="searchMsisdn" class="form-control" placeholder="Search MSISDN">
    </div>
    <div class="col-md-3">
        <input type="text" id="searchToneId" class="form-control" placeholder="Search Tone ID">
    </div>
    <div class="col-md-3">
        <select id="statusFilter" class="form-control">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="expired">Expired</option>
            <option value="cancelled">Cancelled</option>
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
                <h6 class="text-muted mb-3">Total Tones</h6>
                <h3 class="f-w-300 d-flex align-items-center m-b-0" id="totalTones">
                    <i class="feather icon-music text-primary f-24 m-r-5"></i>
                    <span>Loading...</span>
                </h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted mb-3">Active Tones</h6>
                <h3 class="f-w-300 d-flex align-items-center m-b-0" id="activeTones">
                    <i class="feather icon-check-circle text-success f-24 m-r-5"></i>
                    <span>Loading...</span>
                </h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted mb-3">Expired Tones</h6>
                <h3 class="f-w-300 d-flex align-items-center m-b-0" id="expiredTones">
                    <i class="feather icon-clock text-warning f-24 m-r-5"></i>
                    <span>Loading...</span>
                </h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted mb-3">Cancelled Tones</h6>
                <h3 class="f-w-300 d-flex align-items-center m-b-0" id="cancelledTones">
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
                <h5>User Tone Information Data</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>MSISDN</th>
                                <th>Tone ID</th>
                                <th>Tone Name</th>
                                <th>Status</th>
                                <th>Activation Date</th>
                                <th>Expiry Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="userToneInfoBody">
                            <tr><td colspan="7" class="text-center">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    <nav>
                        <ul class="pagination" id="userToneInfoPagination">
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
                <h5>Tone Status Distribution</h5>
            </div>
            <div class="card-body">
                <canvas id="toneStatusChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Popular Tones</h5>
            </div>
            <div class="card-body">
                <canvas id="popularTonesChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let toneStatusChart, popularTonesChart;
        let currentPage = 1;
        const perPage = 20;

        function initCharts(statusData = [], popularTones = []) {
            const statusCtx = document.getElementById('toneStatusChart').getContext('2d');
            const popularCtx = document.getElementById('popularTonesChart').getContext('2d');

            if (toneStatusChart) toneStatusChart.destroy();
            if (popularTonesChart) popularTonesChart.destroy();

            // Tone Status (Doughnut Chart)
            toneStatusChart = new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Active', 'Expired', 'Cancelled'],
                    datasets: [{
                        data: statusData,
                        backgroundColor: ['#28a745', '#ffc107', '#dc3545']
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });

            // Popular Tones (Bar Chart)
            popularTonesChart = new Chart(popularCtx, {
                type: 'bar',
                data: {
                    labels: popularTones.labels || [],
                    datasets: [{
                        label: 'Usage Count',
                        data: popularTones.data || [],
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgb(54, 162, 235)',
                        borderWidth: 1
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }

        function buildUrl() {
            const params = new URLSearchParams();
            const msisdn = document.getElementById('searchMsisdn').value;
            const toneId = document.getElementById('searchToneId').value;
            const status = document.getElementById('statusFilter').value;
            if (msisdn) params.append('msisdn', msisdn);
            if (toneId) params.append('tone_id', toneId);
            if (status) params.append('status', status);
            params.append('page', currentPage);
            params.append('per_page', perPage);
            return `/api/crbt/user-tone-info?${params.toString()}`;
        }

        async function fetchUserToneInfoData() {
            const tbody = document.getElementById('userToneInfoBody');
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

                let totalTones = 0, activeTones = 0, expiredTones = 0, cancelledTones = 0;
                tbody.innerHTML = '';

                rows.forEach(r => {
                    const msisdn = r.msisdn || r.MSISDN || 'N/A';
                    const toneId = r.tone_id || r.TONE_ID || 'N/A';
                    const toneName = r.tone_name || r.TONE_NAME || 'N/A';
                    const status = r.status || r.STATUS || 'Unknown';
                    const activationDate = r.activation_date || r.ACTIVATION_DATE || 'N/A';
                    const expiryDate = r.expiry_date || r.EXPIRY_DATE || 'N/A';
                    
                    totalTones++;
                    if (status.toLowerCase() === 'active') activeTones++;
                    else if (status.toLowerCase() === 'expired') expiredTones++;
                    else if (status.toLowerCase() === 'cancelled') cancelledTones++;
                    
                    tbody.insertAdjacentHTML('beforeend', `
                        <tr>
                            <td>${msisdn}</td>
                            <td>${toneId}</td>
                            <td>${toneName}</td>
                            <td><span class="badge ${getToneStatusBadgeClass(status)}">${status}</span></td>
                            <td>${activationDate}</td>
                            <td>${expiryDate}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="viewToneDetails('${toneId}')">View</button>
                            </td>
                        </tr>
                    `);
                });

                // Update summary cards
                document.getElementById('totalTones').querySelector('span').textContent = totalTones.toLocaleString();
                document.getElementById('activeTones').querySelector('span').textContent = activeTones.toLocaleString();
                document.getElementById('expiredTones').querySelector('span').textContent = expiredTones.toLocaleString();
                document.getElementById('cancelledTones').querySelector('span').textContent = cancelledTones.toLocaleString();

                // Update pagination
                updatePagination(pagination);

                initCharts([activeTones, expiredTones, cancelledTones], { labels: [], data: [] });
            } catch (e) {
                tbody.innerHTML = `<tr><td colspan="7" class="text-danger text-center">Error loading data</td></tr>`;
                initCharts();
            }
        }

        function getToneStatusBadgeClass(status) {
            switch (status.toLowerCase()) {
                case 'active': return 'badge-success';
                case 'expired': return 'badge-warning';
                case 'cancelled': return 'badge-danger';
                default: return 'badge-light';
            }
        }

        function updatePagination(pagination) {
            const paginationEl = document.getElementById('userToneInfoPagination');
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
                        fetchUserToneInfoData();
                    }
                });
            });
        }

        // Global function for tone details
        window.viewToneDetails = function(toneId) {
            alert(`View details for tone: ${toneId}`);
        };

        document.getElementById('applyFilter').addEventListener('click', () => {
            currentPage = 1;
            fetchUserToneInfoData();
        });
        fetchUserToneInfoData();
    });
</script>
@endpush
@endsection