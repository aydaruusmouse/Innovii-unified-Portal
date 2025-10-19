<!doctype html>
<html lang="en">
  <!-- [Head] start -->

  <head>
    @include('layouts.heads_page')
    @include('layouts.heads_css')
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    {{-- @@include('../layouts/head-page-meta.html', {'title': 'Home'}) @@include('../layouts/head-css.html') --}}
  </head>
  <!-- [Head] end -->
  <!-- [Body] Start -->

  <body @@bodySetup>
    {{-- @@include('../layouts/layout-vertical.html') --}}
    @include('layouts.layout_vertical')
    <!-- [ Main Content ] start -->
    <div class="pc-container">
      <div class="pc-content">


      
        {{-- @@include('../layouts/breadcrumb.html', {'breadcrumb-item': 'Dashboard', 'breadcrumb-item-active': 'Home'}) --}}
        <!-- [ Main Content ] start -->
        <div class="row">
            <div class="page-header mb-3">
                <div class="page-block">
                  <div class="row align-items-center">
                    <div class="col-md-12">
                      <div class="page-header-title">
                        <h5 class="mb-0">Dashboard Overview</h5>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <ul class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.simple') }}">Home</a></li>
                        <li class="breadcrumb-item" aria-current="page">Dashboard</li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
              
          <!-- [ Total Offers ] start -->
          <div class="col-md-6 col-xl-3">
            <div class="card">
              <div class="card-body">
                <h6 class="mb-4">Total Offers</h6>
                <div class="row d-flex align-items-center">
                  <div class="col-9">
                    <h3 class="f-w-300 d-flex align-items-center m-b-0">
                      <i class="feather icon-package text-primary f-30 m-r-10"></i>
                      <span id="total-offers">{{ number_format($totalOffers) }}</span>
                    </h3>
                  </div>
                  <div class="col-3 text-end">
                    <p class="m-b-0">
                      <span class="badge bg-light-success" id="active-offers">{{ $activeOffers }} Active</span>
                    </p>
                  </div>
                </div>
                <div class="progress m-t-30" style="height: 7px">
                  <div id="offers-progress" class="progress-bar bg-primary" role="progressbar" 
                       style="width: {{ $totalOffers > 0 ? ($activeOffers / $totalOffers) * 100 : 0 }}%"></div>
                </div>
              </div>
            </div>
          </div>
          <!-- [ Total Offers ] end -->

          <!-- [ Active Subscribers ] start -->
          <div class="col-md-6 col-xl-3">
            <div class="card">
              <div class="card-body">
                <h6 class="mb-4">Active Subscribers</h6>
                <div class="row d-flex align-items-center">
                  <div class="col-9">
                    <h3 class="f-w-300 d-flex align-items-center m-b-0">
                      <i class="feather icon-user-check text-success f-30 m-r-10"></i>
                      <span id="active-subscribers">{{ number_format($totalActive) }}</span>
                    </h3>
                  </div>
                  <div class="col-3 text-end">
                    <p class="m-b-0" id="active-change">
                      @php
                          $total = $totalActive + $totalFailed + $totalCanceled;
                          $percentage = $total > 0 ? ($totalActive / $total) * 100 : 0;
                      @endphp
                      {{ number_format($percentage, 1) }}%
                    </p>
                  </div>
                </div>
                <div class="progress m-t-30" style="height: 7px">
                  <div id="active-progress" class="progress-bar bg-success" role="progressbar" 
                       style="width: {{ $total > 0 ? ($totalActive / $total) * 100 : 0 }}%"></div>
                </div>
              </div>
            </div>
          </div>
          <!-- [ Active Subscribers ] end -->

          <!-- [ Failed Subscribers ] start -->
          <div class="col-md-6 col-xl-3">
            <div class="card">
              <div class="card-body">
                <h6 class="mb-4">Failed Subscribers</h6>
                <div class="row d-flex align-items-center">
                  <div class="col-9">
                    <h3 class="f-w-300 d-flex align-items-center m-b-0">
                      <i class="feather icon-user-x text-danger f-30 m-r-10"></i>
                      <span id="failed-subscribers">{{ number_format($totalFailed) }}</span>
                    </h3>
                  </div>
                  <div class="col-3 text-end">
                    <p class="m-b-0" id="failed-percentage">
                      @php
                          $total = $totalActive + $totalFailed + $totalCanceled;
                          $percentage = $total > 0 ? ($totalFailed / $total) * 100 : 0;
                      @endphp
                      {{ number_format($percentage, 1) }}%
                    </p>
                  </div>
                </div>
                <div class="progress m-t-30" style="height: 7px">
                  <div id="failed-progress" class="progress-bar bg-danger" role="progressbar" 
                       style="width: {{ $total > 0 ? ($totalFailed / $total) * 100 : 0 }}%"></div>
                </div>
              </div>
            </div>
          </div>
          <!-- [ Failed Subscribers ] end -->

          <!-- [ Canceled Subscribers ] start -->
          <div class="col-md-6 col-xl-3">
            <div class="card">
              <div class="card-body">
                <h6 class="mb-4">Canceled Subscribers</h6>
                <div class="row d-flex align-items-center">
                  <div class="col-9">
                    <h3 class="f-w-300 d-flex align-items-center m-b-0">
                      <i class="feather icon-user-minus text-warning f-30 m-r-10"></i>
                      <span id="canceled-subscribers">{{ number_format($totalCanceled) }}</span>
                    </h3>
              </div>
                  <div class="col-3 text-end">
                    <p class="m-b-0" id="canceled-percentage">
                      @php
                          $total = $totalActive + $totalFailed + $totalCanceled;
                          $percentage = $total > 0 ? ($totalCanceled / $total) * 100 : 0;
                      @endphp
                      {{ number_format($percentage, 1) }}%
                    </p>
            </div>
          </div>
                <div class="progress m-t-30" style="height: 7px">
                  <div id="canceled-progress" class="progress-bar bg-warning" role="progressbar" 
                       style="width: {{ $total > 0 ? ($totalCanceled / $total) * 100 : 0 }}%"></div>
                </div>
              </div>
            </div>
          </div>
          <!-- [ Canceled Subscribers ] end -->

          <!-- [ Emergency Credit Status ] start -->
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h5>Emergency Credit Status</h5>
              </div>
              <div class="card-body">
                <div class="row align-items-center mb-4">
                  <div class="col-8">
                    <h3 class="f-w-300 d-flex align-items-center">
                      <i class="feather icon-alert-circle text-info f-30 m-r-10"></i>
                      <span id="emergency-credit-count">0</span>
                    </h3>
                    <p class="mb-0">Subscribers Using Emergency Credit</p>
                  </div>
                  <div class="col-4 text-end">
                    <p class="mb-0" id="emergency-percentage">0%</p>
                  </div>
                </div>
                <div class="progress" style="height: 7px">
                  <div id="emergency-progress" class="progress-bar bg-info" role="progressbar" style="width: 0%"></div>
                </div>
              </div>
            </div>
          </div>
          <!-- [ Emergency Credit Status ] end -->

          <!-- [ Status Distribution Chart ] start -->
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h5>Subscriber Status Distribution</h5>
              </div>
              <div class="card-body">
                <div id="status-distribution-chart"></div>
              </div>
            </div>
          </div>
          <!-- [ Status Distribution Chart ] end -->

          <!-- [ Service Statistics ] start -->
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h5>Top Services by Subscribers</h5>
              </div>
              <div class="card-body">
                <div id="services-chart"></div>
              </div>
            </div>
          </div>
          <!-- [ Service Statistics ] end -->
        </div>
        <!-- [ Main Content ] end -->
      </div>
    </div>
    <!-- [ Main Content ] end -->
 
    <!-- [Page Specific JS] start -->
    <!-- apexcharts js -->
    <script src="{{ asset('admin/assets/js/plugins/apexcharts.min.js') }}"></script>
    
    @include('layouts.footer_js')
   
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize chart variables at a wider scope
            let statusChart = null;
            let servicesChart = null;
            let dashboardData = null;
            let refreshInterval = null;

            function initializeStatusDistributionChart(data) {
                try {
                    // Destroy existing chart if it exists
                    if (statusChart) {
                        statusChart.destroy();
                    }

                    const options = {
                        series: data.map(item => item.count),
                        chart: {
                            type: 'donut',
                            height: 320
                        },
                        labels: data.map(item => item.status),
                        colors: ['#4CAF50', '#F44336', '#FFC107'],
                        legend: {
                            position: 'bottom'
                        },
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '70%'
                                }
                            }
                        }
                    };

                    const chartElement = document.querySelector("#status-distribution-chart");
                    if (chartElement) {
                        statusChart = new ApexCharts(chartElement, options);
                        statusChart.render();
                    }
                } catch (error) {
                    console.error('Error initializing status distribution chart:', error);
                }
            }

            function initializeServicesChart(data) {
                try {
                    // Destroy existing chart if it exists
                    if (servicesChart) {
                        servicesChart.destroy();
                    }

                const options = {
                    series: [{
                        name: 'Subscribers',
                            data: data.map(service => service.total_subscribers)
                    }],
                    chart: {
                        type: 'bar',
                            height: 350
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                                columnWidth: '55%',
                                endingShape: 'rounded'
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    xaxis: {
                            categories: data.map(service => service.name)
                    },
                    yaxis: {
                        title: {
                                text: 'Subscribers'
                            }
                        },
                        fill: {
                            opacity: 1
                        },
                        colors: ['#5c6bc0']
                    };

                    const chartElement = document.querySelector("#services-chart");
                    if (chartElement) {
                        servicesChart = new ApexCharts(chartElement, options);
                        servicesChart.render();
                    }
                } catch (error) {
                    console.error('Error initializing services chart:', error);
                }
            }

            // Function to fetch dashboard data
            async function fetchDashboardData() {
                try {
                    const [dashboardResponse, ecResponse] = await Promise.all([
                        fetch('/api/v1/dashboard-stats'),
                        fetch('/emergency-credit/status/data')
                    ]);

                    if (!dashboardResponse.ok || !ecResponse.ok) {
                        throw new Error('Failed to fetch dashboard data');
                    }

                    const dashboardData = await dashboardResponse.json();
                    const ecData = await ecResponse.json();

                    return { dashboardData, ecData };
                } catch (error) {
                    console.error('Error fetching dashboard data:', error);
                    throw error;
                }
            }

            // Function to update dashboard metrics with animation
            function updateMetricWithAnimation(elementId, newValue, duration = 1000) {
                const element = document.getElementById(elementId);
                if (!element) return;

                const currentValue = parseFloat(element.textContent.replace(/,/g, '')) || 0;
                const targetValue = parseFloat(newValue);
                const startTime = performance.now();

                function animate(currentTime) {
                    const elapsedTime = currentTime - startTime;
                    const progress = Math.min(elapsedTime / duration, 1);
                    
                    const currentValue = Math.floor(currentValue + (targetValue - currentValue) * progress);
                    element.textContent = currentValue.toLocaleString();

                    if (progress < 1) {
                        requestAnimationFrame(animate);
                    }
                }

                requestAnimationFrame(animate);
            }

            // Function to update all metrics with animations
            function updateDashboard(data) {
                try {
                    // Update total offers and active offers with animation
                    const totalOffers = data.total_offers || 0;
                    const activeOffers = data.active_offers || 0;
                    
                    updateMetricWithAnimation('total-offers', totalOffers);
                    document.getElementById('active-offers').textContent = `${activeOffers} Active`;
                    
                    const offersPercentage = totalOffers > 0 ? (activeOffers / totalOffers) * 100 : 0;
                    const offersProgressElement = document.getElementById('offers-progress');
                    if (offersProgressElement) {
                        offersProgressElement.style.transition = 'width 1s ease-in-out';
                        offersProgressElement.style.width = `${offersPercentage}%`;
                    }

                    // Calculate total subscribers from status distribution
                    const statusDistribution = data.status_distribution || [];
                    const totalSubscribers = statusDistribution.reduce((sum, item) => sum + (item.count || 0), 0);

                    // Update subscriber counts and percentages with animation
                    const activeData = statusDistribution.find(item => item.status === 'ACTIVE') || { count: 0 };
                    const failedData = statusDistribution.find(item => item.status === 'FAILED') || { count: 0 };
                    const canceledData = statusDistribution.find(item => item.status === 'CANCELED') || { count: 0 };

                    updateMetricWithAnimation('active-subscribers', activeData.count);
                    updateMetricWithAnimation('failed-subscribers', failedData.count);
                    updateMetricWithAnimation('canceled-subscribers', canceledData.count);

                    // Update percentages with animation
                    const activePercentage = totalSubscribers > 0 ? (activeData.count / totalSubscribers) * 100 : 0;
                    const failedPercentage = totalSubscribers > 0 ? (failedData.count / totalSubscribers) * 100 : 0;
                    const canceledPercentage = totalSubscribers > 0 ? (canceledData.count / totalSubscribers) * 100 : 0;

                    document.getElementById('active-change').textContent = `${activePercentage.toFixed(1)}%`;
                    document.getElementById('failed-percentage').textContent = `${failedPercentage.toFixed(1)}%`;
                    document.getElementById('canceled-percentage').textContent = `${canceledPercentage.toFixed(1)}%`;

                    // Update progress bars with animation
                    ['active-progress', 'failed-progress', 'canceled-progress'].forEach((id, index) => {
                        const element = document.getElementById(id);
                        if (element) {
                            element.style.transition = 'width 1s ease-in-out';
                            element.style.width = `${[activePercentage, failedPercentage, canceledPercentage][index]}%`;
                        }
                    });

                    // Initialize charts with animation
                    if (statusDistribution.length > 0 && document.querySelector("#status-distribution-chart")) {
                        initializeStatusDistributionChart(statusDistribution);
                    }
                    
                    const servicesStats = data.services_stats || [];
                    if (servicesStats.length > 0 && document.querySelector("#services-chart")) {
                        initializeServicesChart(servicesStats);
                    }
                } catch (error) {
                    console.error('Error updating dashboard:', error);
                }
            }

            // Function to update emergency credit data
            function updateEmergencyCreditData(ecData, totalSubscribers) {
                const totalEC = ecData.total || 0;
                const percentage = totalSubscribers > 0 ? (totalEC / totalSubscribers) * 100 : 0;
                
                updateMetricWithAnimation('emergency-credit-count', totalEC);
                document.getElementById('emergency-percentage').textContent = `${percentage.toFixed(1)}%`;
                
                const ecProgressElement = document.getElementById('emergency-progress');
                if (ecProgressElement) {
                    ecProgressElement.style.transition = 'width 1s ease-in-out';
                    ecProgressElement.style.width = `${percentage}%`;
                }
            }

            // Function to refresh dashboard data
            async function refreshDashboard() {
                try {
                    const { dashboardData, ecData } = await fetchDashboardData();
                    updateDashboard(dashboardData);
                    
                    const totalSubscribers = (dashboardData.status_distribution || [])
                        .reduce((sum, item) => sum + (item.count || 0), 0);
                    
                    updateEmergencyCreditData(ecData, totalSubscribers);
                } catch (error) {
                    console.error('Error refreshing dashboard:', error);
                }
            }

            // Initialize dashboard and set up auto-refresh
            refreshDashboard();
            refreshInterval = setInterval(refreshDashboard, 30000); // Refresh every 30 seconds

            // Clean up interval on page unload
            window.addEventListener('beforeunload', function() {
                if (refreshInterval) {
                    clearInterval(refreshInterval);
                }
            });
        });
    </script>
  </body>
  <!-- [Body] end -->
</html>

</html>
