<!doctype html>
<html lang="en">
  <!-- [Head] start -->
  <head>
    @include('layouts.heads_page') 
    @include('layouts.heads_css')
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
                  <h5 class="m-b-10">Daily Emergency Credit Report</h5>
                </div>
                <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.simple') }}">Home</a></li>
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
                      <option value="DECLINED">Declined</option>
                      <option value="REPAID">Repaid</option>
                      <option value="CREDIT">Credit</option>
                      <option value="NEW">New</option>
                      <option value="CREDIT_FAILED">Credit Failed</option>
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
          <!-- [ Stats Cards ] start -->
          <div class="col-md-3">
            <div class="card">
              <div class="card-body">
                <h6 class="text-muted">Unique Users</h6>
                <div class="d-flex align-items-center">
                  <h3 id="uniqueUsers">-</h3>
                  <div id="uniqueUsersLoading" class="spinner-border spinner-border-sm text-primary ms-2" style="display: none;">
                    <span class="visually-hidden">Loading...</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card">
              <div class="card-body">
                <h6 class="text-muted">Total Transactions</h6>
                <div class="d-flex align-items-center">
                  <h3 id="totalTransactions">-</h3>
                  <div id="totalTransactionsLoading" class="spinner-border spinner-border-sm text-primary ms-2" style="display: none;">
                    <span class="visually-hidden">Loading...</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card">
              <div class="card-body">
                <h6 class="text-muted">Total Units</h6>
                <div class="d-flex align-items-center">
                  <h3 id="totalUnits">-</h3>
                  <div id="totalUnitsLoading" class="spinner-border spinner-border-sm text-primary ms-2" style="display: none;">
                    <span class="visually-hidden">Loading...</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card">
              <div class="card-body">
                <h6 class="text-muted">Average Units</h6>
                <div class="d-flex align-items-center">
                  <h3 id="avgUnits">-</h3>
                  <div id="avgUnitsLoading" class="spinner-border spinner-border-sm text-primary ms-2" style="display: none;">
                    <span class="visually-hidden">Loading...</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- [ Stats Cards ] end -->

          <!-- [ Top Users Table ] start -->
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h5>Top Users</h5>
                <button type="button" class="btn btn-success float-end" id="exportBtn">
                  <i class="bi bi-file-excel"></i> Export to Excel
                </button>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>Rank</th>
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
          <!-- [ Top Users Table ] end -->
        </div>
        <!-- [ Main Content ] end -->
      </div>
    </div>

    @include('layouts.footer') 
    @include('layouts.footer_js')

    <script>
    document.addEventListener('DOMContentLoaded', function() {
      const filterForm = document.getElementById('filterForm');
      
      function showLoading() {
        document.getElementById('uniqueUsersLoading').style.display = 'inline-block';
        document.getElementById('totalTransactionsLoading').style.display = 'inline-block';
        document.getElementById('totalUnitsLoading').style.display = 'inline-block';
        document.getElementById('avgUnitsLoading').style.display = 'inline-block';
        document.getElementById('topUsersTable').innerHTML = `
          <tr>
            <td colspan="3" class="text-center">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
            </td>
          </tr>
        `;
      }

      function hideLoading() {
        document.getElementById('uniqueUsersLoading').style.display = 'none';
        document.getElementById('totalTransactionsLoading').style.display = 'none';
        document.getElementById('totalUnitsLoading').style.display = 'none';
        document.getElementById('avgUnitsLoading').style.display = 'none';
      }
      
      function exportTableToExcel() {
        const table = document.querySelector('.table');
        const wb = XLSX.utils.table_to_book(table, {sheet: "Daily Top Users"});
        const fileName = `emergency_credit_daily_${new Date().toISOString().split('T')[0]}.xlsx`;
        XLSX.writeFile(wb, fileName);
      }

      function fetchData(params = {}) {
        showLoading();
        
        console.log('Request parameters:', params);
        
        const url = new URL(`${window.location.origin}/emergency-credit/daily/data`);
        Object.keys(params).forEach(key => url.searchParams.append(key, params[key]));
        
        console.log('Request URL:', url.toString());
        
        fetch(url, {
          method: 'GET',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          credentials: 'same-origin'
        })
          .then(response => {
            if (!response.ok) {
              throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
          })
          .then(data => {
            console.log('Response data:', data);
            
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
          })
          .finally(() => {
            hideLoading();
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
          const wb = XLSX.utils.table_to_book(table, {sheet: "Daily Statistics"});
          const fileName = `emergency_credit_daily_${new Date().toISOString().split('T')[0]}.xlsx`;
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