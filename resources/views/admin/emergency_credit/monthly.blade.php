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
    <div class="pc-container">
      <div class="pc-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
          <div class="page-block">
            <div class="row align-items-center">
              <div class="col-md-12">
                <div class="page-header-title">
                  <h5 class="m-b-10">Emergency Credit Monthly Report</h5>
                </div>
                <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.simple') }}">Home</a></li>
                  <li class="breadcrumb-item">Emergency Credit</li>
                  <li class="breadcrumb-item">Monthly Report</li>
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
                  <div class="col-md-3">
                    <label for="start_month" class="form-label">Start Month</label>
                    <input type="month" class="form-control" id="start_month" name="start_month" 
                           value="{{ date('Y-m', strtotime('-1 month')) }}">
                  </div>
                  <div class="col-md-3">
                    <label for="end_month" class="form-label">End Month</label>
                    <input type="month" class="form-control" id="end_month" name="end_month" 
                           value="{{ date('Y-m') }}">
                  </div>
                  <!-- <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                      <option value="">All Status</option>
                      <option value="SUCCESS">Success</option>
                      <option value="FAILED">Failed</option>
                      <option value="PENDING">Pending</option>
                    </select>
                  </div> -->
                  <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <!-- [ Filter Section ] end -->

        <!-- [ Main Content ] start -->
        <div class="row">
          <!-- [ Monthly Chart ] start -->
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h5>Monthly Transaction Trends</h5>
              </div>
              <div class="card-body">
                <div id="chartContainer">
                  <canvas id="monthlyChart" height="300"></canvas>
                </div>
              </div>
            </div>
          </div>
          <!-- [ Monthly Chart ] end -->

          <!-- [ Monthly Stats ] start -->
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h5>Monthly Statistics</h5>
                <button type="button" class="btn btn-success float-end" id="exportBtn">
                  <i class="bi bi-file-excel"></i> Export to Excel
                </button>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>Month</th>
                        <th>Total Transactions</th>
                        <th>Unique Users</th>
                        <th>Total Units</th>
                      </tr>
                    </thead>
                    <tbody id="monthlyTable">
                      <tr>
                        <td colspan="5" class="text-center">
                          <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <!-- [ Monthly Stats ] end -->
        </div>
        <!-- [ Main Content ] end -->
      </div>
    </div>

    @include('layouts.footer') 
    @include('layouts.footer_js')

    <script>
    document.addEventListener('DOMContentLoaded', function() {
      const filterForm = document.getElementById('filterForm');
      let monthlyChart = null;
      
      function showLoading() {
        document.getElementById('monthlyTable').innerHTML = `
          <tr>
            <td colspan="5" class="text-center">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
            </td>
          </tr>
        `;
        document.getElementById('chartContainer').innerHTML = `
          <div class="text-center p-5">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
        `;
      }

      function updateChart(data) {
        const ctx = document.getElementById('monthlyChart').getContext('2d');
        
        // Destroy existing chart if it exists
        if (monthlyChart) {
          monthlyChart.destroy();
        }

        const months = data.map(item => item.month);
        const uniqueUsers = data.map(item => item.unique_users);
        const totalTransactions = data.map(item => item.total_transactions);
        const totalUnits = data.map(item => item.total_units);

        monthlyChart = new Chart(ctx, {
          type: 'line',
          data: {
            labels: months,
            datasets: [
              {
                label: 'Unique Users',
                data: uniqueUsers,
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
              },
              {
                label: 'Total Transactions',
                data: totalTransactions,
                borderColor: 'rgb(255, 99, 132)',
                tension: 0.1
              },
              {
                label: 'Total Units',
                data: totalUnits,
                borderColor: 'rgb(54, 162, 235)',
                tension: 0.1
              }
            ]
          },
          options: {
            responsive: true,
            plugins: {
              title: {
                display: true,
                text: 'Monthly Emergency Credit Trends'
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
      
      function exportTableToExcel() {
        const table = document.querySelector('.table');
        const wb = XLSX.utils.table_to_book(table, {sheet: "Monthly Statistics"});
        const fileName = `emergency_credit_monthly_${new Date().toISOString().split('T')[0]}.xlsx`;
        XLSX.writeFile(wb, fileName);
      }

      function fetchData(params = {}) {
        showLoading();
        
        console.log('Request parameters:', params);
        
        const url = new URL('${window.location.origin}/emergency-credit/monthly/data');
        Object.keys(params).forEach(key => url.searchParams.append(key, params[key]));
        
        console.log('Request URL:', url.toString());
        
        fetch(url)
          .then(response => {
            if (!response.ok) {
              throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
          })
          .then(data => {
            console.log('Response data:', data);
            
            // Update table
            const tbody = document.getElementById('monthlyTable');
            tbody.innerHTML = '';
            
            if (!data.monthlyStats || data.monthlyStats.length === 0) {
              tbody.innerHTML = '<tr><td colspan="5" class="text-center">No data available for the selected period</td></tr>';
              document.getElementById('chartContainer').innerHTML = '<div class="alert alert-info">No data available for the selected period</div>';
              return;
            }

            data.monthlyStats.forEach(stat => {
              const tr = document.createElement('tr');
              tr.innerHTML = `
                <td>${formatMonthForDisplay(stat.month)}</td>
                <td>${stat.total_transactions}</td>
                <td>${stat.unique_users}</td>
                <td>${stat.total_units}</td>
              `;
              tbody.appendChild(tr);
            });

            // Update chart
            document.getElementById('chartContainer').innerHTML = '<canvas id="monthlyChart" height="300"></canvas>';
            updateChart(data.monthlyStats);
          })
          .catch(error => {
            console.error('Error fetching data:', error);
            document.getElementById('monthlyTable').innerHTML = '<tr><td colspan="5" class="text-center text-danger">Error loading data</td></tr>';
            document.getElementById('chartContainer').innerHTML = '<div class="alert alert-danger">Error loading data</div>';
          });
      }

      function formatMonthForDisplay(monthString) {
        if (!monthString) return '';
        
        const [year, month] = monthString.split('-');
        const date = new Date(year, month - 1);
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long' });
      }

      // Initial data load
      fetchData();

      // Handle form submission
      filterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const params = {};
        formData.forEach((value, key) => {
          if (value) params[key] = value;
        });
        fetchData(params);
      });

      // Add export button click handler
      document.getElementById('exportBtn').addEventListener('click', function() {
        const table = document.querySelector('.table');
        if (!table) {
          console.error('Table not found');
          return;
        }
        
        try {
          const wb = XLSX.utils.table_to_book(table, {sheet: "Monthly Statistics"});
          const fileName = `emergency_credit_monthly_${new Date().toISOString().split('T')[0]}.xlsx`;
          XLSX.writeFile(wb, fileName);
        } catch (error) {
          console.error('Error exporting to Excel:', error);
          alert('Error exporting to Excel. Please try again.');
        }
      });
    });
    </script>
  </body>
</html>