@extends('admin.crpt.base')

@section('page_title', 'CRPT Single Service Report')
@section('report_title', 'CRPT Service Details')

@section('report_content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Service Selection</h5>
            </div>
            <div class="card-body">
                <form id="serviceForm">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="serviceName">Service Name</label>
                                <select class="form-control" id="serviceName" name="service_name">
                                    <option value="">Select Service</option>
                                    <!-- Services will be populated here -->
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="startDate">Start Date</label>
                                <input type="date" class="form-control" id="startDate" name="start_date">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="endDate">End Date</label>
                                <input type="date" class="form-control" id="endDate" name="end_date">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Generate Report</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Total Subscribers</th>
                        <th>New Subscriptions</th>
                        <th>Unsubscriptions</th>
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
                <canvas id="subscriptionTrendChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Revenue Trend</h5>
            </div>
            <div class="card-body">
                <canvas id="revenueTrendChart"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize charts
        const subscriptionCtx = document.getElementById('subscriptionTrendChart').getContext('2d');
        const revenueCtx = document.getElementById('revenueTrendChart').getContext('2d');

        new Chart(subscriptionCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Subscriptions',
                    data: [],
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Revenue',
                    data: [],
                    borderColor: 'rgb(255, 99, 132)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Form submission handler
        document.getElementById('serviceForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Add your form submission logic here
        });
    });
</script>
@endpush 