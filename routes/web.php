<?php
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\SingleServiceController;
use App\Http\Controllers\ServiceReportController;
use App\Http\Controllers\EmergencyCreditController;
// use App\Http\Controllers\OfferController;

Route::prefix('admin1')->name('admin.')->group(function() {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
   
     // Admin Dashboard Route
     Route::get('/admin', function () {
        return view('admin.index');
    })->name('dashboard');
});

Route::get('/', function () {
    return redirect()->route('status_wise_services');
});

// Add dashboard route
Route::get('/dashboard', function () {
    return view('admin.index');
})->name('dashboard');

Route::get('/all-services', function () {
    return view('admin.all_services');
})->name('all_services');

Route::get('/single-service', [ServiceReportController::class, 'index'])->name('single_service');
Route::get('/status-wise-services', [ServiceReportController::class, 'statusWiseServices'])->name('status_wise_services');
Route::get('/status-services', [ServiceReportController::class, 'statusWiseServices'])->name('status_services');

Route::get('/status-insight', function () {
    return view('admin.subscription_insight');
})->name('subscription_insight');
// // web.php (or routes file)

// Route::prefix('admin1')->name('admin.')->group(function() {
//     Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
//     Route::post('/register', [AuthController::class, 'register']);
// });


// Route::get('/admin1/register', [AuthController::class, 'showRegistrationForm'])->name('
// admin.register');
// Route::post('/admin1/register', [AuthController::class, 'register']);
// Route::get('/admin/forget-password', [AuthController::class, 'showForgetPasswordForm
// '])->name('admin.forget.password');
// Route::post('/admin/forget-password', [AuthController::class, 'sendForgetPasswordEmail
// ']);
// Route::get('/admin/reset-password/{token}', [AuthController::class, 'showResetPassword
// Form'])->name('admin.reset.password');
// Route::post('/admin/reset-password', [AuthController::class, 'resetPassword']);
// Route::get('/admin/home', [AuthController::class, 'home'])->name('admin.home
// ');
// Route::get('/admin/dashboard', [AuthController::class, 'dashboard'])->name('admin
// .dashboard');
// Route::get('/admin/profile', [AuthController::class, 'profile'])->name('admin.profile
// ');
// Route::get('/admin/setting', [AuthController::class, 'setting'])->name('admin
// .setting');
// Route::get('/admin/setting/update', [AuthController::class, 'updateSetting'])->name
// ('admin.setting.update');
// Route::get('/admin/setting/permission', [AuthController::class, 'permission'])->name
// ('admin.setting.permission');
// Route::get('/admin/setting/permission/update', [AuthController::class, 'updatePermission
// '])->name('admin.setting.permission.update');
// Route::get('/admin/setting/role', [AuthController::class, 'role'])->name
// ('admin.setting.role');
// Route::get('/admin/setting/role/update', [AuthController::class, 'updateRole
// '])->name('admin.setting.role.update');
// Route::get('/admin/setting/user', [AuthController::class, 'user'])->name
// ('admin.setting.user');
// Route::get('/admin/setting/user/update', [AuthController::class, 'updateUser
// '])->name('admin.setting.user.update');
// Route::get('/admin/setting/user/delete', [AuthController::class, 'deleteUser
// '])->name('admin.setting.user.delete');

Route::get('/admin1', function () {
    return view('admin.index');
});

// routes/web.php
// Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
// Route::get('/admin/vas-services', [VasServiceController::class, 'index'])->name('admin.vas-services');
// Route::get('/admin/orders', [OrderController::class, 'index'])->name('admin.orders');



// Route::get('/admin', function () {
//     return view('admin.dashboard');
// })->name('admin.dashboard');

// Route::middleware(['auth'])->group(function () {
//     Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
// });
// Route::get('/offers', [OfferController::class, 'getOffers']);

// Emergency Credit Routes
Route::prefix('emergency-credit')->group(function () {
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
});
