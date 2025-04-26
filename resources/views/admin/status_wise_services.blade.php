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
        <div class="container-fluid">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Service Status Report</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Service Status Report</li>
                    </ol>
                </nav>
            </div>

            <!-- Filter Section -->
            <div class="card mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Report Filters</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="startDate" class="form-label">Start Date</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                    <input type="date" class="form-control" id="startDate">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="endDate" class="form-label">End Date</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                    <input type="date" class="form-control" id="endDate">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="serviceFilter" class="form-label">Service</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-filter"></i></span>
                                    <select class="form-select" id="serviceFilter">
                                        <option value="all">All Services</option>
                                        @foreach($services as $service)
                                            <option value="{{ $service->name }}">{{ $service->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="statusFilter" class="form-label">Status</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                    <select class="form-select" id="statusFilter">
                                        <option value="all">All Status</option>
                                        <option value="ACTIVE">Active</option>
                                        <option value="CANCELED">Canceled</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 text-end">
                            <button class="btn btn-primary" id="applyFilters">
                                <i class="fas fa-filter me-2"></i>Apply Filters
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Services Table -->
            <div class="card mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Service Status Details</h6>
                    <button type="button" class="btn btn-success" id="exportBtn">
                        <i class="bi bi-file-excel"></i> Export to Excel
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="servicesTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Service Name</th>
                                    <th>Status</th>
                                    <th>Subscribers</th>
                                </tr>
                            </thead>
                            <tbody id="servicesTableBody">
                                <!-- Table content will be populated dynamically -->
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div id="paginationInfo" class="text-muted"></div>
                        <ul class="pagination mb-0" id="paginationLinks"></ul>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>

    @include('layouts.footer') 
    @include('layouts.footer_js')

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const startDate = document.getElementById("startDate");
            const endDate = document.getElementById("endDate");
            const serviceFilter = document.getElementById("serviceFilter");
            const statusFilter = document.getElementById("statusFilter");
            const applyFilters = document.getElementById("applyFilters");
            const servicesTableBody = document.getElementById("servicesTableBody");
            const paginationInfo = document.getElementById("paginationInfo");
            const paginationLinks = document.getElementById("paginationLinks");
            let currentPage = 1;

            // Set default dates (last 30 days)
            const today = new Date();
            const thirtyDaysAgo = new Date();
            thirtyDaysAgo.setDate(today.getDate() - 30);
            
            startDate.value = thirtyDaysAgo.toISOString().split('T')[0];
            endDate.value = today.toISOString().split('T')[0];

            // Apply Filters
            applyFilters.addEventListener("click", function () {
                currentPage = 1;
                fetchData();
            });

            // Fetch data with pagination
            function fetchData(page = 1) {
                const selectedStartDate = startDate.value;
                const selectedEndDate = endDate.value;
                const selectedService = serviceFilter.value;
                const selectedStatus = statusFilter.value;

                fetch(`/api/v1/status-wise-report?start_date=${selectedStartDate}&end_date=${selectedEndDate}&service_name=${selectedService}&status=${selectedStatus}&page=${page}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            throw new Error(data.message || data.error);
                        }

                        // Update Table
                        servicesTableBody.innerHTML = '';
                        data.table_data.forEach(row => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td>${row.date}</td>
                                <td>${row.name}</td>
                                <td>
                                    <span class="badge ${getStatusBadgeClass(row.status)}">
                                        ${row.status || 'N/A'}
                                    </span>
                                </td>
                                <td>${row.total_subs || 0}</td>
                            `;
                            servicesTableBody.appendChild(tr);
                        });

                        // Update pagination
                        updatePagination(data.pagination);
                    })
                    .catch(error => {
                        console.error('Error fetching status-wise report:', error);
                        alert('Error fetching data: ' + error.message);
                    });
            }

            // Update pagination
            function updatePagination(pagination) {
                paginationInfo.textContent = `Showing ${pagination.from} to ${pagination.to} of ${pagination.total} entries`;
                
                paginationLinks.innerHTML = '';
                
                // Previous button
                const prevLi = document.createElement('li');
                prevLi.className = `page-item ${pagination.current_page === 1 ? 'disabled' : ''}`;
                prevLi.innerHTML = `<a class="page-link" href="#" data-page="${pagination.current_page - 1}">«</a>`;
                paginationLinks.appendChild(prevLi);

                // Calculate page range
                let startPage = Math.max(1, pagination.current_page - 2);
                let endPage = Math.min(pagination.last_page, pagination.current_page + 2);

                // Adjust range if at the edges
                if (pagination.current_page <= 3) {
                    endPage = Math.min(5, pagination.last_page);
                }
                if (pagination.current_page >= pagination.last_page - 2) {
                    startPage = Math.max(1, pagination.last_page - 4);
                }

                // First page and ellipsis if needed
                if (startPage > 1) {
                    paginationLinks.appendChild(createPageItem(1));
                    if (startPage > 2) {
                        const ellipsis = document.createElement('li');
                        ellipsis.className = 'page-item disabled';
                        ellipsis.innerHTML = '<span class="page-link">...</span>';
                        paginationLinks.appendChild(ellipsis);
                    }
                }

                // Page numbers
                for (let i = startPage; i <= endPage; i++) {
                    paginationLinks.appendChild(createPageItem(i, i === pagination.current_page));
                }

                // Last page and ellipsis if needed
                if (endPage < pagination.last_page) {
                    if (endPage < pagination.last_page - 1) {
                        const ellipsis = document.createElement('li');
                        ellipsis.className = 'page-item disabled';
                        ellipsis.innerHTML = '<span class="page-link">...</span>';
                        paginationLinks.appendChild(ellipsis);
                    }
                    paginationLinks.appendChild(createPageItem(pagination.last_page));
                }

                // Next button
                const nextLi = document.createElement('li');
                nextLi.className = `page-item ${pagination.current_page === pagination.last_page ? 'disabled' : ''}`;
                nextLi.innerHTML = `<a class="page-link" href="#" data-page="${pagination.current_page + 1}">»</a>`;
                paginationLinks.appendChild(nextLi);

                // Add click event listeners
                paginationLinks.querySelectorAll('.page-link').forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const page = parseInt(this.dataset.page);
                        if (page && page !== pagination.current_page) {
                            currentPage = page;
                            fetchData(page);
                        }
                    });
                });
            }

            // Helper function to create page items
            function createPageItem(pageNumber, isActive = false) {
                const li = document.createElement('li');
                li.className = `page-item ${isActive ? 'active' : ''}`;
                li.innerHTML = `<a class="page-link" href="#" data-page="${pageNumber}">${pageNumber}</a>`;
                return li;
            }

            // Export to Excel
            document.getElementById('exportBtn').addEventListener('click', function() {
                const table = document.querySelector('.table');
                if (!table) {
                    console.error('Table not found');
                    return;
                }
                
                try {
                    const wb = XLSX.utils.table_to_book(table, {sheet: "Services Status"});
                    const fileName = `services_status_${new Date().toISOString().split('T')[0]}.xlsx`;
                    XLSX.writeFile(wb, fileName);
                } catch (error) {
                    console.error('Error exporting to Excel:', error);
                    alert('Error exporting to Excel. Please try again.');
                }
            });

            // Helper function for status badge classes
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

            // Load initial data
            fetchData();
        });
    </script>
  </body>
</html>