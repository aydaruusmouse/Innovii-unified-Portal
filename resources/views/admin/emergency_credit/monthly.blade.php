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
                  <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
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
                    <input type="month" class="form-control" id="start_month" name="start_month" value="{{ date('Y-m', strtotime('-12 months')) }}">
                  </div>
                  <div class="col-md-3">
                    <label for="end_month" class="form-label">End Month</label>
                    <input type="month" class="form-control" id="end_month" name="end_month" value="{{ date('Y-m') }}">
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
          <!-- [ Monthly Chart ] start -->
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h5>Monthly Transaction Trends</h5>
              </div>
              <div class="card-body">
                <canvas id="monthlyChart" height="300"></canvas>
              </div>
            </div>
          </div>
          <!-- [ Monthly Chart ] end -->

          <!-- [ Monthly Stats ] start -->
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h5>Monthly Statistics</h5>
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
                        <th>Success Rate</th>
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
      
      function fetchData(params = {}) {
        const url = new URL('http://127.0.0.1:8000/emergency-credit/monthly/data');
        Object.keys(params).forEach(key => url.searchParams.append(key, params[key]));
        
        fetch(url)
          .then(response => response.json())
          .then(data => {
            // Update chart data
            updateChart(data.monthlyStats);
          })
          .catch(error => {
            console.error('Error fetching data:', error);
            document.getElementById('chartContainer').innerHTML = '<div class="alert alert-danger">Error loading data</div>';
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