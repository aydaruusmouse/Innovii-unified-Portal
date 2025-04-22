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
          <!-- Left Sidebar Filters -->
          <div class="container mt-4">
            <div class="card shadow-sm p-3">
                <div class="row align-items-center g-3">
                    <div class="col-md-2">
                        <label class="fw-bold">Start Date:</label>
                        <input type="date" id="startDate" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="fw-bold">End Date:</label>
                        <input type="date" id="endDate" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="fw-bold">Subscription Offer:</label>
                        <select id="offerSelect" class="form-select">
                            <option value="all">All Offers</option>
                            @foreach($offers as $offer)
                                <option value="{{ $offer->name }}">{{ $offer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="fw-bold">Status:</label>
                        <select id="statusSelect" class="form-select">
                            <option value="all">All Status</option>
                            <option value="ACTIVE">ACTIVE</option>
                            <option value="GRACE">GRACE</option>
                            <option value="INACTIVE">INACTIVE</option>
                            <option value="FAILED">FAILED</option>
                            <option value="NEW">NEW</option>
                            <option value="PROCESSING">PROCESSING</option>
                        </select>
                    </div>
                    {{-- <div class="col-md-2">
                      <label class="form-label">Granularity</label>
                      <div class="btn-group w-100 shadow-sm" id="granularityToggle">
                          <button type="button" class="btn btn-outline-primary active" data-granularity="daily">Daily</button>
                          <button type="button" class="btn btn-outline-primary" data-granularity="weekly">Weekly</button>
                          <button type="button" class="btn btn-outline-primary" data-granularity="monthly">Monthly</button>
                      </div>
                  </div> --}}
                    <div class="col-md-2">
                        <button id="applyFilters" class="btn btn-primary w-100 mt-4">Apply Filters</button>
                    </div>
                </div>
            </div>
        </div>
    
        <!-- Chart Section -->
        <div class="container mt-4">
            <div class="row">
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="fw-bold text-center">Active vs Inactive Users</h5>
                            <canvas id="statusPieChart"></canvas>
                            <button class="btn btn-primary mt-3 w-100" id="exportPieChart">Export Pie Chart</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="fw-bold text-center">Subscription Trends</h5>
                            <canvas id="subscriptionTrendChart"></canvas>
                            <button class="btn btn-primary mt-3 w-100" id="exportTrendChart">Export Trend Chart</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
        <!-- Table Section -->
        <div class="container mt-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold">Filtered Data</h5>
                    <button class="btn btn-success mb-3" id="exportTable">Export Table to Excel</button>
                    <table class="table table-bordered">
                        <thead class="table-primary">
                            <tr>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Offer</th>
                                <th>Status</th>
                                <th>Subscribers</th>
                            </tr>
                        </thead>
                        <tbody id="dataTable">
                            <!-- Dynamic Data Here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    
        <!-- Bootstrap JS -->
        
          </div>
          <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
          <!-- JavaScript -->
          // ... existing code ...
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const startDate = document.getElementById("startDate");
        const endDate = document.getElementById("endDate");
        const offerSelect = document.getElementById("offerSelect");
        const statusSelect = document.getElementById("statusSelect");
        const applyFiltersBtn = document.getElementById("applyFilters");
        const dataTable = document.getElementById("dataTable");

        // Chart.js Instances
        const statusPieChartCtx = document.getElementById("statusPieChart").getContext("2d");
        const subscriptionTrendChartCtx = document.getElementById("subscriptionTrendChart").getContext("2d");

        let statusPieChart = new Chart(statusPieChartCtx, {
            type: "pie",
            data: {
                labels: ["Active", "Inactive"],
                datasets: [{ data: [0, 0], backgroundColor: ["#0d6efd", "#dc3545"] }]
            }
        });

        let subscriptionTrendChart = new Chart(subscriptionTrendChartCtx, {
            type: "line",
            data: {
                labels: [],
                datasets: [{ 
                    label: "Subscribers", 
                    data: [], 
                    borderColor: "#0d6efd", 
                    fill: false 
                }]
            }
        });

        // Apply Filters
        applyFiltersBtn.addEventListener("click", function () {
            const selectedStartDate = startDate.value;
            const selectedEndDate = endDate.value;
            const selectedOffer = offerSelect.value;
            const selectedStatus = statusSelect.value;

            // Fetch data from API
            fetch(`/api/v1/service-report?service_name=${encodeURIComponent(selectedOffer)}&start_date=${selectedStartDate}&end_date=${selectedEndDate}&status=${selectedStatus}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        throw new Error(data.message || data.error);
                    }

                    // Update Pie Chart
                    statusPieChart.data.datasets[0].data = [
                        data.active_count || 0,
                        data.inactive_count || 0
                    ];
                    statusPieChart.update();

                    // Update Trend Chart
                    subscriptionTrendChart.data.labels = data.dates || [];
                    subscriptionTrendChart.data.datasets[0].data = data.subscription_counts || [];
                    subscriptionTrendChart.update();

                    // Update Table
                    dataTable.innerHTML = '';
                    if (data.table_data && data.table_data.length > 0) {
                        data.table_data.forEach(row => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td>${row.start_date || "N/A"}</td>
                                <td>${row.end_date || "N/A"}</td>
                                <td>${row.offer || "N/A"}</td>
                                <td>${row.status || "N/A"}</td>
                                <td>${row.subscribers || 0}</td>
                            `;
                            dataTable.appendChild(tr);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error fetching service report:', error);
                    alert('Error fetching data: ' + error.message);
                });
        });

        // Export Table to Excel
        document.getElementById("exportTable").addEventListener("click", function () {
            let wb = XLSX.utils.book_new();
            let ws = XLSX.utils.table_to_sheet(document.querySelector("table"));
            XLSX.utils.book_append_sheet(wb, ws, "Filtered Data");
            XLSX.writeFile(wb, "Filtered_Data.xlsx");
        });

        // Export Chart Data
        function exportChartData(chart, filename) {
            let wb = XLSX.utils.book_new();
            let ws = XLSX.utils.aoa_to_sheet([
                ["Label", "Value"],
                ...chart.data.labels.map((label, index) => [label, chart.data.datasets[0].data[index]])
            ]);
            XLSX.utils.book_append_sheet(wb, ws, filename);
            XLSX.writeFile(wb, `${filename}.xlsx`);
        }

        document.getElementById("exportPieChart").addEventListener("click", function () {
            exportChartData(statusPieChart, "Pie_Chart_Data");
        });

        document.getElementById("exportTrendChart").addEventListener("click", function () {
            exportChartData(subscriptionTrendChart, "Trend_Chart_Data");
        });
    });
</script>

          <!-- Right Content Area -->
         
    <!-- Keep existing modals and scripts -->
    
    @include('layouts.footer') 
    @include('layouts.footer_js')

    {{-- <script>
        document.addEventListener("DOMContentLoaded", function () {
          
          // Get elements
          const reportMonth = document.getElementById("reportMonth");
          const granularityButtons = document.querySelectorAll(".btn-group .btn");
          const subscriptionSelect = document.querySelector(".form-select");
          const combineStatusToggle = document.getElementById("combineStatus");
          const statusCheckboxes = document.querySelectorAll(".form-check-input");
          const applyFiltersBtn = document.querySelector(".btn-brand-color-1");
      
          // Default values
          let selectedMonthYear = reportMonth.value;
          let selectedGranularity = "Daily";
          let selectedSubscriptions = Array.from(subscriptionSelect.selectedOptions).map(opt => opt.value);
          let statusFilters = {
            base: [],
            system: []
          };
          
          // Handle Month/Year Picker change
          reportMonth.addEventListener("change", function () {
            selectedMonthYear = this.value;
            console.log("Selected Month/Year:", selectedMonthYear);
          });
      
          // Handle Granularity Toggle
          granularityButtons.forEach(button => {
            button.addEventListener("click", function () {
              granularityButtons.forEach(btn => btn.classList.remove("active"));
              this.classList.add("active");
              selectedGranularity = this.innerText;
              console.log("Selected Granularity:", selectedGranularity);
            });
          });
      
          // Handle Subscription Multi-Select
          subscriptionSelect.addEventListener("change", function () {
            selectedSubscriptions = Array.from(this.selectedOptions).map(opt => opt.value);
            console.log("Selected Subscriptions:", selectedSubscriptions);
          });
      
          // Handle Status Filters
          function updateStatusFilters() {
            statusFilters.base = [];
            statusFilters.system = [];
            document.querySelectorAll(".form-check-input").forEach(checkbox => {
              if (checkbox.checked) {
                if (checkbox.id === "active" || checkbox.id === "grace" || checkbox.id === "inactive" || checkbox.id === "failed") {
                  statusFilters.base.push(checkbox.id.toUpperCase());
                } else {
                  statusFilters.system.push(checkbox.id.toUpperCase());
                }
              }
            });
            console.log("Selected Status Filters:", statusFilters);
          }
      
          statusCheckboxes.forEach(checkbox => {
            checkbox.addEventListener("change", updateStatusFilters);
          });
      
          // Handle Apply Filters Button Click
          applyFiltersBtn.addEventListener("click", function () {
            console.log("🔹 Applying Filters...");
            console.log("📆 Month/Year:", selectedMonthYear);
            console.log("📊 Granularity:", selectedGranularity);
            console.log("📜 Selected Subscriptions:", selectedSubscriptions);
            console.log("✅ Status Filters:", statusFilters);
            console.log("🔄 Combine Statuses:", combineStatusToggle.checked);
            
            // Here, you can make an AJAX request or update the Chart.js graphs dynamically.
          });
      
        });
      </script> --}}
      
  
  </body>
</html>