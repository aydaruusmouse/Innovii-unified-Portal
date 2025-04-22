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
                  <h5 class="m-b-10">Weekly Emergency Credit Trends</h5>
                </div>
                <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                  <li class="breadcrumb-item">Emergency Credit</li>
                  <li class="breadcrumb-item">Weekly Trends</li>
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
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ date('Y-m-d', strtotime('-7 days')) }}">
                  </div>
                  <div class="col-md-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ date('Y-m-d') }}">
                  </div>
                  <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                      <option value="">All Status</option>
                      <option value="SUCCESS">Success</option>
                      <option value="FAILED">Failed</option>
                      <option value="PENDING">Pending</option>
                    </select>
                  </div>
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
          <!-- [ Weekly Chart ] start -->
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h5>Weekly Statistics</h5>
              </div>
              <div class="card-body">
                <canvas id="weeklyChart" height="100"></canvas>
              </div>
            </div>
          </div>
          <!-- [ Weekly Chart ] end -->

          <!-- [ Weekly Table ] start -->
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h5>Detailed Weekly Data</h5>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>Date</th>
                        <th>Unique Users</th>
                        <th>Total Transactions</th>
                        <th>Total Units</th>
                      </tr>
                    </thead>
                    <tbody id="weeklyTable">
                      <tr>
                        <td colspan="4" class="text-center">
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
          <!-- [ Weekly Table ] end -->
        </div>
        <!-- [ Main Content ] end -->
      </div>
    </div>

    @include('layouts.footer') 
    @include('layouts.footer_js')

    <script>
    document.addEventListener('DOMContentLoaded', function() {
      const filterForm = document.getElementById('filterForm');
      
      function fetchData(params = {}) {
        const url = new URL('http://127.0.0.1:8000/emergency-credit/weekly/data');
        Object.keys(params).forEach(key => url.searchParams.append(key, params[key]));
        
        fetch(url)
          .then(response => response.json())
          .then(data => {
            // Update weekly table
            const tbody = document.getElementById('weeklyTable');
            tbody.innerHTML = '';
            
            if (data.weeklyStats.length === 0) {
              tbody.innerHTML = '<tr><td colspan="4" class="text-center">No data available for the selected period</td></tr>';
              return;
            }

            data.weeklyStats.forEach(stat => {
              const tr = document.createElement('tr');
              tr.innerHTML = `
                <td>${stat.date}</td>
                <td>${stat.unique_users}</td>
                <td>${stat.total_transactions}</td>
                <td>${stat.total_units}</td>
              `;
              tbody.appendChild(tr);
            });

            // Update chart
            const ctx = document.getElementById('weeklyChart').getContext('2d');
            const dates = data.weeklyStats.map(item => item.date);
            const uniqueUsers = data.weeklyStats.map(item => item.unique_users);
            const totalTransactions = data.weeklyStats.map(item => item.total_transactions);
            const totalUnits = data.weeklyStats.map(item => item.total_units);

            new Chart(ctx, {
              type: 'line',
              data: {
                labels: dates,
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
                    text: 'Weekly Emergency Credit Trends'
                  }
                },
                scales: {
                  y: {
                    beginAtZero: true
                  }
                }
              }
            });
          })
          .catch(error => {
            console.error('Error fetching data:', error);
            document.getElementById('weeklyTable').innerHTML = '<tr><td colspan="4" class="text-center text-danger">Error loading data</td></tr>';
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
    });
    </script>
  </body>
</html>