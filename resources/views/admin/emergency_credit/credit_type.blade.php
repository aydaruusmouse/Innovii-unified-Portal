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
    <!-- XLSX Library -->
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
                  <h5 class="m-b-10">Emergency Credit Types</h5>
                </div>
                <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                  <li class="breadcrumb-item">Emergency Credit</li>
                  <li class="breadcrumb-item">Credit Types</li>
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
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ date('Y-m-d', strtotime('-30 days')) }}">
                  </div>
                  <div class="col-md-4">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ date('Y-m-d') }}">
                  </div>
                  <div class="col-md-4 d-flex align-items-end">
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
          <!-- [ Credit Type Chart ] start -->
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h5>Credit Type Distribution</h5>
              </div>
              <div class="card-body">
                <canvas id="creditTypeChart" height="300"></canvas>
              </div>
            </div>
          </div>
          <!-- [ Credit Type Chart ] end -->

          <!-- [ Credit Type Stats ] start -->
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h5>Credit Type Statistics</h5>
                <button type="button" class="btn btn-success float-end" id="exportBtn">
                  <i class="bi bi-file-excel"></i> Export to Excel
                </button>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>Credit Type</th>
                        <th>Count</th>
                        <th>Unique Users</th>
                        <th>Total Units</th>
                        <th>% of Total</th>
                      </tr>
                    </thead>
                    <tbody id="creditTypeTable">
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
          <!-- [ Credit Type Stats ] end -->
        </div>
        <!-- [ Main Content ] end -->
      </div>
    </div>

    @include('layouts.footer') 
    @include('layouts.footer_js')

    <script>
    document.addEventListener('DOMContentLoaded', function() {
      const filterForm = document.getElementById('filterForm');
      let creditTypeChart = null;
      const chartContainer = document.getElementById('creditTypeChart').parentElement;
      
      function formatDateForAPI(dateString) {
        if (!dateString) return '';
        // Convert YYYY-MM-DD to DD/MM/YYYY
        const [year, month, day] = dateString.split('-');
        return `${day}/${month}/${year}`;
      }

      function formatDateForDisplay(dateString) {
        if (!dateString) return '';
        // Convert DD/MM/YYYY to YYYY-MM-DD
        const [day, month, year] = dateString.split('/');
        return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
      }
      
      function showLoading() {
        document.getElementById('creditTypeTable').innerHTML = `
          <tr>
            <td colspan="5" class="text-center">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
            </td>
          </tr>
        `;
        chartContainer.innerHTML = `
          <div class="text-center p-5">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
        `;
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
          // Clear the chart container first
          chartContainer.innerHTML = '<canvas id="creditTypeChart" height="300"></canvas>';
          const ctx = document.getElementById('creditTypeChart').getContext('2d');
          
          // Destroy existing chart if it exists
          if (creditTypeChart) {
            creditTypeChart.destroy();
          }

          const labels = data.map(item => item.credit_type);
          const counts = data.map(item => parseNumber(item.count));
          const percentages = data.map(item => parsePercentage(item.percentage));

          creditTypeChart = new Chart(ctx, {
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
                  text: 'Credit Type Distribution'
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
          chartContainer.innerHTML = `
            <div class="alert alert-danger">
              Error updating chart: ${error.message}
            </div>
          `;
        }
      }
      
      function fetchData(params = {}) {
        showLoading();
        
        // Format dates before sending
        if (params.start_date) {
          params.start_date = formatDateForAPI(params.start_date);
        }
        if (params.end_date) {
          params.end_date = formatDateForAPI(params.end_date);
        }
        
        console.log('Request parameters:', params);
        
        const url = new URL('http://127.0.0.1:8000/emergency-credit/credit-type/data');
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
            const tbody = document.getElementById('creditTypeTable');
            tbody.innerHTML = '';
            
            if (!data.creditTypeStats || data.creditTypeStats.length === 0) {
              const message = data.message || 'No data available for the selected period';
              tbody.innerHTML = `<tr><td colspan="5" class="text-center">${message}</td></tr>`;
              chartContainer.innerHTML = `<div class="alert alert-info">${message}</div>`;
              return;
            }

            data.creditTypeStats.forEach(stat => {
              const tr = document.createElement('tr');
              tr.innerHTML = `
                <td>${stat.credit_type}</td>
                <td>${stat.count}</td>
                <td>${stat.unique_users}</td>
                <td>${stat.total_units}</td>
                <td>${stat.percentage}%</td>
              `;
              tbody.appendChild(tr);
            });

            // Update chart
            updateChart(data.creditTypeStats);
          })
          .catch(error => {
            console.error('Error fetching data:', error);
            const errorMessage = error.message || 'Error loading data';
            document.getElementById('creditTypeTable').innerHTML = `<tr><td colspan="5" class="text-center text-danger">${errorMessage}</td></tr>`;
            chartContainer.innerHTML = `<div class="alert alert-danger">${errorMessage}</div>`;
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
          const wb = XLSX.utils.table_to_book(table, {sheet: "Credit Type Statistics"});
          const fileName = `emergency_credit_types_${new Date().toISOString().split('T')[0]}.xlsx`;
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