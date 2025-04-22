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
                  <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                  <li class="breadcrumb-item">Emergency Credit</li>
                  <li class="breadcrumb-item">Top Users</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <!-- [ breadcrumb ] end -->

        <!-- [ Main Content ] start -->
        <div class="row">
          <!-- [ Top Users Table ] start -->
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h5>Top Users</h5>
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
      // Fetch data
      fetch('/emergency-credit/top-users/data')
        .then(response => response.json())
        .then(data => {
          const tbody = document.getElementById('topUsersTable');
          tbody.innerHTML = '';
          
          if (data.topUsers.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center">No users found</td></tr>';
            return;
          }

          data.topUsers.forEach((user, index) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
              <td>${index + 1}</td>
              <td>${user.msisdn}</td>
              <td>${user.txn_count}</td>
              <td>${user.total_amount}</td>
              <td>${user.last_transaction}</td>
            `;
            tbody.appendChild(tr);
          });
        })
        .catch(error => {
          console.error('Error fetching data:', error);
          document.getElementById('topUsersTable').innerHTML = '<tr><td colspan="5" class="text-center text-danger">Error loading data</td></tr>';
        });
    });
    </script>
  </body>
</html>