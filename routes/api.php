<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TravelApiController as API;

Route::prefix('auth')->group(function () {
    Route::post('/register', [API::class, 'register']);
    Route::post('/login', [API::class, 'login']);
    Route::middleware('auth:sanctum')->get('/me', [API::class, 'me']);
});

Route::middleware('auth:sanctum')->group(function () {
    // Users & master data
    // NOTE: /users/departments & /users/positions must be registered BEFORE /users/{id}
    Route::get('/users/departments', [API::class, 'departmentsIndex']);
    Route::get('/users/positions', [API::class, 'positionsIndex']);
    Route::get('/departments', [API::class, 'departmentsIndex']);
    Route::get('/positions', [API::class, 'positionsIndex']);

    Route::middleware('role:ADMIN,SUPER_ADMIN')->get('/users', [API::class, 'usersIndex']);
    Route::middleware('role:ADMIN,SUPER_ADMIN')->get('/users/{id}', [API::class, 'userShow']);
    Route::middleware('role:SUPER_ADMIN')->put('/users/{id}', [API::class, 'userUpdate']);
    Route::middleware('role:SUPER_ADMIN')->patch('/users/{id}/role', [API::class, 'userRole']);
    Route::middleware('role:SUPER_ADMIN')->delete('/users/{id}', [API::class, 'userDeactivate']);

    Route::middleware('role:SUPER_ADMIN')->post('/users/departments', [API::class, 'departmentCreate']);
    Route::middleware('role:SUPER_ADMIN')->put('/users/departments/{id}', [API::class, 'departmentUpdate']);
    Route::middleware('role:SUPER_ADMIN')->delete('/users/departments/{id}', [API::class, 'departmentDelete']);
    Route::middleware('role:SUPER_ADMIN')->post('/users/positions', [API::class, 'positionCreate']);
    Route::middleware('role:SUPER_ADMIN')->put('/users/positions/{id}', [API::class, 'positionUpdate']);
    Route::middleware('role:SUPER_ADMIN')->delete('/users/positions/{id}', [API::class, 'positionDelete']);

    // Travel requests
    Route::get('/travel/policies/applicable', [API::class, 'policyApplicable']);
    Route::get('/travel/policies', [API::class, 'policiesIndex']);
    Route::middleware('role:SUPER_ADMIN')->post('/travel/policies', [API::class, 'policyCreate']);
    Route::middleware('role:SUPER_ADMIN')->put('/travel/policies/{id}', [API::class, 'policyUpdate']);
    Route::middleware('role:SUPER_ADMIN')->delete('/travel/policies/{id}', [API::class, 'policyDelete']);

    Route::get('/travel/bookings/pending', [API::class, 'pendingBookings']);
    Route::patch('/travel/bookings/{id}/status', [API::class, 'bookingStatus'])->middleware('role:ADMIN,SUPER_ADMIN');
    Route::get('/travel/{travelId}/bookings', [API::class, 'bookingsIndex']);
    Route::post('/travel/{travelId}/bookings', [API::class, 'bookingCreate'])->middleware('role:ADMIN,SUPER_ADMIN');

    Route::get('/travel', [API::class, 'travelIndex']);
    Route::post('/travel', [API::class, 'travelCreate'])->middleware('role:EMPLOYEE');
    Route::get('/travel/{id}', [API::class, 'travelShow']);
    Route::patch('/travel/{id}', [API::class, 'travelUpdate'])->middleware('role:EMPLOYEE');
    Route::delete('/travel/{id}', [API::class, 'travelDelete'])->middleware('role:EMPLOYEE');
    Route::post('/travel/{id}/submit', [API::class, 'travelSubmit'])->middleware('role:EMPLOYEE');
    Route::post('/travel/{id}/cancel', [API::class, 'travelCancel'])->middleware('role:EMPLOYEE');
    Route::post('/travel/{id}/documents', [API::class, 'documentUpload'])->middleware('role:EMPLOYEE');
    Route::get('/travel/{id}/documents', [API::class, 'documentsIndex']);
    Route::delete('/travel/documents/{docId}', [API::class, 'documentDelete'])->middleware('role:EMPLOYEE');

    // Approvals
    Route::get('/approvals/pending', [API::class, 'approvalsPending'])->middleware('role:MANAGER,DEPARTMENT_HEAD,HRD');
    Route::get('/approvals/travel/{travelId}', [API::class, 'approvalTimeline']);
    Route::patch('/approvals/{id}/decision', [API::class, 'approvalDecision'])->middleware('role:MANAGER,DEPARTMENT_HEAD,HRD');
    Route::post('/approvals/delegations', [API::class, 'delegationCreate'])->middleware('role:MANAGER,DEPARTMENT_HEAD,HRD');
    Route::get('/approvals/delegations', [API::class, 'delegationsIndex'])->middleware('role:MANAGER,DEPARTMENT_HEAD,HRD');
    Route::delete('/approvals/delegations/{id}', [API::class, 'delegationDelete'])->middleware('role:MANAGER,DEPARTMENT_HEAD,HRD');

    // Reimbursement
    Route::get('/reimbursements', [API::class, 'reimbursementsIndex']);
    Route::post('/reimbursements', [API::class, 'reimbursementCreate'])->middleware('role:EMPLOYEE');
    Route::get('/reimbursements/{id}', [API::class, 'reimbursementShow']);
    Route::post('/reimbursements/{id}/items', [API::class, 'reimbursementItemCreate'])->middleware('role:EMPLOYEE');
    Route::delete('/reimbursements/items/{itemId}', [API::class, 'reimbursementItemDelete'])->middleware('role:EMPLOYEE');
    Route::post('/reimbursements/{id}/submit', [API::class, 'reimbursementSubmit'])->middleware('role:EMPLOYEE');
    Route::patch('/reimbursements/{id}/verify', [API::class, 'reimbursementVerify'])->middleware('role:FINANCE');
    Route::patch('/reimbursements/{id}/pay', [API::class, 'reimbursementPay'])->middleware('role:FINANCE');

    // Notifications
    Route::get('/notifications/unread-count', [API::class, 'unreadCount']);
    Route::patch('/notifications/read-all', [API::class, 'notificationsReadAll']);
    Route::get('/notifications', [API::class, 'notificationsIndex']);
    Route::patch('/notifications/{id}/read', [API::class, 'notificationRead']);

    // Reporting
    Route::middleware('role:ADMIN,SUPER_ADMIN')->get('/reports/dashboard', [API::class, 'reportDashboard']);
    Route::middleware('role:FINANCE,ADMIN,SUPER_ADMIN')->get('/reports/expense-by-department', [API::class, 'expenseByDepartment']);
    Route::middleware('role:FINANCE,ADMIN,SUPER_ADMIN')->get('/reports/expense-by-employee', [API::class, 'expenseByEmployee']);
    Route::middleware('role:FINANCE,ADMIN,SUPER_ADMIN')->get('/reports/expense-by-project', [API::class, 'expenseByProject']);
    Route::middleware('role:FINANCE,ADMIN,SUPER_ADMIN')->get('/reports/export', [API::class, 'reportExport']);
});
