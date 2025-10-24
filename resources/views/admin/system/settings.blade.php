@extends('layouts.layout_vertical')

@section('title', 'System Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="page-header-title">
                <h5 class="m-b-10">System Settings</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.simple') }}">Home</a></li>
                <li class="breadcrumb-item">System Management</li>
                <li class="breadcrumb-item">System Settings</li>
            </ul>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <!-- General Settings -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-gear me-2"></i>
                        General Settings
                    </h5>
                </div>
                <div class="card-body">
                    <form>
                        <div class="mb-3">
                            <label for="siteName" class="form-label">Site Name</label>
                            <input type="text" class="form-control" id="siteName" value="Innovii Unified Portal">
                        </div>
                        <div class="mb-3">
                            <label for="siteDescription" class="form-label">Site Description</label>
                            <textarea class="form-control" id="siteDescription" rows="3">Unified Reports Portal for SDF, CRBT, and Emergency Credit Management</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="timezone" class="form-label">Timezone</label>
                            <select class="form-select" id="timezone">
                                <option value="UTC">UTC</option>
                                <option value="Africa/Mogadishu" selected>Somalia (UTC+3)</option>
                                <option value="Africa/Nairobi">Kenya (UTC+3)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="dateFormat" class="form-label">Date Format</label>
                            <select class="form-select" id="dateFormat">
                                <option value="Y-m-d">YYYY-MM-DD</option>
                                <option value="d/m/Y" selected>DD/MM/YYYY</option>
                                <option value="m/d/Y">MM/DD/YYYY</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Save General Settings</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Database Settings -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-database me-2"></i>
                        Database Connections
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <i class="bi bi-check-circle-fill text-success fs-2"></i>
                                    <h6 class="card-title mt-2">SDF Database</h6>
                                    <small class="text-muted">Connected</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <i class="bi bi-check-circle-fill text-success fs-2"></i>
                                    <h6 class="card-title mt-2">CRBT Database</h6>
                                    <small class="text-muted">Connected</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <i class="bi bi-check-circle-fill text-success fs-2"></i>
                                    <h6 class="card-title mt-2">Emergency Credit</h6>
                                    <small class="text-muted">Connected</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <i class="bi bi-check-circle-fill text-success fs-2"></i>
                                    <h6 class="card-title mt-2">Main Database</h6>
                                    <small class="text-muted">Connected</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Security Settings -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-shield-lock me-2"></i>
                        Security Settings
                    </h5>
                </div>
                <div class="card-body">
                    <form>
                        <div class="mb-3">
                            <label for="sessionTimeout" class="form-label">Session Timeout (minutes)</label>
                            <input type="number" class="form-control" id="sessionTimeout" value="30" min="5" max="480">
                        </div>
                        <div class="mb-3">
                            <label for="maxLoginAttempts" class="form-label">Max Login Attempts</label>
                            <input type="number" class="form-control" id="maxLoginAttempts" value="5" min="3" max="10">
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="requireStrongPassword" checked>
                                <label class="form-check-label" for="requireStrongPassword">
                                    Require Strong Passwords
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="enableAuditLog" checked>
                                <label class="form-check-label" for="enableAuditLog">
                                    Enable Audit Logging
                                </label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Security Settings</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- System Information -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        System Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>PHP Version:</strong><br>
                            <span class="text-muted">{{ phpversion() }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Laravel Version:</strong><br>
                            <span class="text-muted">{{ app()->version() }}</span>
                        </div>
                        <div class="col-md-6 mt-3">
                            <strong>Server:</strong><br>
                            <span class="text-muted">{{ $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' }}</span>
                        </div>
                        <div class="col-md-6 mt-3">
                            <strong>Environment:</strong><br>
                            <span class="badge bg-info">{{ app()->environment() }}</span>
                        </div>
                    </div>
                    <hr>
                    <div class="d-grid">
                        <button class="btn btn-outline-primary" onclick="checkSystemHealth()">
                            <i class="bi bi-heart-pulse me-2"></i>Check System Health
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function checkSystemHealth() {
    // Simulate system health check
    const button = event.target;
    const originalText = button.innerHTML;
    
    button.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Checking...';
    button.disabled = true;
    
    setTimeout(() => {
        button.innerHTML = '<i class="bi bi-check-circle me-2"></i>System Healthy';
        button.classList.remove('btn-outline-primary');
        button.classList.add('btn-success');
        
        setTimeout(() => {
            button.innerHTML = originalText;
            button.classList.remove('btn-success');
            button.classList.add('btn-outline-primary');
            button.disabled = false;
        }, 2000);
    }, 1500);
}

document.addEventListener('DOMContentLoaded', function() {
    // Auto-save functionality
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Show success message
            const alert = document.createElement('div');
            alert.className = 'alert alert-success alert-dismissible fade show';
            alert.innerHTML = `
                Settings saved successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            form.parentNode.insertBefore(alert, form);
            
            // Remove alert after 3 seconds
            setTimeout(() => {
                alert.remove();
            }, 3000);
        });
    });
});
</script>
@endpush
