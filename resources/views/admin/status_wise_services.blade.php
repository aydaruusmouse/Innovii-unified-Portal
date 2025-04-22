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
        <div class="row">
            <div class="container-fluid mt-3">
                <!-- Filter Section -->
                <div class="card shadow-sm p-3">
                    <h5 class="text-primary mb-3"><i class="bi bi-funnel"></i> Report Filters</h5>
                    <div class="row g-3 align-items-center">
                        <!-- Date Range -->
                        <div class="col-md-3">
                            <label for="startDate" class="form-label">Start Date</label>
                            <input type="date" id="startDate" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="endDate" class="form-label">End Date</label>
                            <input type="date" id="endDate" class="form-control">
                        </div>
        
                        <!-- Service Selection -->
                        <div class="col-md-3">
                            <label for="serviceSelect" class="form-label">Service</label>
                            <select id="serviceSelect" class="form-select">
                                <option value="all">All Services</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->name }}">{{ $service->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Apply Filters Button -->
                        <div class="col-md-3">
                            <button class="btn btn-primary w-100 mt-4" id="applyFilters">
                                <i class="bi bi-funnel"></i> Apply Filters
                            </button>
                        </div>
                    </div>
                </div>
        
                <!-- Charts Section -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h6 class="text-primary">Status Distribution</h6>
                                <canvas id="statusChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h6 class="text-primary">Subscription Trends</h6>
                                <canvas id="subscriptionChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
        
                <!-- Report Table -->
                <div class="card mt-4 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-primary"><i class="bi bi-table"></i> Report Data</h6>
                        <button class="btn btn-success mb-3" id="exportTable">Export to Excel</button>
                        <table class="table table-bordered mt-3" id="reportTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Name</th>
                                    <th>Total Subs</th>
                                    <th>Active</th>
                                    <th>Failed</th>
                                    <th>New</th>
                                    <th>Canceled</th>
                                </tr>
                            </thead>
                            <tbody id="reportBody">
                                <!-- Data will be populated here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const startDate = document.getElementById("startDate");
            const endDate = document.getElementById("endDate");
            const serviceSelect = document.getElementById("serviceSelect");
            const applyFiltersBtn = document.getElementById("applyFilters");
            const reportBody = document.getElementById("reportBody");

            // Set default dates (last 30 days)
            const today = new Date();
            const thirtyDaysAgo = new Date();
            thirtyDaysAgo.setDate(today.getDate() - 30);
            
            startDate.value = thirtyDaysAgo.toISOString().split('T')[0];
            endDate.value = today.toISOString().split('T')[0];

            // Chart instances
            let statusChart = new Chart(document.getElementById("statusChart").getContext("2d"), {
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

            let subscriptionChart = new Chart(document.getElementById("subscriptionChart").getContext("2d"), {
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
                const selectedService = serviceSelect.value;

                fetch(`/api/v1/status-wise-report?start_date=${selectedStartDate}&end_date=${selectedEndDate}&service_name=${selectedService}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            throw new Error(data.message || data.error);
                        }

                        // Update Status Chart
                        statusChart.data.datasets[0].data = [
                            data.status_totals.active,
                            data.status_totals.failed,
                            data.status_totals.new,
                            data.status_totals.canceled
                        ];
                        statusChart.update();

                        // Update Subscription Chart
                        subscriptionChart.data.labels = data.dates;
                        subscriptionChart.data.datasets[0].data = data.subscription_totals;
                        subscriptionChart.update();

                        // Update Table
                        reportBody.innerHTML = '';
                        data.table_data.forEach(row => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td>${row.date}</td>
                                <td>${row.name}</td>
                                <td>${row.total_subs}</td>
                                <td>${row.active}</td>
                                <td>${row.failed}</td>
                                <td>${row.new}</td>
                                <td>${row.canceled}</td>
                            `;
                            reportBody.appendChild(tr);
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching status-wise report:', error);
                        alert('Error fetching data: ' + error.message);
                    });
            });

            // Export to Excel
            document.getElementById("exportTable").addEventListener("click", function () {
                let wb = XLSX.utils.book_new();
                let ws = XLSX.utils.table_to_sheet(document.getElementById("reportTable"));
                XLSX.utils.book_append_sheet(wb, ws, "Status Wise Report");
                XLSX.writeFile(wb, "Status_Wise_Report.xlsx");
            });

            // Load initial data
            applyFiltersBtn.click();
        });
    </script>

    @include('layouts.footer') 
    @include('layouts.footer_js')
  </body>
</html>