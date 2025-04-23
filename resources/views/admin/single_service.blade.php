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
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
  </head>
  <!-- [Head] end -->
  <!-- [Body] Start -->

  <body @@bodySetup>
    @include('layouts.layout_vertical')

    <!-- [ Main Content ] start -->
    <div class="pc-container">
      <div class="pc-content">
        <!-- Breadcrumb -->
        <div class="page-header">
          <div class="page-block">
            <div class="row align-items-center">
              <div class="col-md-12">
                <div class="page-header-title">
                  <h5 class="m-b-10">Subscription Analytics</h5>
                </div>
                <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                  <li class="breadcrumb-item">SDF Reports</li>
                  <li class="breadcrumb-item">Subscription Analytics</li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <!-- Filter Section -->
        <div class="card shadow-sm mb-4">
          <div class="card-body">
            <h5 class="text-primary mb-3"><i class="bi bi-funnel"></i> Analytics Filters</h5>
            <div class="row g-3">
              <div class="col-md-2">
                <label class="form-label">Date Range</label>
                <div class="input-group">
                  <input type="date" id="startDate" class="form-control">
                  <span class="input-group-text">to</span>
                  <input type="date" id="endDate" class="form-control">
                </div>
              </div>
              <div class="col-md-2">
                <label class="form-label">Subscription Offer</label>
                <select id="offerSelect" class="form-select">
                  <option value="all">All Offers</option>
                  @foreach($offers as $offer)
                    <option value="{{ $offer->name }}">{{ $offer->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-2">
                <label class="form-label">Status</label>
                <select id="statusSelect" class="form-select">
                  <option value="all">All Status</option>
                  <option value="ACTIVE">ACTIVE</option>
                  <option value="CANCELED">CANCELED</option>
                </select>
              </div>
              <div class="col-md-2">
                <label class="form-label">Time Period</label>
                <select id="timePeriod" class="form-select">
                  <option value="daily">Daily</option>
                  <option value="weekly">Weekly</option>
                  <option value="monthly">Monthly</option>
                </select>
              </div>
              <div class="col-md-2">
                <label class="form-label">Compare With</label>
                <select id="comparePeriod" class="form-select">
                  <option value="none">No Comparison</option>
                  <option value="previous">Previous Period</option>
                  <option value="last_year">Same Period Last Year</option>
                </select>
              </div>
              <div class="col-md-2 d-flex align-items-end">
                <button id="applyFilters" class="btn btn-primary w-100">
                  <i class="bi bi-funnel"></i> Apply Filters
                </button>
              </div>
            </div>
          </div>
        </div>

     

        <!-- Key Metrics -->
        <div class="row mb-4">
          <div class="col-md-4">
            <div class="card shadow-sm">
              <div class="card-body">
                <h6 class="text-muted">Total Subscriptions</h6>
                <h3 id="totalSubscriptions">0</h3>
                <div class="d-flex align-items-center">
                  <span id="subscriptionChange" class="text-success">
                    <i class="bi bi-arrow-up"></i> 0%
                  </span>
                  <span class="text-muted ms-2">vs previous period</span>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card shadow-sm">
              <div class="card-body">
                <h6 class="text-muted">Active Subscriptions</h6>
                <h3 id="activeSubscriptions">0</h3>
                <div class="d-flex align-items-center">
                  <span id="activeChange" class="text-success">
                    <i class="bi bi-arrow-up"></i> 0%
                  </span>
                  <span class="text-muted ms-2">vs previous period</span>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card shadow-sm">
              <div class="card-body">
                <h6 class="text-muted">Churn Rate</h6>
                <h3 id="churnRate">0%</h3>
                <div class="d-flex align-items-center">
                  <span id="churnChange" class="text-danger">
                    <i class="bi bi-arrow-up"></i> 0%
                  </span>
                  <span class="text-muted ms-2">vs previous period</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Charts Section -->
        <div class="row mb-4">
          <div class="col-md-6">
            <div class="card shadow-sm">
              <div class="card-body">
                <h5 class="card-title">Subscription Status Distribution</h5>
                <canvas id="statusPieChart"></canvas>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card shadow-sm">
              <div class="card-body">
                <h5 class="card-title">Subscription Trends</h5>
                <canvas id="subscriptionTrendChart"></canvas>
              </div>
            </div>
          </div>
        </div>

        <!-- Detailed Analysis -->
        <div class="row mb-4">
          <div class="col-md-12">
            <div class="card shadow-sm">
              <div class="card-body">
                <h5 class="card-title">Detailed Analysis</h5>
                <div class="text-muted mb-3">
                  <small><i class="bi bi-info-circle"></i> Monthly subscription changes (new and canceled) from subs_in_out_count table</small>
                </div>
                <div class="table-responsive">
                  <table class="table table-hover" id="detailedTable">
                    <thead>
                      <tr>
                        <th>Date</th>
                        <th>Offer</th>
                        <th>Status</th>
                        <th>Subscribers</th>
                        <th>Change</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody id="dataTable">
                      <!-- Dynamic Data Here -->
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
        </div>
      </div>
    </div>

    @include('layouts.footer')
    @include('layouts.footer_js')

    <script>
      document.addEventListener("DOMContentLoaded", function() {
        // Initialize charts
        const statusPieChart = new Chart(document.getElementById("statusPieChart"), {
          type: 'pie',
          data: {
            labels: ['New', 'Canceled'],
            datasets: [{
              data: [0, 0],
              backgroundColor: ['#28a745', '#dc3545']
            }]
          },
          options: {
            responsive: true,
            plugins: {
              legend: {
                position: 'right'
              },
              datalabels: {
                formatter: (value) => {
                  return value + '%';
                }
              }
            }
          }
        });

        const subscriptionTrendChart = new Chart(document.getElementById("subscriptionTrendChart"), {
          type: 'line',
          data: {
            labels: [],
            datasets: [{
              label: 'Current Period',
              data: [],
              borderColor: '#007bff',
              fill: false
            }, {
              label: 'Previous Period',
              data: [],
              borderColor: '#6c757d',
              borderDash: [5, 5],
              fill: false
            }]
          },
          options: {
            responsive: true,
            plugins: {
              legend: {
                position: 'bottom'
              }
            },
            scales: {
              y: {
                beginAtZero: true
              }
            }
          }
        });

        // Handle filter application
        document.getElementById("applyFilters").addEventListener("click", function() {
          const startDate = document.getElementById("startDate").value;
          const endDate = document.getElementById("endDate").value;
          
          // Validate dates
          if (!startDate) {
            alert('Please select a start date');
            return;
          }
          
          if (!endDate) {
            alert('Please select an end date');
            return;
          }
          
          console.log('Raw dates:', { startDate, endDate });
          
          const filters = {
            start_date: formatDateForAPI(startDate),
            end_date: formatDateForAPI(endDate),
            service_name: document.getElementById("offerSelect").value,
            status: document.getElementById("statusSelect").value,
            time_period: document.getElementById("timePeriod").value,
            compare_period: document.getElementById("comparePeriod").value
          };

          console.log('Formatted filters:', filters);

          // Show loading state
          document.getElementById("dataTable").innerHTML = '<tr><td colspan="6" class="text-center">Loading...</td></tr>';

          // Fetch data from API
          fetch(`http://127.0.0.1:8000/api/v1/service-report?${new URLSearchParams(filters)}`, {
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json'
            },
            credentials: 'include'
          })
            .then(response => {
              console.log('Response status:', response.status);
              if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
              }
              return response.json();
            })
            .then(data => {
              console.log('Received data:', data);
              
              if (data.error) {
                throw new Error(data.message || data.error);
              }

              // Ensure data is an array
              const reportData = data.table_data || [];
              const pagination = data.pagination || {};
              
              if (!reportData || reportData.length === 0) {
                document.getElementById("dataTable").innerHTML = '<tr><td colspan="6" class="text-center">No data available for the selected filters</td></tr>';
                return;
              }

              // Get the selected status
              const selectedStatus = document.getElementById("statusSelect").value;

              // Calculate metrics based on the table data
              let totalSubscribers = 0;
              let activeSubscribers = 0;
              let canceledSubscribers = 0;
              const dates = [];
              const subscriberCounts = [];

              reportData.forEach(row => {
                const subscribers = parseInt(row.subscribers) || 0;
                totalSubscribers += subscribers;
                
                if (row.status === 'ACTIVE') {
                  activeSubscribers += subscribers;
                } else if (row.status === 'CANCELED') {
                  canceledSubscribers += subscribers;
                }

                // For trend chart
                dates.push(row.start_date);
                subscriberCounts.push(subscribers);
              });

              // Update metrics
              const metrics = {
                total: totalSubscribers,
                active: activeSubscribers,
                churn_rate: totalSubscribers > 0 ? ((canceledSubscribers / totalSubscribers) * 100).toFixed(2) : 0,
                total_change: 0,
                active_change: 0,
                churn_change: 0
              };
              
              updateMetrics(metrics);

              // Update charts
              if (selectedStatus === 'ACTIVE') {
                // For ACTIVE status, show only active subscribers in pie chart
                statusPieChart.data.datasets[0].data = [activeSubscribers, 0];
                statusPieChart.data.labels = ['Active', 'Canceled'];
              } else {
                // For other statuses, show both active and canceled
                statusPieChart.data.datasets[0].data = [activeSubscribers, canceledSubscribers];
                statusPieChart.data.labels = ['Active', 'Canceled'];
              }
              statusPieChart.update();

              // Update trend chart
              subscriptionTrendChart.data.labels = dates;
              subscriptionTrendChart.data.datasets[0].data = subscriberCounts;
              subscriptionTrendChart.update();

              // Update table
              updateTable(reportData);

              // Update pagination info
              updatePaginationInfo(pagination);
            })
            .catch(error => {
              console.error('Error details:', error);
              document.getElementById("dataTable").innerHTML = `
                <tr>
                  <td colspan="6" class="text-center text-danger">
                    Error loading data: ${error.message}
                  </td>
                </tr>
              `;
            });
        });

        // Add date format conversion
        function formatDateForAPI(dateString) {
          if (!dateString) return '';
          
          // Handle both YYYY-MM-DD and DD/MM/YYYY formats
          if (dateString.includes('/')) {
            // Input is in DD/MM/YYYY format
            const [day, month, year] = dateString.split('/');
            return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
          } else {
            // Input is already in YYYY-MM-DD format
            return dateString;
          }
        }

        function updateMetrics(metrics) {
          document.getElementById("totalSubscriptions").textContent = metrics.total;
          document.getElementById("activeSubscriptions").textContent = metrics.active;
          document.getElementById("churnRate").textContent = metrics.churn_rate + '%';

          // Update changes
          updateChangeIndicator("subscriptionChange", metrics.total_change);
          updateChangeIndicator("activeChange", metrics.active_change);
          updateChangeIndicator("churnChange", metrics.churn_change);
        }

        function updateChangeIndicator(elementId, change) {
          const element = document.getElementById(elementId);
          const icon = element.querySelector('i');
          element.className = change >= 0 ? 'text-success' : 'text-danger';
          icon.className = change >= 0 ? 'bi bi-arrow-up' : 'bi bi-arrow-down';
          element.innerHTML = `${icon.outerHTML} ${Math.abs(change)}%`;
        }

        function updateTable(data) {
          const tbody = document.getElementById("dataTable");
          tbody.innerHTML = '';

          if (!data || data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center">No data available</td></tr>';
            return;
          }

          data.forEach(row => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
              <td>${row.start_date || 'N/A'}</td>
              <td>${row.offer || 'N/A'}</td>
              <td><span class="badge bg-${getStatusColor(row.status)}">${row.status || 'N/A'}</span></td>
              <td>${row.subscribers || 0}</td>
              <td>${formatChange(row.change || 0)}</td>
              <td>
                <button class="btn btn-sm btn-info" onclick="viewDetails(${row.id || 0})">
                  <i class="bi bi-eye"></i>
                </button>
              </td>
            `;
            tbody.appendChild(tr);
          });
        }

        function getStatusColor(status) {
          const colors = {
            'NEW': 'success',
            'CANCELED': 'danger'
          };
          return colors[status] || 'secondary';
        }

        function formatChange(change) {
          const color = change >= 0 ? 'success' : 'danger';
          const icon = change >= 0 ? 'arrow-up' : 'arrow-down';
          return `<span class="text-${color}">
            <i class="bi bi-${icon}"></i> ${Math.abs(change)}%
          </span>`;
        }

        // Add pagination info update function
        function updatePaginationInfo(pagination) {
          const from = pagination.from || 0;
          const to = pagination.to || 0;
          const total = pagination.total || 0;
          
          document.getElementById('paginationInfo').textContent = `Showing ${from} to ${to} of ${total} entries`;
          
          // Update pagination buttons
          const prevBtn = document.getElementById('prevPage');
          const nextBtn = document.getElementById('nextPage');
          
          if (prevBtn) {
            prevBtn.disabled = !pagination.prev_page_url;
            prevBtn.onclick = () => {
              if (pagination.prev_page_url) {
                loadPage(pagination.prev_page_url);
              }
            };
          }
          
          if (nextBtn) {
            nextBtn.disabled = !pagination.next_page_url;
            nextBtn.onclick = () => {
              if (pagination.next_page_url) {
                loadPage(pagination.next_page_url);
              }
            };
          }
        }

        // Add page loading function
        function loadPage(url) {
          fetch(url, {
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json'
            },
            credentials: 'include'
          })
          .then(response => response.json())
          .then(data => {
            // Update table with new data
            updateTable(data.table_data || []);
            updatePaginationInfo(data.pagination || {});
          })
          .catch(error => {
            console.error('Error loading page:', error);
          });
        }
      });
    </script>
  </body>
</html>