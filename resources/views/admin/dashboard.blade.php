<!-- resources/views/admin/dashboard.blade.php -->

<!doctype html>
<html lang="en">
  <head>
    @include('layouts.heads_page')
    @include('layouts.heads_css')
    <!-- ApexCharts -->
    <script src="{{ asset('admin/assets/js/plugins/apexcharts.min.js') }}"></script>
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
                  <h5 class="m-b-10">Dashboard Overview</h5>
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

        <!-- [ Main Content ] start -->
        <div class="row">
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
                      {{ $totalActive > 0 ? round(($totalActive / ($totalActive + $totalFailed + $totalCanceled)) * 100, 1) : 0 }}%
                    </p>
                  </div>
                </div>
                <div class="progress m-t-30" style="height: 7px">
                  <div id="active-progress" class="progress-bar bg-success" role="progressbar" 
                       style="width: {{ $totalActive > 0 ? ($totalActive / ($totalActive + $totalFailed + $totalCanceled)) * 100 : 0 }}%"></div>
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
                      {{ $totalFailed > 0 ? round(($totalFailed / ($totalActive + $totalFailed + $totalCanceled)) * 100, 1) : 0 }}%
                    </p>
                  </div>
                </div>
                <div class="progress m-t-30" style="height: 7px">
                  <div id="failed-progress" class="progress-bar bg-danger" role="progressbar" 
                       style="width: {{ $totalFailed > 0 ? ($totalFailed / ($totalActive + $totalFailed + $totalCanceled)) * 100 : 0 }}%"></div>
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
                      {{ $totalCanceled > 0 ? round(($totalCanceled / ($totalActive + $totalFailed + $totalCanceled)) * 100, 1) : 0 }}%
                    </p>
                  </div>
                </div>
                <div class="progress m-t-30" style="height: 7px">
                  <div id="canceled-progress" class="progress-bar bg-warning" role="progressbar" 
                       style="width: {{ $totalCanceled > 0 ? ($totalCanceled / ($totalActive + $totalFailed + $totalCanceled)) * 100 : 0 }}%"></div>
                </div>
              </div>
            </div>
          </div>
          <!-- [ Canceled Subscribers ] end -->

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
          <div class="col-md-6">
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

    @include('layouts.footer_js')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize status distribution chart
            const statusDistribution = {!! json_encode($statusDistribution) !!};
            const statusChart = new ApexCharts(document.querySelector("#status-distribution-chart"), {
                series: statusDistribution.map(item => item.count),
                chart: {
                    type: 'donut',
                    height: 350
                },
                labels: statusDistribution.map(item => item.status),
                colors: ['#4CAF50', '#F44336', '#FFC107'],
                legend: {
                    position: 'bottom'
                }
            });
            statusChart.render();

            // Initialize services chart
            const serviceStats = {!! json_encode($serviceStats) !!};
            const servicesChart = new ApexCharts(document.querySelector("#services-chart"), {
                series: [{
                    name: 'Subscribers',
                    data: serviceStats.map(service => service.active_count)
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
                    categories: serviceStats.map(service => service.name)
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
            });
            servicesChart.render();
        });
    </script>
  </body>
</html>
