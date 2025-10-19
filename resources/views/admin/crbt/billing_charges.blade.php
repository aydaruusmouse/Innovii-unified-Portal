@extends('admin.crbt.base')

@section('page_title', 'Billing & Charges Report')
@section('report_title', 'CRBT Billing and Charges Analysis')

@section('report_content')
<!-- Date Filter Form -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Filter Options</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('crbt.billing_charges') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" 
                               value="{{ $startDate ?? '' }}" required>
                    </div>
                    <div class="col-md-4">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" 
                               value="{{ $endDate ?? '' }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ route('crbt.billing_charges') }}" class="btn btn-secondary">Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if(isset($error))
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-danger">
            {{ $error }}
        </div>
    </div>
</div>
@endif

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="card-title">Total Users</h6>
                <h3>{{ number_format($billingCharges->count()) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="card-title">Total Revenue</h6>
                <h3>₹{{ number_format($billingCharges->sum('total_charges'), 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6 class="card-title">Average Charge</h6>
                <h3>₹{{ $billingCharges->count() > 0 ? number_format($billingCharges->sum('total_charges') / $billingCharges->count(), 2) : 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h6 class="card-title">Highest Charge</h6>
                <h3>₹{{ number_format($billingCharges->max('total_charges'), 2) }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Revenue Distribution Chart -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Revenue Distribution</h5>
            </div>
            <div class="card-body">
                <canvas id="revenueDistributionChart" height="300"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Monthly Revenue Trend</h5>
            </div>
            <div class="card-body">
                <canvas id="monthlyRevenueChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Data Table -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Billing & Charges Details</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>MSISDN</th>
                                <th>User Name</th>
                                <th>Total Charges (₹)</th>
                                <th>Subscription Date</th>
                                <th>Days Active</th>
                                <th>Avg. Daily Charge</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($billingCharges as $charge)
                            @php
                                $daysActive = $charge->subscription_date ? now()->diffInDays($charge->subscription_date) : 0;
                                $avgDailyCharge = $daysActive > 0 ? ($charge->total_charges / $daysActive) : 0;
                            @endphp
                            <tr>
                                <td><strong>{{ $charge->msisdn }}</strong></td>
                                <td>{{ $charge->user_name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $charge->total_charges > 100 ? 'success' : ($charge->total_charges > 50 ? 'warning' : 'secondary') }}">
                                        ₹{{ number_format($charge->total_charges, 2) }}
                                    </span>
                                </td>
                                <td>{{ $charge->subscription_date ? $charge->subscription_date->format('d-m-Y') : 'N/A' }}</td>
                                <td>{{ $daysActive }} days</td>
                                <td>₹{{ number_format($avgDailyCharge, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No billing data available for the selected period</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top Revenue Generators -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Top 10 Revenue Generators</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>MSISDN</th>
                                <th>User Name</th>
                                <th>Total Charges (₹)</th>
                                <th>Subscription Date</th>
                                <th>Days Active</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($billingCharges->sortByDesc('total_charges')->take(10) as $index => $charge)
                            @php
                                $daysActive = $charge->subscription_date ? now()->diffInDays($charge->subscription_date) : 0;
                            @endphp
                            <tr>
                                <td>
                                    <span class="badge bg-{{ $index < 3 ? 'warning' : 'secondary' }}">#{{ $index + 1 }}</span>
                                </td>
                                <td><strong>{{ $charge->msisdn }}</strong></td>
                                <td>{{ $charge->user_name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-success">₹{{ number_format($charge->total_charges, 2) }}</span>
                                </td>
                                <td>{{ $charge->subscription_date ? $charge->subscription_date->format('d-m-Y') : 'N/A' }}</td>
                                <td>{{ $daysActive }} days</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('admin/assets/js/plugins/apexcharts.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const billingCharges = @json($billingCharges);
        
        // Revenue Distribution Chart
        const revenueRanges = {
            '0-50': billingCharges.filter(charge => charge.total_charges <= 50).length,
            '51-100': billingCharges.filter(charge => charge.total_charges > 50 && charge.total_charges <= 100).length,
            '101-200': billingCharges.filter(charge => charge.total_charges > 100 && charge.total_charges <= 200).length,
            '201-500': billingCharges.filter(charge => charge.total_charges > 200 && charge.total_charges <= 500).length,
            '500+': billingCharges.filter(charge => charge.total_charges > 500).length
        };
        
        const revenueDistributionChart = new ApexCharts(document.querySelector("#revenueDistributionChart"), {
            series: Object.values(revenueRanges),
            chart: {
                type: 'donut',
                height: 300
            },
            labels: Object.keys(revenueRanges),
            colors: ['#4CAF50', '#2196F3', '#FF9800', '#9C27B0', '#F44336'],
            legend: {
                position: 'bottom'
            }
        });
        revenueDistributionChart.render();

        // Monthly Revenue Trend Chart
        const monthlyData = {};
        billingCharges.forEach(charge => {
            if (charge.subscription_date) {
                const month = new Date(charge.subscription_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short' });
                monthlyData[month] = (monthlyData[month] || 0) + parseFloat(charge.total_charges);
            }
        });
        
        const monthlyRevenueChart = new ApexCharts(document.querySelector("#monthlyRevenueChart"), {
            series: [{
                name: 'Revenue',
                data: Object.values(monthlyData)
            }],
            chart: {
                type: 'line',
                height: 300
            },
            xaxis: {
                categories: Object.keys(monthlyData)
            },
            colors: ['#4CAF50'],
            stroke: {
                curve: 'smooth'
            }
        });
        monthlyRevenueChart.render();
    });
</script>
@endpush

