@extends('admin.crpt.base')

@section('page_title', 'CRPT Monthly/Weekly Reports')
@section('report_title', 'CRPT Periodical Reports')

@section('report_content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Report Selection</h5>
            </div>
            <div class="card-body">
                <form id="reportForm">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="reportType">Report Type</label>
                                <select class="form-control" id="reportType" name="report_type">
                                    <option value="weekly">Weekly Report</option>
                                    <option value="monthly">Monthly Report</option>
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
                        <th>Period</th>
                        <th>Total Subscribers</th>
                        <th>New Subscriptions</th>
                        <th>Unsubscriptions</th>
                        <th>Active Subscribers</th>
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
                <h5>Subscriber Growth</h5>
            </div>
            <div class="card-body">
                <canvas id="subscriberGrowthChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Revenue Growth</h5>
            </div>
            <div class="card-body">
                <canvas id="revenueGrowthChart"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize charts
        const subscriberCtx = document.getElementById('subscriberGrowthChart').getContext('2d');
        const revenueCtx = document.getElementById('revenueGrowthChart').getContext('2d');

        new Chart(subscriberCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Subscribers',
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
        document.getElementById('reportForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Add your form submission logic here
        });
    });
</script>
@endpush 