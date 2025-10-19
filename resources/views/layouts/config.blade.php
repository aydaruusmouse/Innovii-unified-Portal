<script>
    // Global configuration for the application
    window.AppConfig = {
        apiBaseUrl: '{{ config("app_config.api.base_url") }}',
        baseUrl: '{{ url("/") }}',
        currentUrl: '{{ url()->current() }}',
        csrfToken: '{{ csrf_token() }}',
        appUrl: '{{ config("app.url") }}',
        appEnv: '{{ config("app.env") }}',
        appDebug: {{ config("app.debug") ? "true" : "false" }},
        apiTimeout: {{ config("app_config.api.timeout") }},
        apiRetryAttempts: {{ config("app_config.api.retry_attempts") }},
        features: {
            enableApiCaching: {{ config("app_config.features.enable_api_caching") ? "true" : "false" }},
            enableRealTimeUpdates: {{ config("app_config.features.enable_real_time_updates") ? "true" : "false" }},
            enableOfflineMode: {{ config("app_config.features.enable_offline_mode") ? "true" : "false" }}
        }
    };
    
    // Helper function to build API URLs
    window.buildApiUrl = function(endpoint) {
        return `${window.AppConfig.apiBaseUrl}/${endpoint}`;
    };
    
    // Helper function for API requests with CSRF token
    window.apiRequest = function(url, options = {}) {
        const defaultOptions = {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.AppConfig.csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            timeout: window.AppConfig.apiTimeout * 1000
        };
        
        return fetch(url, { ...defaultOptions, ...options });
    };
    
    // Helper function for API requests with retry logic
    window.apiRequestWithRetry = async function(url, options = {}, retryCount = 0) {
        try {
            const response = await window.apiRequest(url, options);
            if (!response.ok && retryCount < window.AppConfig.apiRetryAttempts) {
                console.log(`API request failed, retrying... (${retryCount + 1}/${window.AppConfig.apiRetryAttempts})`);
                await new Promise(resolve => setTimeout(resolve, 1000 * (retryCount + 1))); // Exponential backoff
                return window.apiRequestWithRetry(url, options, retryCount + 1);
            }
            return response;
        } catch (error) {
            if (retryCount < window.AppConfig.apiRetryAttempts) {
                console.log(`API request error, retrying... (${retryCount + 1}/${window.AppConfig.apiRetryAttempts})`);
                await new Promise(resolve => setTimeout(resolve, 1000 * (retryCount + 1)));
                return window.apiRequestWithRetry(url, options, retryCount + 1);
            }
            throw error;
        }
    };
</script>
