@extends('layouts.layout_vertical')

@section('title', 'Audit Logs')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="page-header-title">
                <h5 class="m-b-10">Audit Logs</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.simple') }}">Home</a></li>
                <li class="breadcrumb-item">System Management</li>
                <li class="breadcrumb-item">Audit Logs</li>
            </ul>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <!-- Filters -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-funnel me-2"></i>
                        Filter Logs
                    </h5>
                </div>
                <div class="card-body">
                    <form class="row g-3">
                        <div class="col-md-3">
                            <label for="dateFrom" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="dateFrom" value="{{ date('Y-m-d', strtotime('-7 days')) }}">
                        </div>
                        <div class="col-md-3">
                            <label for="dateTo" class="form-label">To Date</label>
                            <input type="date" class="form-control" id="dateTo" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="userFilter" class="form-label">User</label>
                            <select class="form-select" id="userFilter">
                                <option value="">All Users</option>
                                <option value="admin">admin</option>
                                <option value="admin2">admin2</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="actionFilter" class="form-label">Action</label>
                            <select class="form-select" id="actionFilter">
                                <option value="">All Actions</option>
                                <option value="login">Login</option>
                                <option value="logout">Logout</option>
                                <option value="view_report">View Report</option>
                                <option value="export_data">Export Data</option>
                                <option value="system_settings">System Settings</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <button type="button" class="btn btn-primary" onclick="filterLogs()">
                                <i class="bi bi-search me-2"></i>Filter Logs
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="clearFilters()">
                                <i class="bi bi-x-circle me-2"></i>Clear Filters
                            </button>
                            <button type="button" class="btn btn-success" onclick="exportLogs()">
                                <i class="bi bi-download me-2"></i>Export Logs
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Audit Logs Table -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-list-ul me-2"></i>
                        System Audit Logs
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Timestamp</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Module</th>
                                    <th>Details</th>
                                    <th>IP Address</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="auditLogsTable">
                                <tr>
                                    <td>2025-01-15 10:30:15</td>
                                    <td><span class="badge bg-primary">admin</span></td>
                                    <td><span class="badge bg-success">Login</span></td>
                                    <td>Authentication</td>
                                    <td>Successful login from 127.0.0.1</td>
                                    <td>127.0.0.1</td>
                                    <td><span class="badge bg-success">Success</span></td>
                                </tr>
                                <tr>
                                    <td>2025-01-15 10:32:45</td>
                                    <td><span class="badge bg-primary">admin</span></td>
                                    <td><span class="badge bg-info">View Report</span></td>
                                    <td>SDF Reports</td>
                                    <td>Accessed Service Overview report</td>
                                    <td>127.0.0.1</td>
                                    <td><span class="badge bg-success">Success</span></td>
                                </tr>
                                <tr>
                                    <td>2025-01-15 10:35:20</td>
                                    <td><span class="badge bg-primary">admin</span></td>
                                    <td><span class="badge bg-warning">Export Data</span></td>
                                    <td>CRBT Reports</td>
                                    <td>Exported Daily CRBT MIS data</td>
                                    <td>127.0.0.1</td>
                                    <td><span class="badge bg-success">Success</span></td>
                                </tr>
                                <tr>
                                    <td>2025-01-15 10:40:10</td>
                                    <td><span class="badge bg-primary">admin</span></td>
                                    <td><span class="badge bg-info">View Report</span></td>
                                    <td>Emergency Credit</td>
                                    <td>Accessed Revenue Summary report</td>
                                    <td>127.0.0.1</td>
                                    <td><span class="badge bg-success">Success</span></td>
                                </tr>
                                <tr>
                                    <td>2025-01-15 10:45:30</td>
                                    <td><span class="badge bg-primary">admin</span></td>
                                    <td><span class="badge bg-secondary">System Settings</span></td>
                                    <td>System Management</td>
                                    <td>Updated session timeout settings</td>
                                    <td>127.0.0.1</td>
                                    <td><span class="badge bg-success">Success</span></td>
                                </tr>
                                <tr>
                                    <td>2025-01-15 09:15:22</td>
                                    <td><span class="badge bg-warning">admin2</span></td>
                                    <td><span class="badge bg-success">Login</span></td>
                                    <td>Authentication</td>
                                    <td>Successful login from 127.0.0.1</td>
                                    <td>127.0.0.1</td>
                                    <td><span class="badge bg-success">Success</span></td>
                                </tr>
                                <tr>
                                    <td>2025-01-15 09:20:15</td>
                                    <td><span class="badge bg-warning">admin2</span></td>
                                    <td><span class="badge bg-info">View Report</span></td>
                                    <td>Dashboard</td>
                                    <td>Accessed unified dashboard</td>
                                    <td>127.0.0.1</td>
                                    <td><span class="badge bg-success">Success</span></td>
                                </tr>
                                <tr>
                                    <td>2025-01-15 09:25:40</td>
                                    <td><span class="badge bg-warning">admin2</span></td>
                                    <td><span class="badge bg-danger">Logout</span></td>
                                    <td>Authentication</td>
                                    <td>User logged out</td>
                                    <td>127.0.0.1</td>
                                    <td><span class="badge bg-success">Success</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <nav aria-label="Audit logs pagination" class="mt-3">
                        <ul class="pagination justify-content-center">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1">Previous</a>
                            </li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">1,247</h4>
                            <p class="card-text">Total Logs</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-list-ul fs-1"></i>
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
                            <h4 class="card-title">1,198</h4>
                            <p class="card-text">Successful Actions</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-check-circle fs-1"></i>
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
                            <h4 class="card-title">49</h4>
                            <p class="card-text">Failed Actions</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-exclamation-triangle fs-1"></i>
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
                            <h4 class="card-title">2</h4>
                            <p class="card-text">Active Users</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-people fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function filterLogs() {
    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = document.getElementById('dateTo').value;
    const userFilter = document.getElementById('userFilter').value;
    const actionFilter = document.getElementById('actionFilter').value;
    
    // Show loading state
    const tableBody = document.getElementById('auditLogsTable');
    const originalContent = tableBody.innerHTML;
    
    tableBody.innerHTML = `
        <tr>
            <td colspan="7" class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Filtering logs...</p>
            </td>
        </tr>
    `;
    
    // Simulate API call
    setTimeout(() => {
        // In a real application, this would be an AJAX call to filter the logs
        tableBody.innerHTML = originalContent;
        
        // Show success message
        const alert = document.createElement('div');
        alert.className = 'alert alert-info alert-dismissible fade show';
        alert.innerHTML = `
            Logs filtered successfully! Showing results from ${dateFrom} to ${dateTo}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.querySelector('.card-body').insertBefore(alert, document.querySelector('.table-responsive'));
        
        // Remove alert after 3 seconds
        setTimeout(() => {
            alert.remove();
        }, 3000);
    }, 1000);
}

function clearFilters() {
    document.getElementById('dateFrom').value = '';
    document.getElementById('dateTo').value = '';
    document.getElementById('userFilter').value = '';
    document.getElementById('actionFilter').value = '';
    
    // Reload all logs
    filterLogs();
}

function exportLogs() {
    // Show loading state
    const button = event.target;
    const originalText = button.innerHTML;
    
    button.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Exporting...';
    button.disabled = true;
    
    // Simulate export process
    setTimeout(() => {
        button.innerHTML = '<i class="bi bi-check-circle me-2"></i>Exported!';
        button.classList.remove('btn-success');
        button.classList.add('btn-outline-success');
        
        // Show success message
        const alert = document.createElement('div');
        alert.className = 'alert alert-success alert-dismissible fade show';
        alert.innerHTML = `
            Audit logs exported successfully! Download will start shortly.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.querySelector('.card-body').insertBefore(alert, document.querySelector('.table-responsive'));
        
        setTimeout(() => {
            button.innerHTML = originalText;
            button.classList.remove('btn-outline-success');
            button.classList.add('btn-success');
            button.disabled = false;
            alert.remove();
        }, 3000);
    }, 2000);
}

document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh logs every 30 seconds
    setInterval(() => {
        // In a real application, this would fetch new logs
        console.log('Auto-refreshing audit logs...');
    }, 30000);
});
</script>
@endpush
