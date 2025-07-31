<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Register observers for cache management
        \App\Models\KpiDescription::observe(\App\Observers\KpiDescriptionObserver::class);
        \App\Models\KpiCategory::observe(\App\Observers\KpiCategoryObserver::class);
    }
}
