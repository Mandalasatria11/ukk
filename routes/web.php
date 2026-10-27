<?php

use App\Http\Controllers\Web\AdminController;
use App\Http\Controllers\Web\ApprovalController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\FinanceController;
use App\Http\Controllers\Web\ReimbursementController;
use App\Http\Controllers\Web\TravelRequestController;
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Quick Login (Development & Testing)
Route::get('/quick-login/{id}', [AuthController::class, 'quickLogin'])->name('quick.login');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Perjalanan Dinas (Travel Requests)
    Route::prefix('travel')->name('travel.')->group(function () {
        Route::get('/', [TravelRequestController::class, 'index'])->name('index');
        Route::get('/create', [TravelRequestController::class, 'create'])->name('create');
        Route::post('/', [TravelRequestController::class, 'store'])->name('store');
        Route::get('/{id}', [TravelRequestController::class, 'show'])->name('show');
        Route::post('/{id}/submit', [TravelRequestController::class, 'submit'])->name('submit');
        Route::post('/{id}/cancel', [TravelRequestController::class, 'cancel'])->name('cancel');
        Route::delete('/{id}', [TravelRequestController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/documents', [TravelRequestController::class, 'uploadDocument'])->name('documents.upload');
        Route::delete('/documents/{docId}', [TravelRequestController::class, 'deleteDocument'])->name('documents.delete');
    });

    // Persetujuan Perjalanan Dinas (Manager, HRD, Admin, Super Admin)
    Route::middleware('role:MANAGER,HRD,ADMIN,SUPER_ADMIN')->prefix('approvals')->name('approvals.')->group(function () {
        Route::get('/', [ApprovalController::class, 'index'])->name('index');
        Route::post('/{id}/decide', [ApprovalController::class, 'decide'])->name('decide');
    });

    // Pengajuan & Riwayat Reimbursement (Semua Role Login)
    Route::prefix('reimbursements')->name('reimbursements.')->group(function () {
        Route::get('/', [ReimbursementController::class, 'index'])->name('index');
        Route::get('/create', [ReimbursementController::class, 'create'])->name('create');
        Route::post('/', [ReimbursementController::class, 'store'])->name('store');
        Route::get('/{id}', [ReimbursementController::class, 'show'])->name('show');
        Route::post('/{id}/items', [ReimbursementController::class, 'addItem'])->name('items.add');
        Route::delete('/items/{itemId}', [ReimbursementController::class, 'deleteItem'])->name('items.delete');
        Route::post('/{id}/submit', [ReimbursementController::class, 'submit'])->name('submit');
    });

    // Keuangan & Pembayaran (Finance, Admin, Super Admin)
    Route::middleware('role:FINANCE,ADMIN,SUPER_ADMIN')->prefix('finance')->name('finance.')->group(function () {
        Route::get('/budgets', [FinanceController::class, 'budgets'])->name('budgets');
        Route::get('/verifications', [FinanceController::class, 'verifications'])->name('verifications');
        Route::post('/verifications/{id}', [FinanceController::class, 'verifyAction'])->name('verifications.action');
        Route::get('/payments', [FinanceController::class, 'payments'])->name('payments');
        Route::post('/payments/{id}', [FinanceController::class, 'payAction'])->name('payments.action');
        Route::get('/reports', [FinanceController::class, 'reports'])->name('reports');
    });

    // Administrasi & Audit Logs (Admin, Super Admin)
    Route::middleware('role:ADMIN,SUPER_ADMIN')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::post('/users/{id}/toggle', [AdminController::class, 'toggleUserActive'])->name('users.toggle');
        Route::get('/logs', [AdminController::class, 'activityLogs'])->name('logs');
        Route::get('/master', [AdminController::class, 'masterData'])->name('master');
        Route::post('/departments', [AdminController::class, 'storeDepartment'])->name('departments.store');
        Route::delete('/departments/{id}', [AdminController::class, 'deleteDepartment'])->name('departments.delete');
        Route::post('/positions', [AdminController::class, 'storePosition'])->name('positions.store');
        Route::delete('/positions/{id}', [AdminController::class, 'deletePosition'])->name('positions.delete');
        Route::post('/policies', [AdminController::class, 'storePolicy'])->name('policies.store');
        Route::delete('/policies/{id}', [AdminController::class, 'deletePolicy'])->name('policies.delete');
    });
});