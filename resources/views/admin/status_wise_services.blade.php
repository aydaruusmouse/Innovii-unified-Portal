<!doctype html>
<html lang="en">
  <!-- [Head] start -->
  <head>
    @include('layouts.heads_page') 
    @include('layouts.heads_css')
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
  </head>
  <!-- [Head] end -->
  <!-- [Body] Start -->

  <body @@bodySetup>
    @include('layouts.layout_vertical')

    <!-- [ Main Content ] start -->
    <div class="pc-container">
      <div class="pc-content">
        <div class="container-fluid">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Status Wise Services</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Status Wise Services</li>
                    </ol>
                </nav>
            </div>

            <!-- Filter Section -->
            <div class="card mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="startDate" class="form-label">Start Date</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                    <input type="date" class="form-control" id="startDate">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="endDate" class="form-label">End Date</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                    <input type="date" class="form-control" id="endDate">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="serviceFilter" class="form-label">Service</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-filter"></i></span>
                                    <select class="form-select" id="serviceFilter">
                                        <option value="all">All Services</option>
                                        @foreach($services as $service)
                                            <option value="{{ $service->name }}">{{ $service->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="statusFilter" class="form-label">Status</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                    <select class="form-select" id="statusFilter">
                                        <option value="all">All Status</option>
                                        <option value="ACTIVE">Active</option>
                                        <option value="FAILED">Failed</option>
                                        <option value="NEW">New</option>
                                        <option value="CANCELED">Canceled</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 text-end">
                            <button class="btn btn-primary" id="applyFilters">
                                <i class="fas fa-filter me-2"></i>Apply Filters
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Overview Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Services</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalServices">0</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-list fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Active Services</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="activeServices">0</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Inactive Services</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="inactiveServices">0</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Grace Period</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="gracePeriod">0</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clock fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row mb-4">
                <!-- Status Distribution Chart -->
                <div class="col-xl-6">
                    <div class="card mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Status Distribution</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-pie pt-4">
                                <canvas id="statusDistributionChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Subscription Trend Chart -->
                <div class="col-xl-6">
                    <div class="card mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Subscription Trend</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-area">
                                <canvas id="subscriptionTrendChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Services Table -->
            <div class="card mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Services List</h6>
                    <div class="d-flex align-items-center">
                        <div class="input-group">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search services...">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="servicesTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Service Name</th>
                                    <th>Status</th>
                                    <th>Subscribers</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="servicesTableBody">
                                <!-- Table content will be populated dynamically -->
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div id="paginationInfo" class="text-muted"></div>
                        <ul class="pagination mb-0" id="paginationLinks"></ul>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>

    @include('layouts.footer') 
    @include('layouts.footer_js')

    <!-- Service Details Modal -->
    <div class="modal fade" id="serviceDetailsModal" tabindex="-1" aria-labelledby="serviceDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="serviceDetailsModalLabel">Service Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Service Name:</label>
                                <p id="modalServiceName" class="mb-0">-</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Status:</label>
                                <p id="modalStatus" class="mb-0">-</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Short Code:</label>
                                <p id="modalShortCode" class="mb-0">-</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">App ID:</label>
                                <p id="modalAppId" class="mb-0">-</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Validity:</label>
                                <p id="modalValidity" class="mb-0">-</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Message:</label>
                                <p id="modalMessage" class="mb-0">-</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const startDate = document.getElementById("startDate");
            const endDate = document.getElementById("endDate");
            const serviceFilter = document.getElementById("serviceFilter");
            const statusFilter = document.getElementById("statusFilter");
            const applyFiltersBtn = document.getElementById("applyFilters");
            const servicesTableBody = document.getElementById("servicesTableBody");

            // Set default dates (last 30 days)
            const today = new Date();
            const thirtyDaysAgo = new Date();
            thirtyDaysAgo.setDate(today.getDate() - 30);
            
            startDate.value = thirtyDaysAgo.toISOString().split('T')[0];
            endDate.value = today.toISOString().split('T')[0];

            // Chart instances
            let statusDistributionChart = new Chart(document.getElementById("statusDistributionChart").getContext("2d"), {
                type: "pie",
                data: {
                    labels: ["Active", "Failed", "New", "Canceled"],
                    datasets: [{
                        data: [0, 0, 0, 0],
                        backgroundColor: ["#28a745", "#dc3545", "#ffc107", "#6c757d"]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            let subscriptionTrendChart = new Chart(document.getElementById("subscriptionTrendChart").getContext("2d"), {
                type: "line",
                data: {
                    labels: [],
                    datasets: [{
                        label: "Total Subscriptions",
                        data: [],
                        borderColor: "#007bff",
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Apply Filters
            applyFiltersBtn.addEventListener("click", function () {
                const selectedStartDate = startDate.value;
                const selectedEndDate = endDate.value;
                const selectedService = serviceFilter.value;
                const selectedStatus = statusFilter.value;

                fetch(`/api/v1/status-wise-report?start_date=${selectedStartDate}&end_date=${selectedEndDate}&service_name=${selectedService}&status=${selectedStatus}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        throw new Error(data.message || data.error);
                    }

                    // Update Status Chart
                    statusDistributionChart.data.datasets[0].data = [
                        data.status_totals.active,
                        data.status_totals.failed,
                        data.status_totals.new,
                        data.status_totals.canceled
                    ];
                    statusDistributionChart.update();

                    // Update Subscription Chart
                    subscriptionTrendChart.data.labels = data.dates;
                    subscriptionTrendChart.data.datasets[0].data = data.subscription_totals;
                    subscriptionTrendChart.update();

                    // Update Table
                    servicesTableBody.innerHTML = '';
                    data.table_data.forEach(row => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${row.date}</td>
                            <td>${row.name}</td>
                            <td>
                                <span class="badge ${getStatusBadgeClass(row.status)}">
                                    ${row.status || 'N/A'}
                                </span>
                            </td>
                            <td>${row.total_subs || 0}</td>
                            <td>
                                <button class="btn btn-sm btn-info view-details" data-service="${row.name}">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        `;
                        servicesTableBody.appendChild(tr);
                    });

                    // Update metrics
                    document.getElementById('totalServices').textContent = data.status_totals.active + data.status_totals.failed + data.status_totals.new + data.status_totals.canceled;
                    document.getElementById('activeServices').textContent = data.status_totals.active;
                    document.getElementById('inactiveServices').textContent = data.status_totals.failed;
                    document.getElementById('gracePeriod').textContent = data.status_totals.new;
                })
                .catch(error => {
                    console.error('Error fetching status-wise report:', error);
                    alert('Error fetching data: ' + error.message);
                });
            });

            // Load initial data
            applyFiltersBtn.click();
        });

        // Helper function for status badge classes
        function getStatusBadgeClass(status) {
            switch(status) {
                case 'ACTIVE':
                    return 'bg-success';
                case 'FAILED':
                    return 'bg-danger';
                case 'NEW':
                    return 'bg-primary';
                case 'CANCELED':
                    return 'bg-warning';
                default:
                    return 'bg-secondary';
            }
        }
    </script>
  </body>
</html>