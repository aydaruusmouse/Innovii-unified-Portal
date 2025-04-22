<!doctype html>
<html lang="en">
  <!-- [Head] start -->
  <head>
    @include('layouts.heads_page') 
    @include('layouts.heads_css')
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <!-- Filter Section (Top & Horizontal) -->
                <div class="card shadow-sm p-3">
                    <h5 class="text-primary mb-3"><i class="bi bi-funnel"></i> Subscription Insights Filters</h5>
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
        
                        <!-- Service Type Multi-Select -->
                        <div class="col-md-3">
                            <label for="serviceType" class="form-label">Service Type</label>
                            <select id="serviceType" class="form-select" multiple>
                                <option value="Iga Qabo">Iga Qabo</option>
                                <option value="MyStatus">MyStatus</option>
                                <option value="CRBT">CRBT</option>
                                <option value="Voicemail">Voicemail</option>
                                <option value="Emergency Credit">Emergency Credit</option>
                            </select>
                        </div>
        
                        <!-- Granularity Toggle -->
                        <div class="col-md-3">
                            <label class="form-label">Granularity</label>
                            <div class="btn-group w-100">
                                <button type="button" class="btn btn-outline-primary active granularity" data-type="daily">Daily</button>
                                <button type="button" class="btn btn-outline-primary granularity" data-type="weekly">Weekly</button>
                                <button type="button" class="btn btn-outline-primary granularity" data-type="monthly">Monthly</button>
                            </div>
                        </div>
                    </div>
        
                    <!-- Apply Filters & Export Buttons -->
                    <div class="mt-3 text-end">
                        <button class="btn btn-primary" onclick="applyFilters()">
                            <i class="bi bi-funnel"></i> Apply Filters
                        </button>
                        <button class="btn btn-success" onclick="exportToExcel('subscriptionTable', 'SubscriptionInsights')">
                            <i class="bi bi-file-earmark-excel"></i> Export to Excel
                        </button>
                    </div>
                </div>
        
                <!-- Subscription Insights Dashboard -->
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="card shadow-sm p-3 text-center">
                            <h6 class="text-primary">Total Subscriptions</h6>
                            <h3 id="totalSubscriptions">0</h3>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h6 class="text-primary">Active vs Inactive Users</h6>
                                <canvas id="statusChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h6 class="text-primary">Top Services</h6>
                                <canvas id="topServicesChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
        
                <!-- Subscription Trends -->
                <div class="card mt-4 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-primary">Subscription Trends</h6>
                        <canvas id="subscriptionTrendsChart"></canvas>
                    </div>
                </div>
        
                <!-- Subscription Insights Table -->
                <div class="card mt-4 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-primary"><i class="bi bi-table"></i> Subscription Insights Data</h6>
                        <table class="table table-bordered mt-3" id="subscriptionTable">
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
                            <tbody id="subscriptionBody">
                                <tr>
                                    <td>2024-01-31</td>
                                    <td>Iga Qabo</td>
                                    <td>100000</td>
                                    <td>50000</td>
                                    <td>50000</td>
                                    <td>10000</td>
                                    <td>5000</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        
            @include('layouts.footer')
            @include('layouts.footer_js')
        
            <script>
                // Apply Filters
                function applyFilters() {
                    alert('Filters applied! Data will be fetched from the backend.');
                }
        
                // Export to Excel
                function exportToExcel(tableID, filename = '') {
                    let table = document.getElementById(tableID);
                    let ws = XLSX.utils.table_to_sheet(table);
                    let wb = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(wb, ws, "Sheet1");
                    XLSX.writeFile(wb, filename + ".xlsx");
                }
        
                // Subscription Insights Charts
                let statusChartCtx = document.getElementById('statusChart').getContext('2d');
                let statusChart = new Chart(statusChartCtx, {
                    type: 'pie',
                    data: {
                        labels: ['Active', 'Inactive', 'Failed'],
                        datasets: [{
                            data: [60, 30, 10],
                            backgroundColor: ['#28a745', '#dc3545', '#ffc107']
                        }]
                    }
                });
        
                let subscriptionTrendsChartCtx = document.getElementById('subscriptionTrendsChart').getContext('2d');
                let subscriptionTrendsChart = new Chart(subscriptionTrendsChartCtx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr'],
                        datasets: [{
                            label: 'Subscriptions',
                            data: [1000, 1500, 1800, 1200],
                            borderColor: '#007bff',
                            fill: false
                        }]
                    }
                });
        
                let topServicesChartCtx = document.getElementById('topServicesChart').getContext('2d');
                let topServicesChart = new Chart(topServicesChartCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Iga Qabo', 'MyStatus', 'Voicemail'],
                        datasets: [{
                            label: 'Total Subs',
                            data: [5000, 3000, 2000],
                            backgroundColor: '#007bff'
                        }]
                    }
                });
            </script>
        </div>
        </div>
          </div>
        
          <!-- Right Content Area -->
         
    <!-- Keep existing modals and scripts -->
    
    @include('layouts.footer') 
    @include('layouts.footer_js')

   
      
  </body>
</html>