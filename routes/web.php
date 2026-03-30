<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Admin\VehicleMakeController;
use App\Http\Controllers\Admin\ChecklistTemplateController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PartController;
use App\Http\Controllers\Payment\PaymentController;
use App\Http\Controllers\Payment\MpesaCallbackController;
use App\Http\Controllers\ServiceRecordController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

// ─── Guest Routes ─────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',  [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

// ─── Authenticated Routes ─────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/password/change',  [PasswordController::class, 'showChangePassword'])->name('password.change');
    Route::post('/password/change', [PasswordController::class, 'changePassword'])->name('password.update');

    // Vehicles
    Route::get('/vehicles/models-by-make/{make}', [VehicleController::class, 'modelsByMake'])->name('vehicles.models-by-make');
    Route::resource('vehicles', VehicleController::class);

    // Customers
    Route::resource('customers', CustomerController::class);

    // Parts Inventory
    Route::post('/parts/{part}/restock', [PartController::class, 'restock'])->name('parts.restock');
    Route::resource('parts', PartController::class)->except(['show']);

    // Services
    Route::get('/services',                           [ServiceRecordController::class, 'index'])->name('services.index');
    Route::get('/services/new',                       [ServiceRecordController::class, 'create'])->name('services.create');
    Route::post('/services',                          [ServiceRecordController::class, 'store'])->name('services.store');
    Route::get('/services/{service}',                 [ServiceRecordController::class, 'show'])->name('services.show');
    Route::get('/services/{service}/bay',             [ServiceRecordController::class, 'bay'])->name('services.bay');
    Route::post('/services/{service}/checklist',      [ServiceRecordController::class, 'updateChecklist'])->name('services.checklist.update');
    Route::post('/services/{service}/parts',          [ServiceRecordController::class, 'addPart'])->name('services.parts.add');
    Route::delete('/services/{service}/parts/{part}', [ServiceRecordController::class, 'removePart'])->name('services.parts.remove');
    Route::post('/services/{service}/close',          [ServiceRecordController::class, 'closeService'])->name('services.close');
    Route::get('/services/{service}/report',          [ServiceRecordController::class, 'report'])->name('services.report');

    // Payments
    Route::get('/payments',                                [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{payment}/receipt',              [PaymentController::class, 'receipt'])->name('payments.receipt');
    Route::get('/payments/{payment}/mpesa-pending',        [PaymentController::class, 'mpesaPending'])->name('payments.mpesa.pending');
    Route::get('/payments/{payment}/mpesa-poll',           [PaymentController::class, 'pollMpesaStatus'])->name('payments.mpesa.poll');
    Route::post('/payments/{payment}/mpesa-confirm',       [PaymentController::class, 'confirmMpesa'])->name('payments.mpesa.confirm');
    Route::get('/services/{service}/payment',              [PaymentController::class, 'show'])->name('payments.show');
    Route::post('/services/{service}/payment/cash',        [PaymentController::class, 'payCash'])->name('payments.cash');
    Route::post('/services/{service}/payment/mpesa',       [PaymentController::class, 'initiateMpesa'])->name('payments.mpesa.initiate');

    // ─── Admin Only ───────────────────────────────────────────────────────────
    Route::middleware('admin')->group(function () {
        // User management
        Route::get('/users/create',  [RegisterController::class, 'showRegister'])->name('register');
        Route::post('/users/create', [RegisterController::class, 'register'])->name('register.post');
        Route::get('/users',              [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}/edit',  [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}',       [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}',    [UserController::class, 'destroy'])->name('users.destroy');

        // Vehicle Makes & Models
        Route::get('/admin/makes',                                   [VehicleMakeController::class, 'index'])->name('admin.makes.index');
        Route::post('/admin/makes',                                  [VehicleMakeController::class, 'store'])->name('admin.makes.store');
        Route::put('/admin/makes/{make}',                            [VehicleMakeController::class, 'updateMake'])->name('admin.makes.update');
        Route::delete('/admin/makes/{make}',                         [VehicleMakeController::class, 'destroyMake'])->name('admin.makes.destroy');
        Route::post('/admin/makes/{make}/models',                    [VehicleMakeController::class, 'storeModel'])->name('admin.models.store');
        Route::put('/admin/makes/{make}/models/{model}',             [VehicleMakeController::class, 'updateModel'])->name('admin.models.update');
        Route::delete('/admin/makes/{make}/models/{model}',          [VehicleMakeController::class, 'destroyModel'])->name('admin.models.destroy');

        // Checklist Templates
        Route::get('/admin/checklist',              [ChecklistTemplateController::class, 'index'])->name('admin.checklist.index');
        Route::post('/admin/checklist',             [ChecklistTemplateController::class, 'store'])->name('admin.checklist.store');
        Route::put('/admin/checklist/{template}',   [ChecklistTemplateController::class, 'update'])->name('admin.checklist.update');
        Route::delete('/admin/checklist/{template}',[ChecklistTemplateController::class, 'destroy'])->name('admin.checklist.destroy');
        Route::post('/admin/checklist/reorder',     [ChecklistTemplateController::class, 'reorder'])->name('admin.checklist.reorder');
    });
});

Route::get('/env-check', function() {
    return [
        'MPESA_CONSUMER_KEY' => env('MPESA_CONSUMER_KEY'),
        'MPESA_CONSUMER_SECRET' => env('MPESA_CONSUMER_SECRET'),
        'APP_KEY' => env('APP_KEY'),
    ];
});

// ── M-Pesa Daraja Callback (no auth, no CSRF) ─────────────────────────────────
Route::post('/api/mpesa/callback', [MpesaCallbackController::class, 'callback'])
     ->name('mpesa.callback')
     ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
