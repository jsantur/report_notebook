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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
        \Illuminate\Support\Facades\View::composer('layouts.app', function ($view) {
            $config = \App\Models\Setting::where('key', 'shift_configuration')->first();
            $view->with('global_shift_settings', $config ? json_decode($config->value, true) : []);
        });
    }
}
