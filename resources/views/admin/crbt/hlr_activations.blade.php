@extends('admin.crbt.base')

@section('page_title', 'HLR Activations')
@section('report_title', 'HLR Activations Report')

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
                <h6 class="text-muted mb-3">Total Activations</h6>
                <h3 class="f-w-300 d-flex align-items-center m-b-0" id="totalActivations">
                    <i class="feather icon-zap text-primary f-24 m-r-5"></i>
                    <span>Loading...</span>
                </h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted mb-3">Successful</h6>
                <h3 class="f-w-300 d-flex align-items-center m-b-0" id="successfulActivations">
                    <i class="feather icon-check-circle text-success f-24 m-r-5"></i>
                    <span>Loading...</span>
                </h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted mb-3">Failed</h6>
                <h3 class="f-w-300 d-flex align-items-center m-b-0" id="failedActivations">
                    <i class="feather icon-x-circle text-danger f-24 m-r-5"></i>
                    <span>Loading...</span>
                </h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted mb-3">Success Rate</h6>
                <h3 class="f-w-300 d-flex align-items-center m-b-0" id="successRate">
                    <i class="feather icon-percent text-info f-24 m-r-5"></i>
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
                <h5>HLR Activations Data</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>MSISDN</th>
                                <th>Status</th>
                                <th>Activation Time</th>
                                <th>Response Code</th>
                                <th>Error Message</th>
                            </tr>
                        </thead>
                        <tbody id="hlrActivationsBody">
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
                <h5>Activation Status</h5>
            </div>
            <div class="card-body">
                <canvas id="activationStatusChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Daily Activations</h5>
            </div>
            <div class="card-body">
                <canvas id="dailyActivationsChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let activationStatusChart, dailyActivationsChart;

        function initCharts(labels = [], successData = [], failData = []) {
            const statusCtx = document.getElementById('activationStatusChart').getContext('2d');
            const dailyCtx = document.getElementById('dailyActivationsChart').getContext('2d');

            if (activationStatusChart) activationStatusChart.destroy();
            if (dailyActivationsChart) dailyActivationsChart.destroy();

            // Activation Status (Doughnut Chart)
            activationStatusChart = new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Successful', 'Failed'],
                    datasets: [{
                        data: [successData.reduce((a, b) => a + b, 0), failData.reduce((a, b) => a + b, 0)],
                        backgroundColor: ['#28a745', '#dc3545']
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });

            // Daily Activations (Bar Chart)
            dailyActivationsChart = new Chart(dailyCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        { label: 'Successful', data: successData, backgroundColor: 'rgba(40, 167, 69, 0.5)', borderColor: '#28a745', borderWidth: 1 },
                        { label: 'Failed', data: failData, backgroundColor: 'rgba(220, 53, 69, 0.5)', borderColor: '#dc3545', borderWidth: 1 }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }

        async function fetchHLRActivationsData() {
            const params = new URLSearchParams();
            const s = document.getElementById('startDate').value;
            const e = document.getElementById('endDate').value;
            if (s) params.append('start_date', s);
            if (e) params.append('end_date', e);

            const url = `/api/crbt/hlr-activations?${params.toString()}`;
            const tbody = document.getElementById('hlrActivationsBody');
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
                const successData = [];
                const failData = [];
                let totalActivations = 0, successful = 0, failed = 0;
                tbody.innerHTML = '';

                rows.forEach(r => {
                    const date = r.date || r.DATE || '';
                    const msisdn = r.msisdn || r.MSISDN || 'N/A';
                    const status = r.status || r.STATUS || 'Unknown';
                    const activationTime = r.activation_time || r.ACTIVATION_TIME || 'N/A';
                    const responseCode = r.response_code || r.RESPONSE_CODE || 'N/A';
                    const errorMessage = r.error_message || r.ERROR_MESSAGE || 'N/A';
                    
                    labels.push(date);
                    if (status.toLowerCase() === 'success') {
                        successData.push(1);
                        successful++;
                    } else {
                        failData.push(1);
                        failed++;
                    }
                    totalActivations++;
                    
                    tbody.insertAdjacentHTML('beforeend', `
                        <tr>
                            <td>${date}</td>
                            <td>${msisdn}</td>
                            <td><span class="badge ${status.toLowerCase() === 'success' ? 'badge-success' : 'badge-danger'}">${status}</span></td>
                            <td>${activationTime}</td>
                            <td>${responseCode}</td>
                            <td>${errorMessage}</td>
                        </tr>
                    `);
                });

                // Update summary cards
                document.getElementById('totalActivations').querySelector('span').textContent = totalActivations.toLocaleString();
                document.getElementById('successfulActivations').querySelector('span').textContent = successful.toLocaleString();
                document.getElementById('failedActivations').querySelector('span').textContent = failed.toLocaleString();
                document.getElementById('successRate').querySelector('span').textContent = totalActivations > 0 ? ((successful / totalActivations) * 100).toFixed(1) + '%' : '0%';

                initCharts(labels, successData, failData);
            } catch (e) {
                tbody.innerHTML = `<tr><td colspan="6" class="text-danger text-center">Error loading data</td></tr>`;
                initCharts();
            }
        }

        document.getElementById('applyFilter').addEventListener('click', fetchHLRActivationsData);
        fetchHLRActivationsData();
    });
</script>
@endpush
@endsection