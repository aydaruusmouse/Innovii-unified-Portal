@extends('admin.crbt.base')

@section('page_title', 'Daily CRBT MIS Report')
@section('report_title', 'Daily CRBT Activities Summary')

@section('report_content')
<div class="row">
    <div class="col-md-12">
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
                <h5>Subscription Trend</h5>
            </div>
            <div class="card-body">
                <canvas id="subscriptionChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Revenue Trend</h5>
            </div>
            <div class="card-body">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Initialize charts here
    document.addEventListener('DOMContentLoaded', function() {
        // Subscription Chart
        const subscriptionCtx = document.getElementById('subscriptionChart').getContext('2d');
        new Chart(subscriptionCtx, {
            type: 'line',
            data: {
                labels: [], // Add your labels here
                datasets: [{
                    label: 'Subscriptions',
                    data: [], // Add your data here
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
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