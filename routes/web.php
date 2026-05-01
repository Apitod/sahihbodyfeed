<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Agent;
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
    Route::get('/rewards', [Agent\RewardController::class, 'index'])->name('rewards');
    Route::post('/rewards/{reward}/claim', [Agent\RewardController::class, 'claim'])->name('rewards.claim');
    Route::get('/matching-rewards', [Agent\MatchingRewardController::class, 'index'])->name('matching_rewards');
    Route::get('/network', [Agent\NetworkController::class, 'index'])->name('network');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // Placeholders for sidebar implementation
    Route::get('/verifications/transactions', [Admin\VerificationController::class, 'transactionsList'])->name('verifications.transactions');
    Route::get('/verifications/transactions/create-ro', [Admin\VerificationController::class, 'createRepeatOrder'])->name('verifications.ro.create');
    Route::post('/verifications/transactions/create-ro', [Admin\VerificationController::class, 'storeRepeatOrder'])->name('verifications.ro.store');
    Route::post('/verifications/transactions/{transaction}/approve', [Admin\VerificationController::class, 'approveTransaction'])->name('transactions.approve');
    Route::post('/verifications/transactions/{transaction}/reject', [Admin\VerificationController::class, 'rejectTransaction'])->name('transactions.reject');
    Route::get('/verifications/rewards', [Admin\RewardVerificationController::class, 'index'])->name('verifications.rewards');
    Route::post('/verifications/rewards/{claim}/approve', [Admin\RewardVerificationController::class, 'approve'])->name('rewards.approve');
    Route::post('/verifications/rewards/{claim}/reject', [Admin\RewardVerificationController::class, 'reject'])->name('rewards.reject');

    // ─── Agent Management ─────────────────────────────────────────────────────
    Route::resource('agents', Admin\AgentController::class);
    Route::post('/agents/{agent}/approve',  [Admin\AgentController::class, 'approve'])->name('agents.approve');
    Route::post('/agents/{agent}/suspend',  [Admin\AgentController::class, 'suspend'])->name('agents.suspend');

    // ─── Commission Management ────────────────────────────────────────────────
    Route::get('/commissions', [Admin\CommissionReportController::class, 'index'])->name('commissions.index');
    Route::get('/commissions/pdf', [Admin\CommissionReportController::class, 'downloadPdf'])->name('commissions.pdf');
    Route::post('/commissions/mark-paid', [Admin\CommissionReportController::class, 'markAsPaid'])->name('commissions.mark-paid');
});
