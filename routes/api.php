<?php

use App\Http\Controllers\Api\AdminstratorController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EmployeeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix("crm")->name("crm.v1")->middleware(['auth:sanctum', 'can:viewAny,App\Models\User'])->group(function() {
    // route for auth controller
    Route::post("/login", [AuthController::class, "login"])->withoutMiddleware(['auth:sanctum', 'can:viewAny,App\Models\User']);
    Route::get("/auth/user-profile", [AuthController::class, "userProfile"])->withoutMiddleware('can:viewAny,App\Models\User');
    // route for admin controller
    Route::post("invitation/send", [AdminstratorController::class, "sendInvitation"]);
    Route::post("create", [AdminstratorController::class, "CreateAnotherAdmin"]);
    // route for employee controller 
    Route::post("account/complete", [EmployeeController::class, "register"])->withoutMiddleware(['auth:sanctum', 'can:viewAny,App\Models\User']);
    Route::post("employee/check", [EmployeeController::class, "checkpassword"])->withoutMiddleware('auth:sanctum');

    // get company info
    Route::get("companies/data", [AdminstratorController::class, "getAllCompanies"]);
    // get invitation 
    Route::get("invitations/data", [AdminstratorController::class, "getAllEmployeeWithPendingStatus"]);

    Route::patch("invitations/update/{id}", [AdminstratorController::class, "updateInvitationStatus"]);

    Route::get('employee/invite/{token}', [EmployeeController::class, 'validateInvitation'])->withoutMiddleware(['auth:sanctum', 'can:viewAny,App\Models\User']);
});



