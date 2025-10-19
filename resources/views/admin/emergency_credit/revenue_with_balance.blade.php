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
                  <h5 class="m-b-10">Emergency Credit Revenue with Balance</h5>
                </div>
                <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.simple') }}">Home</a></li>
                  <li class="breadcrumb-item">Emergency Credit</li>
                  <li class="breadcrumb-item">Revenue with Balance</li>
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
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ date('Y-m-d') }}">
                  </div>
                  <div class="col-md-4">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ date('Y-m-d') }}">
                  </div>
                  <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                      <i class="bi bi-search"></i> Filter
                    </button>
                    <button type="button" class="btn btn-success" onclick="exportToExcel()">
                      <i class="bi bi-download"></i> Export
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <!-- [ Filter Section ] end -->

        <!-- [ Revenue Summary Cards ] start -->
        <div class="row" id="summaryCards">
          <!-- Cards will be populated by JavaScript -->
        </div>
        <!-- [ Revenue Summary Cards ] end -->

        <!-- [ Revenue Chart ] start -->
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h5>Revenue with Balance Trend</h5>
              </div>
              <div class="card-body">
                <canvas id="revenueChart" height="100"></canvas>
              </div>
            </div>
          </div>
        </div>
        <!-- [ Revenue Chart ] end -->

        <!-- [ Revenue Table ] start -->
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h5>Revenue with Balance Data</h5>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-striped" id="revenueTable">
                    <thead>
                      <tr>
                        <th>Date</th>
                        <th>Total Credit (10K)</th>
                        <th>Total Paid (10K)</th>
                        <th>Balance (10K)</th>
                        <th>Repayment %</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- Data will be populated by JavaScript -->
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- [ Revenue Table ] end -->

      </div>
    </div>
    @include('layouts.footer_js')

    <script>
      let revenueChart;
      let revenueData = [];

      document.addEventListener('DOMContentLoaded', function() {
        loadRevenueData();
        
        // Add form submit event listener
        document.getElementById('filterForm').addEventListener('submit', function(e) {
          e.preventDefault();
          loadRevenueData();
        });
      });

      function loadRevenueData() {
        const formData = new FormData(document.getElementById('filterForm'));
        const params = new URLSearchParams(formData);
        
        fetch(`${window.AppConfig.apiBaseUrl}/emergency-credit/revenue-with-balance/data?${params}`)
          .then(response => response.json())
          .then(data => {
            if (data.error) {
              console.error('Error:', data.error);
              return;
            }
            
            revenueData = data.revenueData;
            updateSummaryCards();
            updateChart();
            updateTable();
          })
          .catch(error => {
            console.error('Error fetching revenue data:', error);
          });
      }

      function updateSummaryCards() {
        if (!revenueData || revenueData.length === 0) {
          document.getElementById('summaryCards').innerHTML = '<div class="col-md-12"><div class="alert alert-info">No data available</div></div>';
          return;
        }

        const totalCredit = revenueData.reduce((sum, item) => sum + parseFloat(item.total_credit), 0);
        const totalPaid = revenueData.reduce((sum, item) => sum + parseFloat(item.total_paid), 0);
        const totalBalance = revenueData.reduce((sum, item) => sum + parseFloat(item.balance), 0);
        const avgRepayment = revenueData.length > 0 ? 
          revenueData.reduce((sum, item) => sum + parseFloat(item.repayment_percentage), 0) / revenueData.length : 0;

        document.getElementById('summaryCards').innerHTML = `
          <div class="col-md-3">
            <div class="card bg-primary text-white">
              <div class="card-body">
                <div class="d-flex justify-content-between">
                  <div>
                    <h4 class="mb-0">${totalCredit.toFixed(2)}</h4>
                    <p class="mb-0">Total Credit (10K)</p>
                  </div>
                  <div class="align-self-center">
                    <i class="bi bi-currency-dollar" style="font-size: 2rem;"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card bg-success text-white">
              <div class="card-body">
                <div class="d-flex justify-content-between">
                  <div>
                    <h4 class="mb-0">${totalPaid.toFixed(2)}</h4>
                    <p class="mb-0">Total Paid (10K)</p>
                  </div>
                  <div class="align-self-center">
                    <i class="bi bi-check-circle" style="font-size: 2rem;"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card bg-warning text-white">
              <div class="card-body">
                <div class="d-flex justify-content-between">
                  <div>
                    <h4 class="mb-0">${totalBalance.toFixed(2)}</h4>
                    <p class="mb-0">Total Balance (10K)</p>
                  </div>
                  <div class="align-self-center">
                    <i class="bi bi-exclamation-triangle" style="font-size: 2rem;"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card bg-info text-white">
              <div class="card-body">
                <div class="d-flex justify-content-between">
                  <div>
                    <h4 class="mb-0">${avgRepayment.toFixed(2)}%</h4>
                    <p class="mb-0">Avg Repayment %</p>
                  </div>
                  <div class="align-self-center">
                    <i class="bi bi-percent" style="font-size: 2rem;"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        `;
      }

      function updateChart() {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        
        if (revenueChart) {
          revenueChart.destroy();
        }

        const labels = revenueData.map(item => item.date_label);
        const creditData = revenueData.map(item => parseFloat(item.total_credit));
        const paidData = revenueData.map(item => parseFloat(item.total_paid));
        const balanceData = revenueData.map(item => parseFloat(item.balance));

        revenueChart = new Chart(ctx, {
          type: 'line',
          data: {
            labels: labels,
            datasets: [{
              label: 'Total Credit (10K)',
              data: creditData,
              borderColor: 'rgb(75, 192, 192)',
              backgroundColor: 'rgba(75, 192, 192, 0.2)',
              tension: 0.1
            }, {
              label: 'Total Paid (10K)',
              data: paidData,
              borderColor: 'rgb(255, 99, 132)',
              backgroundColor: 'rgba(255, 99, 132, 0.2)',
              tension: 0.1
            }, {
              label: 'Balance (10K)',
              data: balanceData,
              borderColor: 'rgb(255, 205, 86)',
              backgroundColor: 'rgba(255, 205, 86, 0.2)',
              tension: 0.1
            }]
          },
          options: {
            responsive: true,
            plugins: {
              title: {
                display: true,
                text: 'Revenue with Balance Trend'
              }
            },
            scales: {
              y: {
                beginAtZero: true
              }
            }
          }
        });
      }

      function updateTable() {
        const tbody = document.querySelector('#revenueTable tbody');
        tbody.innerHTML = '';

        revenueData.forEach(item => {
          const row = document.createElement('tr');
          const balance = parseFloat(item.balance);
          
          // Highlight rows with high balance
          if (balance > 0) {
            row.className = 'table-warning';
          }
          
          row.innerHTML = `
            <td>${item.date_label}</td>
            <td>${parseFloat(item.total_credit).toFixed(2)}</td>
            <td>${parseFloat(item.total_paid).toFixed(2)}</td>
            <td class="${balance > 0 ? 'text-warning fw-bold' : ''}">${balance.toFixed(2)}</td>
            <td>${parseFloat(item.repayment_percentage).toFixed(2)}%</td>
          `;
          tbody.appendChild(row);
        });
      }

      function exportToExcel() {
        if (!revenueData || revenueData.length === 0) {
          alert('No data to export');
          return;
        }

        const wsData = [
          ['Date', 'Total Credit (10K)', 'Total Paid (10K)', 'Balance (10K)', 'Repayment %']
        ];

        revenueData.forEach(item => {
          wsData.push([
            item.date_label,
            parseFloat(item.total_credit).toFixed(2),
            parseFloat(item.total_paid).toFixed(2),
            parseFloat(item.balance).toFixed(2),
            parseFloat(item.repayment_percentage).toFixed(2) + '%'
          ]);
        });

        const ws = XLSX.utils.aoa_to_sheet(wsData);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Revenue with Balance');
        
        const fileName = `emergency_credit_revenue_with_balance_${new Date().toISOString().split('T')[0]}.xlsx`;
        XLSX.writeFile(wb, fileName);
      }
    </script>
  </body>
</html>
