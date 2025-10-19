<!doctype html>
<html lang="en">
  <head>
    @include('layouts.heads_page')
    @include('layouts.heads_css')
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  </head>
  <body>
    @include('layouts.layout_vertical')
    <div class="pc-container">
      <div class="pc-content">
        <div class="row">
          <div class="page-header mb-3">
            <div class="page-block">
              <div class="row align-items-center">
                <div class="col-md-12">
                  <div class="page-header-title">
                    <h5 class="mb-0">Emergency Credit Dashboard</h5>
                  </div>
                </div>
                <div class="col-md-12">
                  <ul class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.simple') }}">Home</a></li>
                    <li class="breadcrumb-item" aria-current="page">Emergency Credit</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
          
          <!-- [ Daily Active Subscribers ] start -->
          <div class="col-md-6 col-xl-4">
            <div class="card">
              <div class="card-body">
                <h6 class="mb-4">Daily Active Subscribers</h6>
                <div class="row d-flex align-items-center">
                  <div class="col-9">
                    <h3 class="f-w-300 d-flex align-items-center m-b-0">
                      <i class="feather icon-users text-success f-30 m-r-10"></i>
                      <span id="daily-active">0</span>
                    </h3>
                  </div>
                  <div class="col-3 text-end">
                    <p class="m-b-0" id="daily-change">0%</p>
                  </div>
                </div>
                <div class="progress m-t-30" style="height: 7px">
                  <div id="daily-progress" class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
                </div>
              </div>
            </div>
          </div>
          <!-- [ Daily Active Subscribers ] end -->

          <!-- [ Monthly Active Subscribers ] start -->
          <div class="col-md-6 col-xl-4">
            <div class="card">
              <div class="card-body">
                <h6 class="mb-4">Monthly Active Subscribers</h6>
                <div class="row d-flex align-items-center">
                  <div class="col-9">
                    <h3 class="f-w-300 d-flex align-items-center m-b-0">
                      <i class="feather icon-users text-primary f-30 m-r-10"></i>
                      <span id="monthly-active">0</span>
                    </h3>
                  </div>
                  <div class="col-3 text-end">
                    <p class="m-b-0" id="monthly-change">0%</p>
                  </div>
                </div>
                <div class="progress m-t-30" style="height: 7px">
                  <div id="monthly-progress" class="progress-bar bg-primary" role="progressbar" style="width: 0%"></div>
                </div>
              </div>
            </div>
          </div>
          <!-- [ Monthly Active Subscribers ] end -->

          <!-- [ Yearly Active Subscribers ] start -->
          <div class="col-md-12 col-xl-4">
            <div class="card">
              <div class="card-body">
                <h6 class="mb-4">Yearly Active Subscribers</h6>
                <div class="row d-flex align-items-center">
                  <div class="col-9">
                    <h3 class="f-w-300 d-flex align-items-center m-b-0">
                      <i class="feather icon-users text-info f-30 m-r-10"></i>
                      <span id="yearly-active">0</span>
                    </h3>
                  </div>
                  <div class="col-3 text-end">
                    <p class="m-b-0" id="yearly-change">0%</p>
                  </div>
                </div>
                <div class="progress m-t-30" style="height: 7px">
                  <div id="yearly-progress" class="progress-bar bg-info" role="progressbar" style="width: 0%"></div>
                </div>
              </div>
            </div>
          </div>
          <!-- [ Yearly Active Subscribers ] end -->

          <!-- [ Top Services Chart ] start -->
          <div class="col-xl-8 col-md-6">
            <div class="card">
              <div class="card-header">
                <h5>Top Emergency Credit Services by Subscribers</h5>
              </div>
              <div class="card-body">
                <div id="services-chart" style="height: 450px"></div>
              </div>
            </div>
          </div>
          <!-- [ Top Services Chart ] end -->

          <!-- [ Statistics Section ] start -->
          <div class="col-xl-4 col-md-6">
            <div class="card bg-primary">
              <div class="card-header border-0">
                <h5 class="text-white">Total Emergency Credit Services</h5>
              </div>
              <div class="card-body" style="padding: 0 25px">
                <div class="earning-text mb-0">
                  <h3 class="mb-2 text-white f-w-300">
                    <span id="total-services">0</span>
                    <i class="feather icon-grid text-white"></i>
                  </h3>
                  <span class="text-uppercase text-white d-block">Active Services</span>
                </div>
                <div id="trend-chart" class="WidgetlineChart2 ChartShadow" style="height: 180px"></div>
              </div>
            </div>
            <div class="card">
              <div class="card-body border-bottom">
                <div class="row d-flex align-items-center">
                  <div class="col-auto">
                    <i class="feather icon-trending-up f-30 text-success"></i>
                  </div>
                  <div class="col">
                    <h3 class="f-w-300" id="total-subscribers">0</h3>
                    <span class="d-block text-uppercase">Total Subscribers</span>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <div class="row d-flex align-items-center">
                  <div class="col-auto">
                    <i class="feather icon-activity f-30 text-primary"></i>
                  </div>
                  <div class="col">
                    <h3 class="f-w-300" id="active-subscribers">0</h3>
                    <span class="d-block text-uppercase">Active Subscribers</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- [ Statistics Section ] end -->

          <!-- [ Service Status Distribution ] start -->
          <div class="col-xl-4 col-md-6">
            <div class="card">
              <div class="card-header">
                <h5>Emergency Credit Status Distribution</h5>
              </div>
              <div class="card-body">
                <div id="status-pie-chart" style="height: 300px"></div>
              </div>
            </div>
          </div>
          <!-- [ Service Status Distribution ] end -->

          <!-- [ Recent Activity ] start -->
          <div class="col-xl-8 col-md-6">
            <div class="card">
              <div class="card-header">
                <h5>Recent Emergency Credit Activity</h5>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>Date</th>
                        <th>Service</th>
                        <th>Status</th>
                        <th>Subscribers</th>
                      </tr>
                    </thead>
                    <tbody id="recent-activity">
                      <!-- Will be populated by JavaScript -->
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <!-- [ Recent Activity ] end -->
        </div>
      </div>
    </div>

    <!-- [Page Specific JS] start -->
    <script src="{{ asset('admin/assets/js/plugins/apexcharts.min.js') }}"></script>
    <script src="{{ asset('admin/assets/js/plugins/jsvectormap.min.js') }}"></script>
    <script src="{{ asset('admin/assets/js/plugins/world.js') }}"></script>
    @include('layouts.footer_js')
   
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fetch emergency credit dashboard data
            fetch('/api/v1/emergency-credit-stats')
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error('Error fetching emergency credit stats:', data.message);
                        return;
                    }

                    // Update daily, monthly, and yearly stats
                    document.getElementById('daily-active').textContent = data.daily_active.toLocaleString();
                    document.getElementById('monthly-active').textContent = data.monthly_active.toLocaleString();
                    document.getElementById('yearly-active').textContent = data.yearly_active.toLocaleString();
                    document.getElementById('total-services').textContent = data.total_services;
                    document.getElementById('total-subscribers').textContent = data.yearly_active.toLocaleString();
                    document.getElementById('active-subscribers').textContent = data.daily_active.toLocaleString();

                    // Calculate and update progress bars
                    const maxDaily = Math.max(data.daily_active, data.monthly_active / 30);
                    const maxMonthly = Math.max(data.monthly_active, data.yearly_active / 12);
                    const maxYearly = data.yearly_active;

                    document.getElementById('daily-progress').style.width = `${(data.daily_active / maxDaily) * 100}%`;
                    document.getElementById('monthly-progress').style.width = `${(data.monthly_active / maxMonthly) * 100}%`;
                    document.getElementById('yearly-progress').style.width = `${(data.yearly_active / maxYearly) * 100}%`;

                    // Update percentage changes
                    document.getElementById('daily-change').textContent = `${data.daily_change}%`;
                    document.getElementById('monthly-change').textContent = `${data.monthly_change}%`;
                    document.getElementById('yearly-change').textContent = `${data.yearly_change}%`;

                    // Initialize charts
                    initializeServicesChart(data.services_stats);
                    initializeTrendChart(data.trend_data);
                    initializeStatusPieChart(data.status_distribution);
                    updateRecentActivity(data.recent_activity);
                })
                .catch(error => {
                    console.error('Error fetching emergency credit stats:', error);
                });

            function initializeServicesChart(servicesData) {
                const options = {
                    series: [{
                        name: 'Subscribers',
                        data: servicesData.map(service => service.total_subscribers)
                    }],
                    chart: {
                        type: 'bar',
                        height: 450,
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 4,
                            horizontal: false,
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    xaxis: {
                        categories: servicesData.map(service => service.name),
                        labels: {
                            style: {
                                colors: '#333',
                                fontSize: '12px'
                            }
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Subscribers',
                            style: {
                                color: '#333'
                            }
                        },
                        labels: {
                            style: {
                                colors: '#333'
                            }
                        }
                    },
                    colors: ['#5c6bc0'],
                    grid: {
                        borderColor: '#f1f1f1',
                    }
                };

                const chart = new ApexCharts(document.querySelector("#services-chart"), options);
                chart.render();
            }

            function initializeTrendChart(trendData) {
                const options = {
                    series: [{
                        name: 'Subscribers',
                        data: trendData.map(item => item.total_subscribers)
                    }],
                    chart: {
                        type: 'area',
                        height: 180,
                        sparkline: {
                            enabled: true
                        },
                        toolbar: {
                            show: false
                        }
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 2
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.7,
                            opacityTo: 0.3,
                            stops: [0, 90, 100]
                        }
                    },
                    colors: ['#fff'],
                    tooltip: {
                        theme: 'dark'
                    }
                };

                const chart = new ApexCharts(document.querySelector("#trend-chart"), options);
                chart.render();
            }

            function initializeStatusPieChart(statusData) {
                const options = {
                    series: statusData.map(item => item.count),
                    chart: {
                        type: 'donut',
                        height: 300
                    },
                    labels: statusData.map(item => item.status),
                    colors: ['#4CAF50', '#FFC107', '#F44336'],
                    legend: {
                        position: 'bottom'
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '70%'
                            }
                        }
                    },
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            chart: {
                                width: 200
                            },
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }]
                };

                const chart = new ApexCharts(document.querySelector("#status-pie-chart"), options);
                chart.render();
            }

            function updateRecentActivity(activityData) {
                const tbody = document.getElementById('recent-activity');
                tbody.innerHTML = '';

                activityData.forEach(activity => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${activity.date}</td>
                        <td>${activity.name}</td>
                        <td><span class="badge ${getStatusBadgeClass(activity.status)}">${activity.status}</span></td>
                        <td>${activity.total_subscribers.toLocaleString()}</td>
                    `;
                    tbody.appendChild(row);
                });
            }

            function getStatusBadgeClass(status) {
                switch(status) {
                    case 'ACTIVE':
                        return 'bg-success';
                    case 'CANCELED':
                        return 'bg-danger';
                    case 'FAILED':
                        return 'bg-warning';
                    default:
                        return 'bg-secondary';
                }
            }
        });
    </script>
  </body>
</html> 