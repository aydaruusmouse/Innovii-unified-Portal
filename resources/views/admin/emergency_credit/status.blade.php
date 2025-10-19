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
                  <h5 class="m-b-10">Emergency Credit Transaction Status</h5>
                </div>
                <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.simple') }}">Home</a></li>
                  <li class="breadcrumb-item">Emergency Credit</li>
                  <li class="breadcrumb-item">Transaction Status</li>
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
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" 
                           value="{{ date('Y-m-d', strtotime('-30 days')) }}">
                  </div>
                  <div class="col-md-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" 
                           value="{{ date('Y-m-d') }}">
                  </div>
                  <div class="col-md-4">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                      <option value="">All Status</option>
                      @foreach($statuses as $status)
                        <option value="{{ $status }}">{{ $status }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-8 d-flex justify-content-end">
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
          <!-- [ Status Chart ] start -->
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h5>Transaction Status Distribution</h5>
              </div>
              <div class="card-body">
                <div id="chartContainer">
                  <canvas id="statusChart" height="300"></canvas>
                </div>
              </div>
            </div>
          </div>
          <!-- [ Status Chart ] end -->

          <!-- [ Status Stats ] start -->
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h5>Status Statistics</h5>
                <button type="button" class="btn btn-success float-end" id="exportBtn">
                  <i class="bi bi-file-excel"></i> Export to Excel
                </button>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>Status</th>
                        <th>Count</th>
                        <th>Unique Users</th>
                        <th>Total Units</th>
                        <th>% of Total</th>
                      </tr>
                    </thead>
                    <tbody id="statusTable">
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
          <!-- [ Status Stats ] end -->
        </div>
        <!-- [ Main Content ] end -->
      </div>
    </div>

    @include('layouts.footer') 
    @include('layouts.footer_js')

    <script>
    document.addEventListener('DOMContentLoaded', function() {
      const filterForm = document.getElementById('filterForm');
      let statusChart = null;
      
      function showLoading() {
        document.getElementById('statusTable').innerHTML = `
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

      function exportTableToExcel() {
        const table = document.querySelector('.table');
        const wb = XLSX.utils.table_to_book(table, {sheet: "Status Statistics"});
        const fileName = `emergency_credit_status_${new Date().toISOString().split('T')[0]}.xlsx`;
        XLSX.writeFile(wb, fileName);
      }

      function parseNumber(value) {
        if (typeof value === 'string') {
          return parseInt(value.replace(/,/g, '')) || 0;
        }
        return value || 0;
      }

      function parsePercentage(value) {
        if (typeof value === 'string') {
          return parseFloat(value) || 0;
        }
        return value || 0;
      }

      function updateChart(data) {
        try {
          const ctx = document.getElementById('statusChart').getContext('2d');
          
          // Destroy existing chart if it exists
          if (statusChart) {
            statusChart.destroy();
          }

          const labels = data.map(item => item.status);
          const counts = data.map(item => parseNumber(item.count));
          const percentages = data.map(item => parsePercentage(item.percentage));

          statusChart = new Chart(ctx, {
            type: 'pie',
            data: {
              labels: labels,
              datasets: [{
                data: counts,
                backgroundColor: [
                  'rgb(75, 192, 192)',
                  'rgb(255, 99, 132)',
                  'rgb(54, 162, 235)',
                  'rgb(255, 205, 86)',
                  'rgb(153, 102, 255)'
                ]
              }]
            },
            options: {
              responsive: true,
              plugins: {
                title: {
                  display: true,
                  text: 'Transaction Status Distribution'
                },
                tooltip: {
                  callbacks: {
                    label: function(context) {
                      const label = context.label || '';
                      const value = context.raw || 0;
                      const percentage = percentages[context.dataIndex] || 0;
                      return `${label}: ${value.toLocaleString()} (${percentage.toFixed(2)}%)`;
                    }
                  }
                }
              }
            }
          });
        } catch (error) {
          console.error('Error updating chart:', error);
          document.getElementById('chartContainer').innerHTML = `
            <div class="alert alert-danger">
              Error updating chart: ${error.message}
            </div>
          `;
        }
      }
      
      function fetchData(params = {}) {
        showLoading();
        
        console.log('Request parameters:', params);
        
        const url = new URL('${window.location.origin}/emergency-credit/status/data');
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
            const tbody = document.getElementById('statusTable');
            tbody.innerHTML = '';
            
            if (!data.statusStats || data.statusStats.length === 0) {
              const message = data.message || 'No data available for the selected period';
              tbody.innerHTML = `<tr><td colspan="5" class="text-center">${message}</td></tr>`;
              document.getElementById('chartContainer').innerHTML = `<div class="alert alert-info">${message}</div>`;
              return;
            }

            data.statusStats.forEach(stat => {
              const tr = document.createElement('tr');
              tr.innerHTML = `
                <td>${stat.status}</td>
                <td>${stat.count}</td>
                <td>${stat.unique_users}</td>
                <td>${stat.total_units}</td>
                <td>${stat.percentage}%</td>
              `;
              tbody.appendChild(tr);
            });

            // Update chart
            document.getElementById('chartContainer').innerHTML = '<canvas id="statusChart" height="300"></canvas>';
            updateChart(data.statusStats);
          })
          .catch(error => {
            console.error('Error fetching data:', error);
            const errorMessage = error.message || 'Error loading data';
            document.getElementById('statusTable').innerHTML = `<tr><td colspan="5" class="text-center text-danger">${errorMessage}</td></tr>`;
            document.getElementById('chartContainer').innerHTML = `<div class="alert alert-danger">${errorMessage}</div>`;
          });
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
          const wb = XLSX.utils.table_to_book(table, {sheet: "Status Statistics"});
          const fileName = `emergency_credit_status_${new Date().toISOString().split('T')[0]}.xlsx`;
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