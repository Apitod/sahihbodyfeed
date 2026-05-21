<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Agent;
use App\Http\Controllers\Superadmin;
use App\Http\Controllers\Superadmin\AdminController as SuperadminAdminController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home.index');
});

// Auth Routes (login only — self-registration has been removed; admins create agents)
Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Global Profile Routes
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

// Agent Routes
Route::middleware(['auth', 'role:agent'])->prefix('agent')->name('agent.')->group(function () {
    Route::get('/dashboard', [Agent\DashboardController::class, 'index'])->name('dashboard');
    
    // Placeholders for sidebar implementation
    // Route::get('/transactions/create', [Agent\TransactionController::class, 'createRepeatOrder'])->name('repeat_order.create');
    // Route::post('/transactions', [Agent\TransactionController::class, 'storeRepeatOrder'])->name('repeat_order.store');
    Route::get('/transactions', [Agent\TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/commissions', [Agent\CommissionController::class, 'index'])->name('commissions');
    Route::get('/commissions/{commission}/invoice', [Agent\CommissionController::class, 'previewInvoice'])->name('commissions.invoice');
    Route::get('/rewards', [Agent\RewardController::class, 'index'])->name('rewards');
    Route::post('/rewards/{reward}/claim', [Agent\RewardController::class, 'claim'])->name('rewards.claim');
    Route::get('/matching-rewards', [Agent\MatchingRewardController::class, 'index'])->name('matching_rewards');
    Route::get('/network', [Agent\NetworkController::class, 'index'])->name('network');
});

// Admin Routes (Tier 2) — input agen baru & first-level verification ONLY
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // First-level verification (moves to pending_superadmin)
    Route::get('/verifications/transactions', [Admin\VerificationController::class, 'transactionsList'])->name('verifications.transactions');
    Route::get('/verifications/transactions/create-ro', [Admin\VerificationController::class, 'createRepeatOrder'])->name('verifications.ro.create');
    Route::post('/verifications/transactions/create-ro', [Admin\VerificationController::class, 'storeRepeatOrder'])->name('verifications.ro.store');
    Route::post('/verifications/transactions/{transaction}/approve', [Admin\VerificationController::class, 'approveTransaction'])->name('transactions.approve');
    Route::post('/verifications/transactions/{transaction}/reject', [Admin\VerificationController::class, 'rejectTransaction'])->name('transactions.reject');

    // Admin can do first-review on reward claims too
    Route::get('/verifications/rewards', [Admin\RewardVerificationController::class, 'index'])->name('verifications.rewards');
    Route::post('/verifications/rewards/{claim}/approve', [Admin\RewardVerificationController::class, 'approve'])->name('rewards.approve');
    Route::post('/verifications/rewards/{claim}/reject', [Admin\RewardVerificationController::class, 'reject'])->name('rewards.reject');

    // ─── Agent Management (Admin: Create and Edit) ─────────────────────────
    Route::get('/agents', [Admin\AgentController::class, 'index'])->name('agents.index');
    Route::get('/agents/create', [Admin\AgentController::class, 'create'])->name('agents.create');
    Route::post('/agents', [Admin\AgentController::class, 'store'])->name('agents.store');
    Route::get('/agents/{agent}', [Admin\AgentController::class, 'show'])->name('agents.show');
    Route::get('/agents/{agent}/edit', [Admin\AgentController::class, 'edit'])->name('agents.edit');
    Route::put('/agents/{agent}', [Admin\AgentController::class, 'update'])->name('agents.update');
    Route::post('/agents/{agent}/approve', [Admin\AgentController::class, 'approve'])->name('agents.approve');
    Route::post('/agents/{agent}/suspend', [Admin\AgentController::class, 'suspend'])->name('agents.suspend');

    // ─── Commission Management ─────────────────────────────────────────────
    Route::get('/commissions', [Admin\CommissionReportController::class, 'index'])->name('commissions.index');
    Route::get('/commissions/pdf', [Admin\CommissionReportController::class, 'downloadPdf'])->name('commissions.pdf');
    Route::post('/commissions/mark-paid', [Admin\CommissionReportController::class, 'markAsPaid'])->name('commissions.mark-paid');
    Route::post('/commissions/{commission}/upload-proof', [Admin\CommissionReportController::class, 'uploadProof'])->name('commissions.upload-proof');
    Route::get('/commissions/{commission}/invoice', [Admin\CommissionReportController::class, 'downloadInvoice'])->name('commissions.invoice');
    Route::get('/commissions/{commission}/invoice/preview', [Admin\CommissionReportController::class, 'previewInvoice'])->name('commissions.invoice.preview');
});

// Superadmin Routes (Tier 1) — full access + final approval + admin management
Route::middleware(['auth', 'role:superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [Superadmin\DashboardController::class, 'index'])->name('dashboard');

    // Final approval verification
    Route::get('/verifications/transactions', [Superadmin\VerificationController::class, 'transactionsList'])->name('verifications.transactions');
    Route::post('/verifications/transactions/{transaction}/approve', [Superadmin\VerificationController::class, 'approveTransaction'])->name('transactions.approve');
    Route::post('/verifications/transactions/{transaction}/reject', [Superadmin\VerificationController::class, 'rejectTransaction'])->name('transactions.reject');

    Route::get('/verifications/rewards', [Superadmin\RewardVerificationController::class, 'index'])->name('verifications.rewards');
    Route::post('/verifications/rewards/{claim}/approve', [Superadmin\RewardVerificationController::class, 'approve'])->name('rewards.approve');
    Route::post('/verifications/rewards/{claim}/reject', [Superadmin\RewardVerificationController::class, 'reject'])->name('rewards.reject');

    // ─── Full Agent Management (CRUD penuh) ──────────────────────────────────
    Route::resource('agents', Admin\AgentController::class); // reuse controller
    Route::post('/agents/{agent}/approve', [Admin\AgentController::class, 'approve'])->name('agents.approve');
    Route::post('/agents/{agent}/suspend', [Admin\AgentController::class, 'suspend'])->name('agents.suspend');

    // ─── Commission Management ─────────────────────────────────────────────
    Route::get('/commissions', [Admin\CommissionReportController::class, 'index'])->name('commissions.index');
    Route::get('/commissions/pdf', [Admin\CommissionReportController::class, 'downloadPdf'])->name('commissions.pdf');
    Route::post('/commissions/mark-paid', [Admin\CommissionReportController::class, 'markAsPaid'])->name('commissions.mark-paid');
    Route::post('/commissions/{commission}/upload-proof', [Admin\CommissionReportController::class, 'uploadProof'])->name('commissions.upload-proof');
    Route::get('/commissions/{commission}/invoice', [Admin\CommissionReportController::class, 'downloadInvoice'])->name('commissions.invoice');
    Route::get('/commissions/{commission}/invoice/preview', [Admin\CommissionReportController::class, 'previewInvoice'])->name('commissions.invoice.preview');

    // ─── Admin Management (Superadmin only) ───────────────────────────────────
    Route::get('/admins',                       [SuperadminAdminController::class, 'index'])->name('admins.index');
    Route::post('/admins',                      [SuperadminAdminController::class, 'store'])->name('admins.store');
    Route::put('/admins/{admin}',               [SuperadminAdminController::class, 'update'])->name('admins.update');
    Route::post('/admins/{admin}/activate',     [SuperadminAdminController::class, 'activate'])->name('admins.activate');
    Route::post('/admins/{admin}/suspend',      [SuperadminAdminController::class, 'suspend'])->name('admins.suspend');
    Route::delete('/admins/{admin}',            [SuperadminAdminController::class, 'destroy'])->name('admins.destroy');
});
