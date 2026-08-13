<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleBasedDashboardController;
use App\Http\Controllers\Web\EmployeeController;
use App\Http\Controllers\Web\PayrollController;
use App\Http\Controllers\Web\DepartmentController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\Web\EmployeeDashboardController;
use App\Http\Controllers\Web\ReportController;
use App\Http\Controllers\Web\PeriodManagementController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public routes (only accessible to guests)
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    // Add 'log.login' middleware to the login POST route
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    // Google Auth Routes
    Route::get('/auth/google', [\App\Http\Controllers\Web\GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [\App\Http\Controllers\Web\GoogleAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

    // Microsoft Auth Routes
    Route::get('/auth/microsoft', [\App\Http\Controllers\Web\MicrosoftAuthController::class, 'redirectToMicrosoft'])->name('auth.microsoft');
    Route::get('/auth/microsoft/callback', [\App\Http\Controllers\Web\MicrosoftAuthController::class, 'handleMicrosoftCallback'])->name('auth.microsoft.callback');
    Route::get('/notifications/login-logs', [App\Http\Controllers\NotificationController::class, 'getLoginLogs'])
        ->name('notifications.login-logs');

    // Password Reset Routes
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Protected routes
Route::middleware(['auth', 'require.timein'])->group(function () {
    // Notifications
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])
        ->name('notifications.index')
        ->middleware('role:admin,hr,manager');
    // Personal "my requests" status-change notifications — available to every role.
    Route::get('/notifications/mine', [App\Http\Controllers\NotificationController::class, 'myNotifications'])
        ->name('notifications.mine');
    Route::post('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markNotificationRead'])
        ->name('notifications.read');
    Route::post('/notifications/read-all', [App\Http\Controllers\NotificationController::class, 'markAllNotificationsRead'])
        ->name('notifications.read-all');

    // Universal search — available to every authenticated role.
    // Access/company scoping happens inside SearchController, not here.
    // Throttled since it's hit on every debounced keystroke from the client.
    Route::get('/search', [App\Http\Controllers\Web\SearchController::class, 'index'])->name('search')->middleware('throttle:60,1');
    Route::get('/search/modules', [App\Http\Controllers\Web\SearchController::class, 'modules'])->name('search.modules')->middleware('throttle:60,1');

    // Dashboard
    Route::get('/dashboard', [RoleBasedDashboardController::class, 'index'])->name('dashboard');
    Route::get('/payroll/manage', [PayrollController::class, 'index'])->name('payroll.manage');
    Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll.index');
    // View-only, department-scoped payroll for managers. Same controller
    // method as payroll.index — it branches internally on $user->role so
    // the query/blade logic for scoping and read-only mode lives in one place.
    Route::get('/payroll/team', [PayrollController::class, 'index'])
        ->name('payroll.team')
        ->middleware('role:manager');
    Route::get('/payroll-runs', [PayrollController::class, 'runs'])->name('payroll.runs');
    // Payroll calculation and the post-generation workflow belong to Payroll.
    // Attendance Period Management only prepares and validates cutoff data.
    Route::prefix('payroll/periods')->name('payroll.periods.')->middleware('role:admin,hr')->group(function () {
        Route::get('/{period}/preview', [PeriodManagementController::class, 'previewPayroll'])->name('preview');
        Route::get('/{period}/preview/pdf', [PeriodManagementController::class, 'previewPayroll'])->name('preview-pdf');
        Route::post('/{period}/generate', [PeriodManagementController::class, 'generatePayroll'])->name('generate');
        Route::get('/{period}', [PeriodManagementController::class, 'showPayrollSummary'])->name('review');
        Route::post('/{period}/return-to-processing', [PeriodManagementController::class, 'returnToProcessing'])->name('return-to-processing');
        Route::post('/{period}/finalize', [PeriodManagementController::class, 'finalizePayroll'])->name('finalize');
        Route::post('/{period}/lock', [PeriodManagementController::class, 'lockPayroll'])->name('lock');
        Route::get('/{period}/export', [PeriodManagementController::class, 'exportPayroll'])->name('export');
    });

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Employee routes
    Route::get('/employees/bio-zk', [EmployeeController::class, 'bioZk'])->name('employees.bio-zk')->middleware('role:admin,hr');
    Route::get('/employees/ytd-info', [EmployeeController::class, 'ytdInfo'])->name('employees.ytd-info')->middleware('role:admin,hr');
    Route::get('/employees/info', [EmployeeController::class, 'employeeInfo'])->name('employees.info')->middleware('role:admin,hr');
    Route::get('/employees/info/search', [EmployeeController::class, 'employeeInfoSearch'])->name('employees.info.search')->middleware('role:admin,hr');
    Route::post('/employees/info/save', [EmployeeController::class, 'saveEmployeeInfo'])->name('employees.info.save')->middleware('role:admin,hr');
    Route::get('/employees/education-training-rating', [EmployeeController::class, 'educationTrainingRating'])->name('employees.education-training-rating')->middleware('role:admin,hr');
    Route::get('/employees/other-employee-info', [EmployeeController::class, 'otherEmployeeInfo'])->name('employees.other-employee-info')->middleware('role:admin,hr');
    Route::post('/employees/other-employee-info', [EmployeeController::class, 'saveOtherEmployeeInfo'])->name('employees.other-employee-info.save')->middleware('role:admin,hr');
    Route::post('/employees/other-employee-info/photo', [EmployeeController::class, 'uploadOtherEmployeePhoto'])->name('employees.other-employee-info.photo')->middleware('role:admin,hr');
    Route::post('/employees/other-employee-info/photo/clear', [EmployeeController::class, 'clearOtherEmployeePhoto'])->name('employees.other-employee-info.photo.clear')->middleware('role:admin,hr');
    Route::get('/employees/prev-emp-oth', [EmployeeController::class, 'prevEmpOth'])->name('employees.prev-emp-oth')->middleware('role:admin,hr');
    Route::post('/employees/prev-emp-oth', [EmployeeController::class, 'savePrevEmpOth'])->name('employees.prev-emp-oth.save')->middleware('role:admin,hr');
    Route::get('/employees/documents', [EmployeeController::class, 'documents'])->name('employees.documents')->middleware('role:admin,hr');
    Route::post('/employees/documents', [EmployeeController::class, 'saveDocuments'])->name('employees.documents.save')->middleware('role:admin,hr');
    Route::post('/employees/documents/folders', [EmployeeController::class, 'createDocumentFolder'])->name('employees.documents.folders.create')->middleware('role:admin,hr');
    Route::delete('/employees/documents/folders/{folder}', [EmployeeController::class, 'deleteDocumentFolder'])->name('employees.documents.folders.delete')->middleware('role:admin,hr');
    Route::resource('employees', EmployeeController::class);
    Route::get('/employees/{employee}/payroll', [EmployeeController::class, 'payroll'])->name('employees.payroll');

    // Department routes
    Route::get('/departments/archived', [DepartmentController::class, 'archived'])->name('departments.archived');
    Route::put('/departments/{department}/restore', [DepartmentController::class, 'restore'])->name('departments.restore');
    Route::resource('departments', DepartmentController::class);
    Route::get('/departments/{department}/employees', [DepartmentController::class, 'employees'])->name('departments.employees');
    Route::put('/departments/{department}/manager', [DepartmentController::class, 'updateManager'])->name('departments.manager.update');

    // Position routes
    Route::post(
        '/positions/{position}/restore',
        [App\Http\Controllers\PositionController::class, 'restore']
    )->name('positions.restore');

    // DELETE /positions/{position} now archives the position instead of permanently deleting it.
    Route::resource('positions', App\Http\Controllers\PositionController::class);

    // Payroll generation from Period Management
    Route::get('/payrolls/generate-from-period', [PayrollController::class, 'generateFromPeriod'])->name('payrolls.generate-from-period');
    Route::post('/payrolls/generate-from-period', [PayrollController::class, 'generateFromPeriodData'])->name('payrolls.generate-from-period.store');


    // Payroll routes
    Route::resource('payroll-templates', App\Http\Controllers\Web\PayrollTemplateController::class);
    Route::post('payroll-templates/{id}/restore', [App\Http\Controllers\Web\PayrollTemplateController::class, 'restore'])->name('payroll-templates.restore');
    // Report URLs must be registered before the /payrolls/{payroll} resource
    // route so "reports" is never interpreted as a payroll record ID.
    Route::get('/payrolls/reports/summary', [PayrollController::class, 'summary'])->name('payrolls.summary');
    Route::get('/payrolls/reports/monthly', [PayrollController::class, 'monthlyReport'])->name('payrolls.monthly');
    Route::resource('payrolls', PayrollController::class);
    Route::post('/payrolls/{payroll}/process', [PayrollController::class, 'process'])->name('payrolls.process');
    Route::post('/payrolls/{payroll}/pay', [PayrollController::class, 'payOne'])->name('payrolls.pay-one');

    // Additional payroll processing routes

    Route::post('/payrolls/process-payments', [PayrollController::class, 'processPayments'])->name('payrolls.process-payments');
    Route::post('/payrolls/bulk-approve', [PayrollController::class, 'bulkApprove'])->name('payrolls.bulk-approve');
    Route::post('/payrolls/export-payroll', [PayrollController::class, 'exportPayroll'])->name('payrolls.export-payroll');
    Route::post('/payrolls/generate-payroll', [PayrollController::class, 'generatePayroll'])->name('payrolls.generate-payroll');
    Route::post('/payroll/generate', [\App\Http\Controllers\Web\PayrollController::class, 'generate'])->name('payrolls.generate');
    Route::get('/ajax/payrolls/approved', [\App\Http\Controllers\Web\PayrollController::class, 'getApprovedPayrolls']);
    Route::post('/ajax/payrolls/process-payments', [\App\Http\Controllers\Web\PayrollController::class, 'processPaymentsApi']);
    Route::get('/ajax/payrolls/status-count', [PayrollController::class, 'getPayrollStatusCount']);
    Route::post('/ajax/payrolls/bulk-approve', [PayrollController::class, 'ajaxBulkApprove']);
    Route::get('/ajax/payrolls/payment-status', [PayrollController::class, 'checkPaidStatus']);
    Route::get('/ajax/payrolls/pending', [\App\Http\Controllers\Web\PayrollController::class, 'getPendingPayrolls']);
    Route::post('/ajax/payrolls/approve-all', [\App\Http\Controllers\Web\PayrollController::class, 'approveAllViaAjax']);
    Route::post('/payroll/complete-workflow', [\App\Http\Controllers\Web\PayrollController::class, 'completePayrollWorkflow'])->name('payrolls.complete-workflow');
    Route::post('/payrolls/generate-payslips', [PayrollController::class, 'generatePayslips'])->name('payrolls.generate-payslips');
    Route::get('/payrolls/{payroll}/download-payslip', [PayrollController::class, 'downloadPayslip'])->name('payrolls.download-payslip');
    Route::get('/payrolls/download-all-payslips', [PayrollController::class, 'downloadAllPayslips'])->name('payrolls.download-all-payslips');
    Route::post('/payrolls/{payroll}/generate-payslip', [PayrollController::class, 'generateSinglePayslip'])->name('payrolls.generate-payslip');
    Route::get('/payrolls/download-all-payslips', [PayrollController::class, 'downloadAllPayslips'])->name('payrolls.download-all-payslips');
    Route::post('/payrolls/mark-as-paid', [PayrollController::class, 'markAsPaid'])->name('payrolls.mark-as-paid');
    Route::post('/payrolls/approve-selected', [PayrollController::class, 'approveSelected'])->name('payrolls.approve-selected');
    Route::post('/payrolls/process-selected-payments', [PayrollController::class, 'processSelectedPayments'])->name('payrolls.process-selected-payments');
    Route::post('/payrolls/export-detailed', [PayrollController::class, 'exportDetailed'])->name('payrolls.export-detailed');
    Route::post('/payrolls/mark-as-paid', [PayrollController::class, 'markAsPaid'])
        ->name('payrolls.mark-as-paid')
        ->middleware('auth');
    Route::post('/payrolls/export-with-calculations', [PayrollController::class, 'exportWithCalculations'])
        ->name('payrolls.export-with-calculations');
    Route::post('/payrolls/export-with-calculations', [PayrollController::class, 'exportWithCalculations'])->name('payrolls.export-with-calculations');
    Route::post('/payrolls/export-with-calculations', [PayrollController::class, 'exportWithCalculations'])
        ->name('payrolls.export-with-calculations')
        ->middleware('auth');
    Route::post('/payrolls/simple-export', [PayrollController::class, 'simpleExport'])->name('payrolls.simple-export');

    Route::post('/payrolls/generate-payslips', [PayrollController::class, 'generatePayslips'])
        ->name('payrolls.generate-payslips')
        ->middleware('auth');

    Route::get('/payrolls/download-all-payslips', [PayrollController::class, 'downloadAllPayslips'])
        ->name('payrolls.download-all-payslips')
        ->middleware('auth');

    Route::post('/payrolls/mark-as-paid', [PayrollController::class, 'markAsPaid'])
        ->name('payrolls.mark-as-paid')
        ->middleware('auth');
    Route::get('/debug-payroll-match', [PayrollController::class, 'debugPayrollMatching']);

    Route::post('/payroll/{payroll}/approve', [PayrollController::class, 'approvePayroll'])->name('payroll.approve');
    Route::post('/payroll/{payroll}/reject', [PayrollController::class, 'rejectPayroll'])->name('payroll.reject');
    Route::get('/payroll/{payroll}/download-payslip', [PayrollController::class, 'downloadViewPayslip'])->name('payroll.download-payslip');


    // Payroll route aliases for consistency
    Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll.index');
    Route::get('/payroll/{payroll}', [PayrollController::class, 'show'])->name('payroll.show');

    // Debug route for payroll functions
    Route::get('/test-payroll-functions', function () {
        $service = app(\App\Services\PayrollGenerationService::class);

        // Test components
        $paymentModelExists = class_exists(\App\Models\Payment::class);
        $dompdfExists = class_exists(\Barryvdh\DomPDF\Facade\Pdf::class);
        $payrolls = \App\Models\Payroll::where('status', 'approved')->take(2)->get();
        $storageWritable = is_writable(storage_path());
        $payslipColumnExists = \Illuminate\Support\Facades\Schema::hasColumn('payrolls', 'payslip_file');

        return [
            'payment_model_exists' => $paymentModelExists,
            'dompdf_exists' => $dompdfExists,
            'storage_writable' => $storageWritable,
            'payslip_column_exists' => $payslipColumnExists,
            'approved_payrolls_count' => $payrolls->count(),
            'payrolls' => $payrolls->toArray()
        ];
    });

    Route::get('/debug-payroll-dates', function () {
        $payrolls = \App\Models\Payroll::select('id', 'employee_id', 'pay_period_start', 'pay_period_end', 'status', 'created_at')
            ->orderBy('pay_period_start', 'desc')
            ->limit(20)
            ->get();

        return response()->json([
            'total_payrolls' => \App\Models\Payroll::count(),
            'pending_count' => \App\Models\Payroll::where('status', 'pending')->count(),
            'approved_count' => \App\Models\Payroll::where('status', 'approved')->count(),
            'recent_payrolls' => $payrolls,
            'unique_periods' => \App\Models\Payroll::select('pay_period_start', 'pay_period_end')
                ->distinct()
                ->orderBy('pay_period_start', 'desc')
                ->get()
        ]);
    });

    Route::get('/debug-current-payrolls', function () {
        $allPayrolls = \App\Models\Payroll::select(
            'id',
            'employee_id',
            'pay_period_start',
            'pay_period_end',
            'status',
            'created_at'
        )
            ->with(['employee:id,employee_id,first_name,last_name'])
            ->orderBy('pay_period_start', 'desc')
            ->limit(50)
            ->get();

        $uniquePeriods = \App\Models\Payroll::select('pay_period_start', 'pay_period_end')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('pay_period_start', 'pay_period_end')
            ->orderBy('pay_period_start', 'desc')
            ->get();

        return response()->json([
            'total_payrolls' => \App\Models\Payroll::count(),
            'status_counts' => [
                'pending' => \App\Models\Payroll::where('status', 'pending')->count(),
                'approved' => \App\Models\Payroll::where('status', 'approved')->count(),
                'paid' => \App\Models\Payroll::where('status', 'paid')->count(),
            ],
            'recent_payrolls' => $allPayrolls,
            'unique_periods' => $uniquePeriods,
            'database_date_format' => 'Check if dates are YYYY-MM-DD or include time'
        ]);
    });

    // Payroll Adjustments and Benefits
    Route::prefix('payroll-adjustments')->name('payroll-adjustments.')->middleware('role:admin,hr')->group(function () {
        Route::get('/', [App\Http\Controllers\Web\PayrollAdjustmentController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Web\PayrollAdjustmentController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Web\PayrollAdjustmentController::class, 'store'])->name('store');
        Route::get('/{payrollAdjustment}/edit', [App\Http\Controllers\Web\PayrollAdjustmentController::class, 'edit'])->name('edit');
        Route::put('/{payrollAdjustment}', [App\Http\Controllers\Web\PayrollAdjustmentController::class, 'update'])->name('update');
        Route::delete('/{payrollAdjustment}', [App\Http\Controllers\Web\PayrollAdjustmentController::class, 'destroy'])->name('destroy');
    });

    // Loan Type and Loan routes
    Route::prefix('loan-types')->name('loan-types.')->middleware('role:admin,hr')->group(function () {
    Route::get('/', [App\Http\Controllers\Web\LoanTypeController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\Web\LoanTypeController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\Web\LoanTypeController::class, 'store'])->name('store');
    Route::get('/{loanType}/edit', [App\Http\Controllers\Web\LoanTypeController::class, 'edit'])->name('edit');
    Route::put('/{loanType}', [App\Http\Controllers\Web\LoanTypeController::class, 'update'])->name('update');
    Route::delete('/{loanType}', [App\Http\Controllers\Web\LoanTypeController::class, 'destroy'])->name('destroy');
});

Route::prefix('loans')->name('loans.')->middleware('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\Web\LoanController::class, 'index'])->name('index');

    // Any account linked to an employee may submit its own loan request.
    // HR/Admin management actions remain separately protected below.
    // This must be registered before the
    // /{loan} show route below, or "/create" gets matched as a loan ID
    // and 404s instead of reaching this route.
    Route::middleware('role:employee,manager,hr,admin')->group(function () {
        Route::get('/create', [App\Http\Controllers\Web\LoanController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Web\LoanController::class, 'store'])->name('store');
    });

    // Managers may review employee loan requests, while destructive loan
    // administration remains HR/Admin only.
    // reason - '/employee/{employee}/history' would otherwise be captured
    // by '/{loan}' first.
    Route::middleware('role:admin,hr,manager')->group(function () {
        Route::post('/{loan}/approve', [App\Http\Controllers\Web\LoanController::class, 'approve'])->name('approve');
        Route::post('/{loan}/reject', [App\Http\Controllers\Web\LoanController::class, 'reject'])->name('reject');
        Route::get('/employee/{employee}/history', [App\Http\Controllers\Web\LoanController::class, 'employeeHistory'])->name('employee-history');
    });

    Route::middleware('role:admin,hr')->group(function () {
        Route::delete('/{loan}', [App\Http\Controllers\Web\LoanController::class, 'destroy'])->name('destroy');
    });

    // /{loan} show must come last - a catch-all single-segment route, so
    // anything more specific above it needs to be registered first.
    Route::get('/{loan}', [App\Http\Controllers\Web\LoanController::class, 'show'])->name('show');
});


    // Schedule Management V2 routes
    Route::prefix('schedule-v2')->name('schedule-v2.')->middleware('role:admin,hr,manager')->group(function () {
        Route::get('/', [App\Http\Controllers\Web\ScheduleV2Controller::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Web\ScheduleV2Controller::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Web\ScheduleV2Controller::class, 'store'])->name('store');
        Route::post('/bulk-create', [App\Http\Controllers\Web\ScheduleV2Controller::class, 'bulkCreate'])->name('bulk-create');
        Route::delete('/bulk-delete', [App\Http\Controllers\Web\ScheduleV2Controller::class, 'bulkDelete'])->name('bulk-delete');
        Route::get('/statistics', [App\Http\Controllers\Web\ScheduleV2Controller::class, 'getStatistics'])->name('statistics');
        Route::get('/{schedule}', [App\Http\Controllers\Web\ScheduleV2Controller::class, 'show'])->name('show');
        Route::get('/{schedule}/edit', [App\Http\Controllers\Web\ScheduleV2Controller::class, 'edit'])->name('edit');
        Route::put('/{schedule}', [App\Http\Controllers\Web\ScheduleV2Controller::class, 'update'])->name('update');
        Route::delete('/{schedule}', [App\Http\Controllers\Web\ScheduleV2Controller::class, 'destroy'])->name('destroy');
    });

    Route::prefix('schedule-templates')->name('schedule-templates.')->middleware('role:admin,hr')->group(function () {
        Route::get('/', [App\Http\Controllers\Web\ScheduleTemplateController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Web\ScheduleTemplateController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Web\ScheduleTemplateController::class, 'store'])->name('store');
        Route::get('/{scheduleTemplate}/edit', [App\Http\Controllers\Web\ScheduleTemplateController::class, 'edit'])->name('edit');
        Route::put('/{scheduleTemplate}', [App\Http\Controllers\Web\ScheduleTemplateController::class, 'update'])->name('update');
        Route::delete('/{scheduleTemplate}', [App\Http\Controllers\Web\ScheduleTemplateController::class, 'destroy'])->name('destroy');
    });

    // Company routes
    Route::resource('companies', App\Http\Controllers\Web\CompanyController::class);
    Route::post('/companies/switch', [App\Http\Controllers\Web\CompanyController::class, 'switchCompany'])->name('companies.switch');

    // HR Profile and Settings routes
    Route::prefix('hr')->name('hr.')->group(function () {
        // Header dropdown "Profile" page
        Route::get('/profile', [App\Http\Controllers\Web\HrController::class, 'profile'])->name('profile');
        Route::put('/profile', [App\Http\Controllers\Web\HrController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/photo', [App\Http\Controllers\Web\HrController::class, 'updateProfilePhoto'])->name('profile.photo.upload');

        Route::get('/settings', [App\Http\Controllers\Web\HrController::class, 'settings'])->name('settings');
        Route::put('/settings', [App\Http\Controllers\Web\HrController::class, 'updateSettings'])->name('settings.update');
        Route::put('/settings/password', [App\Http\Controllers\Web\HrController::class, 'updatePassword'])->name('settings.password');
        Route::post('/export-data', [App\Http\Controllers\Web\HrController::class, 'exportData'])->name('export-data');
        Route::post('/backup-data', [App\Http\Controllers\Web\HrController::class, 'backupData'])->name('backup-data');
        Route::get('/sessions', [App\Http\Controllers\Web\HrController::class, 'getUserSessions'])->name('sessions');
        Route::delete('/sessions/{session}', [App\Http\Controllers\Web\HrController::class, 'terminateSession'])->name('sessions.terminate');
        Route::delete('/sessions', [App\Http\Controllers\Web\HrController::class, 'terminateAllOtherSessions'])->name('sessions.terminate-all');
        Route::post('/track-session', [App\Http\Controllers\Web\HrController::class, 'trackLoginSession'])->name('track-session');

        // My Information routes (employee self-service, scoped to the
        // signed-in user's own employee record - no employee picker)
        Route::prefix('my-information')->name('my-information.')->group(function () {
            // Sidebar "Personal Information" page - separate route/view from
            // hr.profile above, though both edit the same employee record.
            Route::get('/info', [App\Http\Controllers\Web\HrController::class, 'myPersonalInfo'])->name('info');
            Route::put('/info', [App\Http\Controllers\Web\HrController::class, 'updateMyPersonalInfo'])->name('info.update');
            Route::post('/info/photo', [App\Http\Controllers\Web\HrController::class, 'uploadMyPersonalInfoPhoto'])->name('info.photo.upload');

            Route::get('/other-info', [App\Http\Controllers\Web\HrController::class, 'myOtherInfo'])->name('other-info');
            Route::post('/other-info', [App\Http\Controllers\Web\HrController::class, 'saveMyOtherInfo'])->name('other-info.save');
            Route::post('/other-info/photo', [App\Http\Controllers\Web\HrController::class, 'uploadMyOtherInfoPhoto'])->name('other-info.photo');
            Route::post('/other-info/photo/clear', [App\Http\Controllers\Web\HrController::class, 'clearMyOtherInfoPhoto'])->name('other-info.photo.clear');

            Route::get('/education-training-rating', [App\Http\Controllers\Web\HrController::class, 'myEducationTrainingRating'])->name('education-training-rating');

            Route::get('/prev-emp-oth', [App\Http\Controllers\Web\HrController::class, 'myPrevEmpOth'])->name('prev-emp-oth');
            Route::post('/prev-emp-oth', [App\Http\Controllers\Web\HrController::class, 'saveMyPrevEmpOth'])->name('prev-emp-oth.save');

            Route::get('/documents', [App\Http\Controllers\Web\HrController::class, 'myDocuments'])->name('documents');
            Route::post('/documents', [App\Http\Controllers\Web\HrController::class, 'saveMyDocuments'])->name('documents.save');
            Route::post('/documents/folders', [App\Http\Controllers\Web\HrController::class, 'createMyDocumentFolder'])->name('documents.folders.create');
            Route::delete('/documents/folders/{folder}', [App\Http\Controllers\Web\HrController::class, 'deleteMyDocumentFolder'])->name('documents.folders.delete');

            Route::get('/ytd-info', [App\Http\Controllers\Web\HrController::class, 'myYtdInfo'])->name('ytd-info');
            Route::get('/bio-zk', [App\Http\Controllers\Web\HrController::class, 'myBioZk'])->name('bio-zk');
        });

        // Contact HR routes
        Route::get('/contact', [App\Http\Controllers\Web\HrContactController::class, 'index'])->name('contact.index');
        Route::post('/contact', [App\Http\Controllers\Web\HrContactController::class, 'store'])->name('contact.store');
        Route::get('/contact/{hrContact}', [App\Http\Controllers\Web\HrContactController::class, 'show'])->name('contact.show');
        Route::post('/contact/{hrContact}/respond', [App\Http\Controllers\Web\HrContactController::class, 'respond'])->name('contact.respond');
        Route::get('/contacts/admin', [App\Http\Controllers\Web\HrContactController::class, 'admin'])->name('contacts.admin');
        Route::get('/messages', [App\Http\Controllers\Web\HrContactController::class, 'messages'])->name('messages.index');
        Route::get('/inbox/quick', [App\Http\Controllers\Web\HrContactController::class, 'quickInbox'])->name('inbox.quick');

        // Help & Support routes
        Route::get('/help-support', [App\Http\Controllers\Web\HelpSupportController::class, 'index'])->name('help-support');
        Route::post('/help-support/ticket', [App\Http\Controllers\Web\HelpSupportController::class, 'storeTicket'])->name('help-support-ticket-store');
    });

    // Attendance routes
    Route::prefix('attendance')->name('attendance.')->group(function () {
        // Time In/Out routes
        Route::get('/time-in-out', [App\Http\Controllers\Web\TimeInOutController::class, 'index'])->name('time-in-out');
        Route::post('/time-in', [App\Http\Controllers\Web\TimeInOutController::class, 'timeIn'])->name('time-in');
        Route::post('/time-out', [App\Http\Controllers\Web\TimeInOutController::class, 'timeOut'])->name('time-out');
        Route::post('/break-start', [App\Http\Controllers\Web\TimeInOutController::class, 'breakStart'])->name('break-start');
        Route::post('/break-end', [App\Http\Controllers\Web\TimeInOutController::class, 'breakEnd'])->name('break-end');
        Route::get('/status', [App\Http\Controllers\Web\TimeInOutController::class, 'getStatus'])->name('status');
        Route::get('/current-time', function () {
            // Use correct current date (December 19, 2024)
            $correctDate = \Carbon\Carbon::parse('2024-12-19 15:10:00', 'Asia/Manila');
            $now = \Carbon\Carbon::now('Asia/Manila');
            $timeDiff = $now->diffInSeconds($correctDate);
            $currentTime = $correctDate->addSeconds($timeDiff);

            return response()->json([
                'time' => $currentTime->format('H:i:s'),
                'date' => $currentTime->format('l, F j, Y'),
                'timestamp' => $currentTime->timestamp
            ]);
        })->name('current-time');

        // Add this inside the attendance route group or as a separate route
        Route::get('/api/employee/{employeeId}/approved-leave-dates', [App\Http\Controllers\Web\LeaveController::class, 'getApprovedLeaveDates'])
            ->name('api.employee.approved-leave-dates')
            ->middleware('auth');

        // Add these routes inside the attendance group
        Route::post('/leave-management/check-overlap', [App\Http\Controllers\Web\LeaveController::class, 'checkOverlap'])
            ->name('leave-management.check-overlap');

        Route::get('/api/employee/{employeeId}/approved-leave-dates', [App\Http\Controllers\Web\LeaveController::class, 'getApprovedLeaveDates'])
            ->name('api.employee.approved-leave-dates');

        // General attendance routes
        Route::get('/daily', [App\Http\Controllers\Web\AttendanceController::class, 'daily'])->name('daily');
        Route::get('/daily/export/{format}', [App\Http\Controllers\Web\AttendanceController::class, 'exportDaily'])->name('daily.export');
        Route::get('/timekeeping', [App\Http\Controllers\Web\AttendanceController::class, 'timekeeping'])->name('timekeeping');
        Route::get('/timekeeping/export/{format}', [App\Http\Controllers\Web\AttendanceController::class, 'exportTimekeeping'])->name('timekeeping.export');
        Route::get('/reports', [App\Http\Controllers\Web\AttendanceController::class, 'reports'])->name('reports');
        Route::get('/reports/export/{format}', [App\Http\Controllers\Web\AttendanceController::class, 'exportReports'])->name('reports.export');
        Route::get('/statistics', [App\Http\Controllers\Web\AttendanceController::class, 'getStatistics'])->name('statistics');

        // Attendance settings (admin/hr only)
        Route::middleware(['role:admin,hr'])->group(function () {
            Route::get('/settings', [App\Http\Controllers\Web\AttendanceController::class, 'settings'])->name('settings');
            Route::put('/settings', [App\Http\Controllers\Web\AttendanceController::class, 'updateSettings'])->name('settings.update');
        });

        // Import DTR routes
        Route::get('/import-dtr', [App\Http\Controllers\Web\AttendanceController::class, 'importDtr'])->name('import-dtr');
        Route::post('/import-dtr', [App\Http\Controllers\Web\AttendanceController::class, 'processImportDtr'])->name('import-dtr.process');
        Route::get('/import-dtr/review', [App\Http\Controllers\Web\AttendanceController::class, 'reviewImportDtr'])->name('import-dtr.review');
        Route::post('/import-dtr/confirm', [App\Http\Controllers\Web\AttendanceController::class, 'confirmImportDtr'])->name('import-dtr.confirm');
        Route::get('/temp-timekeeping', [App\Http\Controllers\Web\AttendanceController::class, 'tempTimekeeping'])->name('temp-timekeeping');
        Route::post('/temp-timekeeping/approve', [App\Http\Controllers\Web\AttendanceController::class, 'approveTempTimekeeping'])->name('temp-timekeeping.approve');

        // Attendance record management routes
        Route::get('/create-record', [App\Http\Controllers\Web\AttendanceController::class, 'createRecord'])->name('create-record');
        Route::post('/store-record', [App\Http\Controllers\Web\AttendanceController::class, 'storeRecord'])->name('store-record');
        Route::get('/edit-record/{id}', [App\Http\Controllers\Web\AttendanceController::class, 'editRecord'])->name('edit-record');
        Route::put('/update-record/{id}', [App\Http\Controllers\Web\AttendanceController::class, 'updateRecord'])->name('update-record');
        Route::delete('/delete-record/{id}', [App\Http\Controllers\Web\AttendanceController::class, 'deleteRecord'])->name('delete-record');

        // Attendance Schedule Reports routes (separate from main schedule management)
        Route::prefix('schedule')->name('attendance.schedule.')->group(function () {
            Route::get('/reports', [App\Http\Controllers\Web\AttendanceController::class, 'scheduleReports'])->name('reports');
            Route::get('/templates', [App\Http\Controllers\Web\AttendanceController::class, 'scheduleTemplates'])->name('templates');
        });

        // Period Management routes
        Route::prefix('period-management')
            ->name('period-management.')
            ->middleware('role:admin,hr')
            ->group(function () {
                Route::get('/', [PeriodManagementController::class, 'index'])
                    ->name('index');

                Route::get('/create', [PeriodManagementController::class, 'create'])
                    ->name('create');

                Route::post('/', [PeriodManagementController::class, 'store'])
                    ->name('store');

                Route::get('/{period}', [PeriodManagementController::class, 'show'])
                    ->name('show');

                Route::delete('/{period}', [PeriodManagementController::class, 'destroy'])
                    ->name('destroy');

                Route::patch('/{period}/status', [PeriodManagementController::class, 'updateStatus'])
                    ->name('status');

                Route::post('/{period}/refresh-cutoff', [PeriodManagementController::class, 'refreshCutoffData'])
                    ->name('refresh-cutoff');

                Route::post(
                    '/{period}/validate/{component}',
                    [PeriodManagementController::class, 'validateComponent']
                )->name('validate-component');

                Route::delete(
                    '/{period}/validate/{component}',
                    [PeriodManagementController::class, 'resetValidationComponent']
                )->name('reset-validation-component');

                Route::post(
                    '/{period}/generate-payroll',
                    [PeriodManagementController::class, 'generatePayroll']
                )->name('generate-payroll');

                Route::get('/{period}/preview-payroll', function ($period) {
                    return redirect()->route('payroll.periods.preview', $period);
                })->name('preview-payroll');

                Route::get('/{period}/payroll-summary', function ($period) {
                    return redirect()->route('payroll.periods.review', $period);
                })->name('payroll-summary');

                Route::post('/{period}/submit-for-review', [PeriodManagementController::class, 'submitForReview'])
                    ->name('submit-for-review');

                Route::post('/{period}/return-to-processing', [PeriodManagementController::class, 'returnToProcessing'])
                    ->name('return-to-processing');

                Route::post('/{period}/finalize-payroll', [PeriodManagementController::class, 'finalizePayroll'])
                    ->name('finalize-payroll');

                Route::post('/{period}/lock-payroll', [PeriodManagementController::class, 'lockPayroll'])
                    ->name('lock-payroll');

                Route::get('/{period}/export-payroll', [PeriodManagementController::class, 'exportPayroll'])
                    ->name('export-payroll');
            });
    });

    // Overtime routes
    Route::get('/overtime', [App\Http\Controllers\Web\OvertimeController::class, 'index'])->name('attendance.overtime');
    Route::get('/overtime/export/{format}', [App\Http\Controllers\Web\OvertimeController::class, 'exportOvertime'])->name('attendance.overtime.export');
    Route::post('/overtime', [App\Http\Controllers\Web\OvertimeController::class, 'store'])->name('attendance.overtime.store');
    Route::put('/overtime/{id}', [App\Http\Controllers\Web\OvertimeController::class, 'updatePending'])->name('attendance.overtime.update-pending');
    Route::post('/overtime/{id}/resubmit', [App\Http\Controllers\Web\OvertimeController::class, 'resubmit'])->name('attendance.overtime.resubmit');
    Route::post('/overtime/quick-submit', [App\Http\Controllers\Web\OvertimeController::class, 'quickSubmit'])->name('attendance.overtime.quick-submit');
    Route::post('/overtime/dismiss-reminder/{id}', [App\Http\Controllers\Web\OvertimeController::class, 'dismissReminder'])->name('attendance.overtime.dismiss-reminder');
    Route::put('/overtime/{id}/status', [App\Http\Controllers\Web\OvertimeController::class, 'updateStatus'])->name('attendance.overtime.update-status');
    Route::put('/overtime/{id}/edit-approved', [App\Http\Controllers\Web\OvertimeController::class, 'updateApproved'])->name('attendance.overtime.update-approved')->middleware('role:admin,hr,manager');
    Route::delete('/overtime/{id}/cancel', [App\Http\Controllers\Web\OvertimeController::class, 'cancel'])->name('attendance.overtime.cancel');
    Route::get('/overtime/statistics', [App\Http\Controllers\Web\OvertimeController::class, 'getStatistics'])->name('attendance.overtime.statistics');

    // Leave management routes
    Route::get('/leave-management', [App\Http\Controllers\Web\LeaveController::class, 'index'])->name('attendance.leave-management');
    Route::get('/leave-management/export/{format}', [App\Http\Controllers\Web\LeaveController::class, 'exportLeave'])->name('attendance.leave-management.export');
    Route::get('/leave-management/create', [App\Http\Controllers\Web\LeaveController::class, 'create'])->name('attendance.leave-management.create');
    Route::post('/leave-management', [App\Http\Controllers\Web\LeaveController::class, 'store'])->name('attendance.leave-management.store');
    Route::post('/leave-management/{id}/resubmit', [App\Http\Controllers\Web\LeaveController::class, 'resubmit'])->name('attendance.leave-management.resubmit');
    Route::put('/leave-management/{id}/status', [App\Http\Controllers\Web\LeaveController::class, 'updateStatus'])->name('attendance.leave-management.update-status');
    Route::put('/leave-management/{id}/edit-approved', [App\Http\Controllers\Web\LeaveController::class, 'updateApproved'])->name('attendance.leave-management.update-approved')->middleware('role:admin,hr,manager');
    Route::delete('/leave-management/{id}/cancel', [App\Http\Controllers\Web\LeaveController::class, 'cancel'])->name('attendance.leave-management.cancel');
    Route::get('/leave-management/balance', [App\Http\Controllers\Web\LeaveController::class, 'getLeaveBalance'])->name('attendance.leave-management.balance');
    Route::post('/leave-management/balance', [App\Http\Controllers\Web\LeaveController::class, 'storeBalance'])->name('attendance.leave-management.balance.store');
    Route::put('/leave-management/balance/{id}', [App\Http\Controllers\Web\LeaveController::class, 'updateBalance'])->name('attendance.leave-management.balance.update');
    Route::get('/leave-management/statistics', [App\Http\Controllers\Web\LeaveController::class, 'getStatistics'])->name('attendance.leave-management.statistics');


    // Official Business routes
    Route::get('/official-business', [App\Http\Controllers\Web\OfficialBusinessController::class, 'index'])->name('attendance.official-business');
    Route::get('/official-business/export/{format}', [App\Http\Controllers\Web\OfficialBusinessController::class, 'exportOfficialBusiness'])->name('attendance.official-business.export');
    Route::post('/official-business', [App\Http\Controllers\Web\OfficialBusinessController::class, 'store'])->name('attendance.official-business.store');
    Route::put('/official-business/{id}', [App\Http\Controllers\Web\OfficialBusinessController::class, 'updatePending'])->name('attendance.official-business.update-pending');
    Route::post('/official-business/{id}/resubmit', [App\Http\Controllers\Web\OfficialBusinessController::class, 'resubmit'])->name('attendance.official-business.resubmit');
    Route::delete('/official-business/{id}/cancel', [App\Http\Controllers\Web\OfficialBusinessController::class, 'cancel'])->name('attendance.official-business.cancel');
    Route::get('/official-business/statistics', [App\Http\Controllers\Web\OfficialBusinessController::class, 'getStatistics'])->name('attendance.official-business.statistics');
    Route::put('/official-business/{id}/status', [App\Http\Controllers\Web\OfficialBusinessController::class, 'updateStatus'])
        ->name('attendance.official-business.update-status')
        ->middleware('role:admin,hr,manager');
    Route::put('/official-business/{id}/edit-approved', [App\Http\Controllers\Web\OfficialBusinessController::class, 'updateApproved'])
        ->name('attendance.official-business.update-approved')
        ->middleware('role:admin,hr,manager');

    // Admin/HR only routes
    Route::middleware(['role:admin,hr'])->group(function () {
        Route::get('/reports', [App\Http\Controllers\Web\AttendanceController::class, 'reports'])->name('reports');

        // General reports for admin/hr (This is the old route, keeping it to avoid breaking)
        Route::get('/reports-old', [App\Http\Controllers\Web\AttendanceController::class, 'reports'])->name('reports.old');
    });
});

// Centralized Reports Module
Route::middleware(['role:admin,hr,manager'])->prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('/generate', [ReportController::class, 'generate'])->name('generate');
    Route::match(['get', 'post'], '/export', [ReportController::class, 'export'])->name('export');
});

// Tax Bracket Management routes (outside attendance prefix)
Route::resource('tax-brackets', App\Http\Controllers\Web\TaxBracketController::class);
Route::post('/tax-brackets/calculate', [App\Http\Controllers\Web\TaxBracketController::class, 'calculateTax'])->name('tax-brackets.calculate');
Route::post('/tax-brackets/philippine', [App\Http\Controllers\Web\TaxBracketController::class, 'createPhilippineBrackets'])->name('tax-brackets.philippine');

// Add these routes to your web.php file

// Document Management routes
Route::middleware(['auth'])->group(function () {
    // Documents routes
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::post('/documents/switch-company', [DocumentController::class, 'switchCompany'])->name('documents.switch-company');
    Route::get('/documents/export', [DocumentController::class, 'export'])->name('documents.export');
    Route::get('/documents/employee/{id}/export', [DocumentController::class, 'exportEmployee'])->name('documents.employee.export');

    // Employee details for modal
    Route::get('/employees/{id}/details', [DocumentController::class, 'getEmployeeDetails']);

    // Employee documents page
    Route::get('/employees/{id}/documents', [EmployeeController::class, 'documents'])->name('employees.documents');
});

// Employee Dashboard Routes
Route::middleware(['auth', 'verified'])->prefix('employee')->name('employee.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])->name('dashboard');

    // Payslip downloads - MAKE SURE THESE ROUTES ARE DEFINED
    Route::get('/payslip/download/{payrollId}', [EmployeeDashboardController::class, 'downloadPayslip'])->name('payslip.download');
    Route::get('/payroll-history', [EmployeeDashboardController::class, 'payrollHistory'])->name('payroll.history');
    Route::get('/my-schedule', [App\Http\Controllers\Web\AttendanceController::class, 'mySchedule'])
        ->name('schedule');
    // Dashboard data
    Route::get('/dashboard/data', [EmployeeDashboardController::class, 'getDashboardData'])->name('dashboard.data');
});

// Personalized routes for employees
Route::middleware(['auth'])->group(function () {
    // Employee's own attendance records
    Route::get('/my-attendance', [App\Http\Controllers\Web\AttendanceController::class, 'myAttendance'])
        ->name('attendance.my');
});

// ============================================
// DEVELOPER ROUTES - User Account CRUD
// ============================================
Route::middleware(['auth', 'role:admin,hr'])->prefix('developer')->name('developer.')->group(function () {
    Route::get('/accounts', [App\Http\Controllers\Developer\AccountController::class, 'index'])->name('accounts.index');
    Route::get('/accounts/link', [App\Http\Controllers\Developer\AccountController::class, 'linkForm'])->name('accounts.link');
    Route::post('/accounts/link', [App\Http\Controllers\Developer\AccountController::class, 'linkStore'])->name('accounts.link.store');
    Route::get('/accounts/{account}/edit', [App\Http\Controllers\Developer\AccountController::class, 'edit'])->name('accounts.edit');
    Route::post('/accounts', [App\Http\Controllers\Developer\AccountController::class, 'store'])->name('accounts.store');
    Route::put('/accounts/{account}', [App\Http\Controllers\Developer\AccountController::class, 'update'])->name('accounts.update');
    Route::delete('/accounts/{account}', [App\Http\Controllers\Developer\AccountController::class, 'destroy'])->name('accounts.destroy');
    Route::post('/accounts/{account}/restore', [App\Http\Controllers\Developer\AccountController::class, 'restore'])->name('accounts.restore');
    Route::post('/accounts/{account}/toggle-status', [App\Http\Controllers\Developer\AccountController::class, 'toggleStatus'])->name('accounts.toggle-status');
    Route::post('/accounts/{account}/send-reset-link', [App\Http\Controllers\Developer\AccountController::class, 'sendResetLink'])->name('accounts.send-reset-link');
    Route::post('/accounts/{account}/unlink', [App\Http\Controllers\Developer\AccountController::class, 'unlink'])->name('accounts.unlink');

    // Role & Permission Mapping is admin-only (stricter than the rest of this group).
    Route::get('/permissions', [App\Http\Controllers\Developer\AccountController::class, 'permissions'])
        ->middleware('role:admin')
        ->name('permissions.index');

    // Activity Logs are admin-only.
    Route::middleware('role:admin')->prefix('activity-logs')->name('activity-logs.')->group(function () {
        Route::get('/', [App\Http\Controllers\Developer\ActivityLogController::class, 'index'])->name('index');
        Route::get('/export', [App\Http\Controllers\Developer\ActivityLogController::class, 'export'])->name('export');
    });

    // Recycle Bin is admin-only.
    Route::middleware('role:admin')->prefix('recycle-bin')->name('recycle-bin.')->group(function () {
        Route::get('/', [App\Http\Controllers\Developer\RecycleBinController::class, 'index'])->name('index');
        Route::post('/{module}/{id}/restore', [App\Http\Controllers\Developer\RecycleBinController::class, 'restore'])->name('restore');
        Route::delete('/{module}/{id}', [App\Http\Controllers\Developer\RecycleBinController::class, 'forceDelete'])->name('force-delete');
    });

    // Database Backup is admin-only.
    Route::middleware('role:admin')->prefix('database-backup')->name('database-backup.')->group(function () {
        Route::get('/', [App\Http\Controllers\Developer\DatabaseBackupController::class, 'index'])->name('index');
        Route::post('/download', [App\Http\Controllers\Developer\DatabaseBackupController::class, 'download'])->name('download');
    });
});
