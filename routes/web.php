<?php

use App\Http\Controllers\ReportController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\SingleServiceController;
use App\Http\Controllers\ServiceReportController;
use App\Http\Controllers\EmergencyCreditController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CRBTController;

// Test route
Route::get('/test', function () {
    return 'Test route works!';
});

// Direct admin route for testing
Route::get('/admin-test', [DashboardController::class, 'index']);

// Auth routes
Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// Admin dashboard route (using different path to avoid conflict with public/admin directory)
// Protected
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard.simple');
});

// Test route with different name
Route::get('/admin-panel', function() {
    return 'Admin panel works!';
});

// Alternative admin route
Route::get('/admin-dashboard', [DashboardController::class, 'index'])->name('admin.dashboard.alt');

// Redirect root to login if guest, else dashboard
Route::get('/', function () {
    return auth()->check() ? redirect()->route('admin.dashboard.simple') : redirect()->route('admin.login');
});

// Protect SDF reports and related pages
Route::middleware('auth')->group(function () {
    Route::get('/all-services', function () {
        return view('admin.all_services');
    })->name('all_services');

    Route::get('/single-service', [ServiceReportController::class, 'index'])->name('single_service');
    Route::get('/status-wise-services', [ServiceReportController::class, 'statusWiseServices'])->name('status_wise_services');
    Route::get('/status-analysis', [ServiceReportController::class, 'statusAnalysis'])->name('status_analysis');
    Route::get('/status-insight', function () {
        return view('admin.subscription_insight');
    })->name('subscription_insight');
    Route::get('/overall-subscriber-report', [ServiceReportController::class, 'overallSubscriberReport'])->name('overall_subscriber_report');
});

// APIs remain accessible
Route::get('/api/v1/status-analysis', [ServiceReportController::class, 'getStatusAnalysisData']);
Route::get('/api/v1/overall-subscriber-report', [ServiceReportController::class, 'getOverallSubscriberReport']);

// Emergency Credit Routes (protected)
Route::middleware('auth')->prefix('emergency-credit')->group(function () {
    Route::get('/daily', [EmergencyCreditController::class, 'daily'])->name('emergency_credit.daily');
    Route::get('/daily/data', [EmergencyCreditController::class, 'dailyData']);
    Route::get('/top-users', [EmergencyCreditController::class, 'topUsers'])->name('emergency_credit.top_users');
    Route::get('/top-users/data', [EmergencyCreditController::class, 'topUsersData']);
    Route::get('/weekly', [EmergencyCreditController::class, 'weekly'])->name('emergency_credit.weekly');
    Route::get('/weekly/data', [EmergencyCreditController::class, 'weeklyData']);
    Route::get('/monthly', [EmergencyCreditController::class, 'monthly'])->name('emergency_credit.monthly');
    Route::get('/monthly/data', [EmergencyCreditController::class, 'monthlyData']);
    Route::get('/status', [EmergencyCreditController::class, 'status'])->name('emergency_credit.status');
    Route::get('/status/data', [EmergencyCreditController::class, 'statusData']);
    Route::get('/credit-type', [EmergencyCreditController::class, 'creditType'])->name('emergency_credit.credit_type');
    Route::get('/credit-type/data', [EmergencyCreditController::class, 'creditTypeData']);
    Route::get('/status-wise-services', [EmergencyCreditController::class, 'statusWiseService'])->name('status.wise.services');
    Route::get('/api/v1/status-wise-report', [EmergencyCreditController::class, 'statusWiseServiceData']);
    
    // Revenue Reports
    Route::get('/revenue-summary', [EmergencyCreditController::class, 'revenueSummary'])->name('emergency_credit.revenue_summary');
    Route::get('/revenue-data-only', [EmergencyCreditController::class, 'revenueDataOnly'])->name('emergency_credit.revenue_data_only');
    Route::get('/revenue-with-balance', [EmergencyCreditController::class, 'revenueWithBalance'])->name('emergency_credit.revenue_with_balance');
});

// API Routes
Route::get('/api/v1/dashboard-stats', [ServiceReportController::class, 'getDashboardStats']);

// Emergency Credit API Routes
Route::prefix('api/v1/emergency-credit')->group(function () {
    Route::get('/revenue-summary/data', [EmergencyCreditController::class, 'revenueSummaryData']);
    Route::get('/revenue-data-only/data', [EmergencyCreditController::class, 'revenueDataOnlyData']);
    Route::get('/revenue-with-balance/data', [EmergencyCreditController::class, 'revenueWithBalanceData']);
});

// CRBT Routes (protected)
Route::middleware('auth')->prefix('crbt')->name('crbt.')->group(function () {
    // Core Reports
    Route::get('/daily-mis', [CRBTController::class, 'dailyMIS'])->name('daily_mis');
    Route::get('/hourly-mis', [CRBTController::class, 'hourlyMIS'])->name('hourly_mis');
    Route::get('/interface-sub-unsub', [CRBTController::class, 'interfaceSubUnsub'])->name('interface_sub_unsub');
    Route::get('/interface-tone', [CRBTController::class, 'interfaceTone'])->name('interface_tone');
    Route::get('/status-cycle', [CRBTController::class, 'statusCycle'])->name('status_cycle');
    Route::get('/hlr-activations', [CRBTController::class, 'hlrActivations'])->name('hlr_activations');
    Route::get('/user-info', [CRBTController::class, 'userInfo'])->name('user_info');
    Route::get('/user-tone-info', [CRBTController::class, 'userToneInfo'])->name('user_tone_info');
    Route::get('/billing-charges', [CRBTController::class, 'billingCharges'])->name('billing_charges');

    // Corporate CRBT Reports
    Route::get('/corporate-info', [CRBTController::class, 'corporateInfo'])->name('corporate_info');
    Route::get('/corporate-users', [CRBTController::class, 'corporateUsers'])->name('corporate_users');

    // Backup Reports
    Route::get('/backup-reports', [CRBTController::class, 'backupReports'])->name('backup_reports');
});

// CRBT API Routes for AJAX
Route::prefix('api/crbt')->name('api.crbt.')->group(function () {
    Route::get('/daily-mis', [CRBTController::class, 'getDailyMISData'])->name('daily_mis');
    Route::get('/hourly-mis', [CRBTController::class, 'getHourlyMISData'])->name('hourly_mis');
    Route::get('/interface-data', [CRBTController::class, 'getInterfaceData'])->name('interface_data');
});
