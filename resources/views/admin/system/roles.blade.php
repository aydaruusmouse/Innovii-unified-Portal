@extends('layouts.layout_vertical')

@section('title', 'Role & Permissions')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="page-header-title">
                <h5 class="m-b-10">Role & Permissions</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.simple') }}">Home</a></li>
                <li class="breadcrumb-item">System Management</li>
                <li class="breadcrumb-item">Role & Permissions</li>
            </ul>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <!-- Roles List -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-shield me-2"></i>
                        System Roles
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoleModal">
                                <i class="bi bi-plus-circle me-2"></i>Add New Role
                            </button>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search roles..." id="roleSearch">
                                <button class="btn btn-outline-secondary" type="button">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Roles Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Role</th>
                                    <th>Description</th>
                                    <th>Users</th>
                                    <th>Permissions</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <span class="badge bg-danger fs-6">Super Admin</span>
                                    </td>
                                    <td>Full system access with all permissions</td>
                                    <td><span class="badge bg-primary">1</span></td>
                                    <td><span class="badge bg-success">All</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-info" title="Permissions">
                                            <i class="bi bi-key"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="badge bg-warning fs-6">Admin</span>
                                    </td>
                                    <td>Administrative access with most permissions</td>
                                    <td><span class="badge bg-primary">1</span></td>
                                    <td><span class="badge bg-warning">Limited</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-info" title="Permissions">
                                            <i class="bi bi-key"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="badge bg-info fs-6">Viewer</span>
                                    </td>
                                    <td>Read-only access to reports and data</td>
                                    <td><span class="badge bg-secondary">0</span></td>
                                    <td><span class="badge bg-info">Read Only</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-info" title="Permissions">
                                            <i class="bi bi-key"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Permissions Overview -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-key me-2"></i>
                        Permission Categories
                    </h5>
                </div>
                <div class="card-body">
                    <div class="accordion" id="permissionsAccordion">
                        <!-- Dashboard Permissions -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#dashboardPerms">
                                    <i class="bi bi-house me-2"></i>Dashboard
                                </button>
                            </h2>
                            <div id="dashboardPerms" class="accordion-collapse collapse show" data-bs-parent="#permissionsAccordion">
                                <div class="accordion-body">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="dashboardView" checked disabled>
                                        <label class="form-check-label" for="dashboardView">View Dashboard</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="dashboardExport" checked disabled>
                                        <label class="form-check-label" for="dashboardExport">Export Dashboard Data</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SDF Reports Permissions -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sdfPerms">
                                    <i class="bi bi-bar-chart me-2"></i>SDF Reports
                                </button>
                            </h2>
                            <div id="sdfPerms" class="accordion-collapse collapse" data-bs-parent="#permissionsAccordion">
                                <div class="accordion-body">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="sdfView" checked disabled>
                                        <label class="form-check-label" for="sdfView">View SDF Reports</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="sdfExport" checked disabled>
                                        <label class="form-check-label" for="sdfExport">Export SDF Data</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- CRBT Reports Permissions -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#crbtPerms">
                                    <i class="bi bi-music me-2"></i>CRBT Reports
                                </button>
                            </h2>
                            <div id="crbtPerms" class="accordion-collapse collapse" data-bs-parent="#permissionsAccordion">
                                <div class="accordion-body">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="crbtView" checked disabled>
                                        <label class="form-check-label" for="crbtView">View CRBT Reports</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="crbtExport" checked disabled>
                                        <label class="form-check-label" for="crbtExport">Export CRBT Data</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Emergency Credit Permissions -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#emergencyPerms">
                                    <i class="bi bi-credit-card me-2"></i>Emergency Credit
                                </button>
                            </h2>
                            <div id="emergencyPerms" class="accordion-collapse collapse" data-bs-parent="#permissionsAccordion">
                                <div class="accordion-body">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="emergencyView" checked disabled>
                                        <label class="form-check-label" for="emergencyView">View Emergency Credit Reports</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="emergencyExport" checked disabled>
                                        <label class="form-check-label" for="emergencyExport">Export Emergency Credit Data</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- System Management Permissions -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#systemPerms">
                                    <i class="bi bi-gear me-2"></i>System Management
                                </button>
                            </h2>
                            <div id="systemPerms" class="accordion-collapse collapse" data-bs-parent="#permissionsAccordion">
                                <div class="accordion-body">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="userManage" checked disabled>
                                        <label class="form-check-label" for="userManage">Manage Users</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="roleManage" checked disabled>
                                        <label class="form-check-label" for="roleManage">Manage Roles</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="systemSettings" checked disabled>
                                        <label class="form-check-label" for="systemSettings">System Settings</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Role Modal -->
<div class="modal fade" id="addRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="roleName" class="form-label">Role Name</label>
                        <input type="text" class="form-control" id="roleName" required>
                    </div>
                    <div class="mb-3">
                        <label for="roleDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="roleDescription" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Permissions</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="permDashboard">
                                    <label class="form-check-label" for="permDashboard">Dashboard Access</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="permSDF">
                                    <label class="form-check-label" for="permSDF">SDF Reports</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="permCRBT">
                                    <label class="form-check-label" for="permCRBT">CRBT Reports</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="permEmergency">
                                    <label class="form-check-label" for="permEmergency">Emergency Credit</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="permUsers">
                                    <label class="form-check-label" for="permUsers">User Management</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="permSettings">
                                    <label class="form-check-label" for="permSettings">System Settings</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Create Role</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    document.getElementById('roleSearch').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Role creation form
    document.querySelector('#addRoleModal .btn-primary').addEventListener('click', function() {
        const roleName = document.getElementById('roleName').value;
        const roleDescription = document.getElementById('roleDescription').value;
        
        if (roleName && roleDescription) {
            // Show success message
            const alert = document.createElement('div');
            alert.className = 'alert alert-success alert-dismissible fade show';
            alert.innerHTML = `
                Role "${roleName}" created successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.querySelector('.card-body').insertBefore(alert, document.querySelector('.table-responsive'));
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('addRoleModal'));
            modal.hide();
            
            // Remove alert after 3 seconds
            setTimeout(() => {
                alert.remove();
            }, 3000);
        }
    });
});
</script>
@endpush
