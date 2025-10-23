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
                  <h5 class="m-b-10">Emergency Credit Revenue with Balance</h5>
                </div>
                <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.simple') }}">Home</a></li>
                  <li class="breadcrumb-item">Emergency Credit</li>
                  <li class="breadcrumb-item">Revenue with Balance</li>
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
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ date('Y-m-d') }}">
                  </div>
                  <div class="col-md-4">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ date('Y-m-d') }}">
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
                <h5>Revenue with Balance Trend</h5>
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
                <h5>Revenue with Balance Data</h5>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-striped" id="revenueTable">
                    <thead>
                      <tr>
                        <th>Date</th>
                        <th>Total Credit (10K)</th>
                        <th>Total Paid (10K)</th>
                        <th>Balance (10K)</th>
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
        // Add a small delay to ensure server is ready
        setTimeout(() => {
          loadRevenueData();
        }, 1000); // 1 second delay
        
        // Add form submit event listener
        document.getElementById('filterForm').addEventListener('submit', function(e) {
          e.preventDefault();
          loadRevenueData();
        });
      });

      // Health check function to test server availability
      async function checkServerHealth(endpoint) {
        try {
          const response = await fetchWithTimeout(endpoint, {
            method: 'HEAD', // Just check if server responds
            headers: {
              'X-Requested-With': 'XMLHttpRequest'
            }
          }, 3000); // 3 second timeout for health check
          return response.ok;
        } catch (error) {
          return false;
        }
      }

      // Robust API request function with multiple fallback strategies
      async function loadRevenueData() {
        const formData = new FormData(document.getElementById('filterForm'));
        const params = new URLSearchParams(formData);
        
        // Show loading state
        document.getElementById('summaryCards').innerHTML = '<div class="col-md-12"><div class="alert alert-info">Loading data...</div></div>';
        
        // Try multiple API endpoints in order of preference
        const apiEndpoints = [
          `${window.location.origin}/api/v1/emergency-credit/revenue-with-balance/data`,
          `http://127.0.0.1:8000/api/v1/emergency-credit/revenue-with-balance/data`,
          `http://127.0.0.1:8007/api/v1/emergency-credit/revenue-with-balance/data`
        ];
        
        let lastError = null;
        
        for (let i = 0; i < apiEndpoints.length; i++) {
          try {
            const endpoint = apiEndpoints[i];
            const apiUrl = new URL(endpoint);
            Object.keys(Object.fromEntries(params)).forEach(key => {
              apiUrl.searchParams.append(key, params.get(key));
            });
            
            console.log(`Trying API endpoint ${i + 1}/${apiEndpoints.length}:`, apiUrl.toString());
            
            // Add exponential backoff delay for retries
            if (i > 0) {
              const delay = Math.pow(2, i) * 1000; // 2s, 4s, 8s delays
              console.log(`Waiting ${delay}ms before retry...`);
              await new Promise(resolve => setTimeout(resolve, delay));
            }
            
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
            } else {
              revenueData = data.revenueData;
              console.log('Successfully loaded revenue data:', revenueData.length, 'records');
            }
            
            updateSummaryCards();
            updateChart();
            updateTable();
            return; // Success, exit the function
            
          } catch (error) {
            console.error(`API endpoint ${i + 1} failed:`, error.message);
            lastError = error;
            
            // If it's a connection refused error, try next endpoint immediately
            if (error.message.includes('Failed to fetch') || error.message.includes('ERR_CONNECTION_REFUSED')) {
              console.log('Connection refused, trying next endpoint...');
              continue;
            }
            
            // For timeout errors, add a small delay before trying next endpoint
            if (error.message.includes('timeout')) {
              console.log('Timeout occurred, waiting before next attempt...');
              await new Promise(resolve => setTimeout(resolve, 2000));
            }
            
            continue; // Try next endpoint
          }
        }
        
        // All endpoints failed
        console.error('All API endpoints failed. Last error:', lastError);
        document.getElementById('summaryCards').innerHTML = `
          <div class="col-md-12">
            <div class="alert alert-danger">
              <h5>Unable to load data</h5>
              <p>All API endpoints failed. Last error: ${lastError ? lastError.message : 'Unknown error'}</p>
              <button class="btn btn-sm btn-outline-danger" onclick="loadRevenueData()">Retry</button>
            </div>
          </div>
        `;
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
        console.log('updateSummaryCards called with revenueData:', revenueData);
        console.log('revenueData length:', revenueData ? revenueData.length : 'undefined');
        
        if (!revenueData || revenueData.length === 0) {
          console.log('No data available - showing alert');
          document.getElementById('summaryCards').innerHTML = '<div class="col-md-12"><div class="alert alert-info">No data available</div></div>';
          return;
        }
        
        console.log('Processing revenue data for summary cards');

        const totalCredit = revenueData.reduce((sum, item) => sum + parseFloat(item.total_credit), 0);
        const totalPaid = revenueData.reduce((sum, item) => sum + parseFloat(item.total_paid), 0);
        const totalBalance = revenueData.reduce((sum, item) => sum + parseFloat(item.balance), 0);
        const avgRepayment = revenueData.length > 0 ? 
          revenueData.reduce((sum, item) => sum + parseFloat(item.repayment_percentage), 0) / revenueData.length : 0;

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
            <div class="card bg-warning text-white">
              <div class="card-body">
                <div class="d-flex justify-content-between">
                  <div>
                    <h4 class="mb-0">${totalBalance.toFixed(2)}</h4>
                    <p class="mb-0">Total Balance (10K)</p>
                  </div>
                  <div class="align-self-center">
                    <i class="bi bi-exclamation-triangle" style="font-size: 2rem;"></i>
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
        `;
      }

      function updateChart() {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        
        if (revenueChart) {
          revenueChart.destroy();
        }

        const labels = revenueData.map(item => item.date_label);
        const creditData = revenueData.map(item => parseFloat(item.total_credit));
        const paidData = revenueData.map(item => parseFloat(item.total_paid));
        const balanceData = revenueData.map(item => parseFloat(item.balance));

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
            }, {
              label: 'Balance (10K)',
              data: balanceData,
              borderColor: 'rgb(255, 205, 86)',
              backgroundColor: 'rgba(255, 205, 86, 0.2)',
              tension: 0.1
            }]
          },
          options: {
            responsive: true,
            plugins: {
              title: {
                display: true,
                text: 'Revenue with Balance Trend'
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
          
          row.innerHTML = `
            <td>${item.date_label}</td>
            <td>${parseFloat(item.total_credit).toFixed(2)}</td>
            <td>${parseFloat(item.total_paid).toFixed(2)}</td>
            <td>${parseFloat(item.balance).toFixed(2)}</td>
            <td>${parseFloat(item.repayment_percentage).toFixed(2)}%</td>
          `;
          tbody.appendChild(row);
        });
      }

      // Test function - you can call this in browser console: testAPI()
      window.testAPI = function() {
        console.log('Testing API directly...');
        fetch('http://127.0.0.1:8000/api/v1/emergency-credit/revenue-with-balance/data?start_date=2025-01-01&end_date=2025-02-01')
          .then(response => {
            console.log('Direct API test - Status:', response.status);
            return response.json();
          })
          .then(data => {
            console.log('Direct API test - Data:', data);
          })
          .catch(error => {
            console.error('Direct API test - Error:', error);
          });
      };

      // Simple test function
      window.simpleTest = function() {
        console.log('Simple test starting...');
        fetch('http://127.0.0.1:8000/api/v1/emergency-credit/revenue-with-balance/data?start_date=2025-01-01&end_date=2025-02-01')
          .then(r => r.text())
          .then(text => console.log('Raw response:', text))
          .catch(e => console.error('Simple test error:', e));
      };

      function exportToExcel() {
        if (!revenueData || revenueData.length === 0) {
          alert('No data to export');
          return;
        }

        const wsData = [
          ['Date', 'Total Credit (10K)', 'Total Paid (10K)', 'Balance (10K)', 'Repayment %']
        ];

        revenueData.forEach(item => {
          wsData.push([
            item.date_label,
            parseFloat(item.total_credit).toFixed(2),
            parseFloat(item.total_paid).toFixed(2),
            parseFloat(item.balance).toFixed(2),
            parseFloat(item.repayment_percentage).toFixed(2) + '%'
          ]);
        });

        const ws = XLSX.utils.aoa_to_sheet(wsData);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Revenue with Balance');
        
        const fileName = `emergency_credit_revenue_with_balance_${new Date().toISOString().split('T')[0]}.xlsx`;
        XLSX.writeFile(wb, fileName);
      }
    </script>
  </body>
</html>
