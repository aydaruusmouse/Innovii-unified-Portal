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
                  <h5 class="m-b-10">Top Users Emergency Credit</h5>
                </div>
                <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.simple') }}">Home</a></li>
                  <li class="breadcrumb-item">Emergency Credit</li>
                  <li class="breadcrumb-item">Top Users</li>
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
          <!-- [ Top Users Table ] start -->
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h5>Top Users Statistics</h5>
                <button type="button" class="btn btn-success float-end" id="exportBtn">
                  <i class="bi bi-file-excel"></i> Export to Excel
                </button>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>MSISDN</th>
                        <th>Transaction Count</th>
                        <th>Total Amount</th>
                        <th>Last Transaction</th>
                      </tr>
                    </thead>
                    <tbody id="topUsersTable">
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
                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                  <div class="text-muted" id="paginationInfo">
                    Showing 0 to 0 of 0 entries
                  </div>
                  <div class="btn-group">
                    <button type="button" class="btn btn-outline-secondary" id="prevPage" disabled>
                      <i class="bi bi-chevron-left"></i> Previous
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="nextPage" disabled>
                      Next <i class="bi bi-chevron-right"></i>
                    </button>
                  </div>
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
      let currentPage = 1;
      
      function showLoading() {
        document.getElementById('topUsersTable').innerHTML = `
          <tr>
            <td colspan="5" class="text-center">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
            </td>
          </tr>
        `;
      }

      function updatePaginationInfo(pagination) {
        const from = pagination.from || 0;
        const to = pagination.to || 0;
        const total = pagination.total || 0;
        const currentPage = pagination.current_page || 1;
        const lastPage = pagination.last_page || 1;
        
        document.getElementById('paginationInfo').textContent = `Showing ${from} to ${to} of ${total} entries`;
        
        // Update pagination buttons
        const prevBtn = document.getElementById('prevPage');
        const nextBtn = document.getElementById('nextPage');
        
        if (prevBtn) {
          prevBtn.disabled = currentPage <= 1;
          prevBtn.onclick = () => {
            if (currentPage > 1) {
              loadPage(currentPage - 1);
            }
          };
        }
        
        if (nextBtn) {
          nextBtn.disabled = currentPage >= lastPage;
          nextBtn.onclick = () => {
            if (currentPage < lastPage) {
              loadPage(currentPage + 1);
            }
          };
        }
      }

      function loadPage(page) {
        const formData = new FormData(filterForm);
        const params = {};
        formData.forEach((value, key) => {
          if (value) params[key] = value;
        });
        params.page = page;
        fetchData(params);
      }
      
      function exportTableToExcel() {
        const table = document.querySelector('.table');
        const wb = XLSX.utils.table_to_book(table, {sheet: "Top Users"});
        const fileName = `emergency_credit_top_users_${new Date().toISOString().split('T')[0]}.xlsx`;
        XLSX.writeFile(wb, fileName);
      }

      function fetchData(params = {}) {
        showLoading();
        
        const url = new URL('${window.location.origin}/emergency-credit/top-users/data');
        Object.keys(params).forEach(key => url.searchParams.append(key, params[key]));
        
        fetch(url)
          .then(response => response.json())
          .then(data => {
            const tbody = document.getElementById('topUsersTable');
            tbody.innerHTML = '';
            
            if (!data.data || data.data.length === 0) {
              tbody.innerHTML = '<tr><td colspan="5" class="text-center">No users found</td></tr>';
              return;
            }

            data.data.forEach((user, index) => {
              const tr = document.createElement('tr');
              tr.innerHTML = `
                <td>${index + 1}</td>
                <td>${user.msisdn}</td>
                <td>${user.txn_count}</td>
                <td>${user.total_amount}</td>
                <td>${new Date(user.last_transaction).toLocaleString()}</td>
              `;
              tbody.appendChild(tr);
            });

            // Update pagination info
            updatePaginationInfo(data);
          })
          .catch(error => {
            console.error('Error fetching data:', error);
            document.getElementById('topUsersTable').innerHTML = '<tr><td colspan="5" class="text-center text-danger">Error loading data</td></tr>';
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
          const wb = XLSX.utils.table_to_book(table, {sheet: "Top Users Statistics"});
          const fileName = `emergency_credit_top_users_${new Date().toISOString().split('T')[0]}.xlsx`;
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