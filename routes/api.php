<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FarmController;
use App\Http\Controllers\Api\FieldController;
use App\Http\Controllers\Api\CropController;
use App\Http\Controllers\Api\LivestockController;
use App\Http\Controllers\Api\LivestockShedController;
use App\Http\Controllers\Api\BreedingController;
use App\Http\Controllers\Api\WorkerController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\InventoryCategoryController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\InventorySupplierController;
use App\Http\Controllers\Api\FinancialAccountController;
use App\Http\Controllers\Api\FinancialTransactionController;
use App\Http\Controllers\Api\FinancialInvoiceController;
use App\Http\Controllers\Api\FinancialBudgetController;
use App\Http\Controllers\Api\FinancialReportController;
use App\Http\Controllers\Api\UserProfileController;
use App\Http\Controllers\Api\UserManagementController;
use App\Http\Controllers\Api\UserInvitationController;

Route::prefix("v1")->group(function () {
    Route::prefix("auth")->group(function () {
        Route::post("register-company", [AuthController::class, "registerCompany"]);
        Route::post("login", [AuthController::class, "login"]);

        Route::middleware("auth:api")->group(function () {
            Route::get("me", [AuthController::class, "me"]);
            Route::post("logout", [AuthController::class, "logout"]);
            Route::post("refresh-token", [AuthController::class, "refreshToken"]);
            Route::post("change-password", [AuthController::class, "changePassword"]);
        });
    });

    Route::middleware("auth:api")->group(function () {
        Route::apiResource("farms", FarmController::class);
        Route::get("farms/{farm}/summary", [FarmController::class, "summary"]);
        Route::get("farms/{farm}/stats", [FarmController::class, "stats"]);

        Route::apiResource("fields", FieldController::class);
        Route::get("fields/{field}/map", [FieldController::class, "map"]);

        Route::apiResource("crops", CropController::class);
        Route::post("crops/{crop}/health", [CropController::class, "recordHealth"]);
        Route::get("crops/{crop}/health", [CropController::class, "getHealth"]);
        Route::post("crops/{crop}/harvest", [CropController::class, "recordHarvest"]);
        Route::get("crops/{crop}/yield", [CropController::class, "getYield"]);

        Route::apiResource("livestock", LivestockController::class);
        Route::post("livestock/{livestock}/health", [LivestockController::class, "recordHealth"]);
        Route::get("livestock/{livestock}/health", [LivestockController::class, "getHealth"]);

        Route::apiResource("sheds", LivestockShedController::class);
        Route::post("sheds/{shed}/clean", [LivestockShedController::class, "recordCleaning"]);
        Route::get("sheds/{shed}/grid", [LivestockShedController::class, "getGrid"]);
        Route::get("sheds/{shed}/stats", [LivestockShedController::class, "getStats"]);

        Route::apiResource("breeding", BreedingController::class);
        Route::post("breeding/{breeding}/birth", [BreedingController::class, "recordBirth"]);

        Route::apiResource("workers", WorkerController::class);
        Route::get("workers/{worker}/attendance", [WorkerController::class, "getAttendance"]);
        Route::get("workers/{worker}/performance", [WorkerController::class, "getPerformance"]);
        Route::get("workers/{worker}/payroll", [WorkerController::class, "getPayroll"]);

        Route::apiResource("schedules", ScheduleController::class);
        Route::get("schedules/by-date", [ScheduleController::class, "getByDate"]);

        Route::get("attendance", [AttendanceController::class, "index"]);
        Route::post("attendance/record", [AttendanceController::class, "record"]);
        Route::get("attendance/monthly", [AttendanceController::class, "getMonthly"]);

        Route::apiResource("inventory/categories", InventoryCategoryController::class);
        Route::get("inventory/low-stock", [InventoryController::class, "lowStock"]);
        Route::get("inventory/expired", [InventoryController::class, "expired"]);
        Route::get("inventory/value", [InventoryController::class, "value"]);
        Route::get("inventory/transactions", [InventoryController::class, "transactions"]);
        Route::post("inventory/use", [InventoryController::class, "use"]);
        Route::post("inventory/restock", [InventoryController::class, "restock"]);
        Route::get("inventory/transactions/{item}", [InventoryController::class, "itemTransactions"]);
        Route::apiResource("inventory", InventoryController::class);
        Route::apiResource("suppliers", InventorySupplierController::class);
        Route::get("suppliers/{supplier}/items", [InventorySupplierController::class, "items"]);

        Route::apiResource("accounts", FinancialAccountController::class);
        Route::get("accounts/{account}/balance", [FinancialAccountController::class, "balance"]);
        Route::get("transactions/summary", [FinancialTransactionController::class, "summary"]);
        Route::apiResource("transactions", FinancialTransactionController::class);
        Route::get("invoices/overdue", [FinancialInvoiceController::class, "overdue"]);
        Route::post("invoices/{invoice}/mark-paid", [FinancialInvoiceController::class, "markPaid"]);
        Route::apiResource("invoices", FinancialInvoiceController::class);
        Route::get("budgets/summary", [FinancialBudgetController::class, "summary"]);
        Route::apiResource("budgets", FinancialBudgetController::class);
        Route::post("reports/generate", [FinancialReportController::class, "generate"]);
        Route::apiResource("reports", FinancialReportController::class, ["only" => ["index", "show"]]);

        Route::prefix("users")->group(function () {
            Route::get("me", [UserProfileController::class, "me"]);
            Route::put("me", [UserProfileController::class, "updateProfile"]);
            Route::get("me/preferences", [UserProfileController::class, "getPreferences"]);
            Route::put("me/preferences", [UserProfileController::class, "updatePreferences"]);
            Route::post("me/change-password", [UserProfileController::class, "changePassword"]);
            Route::get("me/activity", [UserProfileController::class, "getActivity"]);

            Route::post("{user}/toggle-active", [UserManagementController::class, "toggleActive"]);
            Route::get("{user}/activity", [UserManagementController::class, "getActivity"]);
            Route::get("{user}/audit-trail", [UserManagementController::class, "getAuditTrail"]);
        });
        Route::apiResource("users", UserManagementController::class);

        Route::post("invitations/send", [UserInvitationController::class, "send"]);
        Route::get("invitations/pending", [UserInvitationController::class, "getPending"]);
        Route::apiResource("invitations", UserInvitationController::class, ["only" => ["index", "destroy"]]);
    });

    Route::post("invitations/{token}/accept", [UserInvitationController::class, "accept"]);
    Route::get("invitations/{token}", [UserInvitationController::class, "getByToken"]);
});
