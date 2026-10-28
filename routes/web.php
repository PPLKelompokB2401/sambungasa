<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\PublicNeedController;
use App\Http\Controllers\Public\PublicSchoolController;
use App\Http\Controllers\Public\RegionController;
use App\Http\Controllers\Public\StatisticsController;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\EvidenceController;
use App\Http\Controllers\NotificationController;

use App\Http\Controllers\School\DashboardController as SchoolDashboardController;
use App\Http\Controllers\School\ProfileController as SchoolProfileController;
use App\Http\Controllers\School\NeedController as SchoolNeedController;
use App\Http\Controllers\School\SchoolPledgeController;

use App\Http\Controllers\Contributor\DashboardController as ContributorDashboardController;
use App\Http\Controllers\Contributor\ProfileController as ContributorProfileController;
use App\Http\Controllers\Contributor\PledgeController as ContributorPledgeController;

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\SchoolVerificationController;
use App\Http\Controllers\Admin\NeedModerationController;
use App\Http\Controllers\Admin\PledgeManagementController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\ActivityLogController as AdminActivityLogController;

/*
|--------------------------------------------------------------------------
| Web Routes - SambungAsa
|--------------------------------------------------------------------------
*/

// ==========================================
// 1. PUBLIC DISCOVERY & INFORMATION (F-08, F-09, F-16, F-18)
// ==========================================
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('needs')->name('public.needs.')->group(function () {
    Route::get('/', [PublicNeedController::class, 'index'])->name('index');
    Route::get('/{need}', [PublicNeedController::class, 'show'])->name('show');
});

Route::prefix('schools')->name('public.schools.')->group(function () {
    Route::get('/', [PublicSchoolController::class, 'index'])->name('index');
    Route::get('/{school}', [PublicSchoolController::class, 'show'])->name('show');
});

Route::get('/regions', [RegionController::class, 'index'])->name('public.regions.index');
Route::get('/statistics', [StatisticsController::class, 'index'])->name('public.statistics');

// ==========================================
// 2. AUTHENTICATION (F-02, F-03)
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ==========================================
// 3. COMMON AUTHENTICATED USER ROUTES
// ==========================================
Route::middleware(['auth', 'active'])->group(function () {
    // Notifications (F-21)
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::match(['GET', 'POST'], '/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

    // Evidence Secured Download (F-13)
    Route::get('/evidence/{evidence}/download', [EvidenceController::class, 'download'])->name('evidence.download');
    Route::post('/evidence/{pledge}/upload', [EvidenceController::class, 'upload'])->name('evidence.upload');
});

// ==========================================
// 4. SCHOOL 3T PORTAL (F-01, F-04, F-05, F-06, F-07, F-14, F-15)
// ==========================================
Route::middleware(['auth', 'role:school', 'active'])->prefix('school')->name('school.')->group(function () {
    Route::get('/dashboard', [SchoolDashboardController::class, 'index'])->name('dashboard');

    // School Profile & 3T Verification (F-04)
    Route::get('/profile', [SchoolProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [SchoolProfileController::class, 'update'])->name('profile.update');

    // Needs CRUD (F-05, F-06, F-07)
    Route::resource('needs', SchoolNeedController::class);
    Route::post('/needs/{need}/close', [SchoolNeedController::class, 'close'])->name('needs.close');

    // Incoming Pledges, Receipt & Tracking (F-14, F-15)
    Route::get('/pledges', [SchoolPledgeController::class, 'index'])->name('pledges.index');
    Route::get('/pledges/{pledge}', [SchoolPledgeController::class, 'show'])->name('pledges.show');
    Route::post('/pledges/{pledge}/confirm-receipt', [SchoolPledgeController::class, 'confirmReceipt'])->name('pledges.confirmReceipt');
});

// ==========================================
// 5. CONTRIBUTOR / DONOR / VOLUNTEER PORTAL (F-10, F-11, F-12, F-13)
// ==========================================
Route::middleware(['auth', 'role:contributor', 'active'])->prefix('contributor')->name('contributor.')->group(function () {
    Route::get('/dashboard', [ContributorDashboardController::class, 'index'])->name('dashboard');

    // Profile & Legality
    Route::get('/profile', [ContributorProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ContributorProfileController::class, 'update'])->name('profile.update');

    // Pledges & Commitment (F-10, F-11)
    Route::get('/pledges', [ContributorPledgeController::class, 'index'])->name('pledges.index');
    Route::post('/needs/{need}/pledge', [ContributorPledgeController::class, 'store'])->name('pledges.store');
    Route::get('/pledges/{pledge}', [ContributorPledgeController::class, 'show'])->name('pledges.show');
});

// ==========================================
// 6. ADMIN CONTROL PORTAL (F-01, F-06, F-11, F-12, F-17, F-19, F-20, F-22, F-24)
// ==========================================
Route::middleware(['auth', 'role:admin', 'active'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // User Management (F-19, F-20)
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::post('/users/{user}/verify', [AdminUserController::class, 'verify'])->name('users.verify');
    Route::post('/users/{user}/reject', [AdminUserController::class, 'reject'])->name('users.reject');
    Route::post('/users/{user}/suspend', [AdminUserController::class, 'suspend'])->name('users.suspend');
    Route::post('/users/{user}/activate', [AdminUserController::class, 'activate'])->name('users.activate');

    // School 3T Verification (F-01, F-04)
    Route::get('/schools', [SchoolVerificationController::class, 'index'])->name('schools.index');
    Route::get('/schools/{school}', [SchoolVerificationController::class, 'show'])->name('schools.show');
    Route::post('/schools/{school}/verify', [SchoolVerificationController::class, 'verify'])->name('schools.verify');
    Route::post('/schools/{school}/reject', [SchoolVerificationController::class, 'reject'])->name('schools.reject');
    Route::post('/schools/{school}/request-revision', [SchoolVerificationController::class, 'requestRevision'])->name('schools.revision');

    // Need Moderation (F-06, F-20)
    Route::get('/needs', [NeedModerationController::class, 'index'])->name('needs.index');
    Route::get('/needs/{need}', [NeedModerationController::class, 'show'])->name('needs.show');
    Route::post('/needs/{need}/publish', [NeedModerationController::class, 'publish'])->name('needs.publish');
    Route::post('/needs/{need}/reject', [NeedModerationController::class, 'reject'])->name('needs.reject');
    Route::post('/needs/{need}/close', [NeedModerationController::class, 'close'])->name('needs.close');

    // Pledge & Tracking Management (F-11, F-12, F-15)
    Route::get('/pledges', [PledgeManagementController::class, 'index'])->name('pledges.index');
    Route::get('/pledges/{pledge}', [PledgeManagementController::class, 'show'])->name('pledges.show');
    Route::post('/pledges/{pledge}/approve', [PledgeManagementController::class, 'approve'])->name('pledges.approve');
    Route::post('/pledges/{pledge}/reject', [PledgeManagementController::class, 'reject'])->name('pledges.reject');
    Route::post('/pledges/{pledge}/status', [PledgeManagementController::class, 'updateStatus'])->name('pledges.updateStatus');

    // Reports & CSV Export (F-22)
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [AdminReportController::class, 'exportCsv'])->name('reports.export');

    // Activity Logs & Audit Trail (F-24)
    Route::get('/activity-logs', [AdminActivityLogController::class, 'index'])->name('activity-logs.index');
});
