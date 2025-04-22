<!doctype html>
<html lang="en">
  <!-- [Head] start -->
  <head>
    @include('layouts.heads_page') 
    @include('layouts.heads_css')
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  </head>
  <!-- [Head] end -->
  <!-- [Body] Start -->


  <body @@bodySetup>
    @include('layouts.layout_vertical')

    <!-- [ Main Content ] start -->
    <div class="pc-container">
      <div class="pc-content">
        {{-- @include('layouts.breadcrumps'); --}}
        {{-- <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Search Offers" id="searchOffers">
            <button class="btn btn-outline-secondary" type="button" id="searchBtn">Search</button>
          </div>
           --}}
        <!-- [ Main Content ] start -->
        <!-- List of Offers -->
        <div class="row mb-4">
            <div class="col-md-7">
                <input type="text" class="form-control" id="searchOffers" placeholder="Search Offers">
            </div>
            {{-- <div class="col-md-4">
                <input type="date" class="form-control" id="filterDate">
            </div> --}}
            <div class="col-md-4">
                <button class="btn btn-primary" id="searchBtn">Search</button>
            </div>
        </div>
        
    
        <!-- Offer Statistics -->
        <div class="row">
            <div class="col-md-6 col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-4">Total Offers</h6>
                        <div class="row d-flex align-items-center">
                            <div class="col-9">
                                <h3 class="f-w-300 d-flex align-items-center m-b-0" id="totalOffers">
                                    <i class="feather icon-arrow-up text-success f-30 m-r-10"></i>
                                    <span>Loading...</span>
                                </h3>
                            </div>
                            <div class="col-3 text-end">
                                <p class="m-b-0">100%</p>
                            </div>
                        </div>
                        <div class="progress m-t-30" style="height: 7px">
                            <div class="progress-bar bg-brand-color-1" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-4">Active Offers</h6>
                        <div class="row d-flex align-items-center">
                            <div class="col-9">
                                <h3 class="f-w-300 d-flex align-items-center m-b-0" id="activeOffers">
                                    <i class="feather icon-arrow-up text-success f-30 m-r-10"></i>
                                    <span>Loading...</span>
                                </h3>
                            </div>
                            <div class="col-3 text-end">
                                <p class="m-b-0" id="activeOffersPercentage">0%</p>
                            </div>
                        </div>
                        <div class="progress m-t-30" style="height: 7px">
                            <div class="progress-bar bg-brand-color-1" role="progressbar" id="activeOffersProgress" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <!-- Pagination -->
    {{-- <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <li class="page-item"><a class="page-link" href="#">Previous</a></li>
            <li class="page-item"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
            <li class="page-item"><a class="page-link" href="#">Next</a></li>
        </ul>
    </nav> --}}
        <!-- [ Main Content ] end -->

        <!-- [ Bar Chart for Most Popular Offers ] start -->
        <div class="container">
            <div class="row d-flex flex-wrap">
                <!-- Offers Table -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3>Offers List</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
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
                                        <td colspan="7" class="text-center">Loading offers...</td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div id="paginationInfo" class="text-muted"></div>
                                <nav aria-label="Page navigation">
                                    <ul class="pagination justify-content-center" id="paginationLinks">
                                        <!-- Pagination links will be inserted here -->
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                
                </div>
         {{-- <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <li class="page-item"><a class="page-link" href="#">Previous</a></li>
            <li class="page-item"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
            <li class="page-item"><a class="page-link" href="#">Next</a></li>
        </ul>
    </nav> --}}
                <!-- Top Offers Chart -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3>Top 3 Most Popular Offers</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="topOffersChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- [ Bar Chart for Most Popular Offers ] end -->

      </div>
    </div>
    <!-- [ Main Content ] end -->
     <!-- Export Button -->
     {{-- <div class="d-flex justify-content-end">
        <button class="btn btn-secondary mb-4">Export to CSV</button>
    </div> --}}

   

</div>

<!-- Offer Details Modal -->

<div class="modal fade" id="offerDetailsModal" tabindex="-1" aria-labelledby="offerDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog">
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
</body>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 1;
    let totalPages = 1;
    let searchQuery = '';

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
                                <span class="badge ${offer.status === 'ACTIVE' ? 'bg-success' : 'bg-danger'}">
                                    ${offer.status || 'inactive'}
                                </span>
                            </td>
                            <td>${offer.validity || 'N/A'}</td>
                            <td>${offer.app_id || 'N/A'}</td>
                            <td>
                                <button class="btn btn-info btn-sm" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#offerDetailsModal"
                                        data-offer='${JSON.stringify(offer)}'>
                                    View Details
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
                            <p><strong>ID:</strong> ${offerData.id}</p>
                            <p><strong>Name:</strong> ${offerData.name}</p>
                            <p><strong>Short Code:</strong> ${offerData.short_code}</p>
                            <p><strong>Status:</strong> ${offerData.status}</p>
                            <p><strong>Validity:</strong> ${offerData.validity}</p>
                            <p><strong>App ID:</strong> ${offerData.app_id}</p>
                            <p><strong>Message:</strong> ${offerData.message || 'N/A'}</p>
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
                                    'rgba(54, 162, 235, 0.2)',
                                    'rgba(75, 192, 192, 0.2)',
                                    'rgba(153, 102, 255, 0.2)'
                                ],
                                borderColor: [
                                    'rgba(54, 162, 235, 1)',
                                    'rgba(75, 192, 192, 1)',
                                    'rgba(153, 102, 255, 1)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                title: {
                                    display: true,
                                    text: 'Top 3 Offers by Subscriber Count'
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Number of Subscribers'
                                    }
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
});

  </script>
  
    @include('layouts.footer') 
    @include('layouts.footer_js');
</body>
  <!-- [Body] end -->
</html>
