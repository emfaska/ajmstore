<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Legacy analysis repository (kept for backward compatibility)
use App\Repositories\AnalysisRepositoryInterface;
use App\Repositories\EloquentAnalysisRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Legacy binding – retained until AnalysisRepository is refactored
        $this->app->bind(AnalysisRepositoryInterface::class, EloquentAnalysisRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
