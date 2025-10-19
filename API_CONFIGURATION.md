# API Configuration Guide

This application now uses dynamic API endpoints that automatically adapt to your server configuration.

## Configuration Files

### 1. Global JavaScript Configuration
The application includes a global configuration system in `resources/views/layouts/config.blade.php` that provides:

- **Dynamic API URLs**: Automatically uses the current domain and port
- **CSRF Token Management**: Automatic CSRF token inclusion in requests
- **Retry Logic**: Built-in retry mechanism for failed requests
- **Environment Detection**: Automatically detects development/production environment

### 2. Laravel Configuration
The configuration is managed through `config/app_config.php` and can be customized via environment variables:

```bash
# Add these to your .env file
API_BASE_URL=http://your-domain.com/api/v1
API_TIMEOUT=30
API_RETRY_ATTEMPTS=3
ENABLE_API_CACHING=true
ENABLE_REAL_TIME_UPDATES=false
ENABLE_OFFLINE_MODE=false
```

## Usage Examples

### Basic API Request
```javascript
// Old way (hardcoded)
fetch('http://127.0.0.1:8001/api/v1/offers')

// New way (dynamic)
window.apiRequest(window.buildApiUrl('offers'))
```

### With Retry Logic
```javascript
// For critical requests that need retry logic
window.apiRequestWithRetry(window.buildApiUrl('offers'))
```

### Custom Configuration
```javascript
// Access configuration values
console.log(window.AppConfig.apiBaseUrl)
console.log(window.AppConfig.features.enableApiCaching)
```

## Benefits

1. **Environment Agnostic**: Works in development, staging, and production
2. **Port Independent**: Automatically adapts to different ports
3. **Domain Independent**: Works with any domain or subdomain
4. **Maintainable**: Centralized configuration management
5. **Robust**: Built-in error handling and retry logic

## Migration

All existing hardcoded URLs have been automatically updated to use the new dynamic system. No manual changes are required.
