@extends('admin.crbt.base')

@section('page_title', 'Hourly CRBT MIS')
@section('report_title', 'Hourly CRBT MIS Report')

@section('report_content')
<!-- Filter Section -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <input type="date" id="date" class="form-control" placeholder="Date">
    </div>
    <div class="col-md-4">
        <div></div>
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
                <h3 class="f-w-300 d-flex align-items-center m-b-0" id="totalHourlySubs">
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
                <h3 class="f-w-300 d-flex align-items-center m-b-0" id="totalHourlyUnsubs">
                    <i class="feather icon-user-minus text-danger f-24 m-r-5"></i>
                    <span>Loading...</span>
                </h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted mb-3">Tone Downloads</h6>
                <h3 class="f-w-300 d-flex align-items-center m-b-0" id="totalHourlyTones">
                    <i class="feather icon-music text-success f-24 m-r-5"></i>
                    <span>Loading...</span>
                </h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted mb-3">Revenue</h6>
                <h3 class="f-w-300 d-flex align-items-center m-b-0" id="totalHourlyRevenue">
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
                <h5>Hourly CRBT MIS Data</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Hour</th>
                                <th>Subscriptions</th>
                                <th>Unsubscriptions</th>
                                <th>Tone Downloads</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody id="hourlyMisBody">
                            <tr><td colspan="6" class="text-center">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted small" id="paginationInfo"></div>
                    <nav><ul class="pagination mb-0" id="paginationLinks"></ul></nav>
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
                <h5>Hourly Subscriptions</h5>
            </div>
            <div class="card-body">
                <canvas id="hourlySubsChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Hourly Revenue</h5>
            </div>
            <div class="card-body">
                <canvas id="hourlyRevenueChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentPage = 1;
        const perPage = 24;
        let hourlySubsChart, hourlyRevenueChart;

        function initCharts(labels = [], subs = [], revenue = []) {
            const subsCtx = document.getElementById('hourlySubsChart').getContext('2d');
            const revenueCtx = document.getElementById('hourlyRevenueChart').getContext('2d');

            if (hourlySubsChart) hourlySubsChart.destroy();
            if (hourlyRevenueChart) hourlyRevenueChart.destroy();

            hourlySubsChart = new Chart(subsCtx, {
                type: 'line',
                data: { labels, datasets: [{ label: 'Subscriptions', data: subs, borderColor: 'rgb(75, 192, 192)', tension: 0.1 }] },
                options: { responsive: true, maintainAspectRatio: false }
            });

            hourlyRevenueChart = new Chart(revenueCtx, {
                type: 'bar',
                data: { labels, datasets: [{ label: 'Revenue', data: revenue, backgroundColor: 'rgba(54, 162, 235, 0.5)', borderColor: 'rgb(54, 162, 235)', borderWidth: 1 }] },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }

        function buildUrl() {
            const params = new URLSearchParams();
            const d = document.getElementById('date').value;
            if (d) params.append('date', d);
            params.append('page', currentPage);
            params.append('per_page', perPage);
            return `/api/crbt/hourly-mis?${params.toString()}`;
        }

        async function fetchHourly() {
            const tbody = document.getElementById('hourlyMisBody');
            tbody.innerHTML = '<tr><td colspan="6" class="text-center">Loading...</td></tr>';
            try {
                const res = await fetch(buildUrl());
                const json = await res.json();
                const rows = Array.isArray(json.data) ? json.data : [];
                const pagination = json.pagination || { current_page:1, total:0, per_page:perPage, last_page:1 };

                if (rows.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center">No data</td></tr>';
                    // Clear summary cards
                    document.getElementById('totalHourlySubs').querySelector('span').textContent = '0';
                    document.getElementById('totalHourlyUnsubs').querySelector('span').textContent = '0';
                    document.getElementById('totalHourlyTones').querySelector('span').textContent = '0';
                    document.getElementById('totalHourlyRevenue').querySelector('span').textContent = '0';
                    initCharts();
                } else {
                    tbody.innerHTML = '';
                    let totalSubs = 0, totalUnsubs = 0, totalTones = 0, totalRev = 0;
                    const labels = [];
                    const subs = [];
                    const rev = [];
                    
            rows.forEach(r => {
                // Map CRBT hourly data structure
                const subsCount = Number(r.activeNrml || 0);
                const unsubs = Number(r.vchurnNrml || 0);
                const tones = Number(r.VsmsSuccess || 0);
                const revenue = Number(r.SubsRev || 0) + Number(r.RenewRev || 0);
                                
                                totalSubs += subsCount;
                                totalUnsubs += unsubs;
                                totalTones += tones;
                                totalRev += revenue;
                                
                                const hour = r.hour ?? r.HOUR ?? '';
                                labels.push(hour);
                                subs.push(subsCount);
                                rev.push(revenue);
                                
                                tbody.insertAdjacentHTML('beforeend', `
                                    <tr>
                                        <td>${r.date ?? r.DATE ?? ''}</td>
                                        <td>${hour}</td>
                                        <td>${subsCount.toLocaleString()}</td>
                                        <td>${unsubs.toLocaleString()}</td>
                                        <td>${tones.toLocaleString()}</td>
                                        <td>${revenue.toLocaleString()}</td>
                                    </tr>
                                `);
                            });
                    
                    // Update summary cards
                    document.getElementById('totalHourlySubs').querySelector('span').textContent = totalSubs.toLocaleString();
                    document.getElementById('totalHourlyUnsubs').querySelector('span').textContent = totalUnsubs.toLocaleString();
                    document.getElementById('totalHourlyTones').querySelector('span').textContent = totalTones.toLocaleString();
                    document.getElementById('totalHourlyRevenue').querySelector('span').textContent = totalRev.toLocaleString();

                    initCharts(labels, subs, rev);
                }

                document.getElementById('paginationInfo').textContent = `Page ${pagination.current_page} of ${pagination.last_page}`;
                const links = document.getElementById('paginationLinks');
                links.innerHTML = `
                    <li class="page-item ${pagination.current_page<=1?'disabled':''}"><a class="page-link" href="#" data-dir="prev">Previous</a></li>
                    <li class="page-item ${pagination.current_page>=pagination.last_page?'disabled':''}"><a class="page-link" href="#" data-dir="next">Next</a></li>
                `;
                links.querySelectorAll('a').forEach(a=>{
                    a.addEventListener('click', (e)=>{
                        e.preventDefault();
                        const dir = a.getAttribute('data-dir');
                        if (dir==='prev' && currentPage>1) { currentPage--; fetchHourly(); }
                        if (dir==='next' && currentPage<pagination.last_page) { currentPage++; fetchHourly(); }
                    });
                });
            } catch(e) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-danger text-center">Error loading data</td></tr>';
                initCharts();
            }
        }

        document.getElementById('applyFilter').addEventListener('click', function() {
            console.log('Apply Filter button clicked for Hourly MIS');
            currentPage=1; 
            fetchHourly(); 
        });
        console.log('Initial hourly data load');
        fetchHourly();
    });
</script>
@endpush
@endsection
