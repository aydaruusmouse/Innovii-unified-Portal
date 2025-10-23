<!doctype html>
<html lang="en">
  <!-- [Head] start -->
  <head>
    @include('layouts.heads_page') 
    @include('layouts.heads_css')
    @include('layouts.config')
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    <div class="pc-container">
      <div class="pc-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
          <div class="page-block">
            <div class="row align-items-center">
              <div class="col-md-12">
                <div class="page-header-title">
                  <h5 class="m-b-10">Emergency Credit Revenue - Data Only</h5>
                </div>
                <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.simple') }}">Home</a></li>
                  <li class="breadcrumb-item">Emergency Credit</li>
                  <li class="breadcrumb-item">Revenue Data Only</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <!-- [ breadcrumb ] end -->

        <!-- [ Filter Section ] start -->
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-body">
                <form id="filterForm" class="row g-3">
                  <div class="col-md-4">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ date('Y-m-01') }}">
                  </div>
                  <div class="col-md-4">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ date('Y-m-t') }}">
                  </div>
                  <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                      <i class="bi bi-search"></i> Filter
                    </button>
                    <button type="button" class="btn btn-success" onclick="exportToExcel()">
                      <i class="bi bi-download"></i> Export
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <!-- [ Filter Section ] end -->

        <!-- [ Revenue Summary Cards ] start -->
        <div class="row" id="summaryCards">
          <!-- Cards will be populated by JavaScript -->
        </div>
        <!-- [ Revenue Summary Cards ] end -->

        <!-- [ Revenue Chart ] start -->
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h5>Data Revenue Trend</h5>
              </div>
              <div class="card-body">
                <canvas id="revenueChart" height="100"></canvas>
              </div>
            </div>
          </div>
        </div>
        <!-- [ Revenue Chart ] end -->

        <!-- [ Revenue Table ] start -->
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h5>Data Revenue Summary</h5>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-striped" id="revenueTable">
                    <thead>
                      <tr>
                        <th>Date</th>
                        <th>Total Credit (10K)</th>
                        <th>Total Paid (10K)</th>
                        <th>Repayment %</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- Data will be populated by JavaScript -->
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- [ Revenue Table ] end -->

      </div>
    </div>
    @include('layouts.footer_js')

    <script>
      let revenueChart;
      let revenueData = [];

      document.addEventListener('DOMContentLoaded', function() {
        loadRevenueData();
        
        // Add form submit event listener
        document.getElementById('filterForm').addEventListener('submit', function(e) {
          e.preventDefault();
          loadRevenueData();
        });
      });

      // Simple load and wait function - no retry logic
      async function loadRevenueData() {
        const formData = new FormData(document.getElementById('filterForm'));
        const params = new URLSearchParams(formData);
        
        // Show loading state
        document.getElementById('summaryCards').innerHTML = '<div class="col-md-12"><div class="alert alert-info">Loading data...</div></div>';
        
        // Use the current origin (dynamic)
        const apiUrl = new URL(`${window.location.origin}/api/v1/emergency-credit/revenue-data-only/data`);
        Object.keys(Object.fromEntries(params)).forEach(key => {
          apiUrl.searchParams.append(key, params.get(key));
        });
        
        console.log('Loading data from:', apiUrl.toString());
        
        try {
          // Single request without timeout wrapper
          const response = await fetch(apiUrl.toString(), {
            method: 'GET',
            headers: {
              'X-Requested-With': 'XMLHttpRequest',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
              'Accept': 'application/json',
              'Content-Type': 'application/json'
            },
            credentials: 'same-origin'
          });
          
          if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
          }
          
          const data = await response.json();
          console.log('API Response received:', data);
          
          if (data.error) {
            throw new Error(data.error);
          }
          
          if (!data.revenueData || data.revenueData.length === 0) {
            console.log('No revenue data found');
            revenueData = [];
            document.getElementById('summaryCards').innerHTML = '<div class="col-md-12"><div class="alert alert-info">No data available for the selected date range</div></div>';
          } else {
            revenueData = data.revenueData;
            console.log('Successfully loaded revenue data:', revenueData.length, 'records');
            updateSummaryCards();
            updateChart();
            updateTable();
          }
          
        } catch (error) {
          console.error('Error loading revenue data:', error);
          document.getElementById('summaryCards').innerHTML = `
            <div class="col-md-12">
              <div class="alert alert-warning">
                <h5>Loading took too long</h5>
                <p>Please try again. The server may need a moment to process the request.</p>
                <button class="btn btn-sm btn-primary" onclick="loadRevenueData()">Try Again</button>
              </div>
            </div>
          `;
        }
      }
      
      // Fetch with timeout utility function
      function fetchWithTimeout(url, options = {}, timeout = 10000) {
        return new Promise((resolve, reject) => {
          const controller = new AbortController();
          const timeoutId = setTimeout(() => {
            controller.abort();
            reject(new Error(`Request timeout after ${timeout}ms`));
          }, timeout);
          
          fetch(url, {
            ...options,
            signal: controller.signal
          })
          .then(response => {
            clearTimeout(timeoutId);
            resolve(response);
          })
          .catch(error => {
            clearTimeout(timeoutId);
            reject(error);
          });
        });
      }

      function updateSummaryCards() {
        if (!revenueData || revenueData.length === 0) {
          document.getElementById('summaryCards').innerHTML = '<div class="col-md-12"><div class="alert alert-info">No data available</div></div>';
          return;
        }

        // Separate daily data from grand total
        const dailyData = revenueData.filter(item => item.date_label !== 'Grand Total');
        const grandTotal = revenueData.find(item => item.date_label === 'Grand Total');

        const totalCredit = dailyData.reduce((sum, item) => sum + parseFloat(item.total_credit), 0);
        const totalPaid = dailyData.reduce((sum, item) => sum + parseFloat(item.total_paid), 0);
        const avgRepayment = dailyData.length > 0 ? 
          dailyData.reduce((sum, item) => sum + parseFloat(item.repayment_percentage), 0) / dailyData.length : 0;

        document.getElementById('summaryCards').innerHTML = `
          <div class="col-md-3">
            <div class="card bg-primary text-white">
              <div class="card-body">
                <div class="d-flex justify-content-between">
                  <div>
                    <h4 class="mb-0">${totalCredit.toFixed(2)}</h4>
                    <p class="mb-0">Total Credit (10K)</p>
                  </div>
                  <div class="align-self-center">
                    <i class="bi bi-currency-dollar" style="font-size: 2rem;"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card bg-success text-white">
              <div class="card-body">
                <div class="d-flex justify-content-between">
                  <div>
                    <h4 class="mb-0">${totalPaid.toFixed(2)}</h4>
                    <p class="mb-0">Total Paid (10K)</p>
                  </div>
                  <div class="align-self-center">
                    <i class="bi bi-check-circle" style="font-size: 2rem;"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card bg-info text-white">
              <div class="card-body">
                <div class="d-flex justify-content-between">
                  <div>
                    <h4 class="mb-0">${avgRepayment.toFixed(2)}%</h4>
                    <p class="mb-0">Avg Repayment %</p>
                  </div>
                  <div class="align-self-center">
                    <i class="bi bi-percent" style="font-size: 2rem;"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card bg-warning text-white">
              <div class="card-body">
                <div class="d-flex justify-content-between">
                  <div>
                    <h4 class="mb-0">${grandTotal ? parseFloat(grandTotal.repayment_percentage).toFixed(2) : '0.00'}%</h4>
                    <p class="mb-0">Overall Repayment %</p>
                  </div>
                  <div class="align-self-center">
                    <i class="bi bi-graph-up" style="font-size: 2rem;"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        `;
      }

      function updateChart() {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        
        if (revenueChart) {
          revenueChart.destroy();
        }

        // Filter out grand total for chart
        const chartData = revenueData.filter(item => item.date_label !== 'Grand Total');
        
        const labels = chartData.map(item => item.date_label);
        const creditData = chartData.map(item => parseFloat(item.total_credit));
        const paidData = chartData.map(item => parseFloat(item.total_paid));

        revenueChart = new Chart(ctx, {
          type: 'line',
          data: {
            labels: labels,
            datasets: [{
              label: 'Total Credit (10K)',
              data: creditData,
              borderColor: 'rgb(75, 192, 192)',
              backgroundColor: 'rgba(75, 192, 192, 0.2)',
              tension: 0.1
            }, {
              label: 'Total Paid (10K)',
              data: paidData,
              borderColor: 'rgb(255, 99, 132)',
              backgroundColor: 'rgba(255, 99, 132, 0.2)',
              tension: 0.1
            }]
          },
          options: {
            responsive: true,
            plugins: {
              title: {
                display: true,
                text: 'Data Revenue Trend'
              }
            },
            scales: {
              y: {
                beginAtZero: true
              }
            }
          }
        });
      }

      function updateTable() {
        const tbody = document.querySelector('#revenueTable tbody');
        tbody.innerHTML = '';

        revenueData.forEach(item => {
          const row = document.createElement('tr');
          const isGrandTotal = item.date_label === 'Grand Total';
          row.className = isGrandTotal ? 'table-warning fw-bold' : '';
          
          row.innerHTML = `
            <td>${item.date_label}</td>
            <td>${parseFloat(item.total_credit).toFixed(2)}</td>
            <td>${parseFloat(item.total_paid).toFixed(2)}</td>
            <td>${parseFloat(item.repayment_percentage).toFixed(2)}%</td>
          `;
          tbody.appendChild(row);
        });
      }

      function exportToExcel() {
        if (!revenueData || revenueData.length === 0) {
          alert('No data to export');
          return;
        }

        const wsData = [
          ['Date', 'Total Credit (10K)', 'Total Paid (10K)', 'Repayment %']
        ];

        revenueData.forEach(item => {
          wsData.push([
            item.date_label,
            parseFloat(item.total_credit).toFixed(2),
            parseFloat(item.total_paid).toFixed(2),
            parseFloat(item.repayment_percentage).toFixed(2) + '%'
          ]);
        });

        const ws = XLSX.utils.aoa_to_sheet(wsData);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Data Revenue Summary');
        
        const fileName = `emergency_credit_data_revenue_${new Date().toISOString().split('T')[0]}.xlsx`;
        XLSX.writeFile(wb, fileName);
      }
    </script>
  </body>
</html>
