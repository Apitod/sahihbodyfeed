<?php

namespace App\Providers;

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
        //
    }
}
