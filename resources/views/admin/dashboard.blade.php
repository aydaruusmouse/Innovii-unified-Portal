<!-- resources/views/admin/dashboard.blade.php -->

<!doctype html>
<html lang="en">
  <head>
    @include('layouts.heads_page')
    @include('layouts.heads_css')
    @include('layouts.config')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  </head>
  <body>
    @include('layouts.layout_vertical')
    <div class="pc-container">
      <div class="pc-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
          <div class="page-block">
            <div class="row align-items-center">
              <div class="col-md-12">
                <div class="page-header-title">
                  <h5 class="m-b-10">Unified Dashboard Overview</h5>
                </div>
                <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.simple') }}">Home</a></li>
                  <li class="breadcrumb-item">Dashboard</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <!-- [ breadcrumb ] end -->

        <!-- [ Quick Stats Row ] start -->
        <div class="row">
          <!-- [ SDF Total Offers ] start -->
          <div class="col-md-6 col-xl-3">
            <div class="card">
              <div class="card-body">
                <h6 class="mb-4">SDF Total Offers</h6>
                <div class="row d-flex align-items-center">
                  <div class="col-9">
                    <h3 class="f-w-300 d-flex align-items-center m-b-0">
                      <i class="feather icon-package text-primary f-30 m-r-10"></i>
                      <span id="total-offers">Loading...</span>
                    </h3>
                  </div>
                  <div class="col-3 text-end">
                    <p class="m-b-0">
                      <span class="badge bg-light-success" id="active-offers">Loading...</span>
                    </p>
                  </div>
                </div>
                <div class="progress m-t-30" style="height: 7px">
                  <div id="offers-progress" class="progress-bar bg-primary" role="progressbar" style="width: 0%"></div>
                </div>
              </div>
            </div>
          </div>
          <!-- [ SDF Total Offers ] end -->

          <!-- [ CRBT Daily Active ] start -->
          <div class="col-md-6 col-xl-3">
            <div class="card">
              <div class="card-body">
                <h6 class="mb-4">CRBT Daily Active</h6>
                <div class="row d-flex align-items-center">
                  <div class="col-9">
                    <h3 class="f-w-300 d-flex align-items-center m-b-0">
                      <i class="feather icon-music text-success f-30 m-r-10"></i>
                      <span id="crbt-daily-active">Loading...</span>
                    </h3>
                  </div>
                  <div class="col-3 text-end">
                    <p class="m-b-0">
                      <span class="badge bg-light-info" id="crbt-total-base">Loading...</span>
                    </p>
                  </div>
                </div>
                <div class="progress m-t-30" style="height: 7px">
                  <div id="crbt-progress" class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
                </div>
              </div>
            </div>
          </div>
          <!-- [ CRBT Daily Active ] end -->

          <!-- [ Emergency Credit Revenue ] start -->
          <div class="col-md-6 col-xl-3">
            <div class="card">
              <div class="card-body">
                <h6 class="mb-4">Emergency Credit Revenue</h6>
                <div class="row d-flex align-items-center">
                  <div class="col-9">
                    <h3 class="f-w-300 d-flex align-items-center m-b-0">
                      <i class="feather icon-credit-card text-warning f-30 m-r-10"></i>
                      <span id="emergency-revenue">Loading...</span>
                    </h3>
                  </div>
                  <div class="col-3 text-end">
                    <p class="m-b-0">
                      <span class="badge bg-light-warning" id="emergency-transactions">Loading...</span>
                    </p>
                  </div>
                </div>
                <div class="progress m-t-30" style="height: 7px">
                  <div id="emergency-progress" class="progress-bar bg-warning" role="progressbar" style="width: 0%"></div>
                </div>
              </div>
            </div>
          </div>
          <!-- [ Emergency Credit Revenue ] end -->

          <!-- [ System Health ] start -->
          <div class="col-md-6 col-xl-3">
            <div class="card">
              <div class="card-body">
                <h6 class="mb-4">System Health</h6>
                <div class="row d-flex align-items-center">
                  <div class="col-9">
                    <h3 class="f-w-300 d-flex align-items-center m-b-0">
                      <i class="feather icon-activity text-info f-30 m-r-10"></i>
                      <span id="system-status">Healthy</span>
                    </h3>
                  </div>
                  <div class="col-3 text-end">
                    <p class="m-b-0">
                      <span class="badge bg-light-success" id="uptime">99.9%</span>
                    </p>
                  </div>
                </div>
                <div class="progress m-t-30" style="height: 7px">
                  <div id="health-progress" class="progress-bar bg-info" role="progressbar" style="width: 99%"></div>
                </div>
              </div>
            </div>
          </div>
          <!-- [ System Health ] end -->
        </div>
        <!-- [ Quick Stats Row ] end -->

        <!-- [ Charts Row ] start -->
        <div class="row">
          <!-- [ SDF Status Distribution ] start -->
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h5>SDF Subscriber Status Distribution</h5>
              </div>
              <div class="card-body">
                <div id="sdf-status-chart"></div>
              </div>
            </div>
          </div>
          <!-- [ SDF Status Distribution ] end -->

          <!-- [ CRBT Interface Usage ] start -->
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h5>CRBT Interface Usage</h5>
              </div>
              <div class="card-body">
                <div id="crbt-interface-chart"></div>
              </div>
            </div>
          </div>
          <!-- [ CRBT Interface Usage ] end -->
        </div>
        <!-- [ Charts Row ] end -->

        <!-- [ Revenue Trends Row ] start -->
        <div class="row">
          <!-- [ Emergency Credit Trends ] start -->
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h5>Emergency Credit Revenue Trends (Last 30 Days)</h5>
              </div>
              <div class="card-body">
                <div id="revenue-trends-chart"></div>
              </div>
            </div>
          </div>
          <!-- [ Emergency Credit Trends ] end -->
        </div>
        <!-- [ Revenue Trends Row ] end -->

        <!-- [ Module Quick Access ] start -->
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h5>Quick Access to Modules</h5>
              </div>
              <div class="card-body">
                <div class="row">
                  <!-- SDF Reports -->
                  <div class="col-md-3 mb-3">
                    <div class="card border-primary">
                      <div class="card-body text-center">
                        <i class="feather icon-bar-chart text-primary" style="font-size: 2rem;"></i>
                        <h6 class="mt-2">SDF Reports</h6>
                        <p class="text-muted small">Subscriber Data Files</p>
                        <a href="{{ route('all_services') }}" class="btn btn-primary btn-sm">View Reports</a>
                      </div>
                    </div>
                  </div>

                  <!-- CRBT Reports -->
                  <div class="col-md-3 mb-3">
                    <div class="card border-success">
                      <div class="card-body text-center">
                        <i class="feather icon-music text-success" style="font-size: 2rem;"></i>
                        <h6 class="mt-2">CRBT Reports</h6>
                        <p class="text-muted small">Caller Ring Back Tone</p>
                        <a href="{{ route('crbt.daily_mis') }}" class="btn btn-success btn-sm">View Reports</a>
                      </div>
                    </div>
                  </div>

                  <!-- Emergency Credit -->
                  <div class="col-md-3 mb-3">
                    <div class="card border-warning">
                      <div class="card-body text-center">
                        <i class="feather icon-credit-card text-warning" style="font-size: 2rem;"></i>
                        <h6 class="mt-2">Emergency Credit</h6>
                        <p class="text-muted small">Credit Management</p>
                        <a href="{{ route('emergency_credit.daily') }}" class="btn btn-warning btn-sm">View Reports</a>
                      </div>
                    </div>
                  </div>

                  <!-- System Settings -->
                  <div class="col-md-3 mb-3">
                    <div class="card border-info">
                      <div class="card-body text-center">
                        <i class="feather icon-settings text-info" style="font-size: 2rem;"></i>
                        <h6 class="mt-2">System Settings</h6>
                        <p class="text-muted small">Configuration</p>
                        <a href="#" class="btn btn-info btn-sm">Manage</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- [ Module Quick Access ] end -->

        <!-- [ Recent Activity ] start -->
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h5>Recent System Activity</h5>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>Time</th>
                        <th>Module</th>
                        <th>Action</th>
                        <th>Status</th>
                      </tr>
                    </thead>
                    <tbody id="recent-activity">
                      <tr>
                        <td>Just now</td>
                        <td>SDF Reports</td>
                        <td>Data refresh</td>
                        <td><span class="badge bg-success">Success</span></td>
                      </tr>
                      <tr>
                        <td>2 min ago</td>
                        <td>CRBT Reports</td>
                        <td>Daily MIS update</td>
                        <td><span class="badge bg-success">Success</span></td>
                      </tr>
                      <tr>
                        <td>5 min ago</td>
                        <td>Emergency Credit</td>
                        <td>Revenue calculation</td>
                        <td><span class="badge bg-success">Success</span></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- [ Recent Activity ] end -->
      </div>
    </div>

    @include('layouts.footer_js')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Dashboard DOM loaded');
            console.log('AppConfig available:', typeof window.AppConfig !== 'undefined');
            console.log('AppConfig:', window.AppConfig);
            
            // Initialize charts first
            console.log('Initializing charts first...');
            initializeCharts();
            
            // Load dashboard data after a short delay to ensure charts are ready
            setTimeout(() => {
                loadDashboardData();
            }, 1000);
            
            // Refresh data every 5 minutes
            setInterval(loadDashboardData, 300000);
        });

        async function loadDashboardData() {
            try {
                console.log('Starting dashboard data loading...');
                
                // Load SDF data
                console.log('Loading SDF data...');
                await loadSDFData();
                
                // Load CRBT data
                console.log('Loading CRBT data...');
                await loadCRBTData();
                
                // Load Emergency Credit data
                console.log('Loading Emergency Credit data...');
                await loadEmergencyCreditData();
                
                console.log('Dashboard data loading completed');
                
            } catch (error) {
                console.error('Error loading dashboard data:', error);
            }
        }

        async function loadSDFData() {
            try {
                const response = await fetch(`${window.AppConfig.baseUrl}/api/v1/offers`);
                const data = await response.json();
                
                document.getElementById('total-offers').textContent = data.totalOffers || '0';
                document.getElementById('active-offers').textContent = `${data.activeOffers || 0} Active`;
                
                const progress = data.totalOffers > 0 ? ((data.activeOffers || 0) / data.totalOffers) * 100 : 0;
                document.getElementById('offers-progress').style.width = `${progress}%`;
                
                // Load SDF status distribution for chart
                await loadSDFStatusDistribution();
                
            } catch (error) {
                console.error('Error loading SDF data:', error);
                document.getElementById('total-offers').textContent = '0';
                document.getElementById('active-offers').textContent = '0 Active';
            }
        }

        async function loadSDFStatusDistribution() {
            try {
                console.log('Loading SDF status distribution...');
                const response = await fetch(`${window.AppConfig.baseUrl}/api/v1/status-analysis`);
                const data = await response.json();
                console.log('SDF status data received:', data);
                
                if (data.status_distribution && data.status_distribution.length > 0) {
                    const statusData = data.status_distribution;
                    const labels = statusData.map(item => item.status);
                    const values = statusData.map(item => item.count);
                    
                    console.log('SDF status labels:', labels);
                    console.log('SDF status values:', values);
                    
                    // Update SDF Status Chart with real data
                    updateSDFStatusChart(labels, values);
                } else {
                    console.log('No SDF status distribution data available');
                }
                
            } catch (error) {
                console.error('Error loading SDF status distribution:', error);
            }
        }

        async function loadCRBTData() {
            try {
                const response = await fetch(`${window.AppConfig.baseUrl}/api/crbt/daily-mis`);
                const data = await response.json();
                
                if (data.data && data.data.length > 0) {
                    const latest = data.data[0];
                    document.getElementById('crbt-daily-active').textContent = latest.activeNrml || '0';
                    document.getElementById('crbt-total-base').textContent = `${latest.activeBase || 0} Total`;
                    
                    const progress = latest.activeBase > 0 ? ((latest.activeNrml || 0) / latest.activeBase) * 100 : 0;
                    document.getElementById('crbt-progress').style.width = `${Math.min(progress, 100)}%`;
                } else {
                    document.getElementById('crbt-daily-active').textContent = '0';
                    document.getElementById('crbt-total-base').textContent = '0 Total';
                }
                
                // Load CRBT interface data for chart
                await loadCRBTInterfaceData();
                
            } catch (error) {
                console.error('Error loading CRBT data:', error);
                document.getElementById('crbt-daily-active').textContent = '0';
                document.getElementById('crbt-total-base').textContent = '0 Total';
            }
        }

        async function loadCRBTInterfaceData() {
            try {
                console.log('Loading CRBT interface data...');
                const response = await fetch(`${window.AppConfig.baseUrl}/api/crbt/interface-data`);
                const data = await response.json();
                console.log('CRBT interface data received:', data);
                
                if (data.data && data.data.length > 0) {
                    const interfaceData = data.data.slice(0, 7); // Get top 7 interfaces
                    const labels = interfaceData.map(item => item.interface);
                    const values = interfaceData.map(item => parseInt(item.total_subscriptions) || 0);
                    
                    console.log('CRBT interface labels:', labels);
                    console.log('CRBT interface values:', values);
                    
                    // Update CRBT Interface Chart with real data
                    updateCRBTInterfaceChart(labels, values);
                } else {
                    console.log('No CRBT interface data available');
                }
                
            } catch (error) {
                console.error('Error loading CRBT interface data:', error);
            }
        }

        async function loadEmergencyCreditData() {
            try {
                // Get current date range for last 30 days
                const endDate = new Date().toISOString().split('T')[0];
                const startDate = new Date(Date.now() - 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
                
                console.log('Loading Emergency Credit data...');
                console.log('Date range:', startDate, 'to', endDate);
                
                const response = await fetch(`${window.AppConfig.baseUrl}/api/v1/emergency-credit/revenue-summary/data?start_date=${startDate}&end_date=${endDate}`);
                const data = await response.json();
                console.log('Emergency Credit data received:', data);
                
                if (data.revenueData && data.revenueData.length > 0) {
                    const totalRevenue = data.revenueData.reduce((sum, item) => sum + parseFloat(item.total_credit), 0);
                    const totalPaid = data.revenueData.reduce((sum, item) => sum + parseFloat(item.total_paid), 0);
                    
                    document.getElementById('emergency-revenue').textContent = `${totalRevenue.toFixed(2)}K`;
                    document.getElementById('emergency-transactions').textContent = `${data.revenueData.length} Days`;
                    
                    const progress = totalRevenue > 0 ? (totalPaid / totalRevenue) * 100 : 0;
                    document.getElementById('emergency-progress').style.width = `${Math.min(progress, 100)}%`;
                    
                    console.log('Emergency Credit revenue data:', data.revenueData);
                    
                    // Update revenue trends chart with real data
                    updateRevenueTrendsChart(data.revenueData);
                } else {
                    console.log('No Emergency Credit data available');
                    document.getElementById('emergency-revenue').textContent = '0.00K';
                    document.getElementById('emergency-transactions').textContent = 'No Data';
                    document.getElementById('emergency-progress').style.width = '0%';
                }
                
            } catch (error) {
                console.error('Error loading Emergency Credit data:', error);
                document.getElementById('emergency-revenue').textContent = '0.00K';
                document.getElementById('emergency-transactions').textContent = 'No Data';
            }
        }

        // Global chart variables
        let sdfStatusChart, crbtInterfaceChart, revenueTrendsChart;

        function initializeCharts() {
            console.log('Initializing charts...');
            console.log('ApexCharts available:', typeof ApexCharts !== 'undefined');
            
            // Check if chart elements exist
            const sdfElement = document.querySelector("#sdf-status-chart");
            const crbtElement = document.querySelector("#crbt-interface-chart");
            const revenueElement = document.querySelector("#revenue-trends-chart");
            
            console.log('Chart elements found:', {
                sdf: !!sdfElement,
                crbt: !!crbtElement,
                revenue: !!revenueElement
            });
            
            // SDF Status Chart - Initialize with sample data
            try {
                sdfStatusChart = new ApexCharts(sdfElement, {
                    series: [45, 25, 15],
                chart: {
                    type: 'donut',
                        height: 300
                },
                    labels: ['ACTIVE', 'FAILED', 'CANCELED'],
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
                });
                sdfStatusChart.render();
                console.log('SDF Status Chart initialized with sample data');
            } catch (error) {
                console.error('Error initializing SDF Status Chart:', error);
            }

            // CRBT Interface Chart - Initialize with sample data
            try {
                crbtInterfaceChart = new ApexCharts(crbtElement, {
                    series: [{
                        name: 'Subscriptions',
                        data: [298, 161, 117, 13, 3, 0, 0]
                    }],
                    chart: {
                        type: 'line',
                        height: 300
                    },
                    xaxis: {
                        categories: ['IVR', 'USSD', 'copytone', 'WEB', 'VIRAL_SMS', 'OBD', 'RULE-ENGINE']
                    },
                    colors: ['#4CAF50']
                });
                crbtInterfaceChart.render();
                console.log('CRBT Interface Chart initialized with sample data');
            } catch (error) {
                console.error('Error initializing CRBT Interface Chart:', error);
            }

            // Revenue Trends Chart - Initialize with sample data
            try {
                revenueTrendsChart = new ApexCharts(revenueElement, {
                series: [{
                        name: 'Total Credit',
                        data: [231, 198, 203, 192, 194, 194, 196, 191, 202, 201]
                    }, {
                        name: 'Total Paid',
                        data: [230, 197, 202, 191, 193, 193, 196, 191, 202, 200]
                }],
                chart: {
                        type: 'area',
                    height: 350
                },
                    xaxis: {
                        categories: ['Jan 1', 'Jan 2', 'Jan 3', 'Jan 4', 'Jan 5', 'Jan 6', 'Jan 7', 'Jan 8', 'Jan 9', 'Jan 10']
                    },
                    colors: ['#FFC107', '#4CAF50'],
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.7,
                            opacityTo: 0.9,
                            stops: [0, 90, 100]
                        }
                    }
                });
                revenueTrendsChart.render();
                console.log('Revenue Trends Chart initialized with sample data');
            } catch (error) {
                console.error('Error initializing Revenue Trends Chart:', error);
            }
        }

        function updateSDFStatusChart(labels, values) {
            console.log('Updating SDF Status Chart with:', labels, values);
            if (sdfStatusChart) {
                // Ensure all three statuses are represented, even if they have 0 count
                const allLabels = ['ACTIVE', 'FAILED', 'CANCELED'];
                const allValues = allLabels.map(label => {
                    const index = labels.indexOf(label);
                    const value = index !== -1 ? values[index] : 0;
                    // Show a small value for 0 counts to make them visible in the chart
                    return value === 0 ? 1 : value;
                });
                
                console.log('Normalized chart data:', allLabels, allValues);
                
                sdfStatusChart.updateOptions({
                    series: allValues,
                    labels: allLabels,
                    tooltip: {
                        y: {
                            formatter: function (val, opts) {
                                const label = opts.w.globals.labels[opts.seriesIndex];
                                const originalValue = labels.indexOf(label) !== -1 ? values[labels.indexOf(label)] : 0;
                                return originalValue === 0 ? '0' : val.toLocaleString();
                            }
                        }
                    }
                });
                console.log('SDF Status Chart updated successfully');
            } else {
                console.error('SDF Status Chart not initialized');
            }
        }

        function updateCRBTInterfaceChart(labels, values) {
            console.log('Updating CRBT Interface Chart with:', labels, values);
            if (crbtInterfaceChart) {
                crbtInterfaceChart.updateOptions({
                    series: [{
                        name: 'Subscriptions',
                        data: values
                    }],
                xaxis: {
                        categories: labels
                    }
                });
                console.log('CRBT Interface Chart updated successfully');
            } else {
                console.error('CRBT Interface Chart not initialized');
            }
        }

        function updateRevenueTrendsChart(revenueData) {
            console.log('Updating Revenue Trends Chart with:', revenueData);
            if (revenueTrendsChart && revenueData.length > 0) {
                const dates = revenueData.map(item => item.date_label);
                const totalCredit = revenueData.map(item => parseFloat(item.total_credit));
                const totalPaid = revenueData.map(item => parseFloat(item.total_paid));
                
                console.log('Revenue chart dates:', dates);
                console.log('Revenue chart credit data:', totalCredit);
                console.log('Revenue chart paid data:', totalPaid);
                
                revenueTrendsChart.updateOptions({
                    series: [{
                        name: 'Total Credit',
                        data: totalCredit
                    }, {
                        name: 'Total Paid',
                        data: totalPaid
                    }],
                    xaxis: {
                        categories: dates
                    }
                });
                console.log('Revenue Trends Chart updated successfully');
            } else {
                console.error('Revenue Trends Chart not initialized or no data');
            }
        }
    </script>
  </body>
</html>
