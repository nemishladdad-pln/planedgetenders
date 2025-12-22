<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\RoleAdminController;
use App\Http\Controllers\ProjectAdminController;

Route::prefix('tenders')->group(function () {
    Route::post('{id}/budget', [ApiController::class, 'uploadBudget']); // protected by auth/roles in real usage
    Route::post('{id}/upload-signed', [ApiController::class, 'uploadSignedWorkOrder']);
    Route::get('/', [ApiController::class, 'mobileTenders']);
});

Route::prefix('vendors')->group(function () {
    Route::post('partial', [ApiController::class, 'vendorPartial']);
    Route::post('complete', [ApiController::class, 'vendorComplete']); // admin required
});

Route::post('auth/request-otp', [ApiController::class, 'requestOtp']);
Route::post('auth/verify-otp', [ApiController::class, 'verifyOtp']);

Route::post('buyers/register', [ApiController::class, 'buyerRegister']);
Route::post('buyers/{id}/approve', [ApiController::class, 'buyerApprove']); // admin required

Route::post('subscribe', [ApiController::class, 'subscribe']);
Route::post('invoices', [ApiController::class, 'createInvoice']);

Route::get('calendar', [ApiController::class, 'calendar']);
Route::get('admin/dashboard', [ApiController::class, 'dashboard']);

// Admin role & permission management (requires auth)
Route::middleware(['auth:sanctum'])->prefix('admin')->group(function () {
    Route::get('permissions', [RoleAdminController::class, 'indexPermissions']);
    Route::get('roles', [RoleAdminController::class, 'indexRoles']);
    Route::post('roles', [RoleAdminController::class, 'createRole']);
    Route::post('roles/{id}/permissions', [RoleAdminController::class, 'updateRolePermissions']);
    Route::post('users/{id}/roles', [RoleAdminController::class, 'assignRoleToUser']);
    Route::get('users/roles', [RoleAdminController::class, 'usersWithRoles']);

    // Project access management
    Route::get('projects', [ProjectAdminController::class, 'listProjects']);
    Route::get('roles', [ProjectAdminController::class, 'listRoles']);
    Route::get('projects/{id}/roles', [ProjectAdminController::class, 'getProjectRoles']);
    Route::post('projects/{id}/roles', [ProjectAdminController::class, 'assignRolesToProject']);
    Route::delete('projects/{id}/roles/{roleId}', [ProjectAdminController::class, 'removeRoleFromProject']);
    Route::get('project-assignments', [ProjectAdminController::class, 'listAssignments']);
    Route::post('projects/assign-multiple', [ProjectAdminController::class, 'assignRolesToProjects']);
});
