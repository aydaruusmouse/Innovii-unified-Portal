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
    <!-- XLSX Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
  </head>
  <!-- [Head] end -->
  <!-- [Body] Start -->

  <body @@bodySetup>
    @include('layouts.layout_vertical')

    <!-- [ Main Content ] start -->
    <div class="pc-container">
      <div class="pc-content">
      <div class="page-header">
          <div class="page-block">
            <div class="row align-items-center">
              <div class="col-md-12">
                <div class="page-header-title">
                  <h5 class="m-b-10">Service Overview</h5>
                </div>
                <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                  <li class="breadcrumb-item">SDF Reports</li>
                  <li class="breadcrumb-item">Service Overview</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <!-- Search Section -->
        <div class="card mb-4">
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-8">
                <input type="text" class="form-control" id="searchOffers" placeholder="Search offers by name, code or status...">
              </div>
              <div class="col-md-4">
                <button class="btn btn-primary w-100" id="searchBtn">
                  <i class="bi bi-search me-2"></i>Search
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
          <div class="col-md-6">
            <div class="card">
              <div class="card-body">
                <h6 class="text-muted mb-3">Total Offers</h6>
                <div class="row d-flex align-items-center">
                  <div class="col-9">
                    <h3 class="f-w-300 d-flex align-items-center m-b-0" id="totalOffers">
                      <i class="bi bi-arrow-up text-primary f-24 m-r-5"></i>
                      <span>Loading...</span>
                    </h3>
                  </div>
                  <div class="col-3 text-end">
                    <p class="m-b-0 text-muted small">100%</p>
                  </div>
                </div>
                <div class="progress mt-3">
                  <div class="progress-bar bg-primary" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card">
              <div class="card-body">
                <h6 class="text-muted mb-3">Active Offers</h6>
                <div class="row d-flex align-items-center">
                  <div class="col-9">
                    <h3 class="f-w-300 d-flex align-items-center m-b-0" id="activeOffers">
                      <i class="bi bi-arrow-up text-primary f-24 m-r-5"></i>
                      <span>Loading...</span>
                    </h3>
                  </div>
                  <div class="col-3 text-end">
                    <p class="m-b-0 text-muted small" id="activeOffersPercentage">0%</p>
                  </div>
                </div>
                <div class="progress mt-3">
                  <div class="progress-bar bg-primary" role="progressbar" id="activeOffersProgress" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Services Table and Chart Section -->
        <div class="row">
          <div class="col-md-8">
            <div class="card mb-4">
              <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Services Overview</h6>
                <button type="button" class="btn btn-success" id="exportBtn">
                  <i class="bi bi-file-excel"></i> Export to Excel
                </button>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Offer Name</th>
                        <th>Short Code</th>
                        <th>Status</th>
                        <th>Validity</th>
                        <th>App ID</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody id="offersList">
                      <tr>
                        <td colspan="7" class="text-center">
                          <div class="d-flex align-items-center justify-content-center py-4">
                            <i class="bi bi-arrow-repeat spin me-2"></i>
                            Loading offers...
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div class="d-flex justify-content-between align-items-center p-3">
                  <div id="paginationInfo" class="text-muted small"></div>
                  <nav aria-label="Page navigation">
                    <ul class="pagination mb-0" id="paginationLinks">
                      <!-- Pagination links will be inserted here -->
                    </ul>
                  </nav>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Top 3 Most Popular Offers</h5>
                <span class="badge bg-primary">Live</span>
              </div>
              <div class="card-body">
                <div class="chart-container" style="height: 200px;">
                  <canvas id="topOffersChart"></canvas>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Offer Details Modal -->
    <div class="modal fade" id="offerDetailsModal" tabindex="-1" aria-labelledby="offerDetailsModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="offerDetailsModalLabel">Offer Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" id="modalOfferDetails">
            <!-- Details will be injected here dynamically -->
          </div>
        </div>
      </div>
    </div>

    <!-- [ Main Content ] end -->
    @include('layouts.footer') 
    @include('layouts.footer_js')

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentPage = 1;
        let totalPages = 1;
        let searchQuery = '';

        // Add export button click handler
        document.getElementById('exportBtn').addEventListener('click', function() {
            const table = document.querySelector('.table');
            if (!table) {
                console.error('Table not found');
                return;
            }
            
            try {
                const wb = XLSX.utils.table_to_book(table, {sheet: "Services Overview"});
                const fileName = `services_overview_${new Date().toISOString().split('T')[0]}.xlsx`;
                XLSX.writeFile(wb, fileName);
            } catch (error) {
                console.error('Error exporting to Excel:', error);
                alert('Error exporting to Excel. Please try again.');
            }
        });

        // Fetch data from backend on page load
        fetchOffersData(currentPage);

        // Fetch the offers data from the backend
        function fetchOffersData(page = 1) {
            console.log('Fetching offers data for page:', page);
            const url = new URL('http://127.0.0.1:8000/api/v1/offers');
            url.searchParams.append('page', page);
            if (searchQuery) {
                url.searchParams.append('search', searchQuery);
            }

            fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                credentials: 'same-origin'
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errorData => {
                            throw new Error(errorData.message || 'Network response was not ok');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        throw new Error(data.message || data.error);
                    }

                    // Update statistics
                    const totalOffers = data.totalOffers || 0;
                    const activeOffers = data.activeOffers || 0;

                    document.getElementById('totalOffers').querySelector('span').textContent = totalOffers;
                    
                    const activePercentage = totalOffers > 0 ? Math.round((activeOffers / totalOffers) * 100) : 0;
                    document.getElementById('activeOffers').querySelector('span').textContent = activeOffers;
                    document.getElementById('activeOffersPercentage').textContent = activePercentage + '%';
                    document.getElementById('activeOffersProgress').style.width = activePercentage + '%';
                    document.getElementById('activeOffersProgress').setAttribute('aria-valuenow', activePercentage);

                    // Update the offers table
                    let offersList = document.getElementById('offersList');
                    offersList.innerHTML = '';
                    
                    if (Array.isArray(data.offers) && data.offers.length > 0) {
                        data.offers.forEach(offer => {
                            if (!offer || typeof offer !== 'object') return;
                            
                            let row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${offer.id || 'N/A'}</td>
                                <td>${offer.name || 'N/A'}</td>
                                <td>${offer.short_code || 'N/A'}</td>
                                <td>
                                    <span class="badge ${offer.status === 'ACTIVE' ? 'bg-primary' : 'bg-danger'}">
                                        ${offer.status || 'inactive'}
                                    </span>
                                </td>
                                <td>${offer.validity || 'N/A'}</td>
                                <td>${offer.app_id || 'N/A'}</td>
                                <td>
                                    <button class="btn btn-sm btn-info" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#offerDetailsModal"
                                            data-offer='${JSON.stringify(offer)}'>
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            `;
                            offersList.appendChild(row);
                        });
                    } else {
                        offersList.innerHTML = '<tr><td colspan="7" class="text-center">No offers found</td></tr>';
                    }

                    // Update pagination
                    if (data.pagination) {
                        currentPage = data.pagination.current_page;
                        totalPages = data.pagination.last_page;
                        
                        // Update pagination info
                        const startItem = (currentPage - 1) * data.pagination.per_page + 1;
                        const endItem = Math.min(startItem + data.pagination.per_page - 1, data.pagination.total);
                        document.getElementById('paginationInfo').textContent = 
                            `Showing ${startItem} to ${endItem} of ${data.pagination.total} entries`;

                        // Update pagination links
                        const paginationLinks = document.getElementById('paginationLinks');
                        paginationLinks.innerHTML = '';

                        // Previous button
                        paginationLinks.innerHTML += `
                            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                                <a class="page-link" href="#" data-page="${currentPage - 1}">Previous</a>
                            </li>
                        `;

                        // Page numbers
                        for (let i = 1; i <= totalPages; i++) {
                            paginationLinks.innerHTML += `
                                <li class="page-item ${i === currentPage ? 'active' : ''}">
                                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                                </li>
                            `;
                        }

                        // Next button
                        paginationLinks.innerHTML += `
                            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                                <a class="page-link" href="#" data-page="${currentPage + 1}">Next</a>
                            </li>
                        `;

                        // Add click event listeners to pagination links
                        document.querySelectorAll('#paginationLinks .page-link').forEach(link => {
                            link.addEventListener('click', function(e) {
                                e.preventDefault();
                                const page = parseInt(this.getAttribute('data-page'));
                                if (page >= 1 && page <= totalPages) {
                                    fetchOffersData(page);
                                }
                            });
                        });
                    }

                    // Add event listeners for the view details buttons
                    document.querySelectorAll('[data-bs-toggle="modal"]').forEach(button => {
                        button.addEventListener('click', function() {
                            const offerData = JSON.parse(this.getAttribute('data-offer'));
                            const modalContent = `
                                <div class="mb-3">
                                    <strong>ID:</strong> ${offerData.id}
                                </div>
                                <div class="mb-3">
                                    <strong>Name:</strong> ${offerData.name}
                                </div>
                                <div class="mb-3">
                                    <strong>Short Code:</strong> ${offerData.short_code}
                                </div>
                                <div class="mb-3">
                                    <strong>Status:</strong> ${offerData.status}
                                </div>
                                <div class="mb-3">
                                    <strong>Validity:</strong> ${offerData.validity}
                                </div>
                                <div class="mb-3">
                                    <strong>App ID:</strong> ${offerData.app_id}
                                </div>
                                <div class="mb-3">
                                    <strong>Message:</strong> ${offerData.message || 'N/A'}
                                </div>
                            `;
                            document.getElementById('modalOfferDetails').innerHTML = modalContent;
                        });
                    });

                    // Update the chart (top 3 most popular offers)
                    if (Array.isArray(data.topOffers) && data.topOffers.length > 0) {
                        const ctx = document.getElementById('topOffersChart').getContext('2d');
                        
                        // Check if chart exists and is a valid Chart instance before destroying
                        if (window.topOffersChart && typeof window.topOffersChart.destroy === 'function') {
                            window.topOffersChart.destroy();
                        }

                        // Create new chart instance
                        window.topOffersChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: data.topOffers.map(offer => offer.name || 'N/A'),
                                datasets: [{
                                    label: 'Subscriber Count',
                                    data: data.topOffers.map(offer => offer.subscriber_count || 0),
                                    backgroundColor: [
                                        'rgba(13, 110, 253, 0.8)',
                                        'rgba(13, 110, 253, 0.6)',
                                        'rgba(13, 110, 253, 0.4)'
                                    ],
                                    borderColor: [
                                        'rgba(13, 110, 253, 1)',
                                        'rgba(13, 110, 253, 1)',
                                        'rgba(13, 110, 253, 1)'
                                    ],
                                    borderWidth: 1,
                                    borderRadius: 8,
                                    barThickness: 45,
                                    maxBarThickness: 50,
                                    minBarLength: 5,
                                    barPercentage: 0.8,
                                    categoryPercentage: 0.9
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: false
                                    },
                                    title: {
                                        display: false
                                    },
                                    tooltip: {
                                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                        padding: 10,
                                        titleFont: {
                                            size: 14,
                                            weight: 'bold'
                                        },
                                        bodyFont: {
                                            size: 13
                                        },
                                        callbacks: {
                                            label: function(context) {
                                                return `Subscribers: ${context.raw.toLocaleString()}`;
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        grid: {
                                            display: true,
                                            color: 'rgba(0, 0, 0, 0.05)'
                                        },
                                        ticks: {
                                            font: {
                                                size: 12
                                            }
                                        }
                                    },
                                    x: {
                                        grid: {
                                            display: false
                                        },
                                        ticks: {
                                            font: {
                                                size: 12
                                            },
                                            maxRotation: 45,
                                            minRotation: 45
                                        }
                                    }
                                },
                                layout: {
                                    padding: {
                                        left: 10,
                                        right: 10,
                                        top: 10,
                                        bottom: 10
                                    }
                                }
                            }
                        });
                    } else {
                        // If no data, clear the canvas
                        const ctx = document.getElementById('topOffersChart').getContext('2d');
                        ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
                    }
                })
                .catch(error => {
                    console.error('Error fetching offers data:', error);
                    document.getElementById('totalOffers').querySelector('span').textContent = 'Error';
                    document.getElementById('activeOffers').querySelector('span').textContent = 'Error';
                    
                    let offersList = document.getElementById('offersList');
                    offersList.innerHTML = `
                        <tr>
                            <td colspan="7" class="text-center text-danger">
                                Error: ${error.message}<br>
                                Please check the console for more details
                            </td>
                        </tr>
                    `;
                });
        }

        // Search functionality
        document.getElementById("searchBtn").addEventListener("click", function() {
            searchQuery = document.getElementById("searchOffers").value.trim();
            currentPage = 1;
            fetchOffersData(currentPage);
        });

        // Add Enter key functionality for search
        document.getElementById("searchOffers").addEventListener("keypress", function(e) {
            if (e.key === "Enter") {
                e.preventDefault();
                searchQuery = this.value.trim();
                currentPage = 1;
                fetchOffersData(currentPage);
            }
        });
    });
    </script>
  </body>
  <!-- [Body] end -->
</html>