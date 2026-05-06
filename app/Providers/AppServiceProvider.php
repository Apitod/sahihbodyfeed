<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Services\CommissionDistributionService::class);
        $this->app->singleton(\App\Services\AgentRegistrationService::class);
        $this->app->singleton(\App\Services\RepeatOrderService::class);
        $this->app->singleton(\App\Services\RewardClaimService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ─── Superadmin bypass ──────────────────────────────────────────────────
        // Superadmin inherits ALL gates without explicit definition.
        Gate::before(function (User $user, string $ability) {
            if ($user->isSuperAdmin()) {
                return true;
            }
        });

        // ─── Agent Management ─────────────────────────────────────────────────

        // Only Superadmin can edit / delete master agent data.
        Gate::define('edit-agent', fn (User $user) => $user->isSuperAdmin());
        Gate::define('delete-agent', fn (User $user) => $user->isSuperAdmin());

        // Admin & Superadmin can create / view agents.
        Gate::define('create-agent', fn (User $user) => $user->isStaff());
        Gate::define('view-agents', fn (User $user) => $user->isStaff());

        // ─── Verification Flow ───────────────────────────────────────────────

        // Admin can do first-review (Pending -> PendingSuperadmin).
        Gate::define('first-review-transaction', fn (User $user) => $user->isStaff());

        // Only Superadmin can give final approval (bypassed via before()).
        Gate::define('final-approve-transaction', fn (User $user) => $user->isSuperAdmin());
        Gate::define('final-approve-claim', fn (User $user) => $user->isSuperAdmin());

        // ─── Admin Management ────────────────────────────────────────────────

        // Only Superadmin can create / manage Admin accounts.
        Gate::define('manage-admin-accounts', fn (User $user) => $user->isSuperAdmin());
    }
}
