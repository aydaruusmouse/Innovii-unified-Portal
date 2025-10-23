<!-- Required Js -->
<script src="{{ asset('admin/assets/js/plugins/popper.min.js') }}"></script>
<script src="{{ asset('admin/assets/js/plugins/simplebar.min.js') }}"></script>
<script src="{{ asset('admin/assets/js/plugins/bootstrap.min.js') }}"></script>
<script src="{{ asset('admin/assets/js/fonts/custom-font.js') }}"></script>
<script src="{{ asset('admin/assets/js/script.js') }}"></script>
<script src="{{ asset('admin/assets/js/theme.js') }}"></script>
<script src="{{ asset('admin/assets/js/plugins/feather.min.js') }}"></script>

@stack('scripts')

<!-- Session Timeout Handler -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    let sessionTimeout;
    let warningTimeout;
    const SESSION_WARNING_TIME = 25 * 60 * 1000; // 25 minutes
    const SESSION_TIMEOUT = 30 * 60 * 1000; // 30 minutes
    
    function resetSessionTimer() {
        clearTimeout(sessionTimeout);
        clearTimeout(warningTimeout);
        
        // Set warning timeout (25 minutes)
        warningTimeout = setTimeout(showSessionWarning, SESSION_WARNING_TIME);
        
        // Set session timeout (30 minutes)
        sessionTimeout = setTimeout(logoutUser, SESSION_TIMEOUT);
    }
    
    function showSessionWarning() {
        // Show warning modal
        const modal = document.createElement('div');
        modal.className = 'modal fade show';
        modal.style.display = 'block';
        modal.style.backgroundColor = 'rgba(0,0,0,0.5)';
        modal.innerHTML = `
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            Session Expiring Soon
                        </h5>
                    </div>
                    <div class="modal-body">
                        <p>Your session will expire in 5 minutes due to inactivity.</p>
                        <p>Click "Stay Logged In" to continue your session, or you will be automatically logged out.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="logoutUser()">Logout Now</button>
                        <button type="button" class="btn btn-primary" onclick="extendSession()">Stay Logged In</button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }
    
    function extendSession() {
        // Remove warning modal
        const modal = document.querySelector('.modal.show');
        if (modal) {
            modal.remove();
        }
        
        // Reset timers
        resetSessionTimer();
        
        // Show success message
        showToast('Session extended successfully', 'success');
    }
    
    function logoutUser() {
        // Remove any modals
        const modal = document.querySelector('.modal.show');
        if (modal) {
            modal.remove();
        }
        
        // Show logout message
        showToast('Session expired. Redirecting to login...', 'warning');
        
        // Redirect to login after short delay
        setTimeout(() => {
            window.location.href = '/admin/login';
        }, 2000);
    }
    
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        // Add to toast container
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }
        
        container.appendChild(toast);
        
        // Show toast
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            toast.remove();
        }, 5000);
    }
    
    // Track user activity
    const activityEvents = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'];
    activityEvents.forEach(event => {
        document.addEventListener(event, resetSessionTimer, true);
    });
    
    // Initialize timer
    resetSessionTimer();
    
    // Make functions globally available
    window.extendSession = extendSession;
    window.logoutUser = logoutUser;
});
</script>

{{-- if (pc_dark_layout == 'default') {
<script>
  if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
    dark_layout = 'true';
  } else {
    dark_layout = 'false';
  }
  layout_change_default();
  if (dark_layout == 'true') {
    layout_change('dark');
  } else {
    layout_change('light');
  }
</script>
}  --}}