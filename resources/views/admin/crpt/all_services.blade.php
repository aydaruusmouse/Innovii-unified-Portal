@extends('admin.crpt.base')

@section('page_title', 'CRPT All Services Report')
@section('report_title', 'CRPT All Services Overview')

@section('report_content')
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Service Name</th>
                        <th>Total Subscribers</th>
                        <th>Active Subscribers</th>
                        <th>Inactive Subscribers</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be populated here -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Service Distribution</h5>
            </div>
            <div class="card-body">
                <canvas id="serviceDistributionChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Revenue Distribution</h5>
            </div>
            <div class="card-body">
                <canvas id="revenueDistributionChart"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Service Distribution Chart
        const serviceCtx = document.getElementById('serviceDistributionChart').getContext('2d');
        new Chart(serviceCtx, {
            type: 'pie',
            data: {
                labels: [], // Add your labels here
                datasets: [{
                    data: [], // Add your data here
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.5)',
                        'rgba(54, 162, 235, 0.5)',
                        'rgba(255, 206, 86, 0.5)',
                        'rgba(75, 192, 192, 0.5)',
                        'rgba(153, 102, 255, 0.5)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Revenue Distribution Chart
        const revenueCtx = document.getElementById('revenueDistributionChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: [], // Add your labels here
                datasets: [{
                    label: 'Revenue',
                    data: [], // Add your data here
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgb(54, 162, 235)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    });
</script>
@endpush 