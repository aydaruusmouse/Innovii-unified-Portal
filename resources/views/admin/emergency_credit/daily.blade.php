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
                  <h5 class="m-b-10">Daily Emergency Credit Report</h5>
                </div>
                <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                  <li class="breadcrumb-item">Emergency Credit</li>
                  <li class="breadcrumb-item">Daily Report</li>
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
                    <label for="date" class="form-label">Date</label>
                    <input type="date" class="form-control" id="date" name="date" value="{{ date('Y-m-d') }}">
                  </div>
                  <div class="col-md-4">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                      <option value="">All Status</option>
                      <option value="SUCCESS">Success</option>
                      <option value="FAILED">Failed</option>
                      <option value="PENDING">Pending</option>
                    </select>
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
          <!-- [ Daily Stats ] start -->
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h5>Today's Statistics</h5>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-3">
                    <div class="card bg-primary text-white">
                      <div class="card-body">
                        <h6 class="text-white">Unique Users</h6>
                        <h3 class="text-white" id="uniqueUsers">
                          <div class="spinner-border spinner-border-sm" role="status">
                            <span class="visually-hidden">Loading...</span>
                          </div>
                        </h3>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="card bg-success text-white">
                      <div class="card-body">
                        <h6 class="text-white">Total Transactions</h6>
                        <h3 class="text-white" id="totalTransactions">
                          <div class="spinner-border spinner-border-sm" role="status">
                            <span class="visually-hidden">Loading...</span>
                          </div>
                        </h3>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="card bg-info text-white">
                      <div class="card-body">
                        <h6 class="text-white">Total Units</h6>
                        <h3 class="text-white" id="totalUnits">
                          <div class="spinner-border spinner-border-sm" role="status">
                            <span class="visually-hidden">Loading...</span>
                          </div>
                        </h3>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="card bg-warning text-white">
                      <div class="card-body">
                        <h6 class="text-white">Average Units</h6>
                        <h3 class="text-white" id="avgUnits">
                          <div class="spinner-border spinner-border-sm" role="status">
                            <span class="visually-hidden">Loading...</span>
                          </div>
                        </h3>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- [ Daily Stats ] end -->

          <!-- [ Top Users ] start -->
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h5>Top Users Today</h5>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>MSISDN</th>
                        <th>Transaction Count</th>
                      </tr>
                    </thead>
                    <tbody id="topUsersTable">
                      <tr>
                        <td colspan="3" class="text-center">
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
          <!-- [ Top Users ] end -->
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
        const url = new URL('http://127.0.0.1:8000/emergency-credit/daily/data');
        Object.keys(params).forEach(key => url.searchParams.append(key, params[key]));
        
        fetch(url)
          .then(response => response.json())
          .then(data => {
            // Update stats
            document.getElementById('uniqueUsers').textContent = data.dailyStats.unique_users || 0;
            document.getElementById('totalTransactions').textContent = data.dailyStats.total_transactions || 0;
            document.getElementById('totalUnits').textContent = data.dailyStats.total_units || 0;
            document.getElementById('avgUnits').textContent = data.dailyStats.avg_units ? Number(data.dailyStats.avg_units).toFixed(2) : '0.00';

            // Update top users table
            const tbody = document.getElementById('topUsersTable');
            tbody.innerHTML = '';
            
            if (!data.topUsers || data.topUsers.length === 0) {
              tbody.innerHTML = '<tr><td colspan="3" class="text-center">No transactions found</td></tr>';
              return;
            }

            data.topUsers.forEach((user, index) => {
              const tr = document.createElement('tr');
              tr.innerHTML = `
                <td>${index + 1}</td>
                <td>${user.msisdn}</td>
                <td>${user.txn_count}</td>
              `;
              tbody.appendChild(tr);
            });
          })
          .catch(error => {
            console.error('Error fetching data:', error);
            document.querySelectorAll('.card-body h3').forEach(el => {
              el.textContent = '-';
            });
            document.getElementById('topUsersTable').innerHTML = '<tr><td colspan="3" class="text-center text-danger">Error loading data</td></tr>';
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