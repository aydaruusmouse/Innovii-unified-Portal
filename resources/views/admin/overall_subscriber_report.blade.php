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

    <!-- [ Main Content ] start -->
    <div class="pc-container">
      
      <div class="pc-content">
        
        <div class="row">
          
          <div class="col-12">
            
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Overall Subscriber Report</h4>
                
              </div>
              <div class="card-body">
                <!-- Filters -->
                <div class="row mb-4">
                  <div class="col-md-3">
                    <label for="startDate" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="startDate" value="2025-03-16">
                  </div>
                  <div class="col-md-3">
                    <label for="endDate" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="endDate" value="2025-03-20">
                  </div>
                  <div class="col-md-3">
                    <label for="serviceFilter" class="form-label">Service</label>
                    <select class="form-select" id="serviceFilter">
                      <option value="all">All Services</option>
                      @foreach($services as $service)
                        <option value="{{ $service->name }}">{{ $service->name }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <button class="btn btn-primary w-100" id="applyFilter">
                      <i class="bi bi-funnel"></i> Apply Filters
                    </button>
                  </div>
                </div>

                <!-- Summary Cards -->
                <div class="row mb-4">
                  <div class="col-md-4">
                    <div class="card border-success">
                      <div class="card-body">
                        <h5 class="card-title text-success">Total Active Subscribers</h5>
                        <h2 class="mb-0" id="totalActive">0</h2>
                        <small class="text-muted" id="activeDate"></small>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="card border-danger">
                      <div class="card-body">
                        <h5 class="card-title text-danger">Total Failed Subscribers</h5>
                        <h2 class="mb-0" id="totalFailed">0</h2>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="card border-warning">
                      <div class="card-body">
                        <h5 class="card-title text-warning">Total Canceled Subscribers</h5>
                        <h2 class="mb-0" id="totalCanceled">0</h2>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Charts -->
                <div class="row mb-4">
                  <div class="col-md-6">
                    <div class="card">
                      <div class="card-body">
                        <h5 class="card-title">Status Distribution</h5>
                        <canvas id="statusChart"></canvas>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="card">
                      <div class="card-body">
                        <h5 class="card-title">Status Trends</h5>
                        <canvas id="trendChart"></canvas>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Data Table -->
                <div class="card">
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-striped" id="subscriberTable">
                        <thead>
                          <tr>
                            <th>Date</th>
                            <th>Service Name</th>
                            <th>Status</th>
                            <th>Subscriber Count</th>
                          </tr>
                        </thead>
                        <tbody>
                          <!-- Data will be populated dynamically -->
                        </tbody>
                      </table>
                    </div>
                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                      <div>
                        <select class="form-select" id="perPage">
                          <option value="10">10 per page</option>
                          <option value="25">25 per page</option>
                          <option value="50">50 per page</option>
                          <option value="100">100 per page</option>
                        </select>
                      </div>
                      <nav aria-label="Page navigation">
                        <ul class="pagination mb-0" id="pagination">
                          <!-- Pagination will be populated dynamically -->
                        </ul>
                      </nav>
                    </div>
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
    document.addEventListener('DOMContentLoaded', function() {
        let currentPage = 1;
        let perPage = 10;

        // Initialize charts
        const statusChart = new Chart(document.getElementById('statusChart'), {
            type: 'pie',
            data: {
                labels: ['Active', 'Failed', 'Canceled'],
                datasets: [{
                    data: [0, 0, 0],
                    backgroundColor: ['#198754', '#dc3545', '#ffc107']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });

        const trendChart = new Chart(document.getElementById('trendChart'), {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Active',
                    data: [],
                    borderColor: '#198754',
                    backgroundColor: '#198754',
                    borderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointStyle: 'circle',
                    pointBackgroundColor: '#198754',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    fill: false,
                    tension: 0.1
                }, {
                    label: 'Failed',
                    data: [],
                    borderColor: '#dc3545',
                    backgroundColor: '#dc3545',
                    borderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointStyle: 'circle',
                    pointBackgroundColor: '#dc3545',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    fill: false,
                    tension: 0.1
                }, {
                    label: 'Canceled',
                    data: [],
                    borderColor: '#ffc107',
                    backgroundColor: '#ffc107',
                    borderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointStyle: 'circle',
                    pointBackgroundColor: '#ffc107',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    fill: false,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    title: {
                        display: true,
                        text: 'Status Trends Over Time'
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#fff',
                        borderWidth: 1,
                        padding: 10,
                        displayColors: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Subscribers'
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }
            }
        });

        // Function to fetch and update data
        function fetchData(page = 1) {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            const service = document.getElementById('serviceFilter').value;

            console.log('Fetching data with params:', {
                startDate,
                endDate,
                service,
                page,
                perPage
            });

            fetch(`/api/v1/overall-subscriber-report?start_date=${startDate}&end_date=${endDate}&service_name=${service}&page=${page}&per_page=${perPage}`)
                .then(response => response.json())
                .then(data => {
                    console.log('Received data:', data);
                    
                    if (data.error) {
                        console.error('Error:', data.message);
                        return;
                    }

                    // Update summary cards
                    document.getElementById('totalActive').textContent = data.totals.active.toLocaleString();
                    document.getElementById('totalFailed').textContent = data.totals.failed.toLocaleString();
                    document.getElementById('totalCanceled').textContent = data.totals.canceled.toLocaleString();
                    
                    // Update active date
                    if (data.latest_active_date) {
                        document.getElementById('activeDate').textContent = `As of ${data.latest_active_date}`;
                    } else {
                        document.getElementById('activeDate').textContent = 'No active data available';
                    }

                    // Update status chart
                    statusChart.data.datasets[0].data = [
                        data.totals.active,
                        data.totals.failed,
                        data.totals.canceled
                    ];
                    statusChart.update();

                    // Update trend chart
                    const dates = data.dates.sort();
                    const activeData = dates.map(date => data.active_data[date] || 0);
                    const failedData = dates.map(date => data.failed_data[date] || 0);
                    const canceledData = dates.map(date => data.canceled_data[date] || 0);

                    trendChart.data.labels = dates;
                    trendChart.data.datasets[0].data = activeData;
                    trendChart.data.datasets[1].data = failedData;
                    trendChart.data.datasets[2].data = canceledData;
                    trendChart.update();

                    // Update table
                    const tbody = document.querySelector('#subscriberTable tbody');
                    tbody.innerHTML = '';
                    data.table_data.forEach(row => {
                        tbody.innerHTML += `
                            <tr>
                                <td>${row.date}</td>
                                <td>${row.name}</td>
                                <td>${row.status}</td>
                                <td>${row.base_count.toLocaleString()}</td>
                            </tr>
                        `;
                    });

                    // Update pagination
                    updatePagination(data.pagination);
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                });
        }

        // Function to update pagination
        function updatePagination(pagination) {
            const paginationElement = document.getElementById('pagination');
            paginationElement.innerHTML = '';

            // Previous button
            paginationElement.innerHTML += `
                <li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${pagination.current_page - 1}">Previous</a>
                </li>
            `;

            // Page numbers
            for (let i = 1; i <= pagination.last_page; i++) {
                paginationElement.innerHTML += `
                    <li class="page-item ${pagination.current_page === i ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>
                `;
            }

            // Next button
            paginationElement.innerHTML += `
                <li class="page-item ${pagination.current_page === pagination.last_page ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${pagination.current_page + 1}">Next</a>
                </li>
            `;

            // Add click event listeners to pagination links
            document.querySelectorAll('.page-link').forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const page = parseInt(e.target.dataset.page);
                    if (page && page !== currentPage) {
                        currentPage = page;
                        fetchData(page);
                    }
                });
            });
        }

        // Add event listeners
        document.getElementById('applyFilter').addEventListener('click', () => {
            currentPage = 1;
            fetchData();
        });

        document.getElementById('perPage').addEventListener('change', (e) => {
            perPage = parseInt(e.target.value);
            currentPage = 1;
            fetchData();
        });

        // Initial data fetch
        fetchData();
    });
    </script>
  </body>
</html> 