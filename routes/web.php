<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FollowupController;
use App\Http\Controllers\KpiController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ReportExportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WaManagementController;
use App\Http\Controllers\ZakatController;
use Illuminate\Support\Facades\Route;

// Authentication
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware(['auth'])->group(function () {
    Route::get('/', fn () => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Contacts
    Route::get('/contacts/export-xlsx', [ContactController::class, 'exportXlsx'])->name('contacts.export-xlsx');
    Route::resource('contacts', ContactController::class);

    // Pipeline / Leads
    Route::get('/pipeline', [LeadController::class, 'index'])->name('leads.index');
    Route::post('/leads', [LeadController::class, 'store'])->name('leads.store');
    Route::put('/leads/{lead}', [LeadController::class, 'update'])->name('leads.update');
    Route::post('/leads/{lead}/stage', [LeadController::class, 'updateStage'])->name('leads.update-stage');

    // Follow-ups
    Route::get('/followups', [FollowupController::class, 'index'])->name('followups.index');
    Route::post('/followups', [FollowupController::class, 'store'])->name('followups.store');
    Route::post('/followups/{followup}/complete', [FollowupController::class, 'complete'])->name('followups.complete');
    Route::post('/followups/{followup}/reschedule', [FollowupController::class, 'reschedule'])->name('followups.reschedule');

    // Zakat Module
    Route::get('/zakat', [ZakatController::class, 'index'])->name('zakat.index');
    Route::get('/zakat/calculator', [ZakatController::class, 'calculator'])->name('zakat.calculator');
    Route::post('/zakat/calculate-ajax', [ZakatController::class, 'calculateAjax'])->name('zakat.calculate-ajax');
    Route::post('/zakat/calculations', [ZakatController::class, 'store'])->name('zakat.store');
    Route::get('/zakat/history', [ZakatController::class, 'history'])->name('zakat.history');
    Route::get('/zakat/calculations/{calculation}', [ZakatController::class, 'show'])->name('zakat.show');

    // Transactions
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
    Route::put('/transactions/{transaction}', [TransactionController::class, 'update'])->name('transactions.update');

    // Campaigns & Programs
    Route::resource('campaigns', CampaignController::class)->only(['index', 'store', 'update']);
    Route::resource('programs', ProgramController::class)->only(['index', 'store', 'update']);

    // Reports Export XLSX
    Route::get('/reports/export-xlsx', [ReportExportController::class, 'export'])->name('reports.export-xlsx');

    // Master Admin Protected Routes
    Route::middleware('can:is-master')->group(function () {
        Route::resource('payment-methods', PaymentMethodController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::post('/payment-methods/{payment_method}/toggle', [PaymentMethodController::class, 'toggle'])->name('payment-methods.toggle');

        Route::get('/wa-management', [WaManagementController::class, 'index'])->name('whatsapp.index');
        Route::put('/wa-management/{wa_account}', [WaManagementController::class, 'updateAccount'])->name('whatsapp.update-account');
        Route::post('/wa-management/assign', [WaManagementController::class, 'assignContact'])->name('whatsapp.assign');

        Route::resource('users', UserController::class)->only(['index', 'store']);
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::post('/users/{user}/transfer', [UserController::class, 'transfer'])->name('users.transfer');

        Route::get('/kpi-targets', [KpiController::class, 'index'])->name('kpi-targets.index');
        Route::post('/kpi-targets', [KpiController::class, 'updateAll'])->name('kpi-targets.update-all');

        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    });
});
